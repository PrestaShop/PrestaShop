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

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductSupplierHandler;
use PrestaShop\PrestaShop\Adapter\Product\ProductSupplierPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\CommandHandler\UpdateProductSupplierHandlerInterface;

/**
 * Handles @var UpdateProductSupplierCommand using legacy object model
 */
final class UpdateProductSupplierHandler extends AbstractProductSupplierHandler implements UpdateProductSupplierHandlerInterface
{
    /**
     * @var ProductSupplierPersister
     */
    private $productSupplierPersister;

    /**
     * @param ProductSupplierPersister $productSupplierPersister
     */
    public function __construct(
        ProductSupplierPersister $productSupplierPersister
    ) {
        $this->productSupplierPersister = $productSupplierPersister;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductSupplierCommand $command): void
    {
        $productSupplier = $this->getProductSupplier($command->getProductSupplierId());
        $this->productSupplierPersister->update($productSupplier, $this->formatPropertiesToUpdate($command));
    }

    /**
     * @param UpdateProductSupplierCommand $command
     */
    private function formatPropertiesToUpdate(UpdateProductSupplierCommand $command): array
    {
        $propertiesToUpdate = [];
        if (null !== $command->getCurrencyId()) {
            $propertiesToUpdate['id_currency'] = $command->getCurrencyId()->getValue();
        }

        if (null !== $command->getReference()) {
            $propertiesToUpdate['product_supplier_reference'] = $command->getReference();
        }

        if (null !== $command->getPriceTaxExcluded()) {
            $propertiesToUpdate['product_supplier_price_te'] = (string) $command->getPriceTaxExcluded();
        }

        if (null !== $command->getCombinationId()) {
            $propertiesToUpdate['id_product_attribute'] = $command->getCombinationId()->getValue();
        }

        return $propertiesToUpdate;
    }
}
