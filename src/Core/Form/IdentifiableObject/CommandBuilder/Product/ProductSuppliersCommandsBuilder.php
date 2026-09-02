<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

final class ProductSuppliersCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['options']['suppliers']['supplier_ids'])
            && !isset($formData['options']['suppliers']['default_supplier_id'])
            && !isset($formData['options']['product_suppliers'])) {
            return [];
        }

        $associatedSuppliers = $formData['options']['suppliers']['supplier_ids'] ?? [];
        if (empty($associatedSuppliers)) {
            return [new RemoveAllAssociatedProductSuppliersCommand($productId->getValue())];
        }

        $commands[] = new SetSuppliersCommand(
            $productId->getValue(),
            $associatedSuppliers
        );

        $defaultSupplierId = $formData['options']['suppliers']['default_supplier_id'] ?? null;
        if (!empty($defaultSupplierId)) {
            $commands[] = new SetProductDefaultSupplierCommand(
                $productId->getValue(),
                (int) $defaultSupplierId
            );
        }

        $productSuppliersUpdates = $formData['options']['product_suppliers'] ?? [];
        if (!empty($productSuppliersUpdates)) {
            $productSuppliers = [];
            foreach ($productSuppliersUpdates as $productSupplierUpdate) {
                $supplierId = (int) $productSupplierUpdate['supplier_id'];

                $productSuppliers[] = $this->formatProductSupplier(
                    $supplierId,
                    $productSupplierUpdate
                );
            }

            $commands[] = new UpdateProductSuppliersCommand(
                $productId->getValue(),
                $productSuppliers
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
