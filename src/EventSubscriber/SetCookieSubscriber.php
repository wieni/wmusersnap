<?php

namespace Drupal\wmusersnap\EventSubscriber;

use Drupal\wmusersnap\UsersnapInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCookieSubscriber implements EventSubscriberInterface
{
    /** @var UsersnapInterface */
    protected $usersnap;

    public function __construct(
        UsersnapInterface $usersnap
    ) {
        $this->usersnap = $usersnap;
    }

    public static function getSubscribedEvents(): array
    {
        $events[KernelEvents::RESPONSE][] = ['setUsersnapCookie', 0];

        return $events;
    }

    public function setUsersnapCookie(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$this->usersnap->shouldSetCookie()) {
            return;
        }

        if ($this->usersnap->hasAccess()) {
            $expirationDate = (new \DateTime())->modify('3 weeks');
        } else if ($this->usersnap->shouldRemoveCookieOnLogout()) {
            $expirationDate = (new \DateTime())->modify('-1 day');
        } else {
            return;
        }

        foreach ($this->usersnap->getEnabledDomains() as $domain) {
            $cookie = $this->buildCookie($domain, $expirationDate);
            $response->headers->setCookie($cookie);
        }
    }

    protected function buildCookie(string $domain, \DateTime $expiration): Cookie
    {
        return new Cookie(
            'usersnap_enable:' . $domain,
            $this->usersnap->getApiKey(),
            $expiration,
            '/',
            $this->usersnap->getCookieDomain(),
            false,
            false
        );
    }
}
