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
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplier as ProductSupplierDTO;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierInfo;
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
     * @return array<int, ProductSupplierInfo>
     */
    protected function getProductSuppliersInfo(ProductId $productId, ?CombinationId $combinationId = null): array
    {
        $productSuppliersInfo = $this->productSupplierRepository->getProductSuppliersInfo($productId, $combinationId);

        $suppliersInfo = [];
        foreach ($productSuppliersInfo as $productSupplierInfo) {
            $supplierId = (int) $productSupplierInfo['id_supplier'];

            $suppliersInfo[] = new ProductSupplierInfo(
                $productSupplierInfo['name'],
                $supplierId,
                new ProductSupplierForEditing(
                    (int) $productSupplierInfo['id_product_supplier'],
                    (int) $productSupplierInfo['id_product'],
                    (int) $productSupplierInfo['id_supplier'],
                    $productSupplierInfo['product_supplier_reference'],
                    $productSupplierInfo['product_supplier_price_te'],
                    (int) $productSupplierInfo['id_currency'],
                    (int) $productSupplierInfo['id_product_attribute']
                )
            );
        }

        return $suppliersInfo;
    }

    /**
     * Loads ProductSupplier object model with data from DTO.
     *
     * @param ProductId $productId
     * @param ProductSupplierDTO $productSupplierDTO
     * @param CombinationId|null $combinationId
     *
     * @return ProductSupplier
     */
    protected function loadEntityFromDTO(ProductId $productId, ProductSupplierDTO $productSupplierDTO, ?CombinationId $combinationId = null): ProductSupplier
    {
        if ($productSupplierDTO->getProductSupplierId()) {
            $productSupplier = $this->productSupplierRepository->get($productSupplierDTO->getProductSupplierId());
        } else {
            $productSupplier = new ProductSupplier();
        }

        $productSupplier->id_product = $productId->getValue();
        $productSupplier->id_product_attribute = $combinationId ? $combinationId->getValue() : CombinationId::NO_COMBINATION;
        $productSupplier->id_supplier = $productSupplierDTO->getSupplierId()->getValue();
        $productSupplier->id_currency = $productSupplierDTO->getCurrencyId()->getValue();
        $productSupplier->product_supplier_reference = $productSupplierDTO->getReference();
        $productSupplier->product_supplier_price_te = $productSupplierDTO->getPriceTaxExcluded();

        return $productSupplier;
    }
}
