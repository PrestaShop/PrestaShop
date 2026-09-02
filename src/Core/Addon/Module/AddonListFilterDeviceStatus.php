<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Module;

/**
 * @deprecated since 9.0.0 - This functionality was disabled. Class will be completely removed
 * in the next major. There is no replacement, all clients should have the same experience.
 */
class AddonListFilterDeviceStatus
{
    public const DEVICE_COMPUTER = 1;
    public const DEVICE_TABLET = 2;
    public const DEVICE_MOBILE = 4;

    public const ALL = 7;
}
