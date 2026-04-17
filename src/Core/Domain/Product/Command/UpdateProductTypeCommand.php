<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

class UpdateProductTypeCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ProductType
     */
    private $productType;

    /**
     * @param int $productId
     * @param string $productType
     */
    public function __construct(int $productId, string $productType)
    {
        $this->productId = new ProductId($productId);
        $this->productType = new ProductType($productType);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ProductType
     */
    public function getProductType(): ProductType
    {
        return $this->productType;
    }
}
