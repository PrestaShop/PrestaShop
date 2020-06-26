<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\AddCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\UpdateCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CommandHandler\AddCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CommandHandler\UpdateCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CustomizationField;

class UpdateCustomizationFieldsHandler extends AbstractProductHandler implements UpdateCustomizationFieldsHandlerInterface
{
    /**
     * @var AddCustomizationFieldHandlerInterface
     */
    private $addCustomizationFieldHandler;

    public function __construct(AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler)
    {
        $this->addCustomizationFieldHandler = $addCustomizationFieldHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(UpdateCustomizationFieldsCommand $command): void
    {
        $this->handleDeletion($command);

        /** @var CustomizationField $customizationField */
        foreach ($command->getCustomizationFields() as $customizationField) {
            if ($customizationField->getCustomizationFieldId()) {
                //@todo: update
            } else {
                //@todo: if it worth having separate handler?
                $this->addCustomizationFieldHandler->handle(new AddCustomizationFieldCommand(
                    $command->getProductId()->getValue(),
                    $customizationField->getType(),
                    $customizationField->isRequired(),
                    $customizationField->getLocalizedNames(),
                    $customizationField->isAddedByModule(),
                    false
                ));
            }
        }
    }

    private function handleDeletion(UpdateCustomizationFieldsCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());

        $usedFieldIds = array_map('intval', $product->getUsedCustomizationFieldsIds());
        $existingFieldIds = array_map('intval', $product->getCustomizationFieldIds());
        $providedFieldsIds = array_map(function (CustomizationField $field) {
            return $field->getCustomizationFieldId() ? $field->getCustomizationFieldId()->getValue() : null;
        }, $command->getCustomizationFields());

        $fieldIdsForDeletion = array_diff($existingFieldIds, $providedFieldsIds);

        foreach ($fieldIdsForDeletion as $fieldId) {
            $customizationFieldEntity = new \CustomizationField($fieldId);

            if (in_array($fieldId, $usedFieldIds)) {
                $customizationFieldEntity->softDelete();
            } else {
                $customizationFieldEntity->delete();
            }
        }
    }
}
