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
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\CannotUpdateProductSupplierException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierException;
use PrestaShopException;
use ProductSupplier;

/**
 * Handles @var UpdateProductSupplierCommand using legacy object model
 */
final class UpdateProductSupplierHandler extends AbstractProductSupplierHandler implements UpdateProductSupplierHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSupplierCommand $command): void
    {
        $productSupplier = $this->getProductSupplier($command->getProductSupplierId());
        $this->fillEntityWithCommandData($productSupplier, $command);

        try {
            $this->validateProductSupplierFields($productSupplier);
            if (!$productSupplier->update()) {
                throw new CannotUpdateProductSupplierException(sprintf(
                    'Failed updating product supplier #%d',
                    $productSupplier->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new ProductSupplierException(
                sprintf('Error occurred when updating product supplier #%d', $productSupplier->id),
                0,
                $e
            );
        }
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param UpdateProductSupplierCommand $command
     */
    private function fillEntityWithCommandData(
        ProductSupplier $productSupplier,
        UpdateProductSupplierCommand $command
    ): void {
        if (null !== $command->getCurrencyId()) {
            $currencyIdValue = $command->getCurrencyId()->getValue();
            $this->assertCurrencyExists($currencyIdValue);
            $productSupplier->id_currency = $currencyIdValue;
        }

        if (null !== $command->getReference()) {
            $productSupplier->product_supplier_reference = $command->getReference();
        }

        if (null !== $command->getPriceTaxExcluded()) {
            $productSupplier->product_supplier_price_te = (string) $command->getPriceTaxExcluded();
        }

        if (null !== $command->getCombinationId()) {
            $productSupplier->id_product_attribute = $command->getCombinationId()->getValue();
        }
    }

    /**
     * @param int $currencyId
     *
     * @throws CurrencyNotFoundException
     */
    private function assertCurrencyExists(int $currencyId): void
    {
        if (!Currency::existsInDatabase($currencyId, 'currency')) {
            throw new CurrencyNotFoundException(sprintf('Currency #%d does not exist', $currencyId));
        }
    }
}
