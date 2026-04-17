<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon;

class AddonListFilterOrigin
{
    /* Bitwise operators */
    public const DISK = 1;
    public const ADDONS_MUST_HAVE = 2;
    public const ADDONS_SERVICE = 4;
    public const ADDONS_NATIVE = 8;
    public const ADDONS_NATIVE_ALL = 16;
    public const ADDONS_CUSTOMER = 32;
    public const ADDONS_ALL = 62;

    public const ALL = 63;
}
