<?php

namespace Drupal\wmusersnap;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Usersnap implements UsersnapInterface
{
    /** @var ModuleHandlerInterface */
    protected $moduleHandler;
    /** @var ConfigFactoryInterface */
    protected $configFactory;
    /** @var AccountProxyInterface */
    protected $currentUser;
    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        ModuleHandlerInterface $moduleHandler,
        ConfigFactoryInterface $configFactory,
        AccountProxyInterface $currentUser,
        RequestStack $requestStack
    ) {
        $this->moduleHandler = $moduleHandler;
        $this->configFactory = $configFactory;
        $this->currentUser = $currentUser;
        $this->requestStack = $requestStack;
    }

    public function shouldSetCookie(): bool
    {
        $result = array_reduce(
            $this->moduleHandler->invokeAll('usersnap_set_cookie_access'),
            static function (AccessResult $finalResult, AccessResult $result) {
                return $finalResult->orIf($result);
            },
            AccessResult::neutral()
        );

        // Also execute the default access check except when the access result is
        // already forbidden, as in that case, it can not be anything else.
        if (!$result->isForbidden()) {
            $notDisabled = $this->getSetting('enable') !== static::STATUS_DISABLED;
            $result = $result->orIf(AccessResult::allowedIf($notDisabled));
        }

        return $result->isAllowed();
    }

    public function hasAccess(): bool
    {
        if ($this->getSetting('enable') === static::STATUS_ENABLED_IF_PERMISSION) {
            return $this->currentUser->hasPermission('view the usersnap feedback widget');
        }

        if ($this->getSetting('enable') === static::STATUS_ENABLED) {
            return true;
        }

        if ($this->getSetting('enable') === static::STATUS_DISABLED) {
            return false;
        }

        return false;
    }

    public function getApiKey(): ?string
    {
        return $this->getSetting('api_key');
    }

    public function getEnabledDomains(): array
    {
        if ($domains = $this->getSetting('domains')) {
            return $domains;
        }

        if ($request = $this->requestStack->getCurrentRequest()) {
            return [$request->getHost()];
        }

        return [];
    }

    public function getCookieDomain(): ?string
    {
        return $this->getSetting('cookie_domain');
    }

    protected function getSetting(string $key)
    {
        return $this->configFactory
            ->get('wmusersnap.settings')
            ->get($key);
    }
}
