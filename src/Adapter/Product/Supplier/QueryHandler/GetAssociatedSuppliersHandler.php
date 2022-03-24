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

namespace PrestaShop\PrestaShop\Adapter\Product\Supplier\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryHandler\GetAssociatedSuppliersHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\NoSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

class GetAssociatedSuppliersHandler implements GetAssociatedSuppliersHandlerInterface
{
    /**
     * @var ProductSupplierRepository
     */
    private $productSupplierRepository;

    /**
     * @param ProductSupplierRepository $productSupplierRepository
     */
    public function __construct(
        ProductSupplierRepository $productSupplierRepository
    ) {
        $this->productSupplierRepository = $productSupplierRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetAssociatedSuppliers $query): AssociatedSuppliers
    {
        $defaultSupplier = $this->productSupplierRepository->getDefaultSupplierId($query->getProductId());
        $supplierIds = $this->productSupplierRepository->getAssociatedSupplierIds($query->getProductId());

        return new AssociatedSuppliers(
            $defaultSupplier ? $defaultSupplier->getValue() : NoSupplierId::NO_SUPPLIER_ID,
            array_map(static function (SupplierId $supplierId): int {
                return $supplierId->getValue();
            }, $supplierIds)
        );
    }
}
