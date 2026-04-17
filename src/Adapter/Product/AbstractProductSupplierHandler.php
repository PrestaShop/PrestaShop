<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierUpdate;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use ProductSupplier;

/**
 * Holds reusable methods for ProductSupplier related query/command handlers
 */
abstract class AbstractProductSupplierHandler
{
    /**
     * @var ProductSupplierRepository
     */
    protected $productSupplierRepository;

    /**
     * @param ProductSupplierRepository $productSupplierRepository
     */
    public function __construct(
        ProductSupplierRepository $productSupplierRepository
    ) {
        $this->productSupplierRepository = $productSupplierRepository;
    }

    /**
     * @param ProductId $productId
     * @param CombinationId|null $combinationId
     *
     * @return array<int, ProductSupplierForEditing>
     */
    protected function getProductSuppliersInfo(ProductId $productId, ?CombinationId $combinationId = null): array
    {
        $hasDuplicatedSupplierNames = $this->productSupplierRepository->hasDuplicateSuppliersName();
        $productSuppliersInfo = $this->productSupplierRepository->getProductSuppliersInfo($productId, $combinationId);

        $productSuppliers = [];
        foreach ($productSuppliersInfo as $productSupplierInfo) {
            // Integrate the ID in the name so that suppliers with identical names are less confusing
            $supplierName = $productSupplierInfo['name'];
            if ($hasDuplicatedSupplierNames) {
                $supplierName = sprintf('%d - %s', (int) $productSupplierInfo['id_supplier'], $supplierName);
            }

            $productSuppliers[] = new ProductSupplierForEditing(
                (int) $productSupplierInfo['id_product_supplier'],
                (int) $productSupplierInfo['id_product'],
                (int) $productSupplierInfo['id_supplier'],
                $supplierName,
                $productSupplierInfo['product_supplier_reference'],
                $productSupplierInfo['product_supplier_price_te'],
                (int) $productSupplierInfo['id_currency'],
                (int) $productSupplierInfo['id_product_attribute']
            );
        }

        return $productSuppliers;
    }

    /**
     * Loads ProductSupplier object model with data from DTO.
     *
     * @param ProductSupplierUpdate $productSupplierUpdate
     *
     * @return ProductSupplier
     */
    protected function loadEntityFromDTO(ProductSupplierUpdate $productSupplierUpdate): ProductSupplier
    {
        $productSupplier = $this->productSupplierRepository->getByAssociation($productSupplierUpdate->getAssociation());
        $productSupplier->id_currency = $productSupplierUpdate->getCurrencyId()->getValue();
        $productSupplier->product_supplier_reference = $productSupplierUpdate->getReference();
        $productSupplier->product_supplier_price_te = (float) $productSupplierUpdate->getPriceTaxExcluded();

        return $productSupplier;
    }
}
