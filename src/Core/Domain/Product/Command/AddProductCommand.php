<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Command for creating product with basic information
 */
class AddProductCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var ProductType
     */
    private $productType;

    /**
     * @var ShopId
     */
    private $shopId;

    /**
     * @param string $productType
     * @param int $shopId
     * @param array $localizedNames
     */
    public function __construct(
        string $productType,
        int $shopId,
        array $localizedNames = []
    ) {
        $this->productType = new ProductType($productType);
        $this->shopId = new ShopId($shopId);
        $this->localizedNames = $localizedNames;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    /**
     * @return ProductType
     */
    public function getProductType(): ProductType
    {
        return $this->productType;
    }

    /**
     * @return ShopId
     */
    public function getShopId(): ShopId
    {
        return $this->shopId;
    }
}
