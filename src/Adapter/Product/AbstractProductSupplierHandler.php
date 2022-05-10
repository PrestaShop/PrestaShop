<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
