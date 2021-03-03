<?php

/**
 * @addtogroup hooks
 * @{
 */

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;

function hook_usersnap_set_cookie_access(): AccessResultInterface
{
    return AccessResult::allowedIf(weather_is_nice());
}

/**
 * @} End of "addtogroup hooks".
 */
