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

use CustomizationField as CustomizationFieldEntity;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\AddCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\UpdateCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\Command\UpdateProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CommandHandler\AddCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CommandHandler\UpdateCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CommandHandler\UpdateProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Field\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Handles @var UpdateProductCustomizationFieldsCommand using legacy object model
 */
class UpdateProductCustomizationFieldsHandler extends AbstractProductHandler implements UpdateProductCustomizationFieldsHandlerInterface
{
    /**
     * @var AddCustomizationFieldHandlerInterface
     */
    private $addCustomizationFieldHandler;

    /**
     * @var UpdateCustomizationFieldHandlerInterface
     */
    private $updateCustomizationFieldHandler;

    /**
     * @param AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler
     * @param UpdateCustomizationFieldHandlerInterface $updateCustomizationFieldHandler
     */
    public function __construct(
        AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler,
        UpdateCustomizationFieldHandlerInterface $updateCustomizationFieldHandler
    ) {
        $this->addCustomizationFieldHandler = $addCustomizationFieldHandler;
        $this->updateCustomizationFieldHandler = $updateCustomizationFieldHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductCustomizationFieldsCommand $command): void
    {
        $this->handleDeletion($command);

        foreach ($command->getCustomizationFields() as $customizationField) {
            if ($customizationField->getCustomizationFieldId()) {
                $this->handleUpdate($command->getProductId(), $customizationField);
            } else {
                $this->handleCreation($command->getProductId(), $customizationField);
            }
        }
    }

    /**
     * @param ProductId $productId
     * @param CustomizationField $customizationField
     */
    public function handleCreation(ProductId $productId, CustomizationField $customizationField): void
    {
        $this->addCustomizationFieldHandler->handle(new AddCustomizationFieldCommand(
            $productId->getValue(),
            $customizationField->getType(),
            $customizationField->isRequired(),
            $customizationField->getLocalizedNames(),
            $customizationField->isAddedByModule(),
            false
        ));
    }

    /**
     * @param ProductId $productId
     * @param CustomizationField $customizationField
     */
    private function handleUpdate(ProductId $productId, CustomizationField $customizationField): void
    {
        $command = new UpdateCustomizationFieldCommand($productId->getValue());
        $command->setType($customizationField->getType());
        $command->setRequired($customizationField->isRequired());
        $command->setLocalizedNames($customizationField->getLocalizedNames());
        $command->setAddedByModule($customizationField->isAddedByModule());
        $command->setDeleted(false);

        $this->updateCustomizationFieldHandler->handle($command);
    }

    /**
     * @param UpdateProductCustomizationFieldsCommand $command
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function handleDeletion(UpdateProductCustomizationFieldsCommand $command): void
    {
        $product = $this->getProduct($command->getProductId());

        $usedFieldIds = array_map('intval', $product->getUsedCustomizationFieldsIds());
        $existingFieldIds = array_map('intval', $product->getCustomizationFieldIds());
        $providedFieldsIds = array_map(function (CustomizationField $field) {
            return $field->getCustomizationFieldId() ? $field->getCustomizationFieldId()->getValue() : null;
        }, $command->getCustomizationFields());

        $fieldIdsForDeletion = array_diff($existingFieldIds, $providedFieldsIds);

        foreach ($fieldIdsForDeletion as $fieldId) {
            $customizationFieldEntity = new CustomizationFieldEntity($fieldId);

            $successfullyDeleted = false;
            if (in_array($fieldId, $usedFieldIds)) {
                $successfullyDeleted = $customizationFieldEntity->softDelete();
            } else {
                $successfullyDeleted = $customizationFieldEntity->delete();
            }

            if (!$successfullyDeleted) {
                //@throw e
            }
        }
    }
}
