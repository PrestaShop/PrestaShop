<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult;

/**
 * Transfers product suppliers data
 */
class ProductSupplierOptions
{
    /**
     * @var int
     */
    private $defaultSupplierId;

    /**
     * @var int[]
     */
    private $supplierIds;

    /**
     * @var ProductSupplierForEditing[]
     */
    private $productSuppliers;

    /**
     * @param int $defaultSupplierId
     * @param ProductSupplierForEditing[] $productSuppliers
     */
    public function __construct(
        int $defaultSupplierId,
        array $supplierIds,
        array $productSuppliers
    ) {
        $this->defaultSupplierId = $defaultSupplierId;
        $this->supplierIds = $supplierIds;
        $this->productSuppliers = $productSuppliers;
    }

    /**
     * @return int
     */
    public function getDefaultSupplierId(): int
    {
        return $this->defaultSupplierId;
    }

    /**
     * @return int[]
     */
    public function getSupplierIds(): array
    {
        return $this->supplierIds;
    }

    /**
     * @return ProductSupplierForEditing[]
     */
    public function getProductSuppliers(): array
    {
        return $this->productSuppliers;
    }
}
