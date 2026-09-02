<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier;

/**
 * Defines settings for supplier
 */
final class SupplierSettings
{
    /**
     * Maximum allowed symbols for name
     */
    public const MAX_NAME_LENGTH = 64;

    /**
     * Maximum allowed symbols for meta title
     */
    public const MAX_META_TITLE_LENGTH = 255;

    /**
     * Maximum allowed symbols for meta description
     */
    public const MAX_META_DESCRIPTION_LENGTH = 512;
}
