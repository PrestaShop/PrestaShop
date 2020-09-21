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
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotAddProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotUpdateProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ProductSupplier;

/**
 * Persists ProductSupplier
 */
class ProductSupplierPersister extends AbstractObjectModelPersister
{
    /**
     * @var ProductSupplierValidator
     */
    private $productSupplierValidator;

    /**
     * @param ProductSupplierValidator $productSupplierValidator
     */
    public function __construct(
        ProductSupplierValidator $productSupplierValidator
    ) {
        $this->productSupplierValidator = $productSupplierValidator;
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param int $errorCode
     *
     * @return ProductSupplierId
     *
     * @throws CoreException
     */
    public function add(ProductSupplier $productSupplier, int $errorCode = 0): ProductSupplierId
    {
        $this->productSupplierValidator->validate($productSupplier);
        $id = $this->addObjectModel($productSupplier, CannotAddProductSupplierException::class, $errorCode);

        return new ProductSupplierId($id);
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function update(ProductSupplier $productSupplier, array $propertiesToUpdate, int $errorCode = 0): void
    {
        $this->fillProperties($productSupplier, $propertiesToUpdate);
        $this->productSupplierValidator->validate($productSupplier);
        $this->updateObjectModel($productSupplier, CannotUpdateProductSupplierException::class, $errorCode);
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param array $propertiesToUpdate
     */
    private function fillProperties(ProductSupplier $productSupplier, array $propertiesToUpdate): void
    {
        $existingProperties = [
            'product_supplier_reference',
            'id_product',
            'id_product_attribute',
            'id_supplier',
            'product_supplier_price_te',
            'id_currency',
        ];

        foreach ($existingProperties as $propertyName) {
            $this->fillProperty($productSupplier, $propertyName, $propertiesToUpdate);
        }
    }
}
