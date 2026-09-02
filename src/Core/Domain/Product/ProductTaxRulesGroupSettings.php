<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product;

/**
 * Some static settings of tax rules group inside Product context
 */
class ProductTaxRulesGroupSettings
{
    /**
     * Value of tax rules group which reflects that product has no tax rules group applied
     * Null value doesn't fit because it is used to identify partial updates
     */
    public const NONE_APPLIED = 0;
}
