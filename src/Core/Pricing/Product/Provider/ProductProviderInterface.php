<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Provider;

/**
 * Data-access layer for product pricing data. Different implementations serve
 * different contexts (catalog for FO, order_detail for BO, mock for tests).
 */
interface ProductProviderInterface
{
    /**
     * Returns the raw pricing data for a product and optionally its combination.
     *
     * @throws \PrestaShop\PrestaShop\Core\Pricing\Exception\ProductPriceNotFoundException when the product does not exist
     */
    public function getProductPriceData(int $productId, int $combinationId): ProductPriceData;
}
