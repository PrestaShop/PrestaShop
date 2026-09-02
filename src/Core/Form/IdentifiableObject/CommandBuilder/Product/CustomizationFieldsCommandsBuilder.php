<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Builds commands from product customizations form
 */
final class CustomizationFieldsCommandsBuilder implements ProductCommandsBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildCommands(ProductId $productId, array $formData, ShopConstraint $singleShopConstraint): array
    {
        if (!isset($formData['details']['customizations'])) {
            return [];
        }

        $customizations = $formData['details']['customizations'];

        if (empty($customizations['customization_fields'])) {
            return [new RemoveAllCustomizationFieldsFromProductCommand($productId->getValue())];
        }

        return [
            new SetProductCustomizationFieldsCommand(
                $productId->getValue(),
                $this->buildCustomizationFields($customizations['customization_fields']),
                $singleShopConstraint
            ),
        ];
    }

    /**
     * @param array $customizationsFormData
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildCustomizationFields(array $customizationsFormData): array
    {
        $customizationFields = [];
        foreach ($customizationsFormData as $customization) {
            $customizationFields[] = [
                'type' => (int) $customization['type'],
                'localized_names' => $customization['name'],
                'is_required' => (bool) $customization['required'],
                'added_by_module' => isset($customization['addedByModule']) ? (bool) $customization['addedByModule'] : false,
                'id' => isset($customization['id']) ? (int) $customization['id'] : null,
            ];
        }

        return $customizationFields;
    }
}
