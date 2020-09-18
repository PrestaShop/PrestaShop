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

use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\AddProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\AddProductSupplierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use ProductSupplier;

/**
 * Handles @var AddProductSupplierCommand using legacy object model
 */
final class AddProductSupplierHandler implements AddProductSupplierHandlerInterface
{
    /**
     * @var ProductSupplierPersister
     */
    private $productSupplierPersister;

    /**
     * @param ProductSupplierPersister $productSupplierPersister
     */
    public function __construct(ProductSupplierPersister $productSupplierPersister)
    {
        $this->productSupplierPersister = $productSupplierPersister;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddProductSupplierCommand $command): ProductSupplierId
    {
        $productSupplier = new ProductSupplier();
        $this->fillEntityWithCommandData($productSupplier, $command);

        return $this->productSupplierPersister->add($productSupplier);
    }

    /**
     * @param ProductSupplier $productSupplier
     * @param AddProductSupplierCommand $command
     */
    private function fillEntityWithCommandData(ProductSupplier $productSupplier, AddProductSupplierCommand $command): void
    {
        $productIdValue = $command->getProductId()->getValue();
        $supplierIdValue = $command->getSupplierId()->getValue();
        $currencyIdValue = $command->getCurrencyId()->getValue();

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
}
