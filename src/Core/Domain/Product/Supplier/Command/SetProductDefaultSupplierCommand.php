<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

class SetProductDefaultSupplierCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var SupplierId
     */
    private $defaultSupplierId;

    /**
     * @param int $productId
     * @param int $defaultSupplierId
     */
    public function __construct(int $productId, int $defaultSupplierId)
    {
        $this->productId = new ProductId($productId);
        $this->defaultSupplierId = new SupplierId($defaultSupplierId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return SupplierId
     */
    public function getDefaultSupplierId(): SupplierId
    {
        return $this->defaultSupplierId;
    }
}
