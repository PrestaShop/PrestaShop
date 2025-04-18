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
