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
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyProvider;
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProvider;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use ProductSupplier;

/**
 * Vvalidates ProductSupplier legacy object model
 */
class ProductSupplierValidator extends AbstractObjectModelValidator
{
    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var SupplierProvider
     */
    private $supplierProvider;

    /**
     * @var CurrencyProvider
     */
    private $currencyProvider;

    /**
     * @param ProductProvider $productProvider
     * @param SupplierProvider $supplierProvider
     * @param CurrencyProvider $currencyProvider
     */
    public function __construct(
        ProductProvider $productProvider,
        SupplierProvider $supplierProvider,
        CurrencyProvider $currencyProvider
    ) {
        $this->productProvider = $productProvider;
        $this->supplierProvider = $supplierProvider;
        $this->currencyProvider = $currencyProvider;
    }

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

        $this->assertRelatedEntitiesExists($productSupplier);
    }

    /**
     * @param ProductSupplier $productSupplier
     */
    private function assertRelatedEntitiesExists(ProductSupplier $productSupplier): void
    {
        $this->productProvider->assertProductExists(new ProductId((int) $productSupplier->id_product));
        $this->supplierProvider->assertSupplierExists(new SupplierId((int) $productSupplier->id_supplier));
        $this->currencyProvider->assertCurrencyExists(new CurrencyId((int) $productSupplier->id_currency));
    }
}
