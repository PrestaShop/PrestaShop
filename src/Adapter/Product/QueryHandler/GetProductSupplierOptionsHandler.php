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

namespace PrestaShop\PrestaShop\Adapter\Product\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetProductSupplierOptionsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;

/**
 * Handles @see GetProductSupplierOptions query
 */
final class GetProductSupplierOptionsHandler extends AbstractProductHandler implements GetProductSupplierOptionsHandlerInterface
{
    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductSupplierRepository $productSupplierRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductSupplierRepository $productSupplierRepository,
        ProductRepository $productRepository
    ) {
        $this->productSupplierRepository = $productSupplierRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param GetProductSupplierOptions $query
     *
     * @return ProductSupplierOptions
     */
    public function handle(GetProductSupplierOptions $query): ProductSupplierOptions
    {
        $product = $this->productRepository->get($query->getProductId());
        $productSuppliersInfo = $this->productSupplierRepository->getProductSuppliersInfo($query->getProductId());

        $supplierOptions = [];

        foreach ($productSuppliersInfo as $productSupplierInfo) {
            $supplierId = (int) $productSupplierInfo['id_supplier'];

            $supplierOptions[] = new ProductSupplierInfo(
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

        return new ProductSupplierOptions(
            (int) $product->id_supplier,
            $supplierOptions
        );
    }
}
