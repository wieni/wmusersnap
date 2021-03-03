<?php

namespace Drupal\wmusersnap;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Usersnap
{
    public const STATUS_ENABLED_IF_PERMISSION = 'if_permission';
    public const STATUS_ENABLED = 'always';
    public const STATUS_DISABLED = 'never';

    /** @var ConfigFactoryInterface */
    protected $configFactory;
    /** @var AccountProxyInterface */
    protected $currentUser;
    /** @var RequestStack */
    protected $requestStack;

    public function __construct(
        ConfigFactoryInterface $configFactory,
        AccountProxyInterface $currentUser,
        RequestStack $requestStack
    ) {
        $this->configFactory = $configFactory;
        $this->currentUser = $currentUser;
        $this->requestStack = $requestStack;
    }

    public function isEnabled(): bool
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
