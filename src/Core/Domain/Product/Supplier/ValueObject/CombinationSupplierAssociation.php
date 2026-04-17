<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * This value object identifies a specific association between a supplier and a combination. Usually the association is
 * based on three criteria (productId, combinationId, supplierId) @see ProductSupplierAssociation. But in a context of
 * search only the combinationId is mandatory. So this VO can be used to search a supplier association with a combination.
 */
class CombinationSupplierAssociation implements SupplierAssociationInterface
{
    /**
     * @var CombinationId
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
     * @param int $combinationId
     * @param int $supplierId
     * @param int|null $productSupplierId
     */
    public function __construct(int $combinationId, int $supplierId, ?int $productSupplierId = null)
    {
        $this->combinationId = new CombinationId($combinationId);
        $this->supplierId = new SupplierId($supplierId);
        $this->productSupplierId = null !== $productSupplierId ? new ProductSupplierId($productSupplierId) : null;
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

    /**
     * No need for product ID to identify a supplier association for a designated combination, we can match it by
     * combination ID only.
     *
     * @return ProductId|null
     */
    public function getProductId(): ?ProductId
    {
        return null;
    }

    public function __toString()
    {
        return sprintf(
            '[productId: null, combinationId: %d, supplierId: %d]',
            $this->getCombinationId()->getValue(),
            $this->getSupplierId()->getValue()
        );
    }
}
