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

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ProductSupplier;

/**
 * Vvalidates ProductSupplier legacy object model
 */
class ProductSupplierValidator extends AbstractObjectModelValidator
{
    /**
     * @param ProductSupplier $productSupplier
     *
     * @throws CoreException
     */
    public function validate(ProductSupplier $productSupplier): void
    {
        $propertiesErrorMap = [
            'product_supplier_reference' => ProductSupplierConstraintException::INVALID_REFERENCE,
            'product_supplier_price_te' => ProductSupplierConstraintException::INVALID_PRICE,
        ];

        foreach ($propertiesErrorMap as $property => $errorCode) {
            $this->validateObjectModelProperty(
                $productSupplier,
                $property,
                ProductSupplierConstraintException::class,
                $errorCode
            );
        }
    }

    public function assertRelatedEntitiesExists(ProductSupplier $productSupplier): void
    {
        //@todo: use provider services for asserts
        if (!Product::existsInDatabase($productId, 'product')) {
            throw new ProductNotFoundException(sprintf('Product #%d does not exist', $productId));
        }

        if (!Supplier::supplierExists($supplierId)) {
            throw new SupplierNotFoundException(sprintf('Supplier #%d does not exist', $supplierId));
        }

        if (!Currency::existsInDatabase($currencyId, 'currency')) {
            throw new CurrencyNotFoundException(sprintf('Currency #%d does not exist', $currencyId));
        }
    }
}
