<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Product\Provider;

use PrestaShop\PrestaShop\Core\Pricing\Exception\ProductPriceNotFoundException;

/**
 * In-memory product provider for unit tests. Accepts pre-configured arrays of
 * ProductPriceData keyed by "productId" or "productId-combinationId".
 */
class MockProductProvider implements ProductProviderInterface
{
    /**
     * @param array<int|string, ProductPriceData> $priceDataMap keyed by productId (int) or "productId-combinationId" (string)
     */
    public function __construct(
        protected readonly array $priceDataMap = [],
    ) {
    }

    public function getProductPriceData(int $productId, int $combinationId): ProductPriceData
    {
        $key = $combinationId > 0 ? $productId . '-' . $combinationId : (string) $productId;

        if (!isset($this->priceDataMap[$key])) {
            throw new ProductPriceNotFoundException(sprintf('Product %d not found', $productId));
        }

        return $this->priceDataMap[$key];
    }
}
