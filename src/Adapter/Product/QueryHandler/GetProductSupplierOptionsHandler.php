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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSupplierOption;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSuppliersForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetProductSupplierOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use Supplier;

/**
 * Handles @see GetProductSupplierOptions query
 */
final class GetProductSupplierOptionsHandler extends AbstractProductHandler implements GetProductSupplierOptionsHandlerInterface
{
    /**
     * @var GetProductSuppliersForEditingHandler
     */
    private $getProductSuppliersForEditingHandler;

    /**
     * @param GetProductSuppliersForEditingHandler $getProductSuppliersForEditingHandler
     */
    public function __construct(GetProductSuppliersForEditingHandler $getProductSuppliersForEditingHandler)
    {
        $this->getProductSuppliersForEditingHandler = $getProductSuppliersForEditingHandler;
    }

    /**
     * @param GetProductSupplierOptions $query
     *
     * @return ProductSupplierOptions
     */
    public function handle(GetProductSupplierOptions $query): ProductSupplierOptions
    {
        $product = $this->getProduct($query->getProductId());
        $productSuppliersForEditing = $this->getProductSuppliersForEditingHandler->handle(new GetProductSuppliersForEditing((int) $product->id));
        $supplierOptions = [];

        $processedSuppliers = [];
        foreach ($productSuppliersForEditing as $productSupplierForEditing) {
            $supplierId = $productSupplierForEditing->getSupplierId();

            if (in_array($supplierId, $processedSuppliers)) {
                continue;
            }

            $supplierOptions[] = new ProductSupplierOption(
                Supplier::getNameById($supplierId),
                $supplierId,
                $this->filterProductSuppliersBySupplier($supplierId, $productSuppliersForEditing)
            );
            $processedSuppliers[] = $supplierId;
        }

        return new ProductSupplierOptions(
            (int) $product->id_supplier,
            $product->supplier_reference,
            $supplierOptions
        );
    }

    /**
     * Gets EditableProductSuppliers list for its supplier
     *
     * @param int $supplierId
     * @param ProductSupplierForEditing[] $productSuppliersForEditing
     *
     * @return ProductSupplierForEditing[]
     */
    private function filterProductSuppliersBySupplier(int $supplierId, array $productSuppliersForEditing): array
    {
        $productSuppliersBySupplier = [];

        foreach ($productSuppliersForEditing as $productSupplierForEditing) {
            if ($productSupplierForEditing->getSupplierId() === $supplierId) {
                $productSuppliersBySupplier[] = $productSupplierForEditing;
            }
        }

        return $productSuppliersBySupplier;
    }
}
