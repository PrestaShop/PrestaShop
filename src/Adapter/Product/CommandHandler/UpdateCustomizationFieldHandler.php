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

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\AbstractCustomizationFieldHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\UpdateCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\UpdateCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotUpdateCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;

/**
 * Updates single customization field using legacy object model
 */
final class UpdateCustomizationFieldHandler extends AbstractCustomizationFieldHandler implements UpdateCustomizationFieldHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCustomizationFieldCommand $command): void
    {
        $customizationField = $this->getCustomizationField($command->getCustomizationFieldId());
        $this->fillEntityWithCommandData($command, $customizationField);
        $this->validateCustomizationFieldName($customizationField);

        try {
            if (false === $customizationField->update()) {
                throw new CannotUpdateCustomizationFieldException(sprintf(
                    'Failed to update customization field #%s',
                    $customizationField->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CustomizationFieldException(sprintf(
                'Error occurred when trying to update customization field #%d',
                $customizationField->id
            ));
        }

        $product = $this->getProduct(new ProductId((int) $customizationField->id_product));
        $this->refreshProductCustomizability($product);
        $this->refreshCustomizationFieldsCount($product);
        $this->performUpdate($product, CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS);
    }

    /**
     * @param UpdateCustomizationFieldCommand $command
     * @param CustomizationField $field
     */
    private function fillEntityWithCommandData(UpdateCustomizationFieldCommand $command, CustomizationField $field): void
    {
        if (null !== $command->getType()) {
            $field->type = $command->getType()->getValue();
        }

        if (null !== $command->isAddedByModule()) {
            $field->is_module = $command->isAddedByModule();
        }

        if (null !== $command->isRequired()) {
            $field->required = $command->isRequired();
        }

        if (null !== $command->getLocalizedNames()) {
            $field->name = $command->getLocalizedNames();
        }
    }
}
