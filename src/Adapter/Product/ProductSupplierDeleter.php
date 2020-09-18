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

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotDeleteProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ProductSupplierDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ProductSupplier;

/**
 * Deletes Product using legacy object model
 */
final class ProductSupplierDeleter extends AbstractObjectModelPersister implements ProductSupplierDeleterInterface
{
    /**
     * @var ProductSupplier[]
     */
    private $cachedProductSuppliers;

    /**
     * @var ProductSupplierProvider
     */
    private $productSupplierProvider;

    /**
     * @param ProductSupplierProvider $productSupplierProvider
     */
    public function __construct(
        ProductSupplierProvider $productSupplierProvider
    ) {
        $this->productSupplierProvider = $productSupplierProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ProductSupplierId $productSupplierId): void
    {
        if (!$this->deleteObjectModel($this->getProductSupplier($productSupplierId))) {
            throw new CannotDeleteProductSupplierException(
                sprintf('Failed to delete product supplier #%d', $productSupplierId->getValue())
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $productSupplierIds): void
    {
        // TODO: Implement bulkDelete() method.
    }

    /**
     * @param ProductSupplierId $productSupplierId
     *
     * @return ProductSupplier
     *
     * @throws ProductSupplierNotFoundException
     * @throws CoreException
     */
    private function getProductSupplier(ProductSupplierId $productSupplierId): ProductSupplier
    {
        $productSupplierIdValue = $productSupplierId->getValue();

        if (!isset($this->cachedProductSuppliers[$productSupplierIdValue])) {
            $this->cachedProductSuppliers[$productSupplierIdValue] = $this->productSupplierProvider->get($productSupplierId);
        }

        return $this->cachedProductSuppliers[$productSupplierIdValue];
    }
}
