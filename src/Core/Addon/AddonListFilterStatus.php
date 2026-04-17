<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon;

class AddonListFilterStatus
{
    public const NOT_ON_DISK = 1;
    public const ON_DISK = 2; // = Present on disk but not installed
    public const UNINSTALLED = 4;
    public const INSTALLED = 8; // = Installed
    public const DISABLED = 16;
    public const ENABLED = 32;

    public const ALL = 63;
}
