<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon;

class AddonListFilterType
{
    /* Bitwise operator */
    public const THEME = 1;
    public const MODULE = 2;
    public const SERVICE = 4;

    public const ALL = 7; /* = 1 | 2 | 4 */
}
