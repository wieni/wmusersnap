<?php

namespace Drupal\wmusersnap;

interface UsersnapInterface
{
    public const STATUS_ENABLED_IF_PERMISSION = 'if_permission';
    public const STATUS_ENABLED = 'always';
    public const STATUS_DISABLED = 'never';

    public function shouldSetCookie(): bool;

    public function hasAccess(): bool;

    public function getApiKey(): ?string;

    public function getEnabledDomains(): array;

    public function getCookieDomain(): ?string;
}
