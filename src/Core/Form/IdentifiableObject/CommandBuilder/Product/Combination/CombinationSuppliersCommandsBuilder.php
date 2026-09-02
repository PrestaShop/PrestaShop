<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class CombinationSuppliersCommandsBuilder implements CombinationCommandsBuilderInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildCommands(CombinationId $combinationId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (empty($formData['product_suppliers'])) {
            return [];
        }

        $productSuppliersData = $formData['product_suppliers'];
        $productSuppliers = [];
        foreach ($productSuppliersData as $productSupplierDatum) {
            $productSuppliers[] = $this->formatProductSupplier(
                $productSupplierDatum
            );
        }

        return [
            new UpdateCombinationSuppliersCommand(
                $combinationId->getValue(),
                $productSuppliers
            ),
        ];
    }

    /**
     * @param array $productSupplierData
     *
     * @return array<string, mixed>
     */
    private function formatProductSupplier(array $productSupplierData): array
    {
        return [
            'supplier_id' => (int) $productSupplierData['supplier_id'],
            'currency_id' => (int) $productSupplierData['currency_id'],
            'reference' => (string) $productSupplierData['reference'],
            'price_tax_excluded' => (string) $productSupplierData['price_tax_excluded'],
            'product_supplier_id' => (int) $productSupplierData['product_supplier_id'],
        ];
    }
}
