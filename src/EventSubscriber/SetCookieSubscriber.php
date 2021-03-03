<?php

namespace Drupal\wmusersnap\EventSubscriber;

use Drupal\wmusersnap\Usersnap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCookieSubscriber implements EventSubscriberInterface
{
    /** @var Usersnap */
    protected $usersnap;

    public function __construct(
        Usersnap $usersnap
    ) {
        $this->usersnap = $usersnap;
    }

    public static function getSubscribedEvents(): array
    {
        $events[KernelEvents::RESPONSE][] = ['setUsersnapCookie', 0];

        return $events;
    }

    public function setUsersnapCookie(FilterResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$this->usersnap->shouldSetCookie()) {
            return;
        }

        foreach ($this->usersnap->getEnabledDomains() as $domain) {
            $expirationDate = $this->usersnap->hasAccess()
                ? (new \DateTime())->modify('1 week')
                : (new \DateTime())->modify('-1 day');

            $cookie = $this->buildCookie($domain, $expirationDate);
            $response->headers->setCookie($cookie);
        }
    }

    protected function buildCookie(string $domain, \DateTime $expiration): Cookie
    {
        return new Cookie(
            'usersnap_enable:' . $domain,
            true,
            $expiration,
            '/',
            $this->usersnap->getCookieDomain(),
            false,
            false
        );
    }
}
