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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\AddProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\AddProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShopException;
use Product;
use ProductSupplier;
use Supplier;

/**
 * Handles @var AddProductSupplierCommand using legacy object model
 */
final class AddProductSupplierHandler extends AbstractProductSupplierHandler implements AddProductSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddProductSupplierCommand $command): ProductSupplierId
    {
        $productSupplier = new ProductSupplier();

        try {
            $this->fillEntityWithCommandData($productSupplier, $command);
            $this->validateProductSupplierFields($productSupplier);

            if (!$productSupplier->add()) {
                throw new ProductSupplierException('Failed to add product supplier');
            }
        } catch (PrestaShopException $e) {
            throw new ProductSupplierException('Error occurred when adding product supplier');
        }

        return new ProductSupplierId((int) $productSupplier->id);
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param AddProductSupplierCommand $command
     *
     * @throws CurrencyNotFoundException
     * @throws ProductNotFoundException
     * @throws SupplierNotFoundException
     */
    private function fillEntityWithCommandData(ProductSupplier $productSupplier, AddProductSupplierCommand $command)
    {
        $productIdValue = $command->getProductId()->getValue();
        $supplierIdValue = $command->getSupplierId()->getValue();
        $currencyIdValue = $command->getCurrencyId()->getValue();

        $this->assertRelatedEntitiesExist($productIdValue, $supplierIdValue, $currencyIdValue);

        $productSupplier->id_product = $productIdValue;
        $productSupplier->id_supplier = $supplierIdValue;
        $productSupplier->id_currency = $currencyIdValue;

        if (null !== $command->getReference()) {
            $productSupplier->product_supplier_reference = $command->getReference();
        } else {
            $productSupplier->product_supplier_reference = '';
        }

        if (null !== $command->getPriceTaxExcluded()) {
            $productSupplier->product_supplier_price_te = (string) $command->getPriceTaxExcluded();
        } else {
            $productSupplier->product_supplier_price_te = 0;
        }

        if (null !== $command->getCombinationId()) {
            $productSupplier->id_product_attribute = $command->getCombinationId()->getValue();
        } else {
            $productSupplier->id_product_attribute = CombinationId::NO_COMBINATION;
        }
    }

    /**
     * @param int $productId
     * @param int $supplierId
     * @param int $currencyId
     *
     * @throws CurrencyNotFoundException
     * @throws ProductNotFoundException
     * @throws SupplierNotFoundException
     */
    private function assertRelatedEntitiesExist(int $productId, int $supplierId, int $currencyId): void
    {
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
