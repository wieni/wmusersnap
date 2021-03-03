<?php

namespace Drupal\wmusersnap;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Usersnap
{
    public const STATUS_ENABLED_IF_PERMISSION = 'if_permission';
    public const STATUS_ENABLED = 'always';
    public const STATUS_DISABLED = 'never';

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
            $result = $result->orIf(AccessResult::allowedIf($this->hasAccess()));
        }

        return $result->isAllowed();
    }

    public function hasAccess(): bool
    {
        if ($this->getSetting('enable') === 'if_permission') {
            return $this->currentUser->hasPermission('view the usersnap feedback widget');
        }

        if ($this->getSetting('enable') === 'always') {
            return true;
        }

        if ($this->getSetting('enable') === 'never') {
            return false;
        }

        return false;
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
