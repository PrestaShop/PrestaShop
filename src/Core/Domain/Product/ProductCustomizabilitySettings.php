<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product;

/**
 * Holds value which represents how customizable the product is
 */
class ProductCustomizabilitySettings
{
    /**
     * The product does not have any customiztion fields, so it is not customizable at all
     */
    public const NOT_CUSTOMIZABLE = 0;

    /**
     * The product has at least one customization field, but none of them are required
     */
    public const ALLOWS_CUSTOMIZATION = 1;

    /**
     * The product has at least one customization field which is required
     */
    public const REQUIRES_CUSTOMIZATION = 2;

    /**
     * This class shouldn't be instantiated as its purpose is to hold some setting values
     */
    private function __construct()
    {
    }
}
