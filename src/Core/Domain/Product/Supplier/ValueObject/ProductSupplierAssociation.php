<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * This value object identifies a specific association between a supplier and a product. It is based on three criteria
 * productId, combinationId and supplierId. For product without combinations combinationId is always 0.
 *
 * The productSupplierId is optional, it may be present when the association already exists in DB or absent if we
 * haven't created it yet but we can still use this VO based on the criteria.
 */
class ProductSupplierAssociation implements SupplierAssociationInterface
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CombinationIdInterface
     */
    private $combinationId;

    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var ProductSupplierId|null
     */
    private $productSupplierId;

    /**
     * @param int $productId
     * @param int $combinationId
     * @param int $supplierId
     * @param int|null $productSupplierId
     */
    public function __construct(int $productId, int $combinationId, int $supplierId, ?int $productSupplierId = null)
    {
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId ? new CombinationId($combinationId) : new NoCombinationId();
        $this->supplierId = new SupplierId($supplierId);
        $this->productSupplierId = null !== $productSupplierId ? new ProductSupplierId($productSupplierId) : null;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CombinationIdInterface
     */
    public function getCombinationId(): CombinationIdInterface
    {
        return $this->combinationId;
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId(): SupplierId
    {
        return $this->supplierId;
    }

    /**
     * @return ProductSupplierId|null
     */
    public function getProductSupplierId(): ?ProductSupplierId
    {
        return $this->productSupplierId;
    }

    public function __toString()
    {
        return sprintf(
            '[productId: %d, combinationId: %d, supplierId: %d]',
            $this->getProductId()->getValue(),
            $this->getCombinationId()->getValue(),
            $this->getSupplierId()->getValue()
        );
    }
}
