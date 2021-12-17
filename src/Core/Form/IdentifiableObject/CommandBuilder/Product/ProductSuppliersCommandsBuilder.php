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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

final class ProductSuppliersCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData): array
    {
        if (!isset($formData['options']['suppliers']['product_suppliers']) && !isset($formData['options']['suppliers']['default_supplier_id'])) {
            return [];
        }

        $productSuppliersData = $formData['options']['suppliers']['product_suppliers'];
        if (empty($productSuppliersData)) {
            return [new RemoveAllAssociatedProductSuppliersCommand($productId->getValue())];
        }

        $productSuppliers = [];
        foreach ($productSuppliersData as $productSupplierDatum) {
            $supplierId = (int) $productSupplierDatum['supplier_id'];

            $productSuppliers[] = $this->formatProductSupplier(
                $supplierId,
                $productSupplierDatum
            );
        }

        $commands = [
            new SetProductSuppliersCommand(
                $productId->getValue(),
                $productSuppliers
            ),
        ];

        if (!empty($formData['options']['suppliers']['default_supplier_id'])) {
            $commands[] = new SetProductDefaultSupplierCommand(
                $productId->getValue(),
                (int) $formData['options']['suppliers']['default_supplier_id']
            );
        }

        return $commands;
    }

    /**
     * @param int $supplierId
     * @param array $productSupplierData
     *
     * @return array<string, mixed>
     */
    private function formatProductSupplier(int $supplierId, array $productSupplierData): array
    {
        return [
            'supplier_id' => $supplierId,
            'currency_id' => (int) $productSupplierData['currency_id'],
            'reference' => (string) $productSupplierData['reference'],
            'price_tax_excluded' => (string) $productSupplierData['price_tax_excluded'],
            'product_supplier_id' => (int) $productSupplierData['product_supplier_id'],
        ];
    }
}
