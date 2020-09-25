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

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShopException;
use ProductSupplier;

/**
 * Provides reusable methods for product supplier handlers
 */
abstract class AbstractProductSupplierHandler extends AbstractProductHandler
{
    /**
     * @param ProductSupplierId $productSupplierId
     *
     * @return ProductSupplier
     */
    protected function getProductSupplier(ProductSupplierId $productSupplierId): ProductSupplier
    {
        $productSupplierIdValue = $productSupplierId->getValue();

        try {
            $productSupplier = new ProductSupplier($productSupplierIdValue);

            if ((int) $productSupplier->id !== $productSupplierIdValue) {
                throw new ProductSupplierNotFoundException(sprintf(
                    'Product supplier #%d was not found',
                    $productSupplierIdValue
                ));
            }
        } catch (PrestaShopException $e) {
            throw new ProductSupplierException(
                sprintf('Error occurred when trying to get product supplier #%d', $productSupplierIdValue),
                0,
                $e
            );
        }

        return $productSupplier;
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param string $field
     * @param int $errorCode
     */
    protected function validateProductSupplierFields(ProductSupplier $productSupplier): void
    {
        $fieldsErrorMap = [
            ProductSupplierConstraintException::INVALID_REFERENCE => 'product_supplier_reference',
            ProductSupplierConstraintException::INVALID_PRICE => 'product_supplier_price_te',
        ];

        foreach ($fieldsErrorMap as $errorCode => $field) {
            $value = $productSupplier->{$field};

            if (true !== $productSupplier->validateField($field, $value)) {
                throw new ProductSupplierConstraintException(
                    sprintf('Invalid product supplier "%s" value. Got "%s"', $field, var_export($value, true)),
                    $errorCode
                );
            }
        }
    }
}
