<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

/**
 * Provide dynamic complex options to the product type (like preview data that depend
 * on product current data, or specific options for inputs that are deep in the form
 * structure).
 */
class ProductFormOptionsProvider implements FormOptionsProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getOptions(int $id, array $data): array
    {
        return [
            'product_type' => $data['header']['type'] ?? ProductType::TYPE_STANDARD,
            'virtual_product_file_id' => $data['stock']['virtual_product_file']['virtual_product_file_id'] ?? null,
            'active' => $data['header']['active'] ?? false,
            'tax_rules_group_id' => $data['pricing']['retail_price']['tax_rules_group_id'] ?? null,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultOptions(array $data): array
    {
        return [];
    }
}
