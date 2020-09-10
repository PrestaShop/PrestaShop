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

use PrestaShop\PrestaShop\Adapter\Product\AbstractCustomizationFieldHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\AddCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\DeleteCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\UpdateCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\UpdateProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\AddCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\DeleteCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\UpdateCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\UpdateProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Handles @var UpdateProductCustomizationFieldsCommand using legacy object model
 */
class UpdateProductCustomizationFieldsHandler extends AbstractCustomizationFieldHandler implements UpdateProductCustomizationFieldsHandlerInterface
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
     * @var DeleteCustomizationFieldHandlerInterface
     */
    private $deleteCustomizationFieldHandler;

    /**
     * @param AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler
     * @param UpdateCustomizationFieldHandlerInterface $updateCustomizationFieldHandler
     * @param DeleteCustomizationFieldHandlerInterface $deleteCustomizationFieldHandler
     */
    public function __construct(
        AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler,
        UpdateCustomizationFieldHandlerInterface $updateCustomizationFieldHandler,
        DeleteCustomizationFieldHandlerInterface $deleteCustomizationFieldHandler
    ) {
        $this->addCustomizationFieldHandler = $addCustomizationFieldHandler;
        $this->updateCustomizationFieldHandler = $updateCustomizationFieldHandler;
        $this->deleteCustomizationFieldHandler = $deleteCustomizationFieldHandler;
    }

    /**
     * {@inheritdoc}
     *
     * Creates, updates or deletes customization fields depending on differences of existing and provided fields
     */
    public function handle(UpdateProductCustomizationFieldsCommand $command): array
    {
        $deletableFieldIds = $this->getDeletableFieldIds($command);

        foreach ($command->getCustomizationFields() as $customizationField) {
            if ($customizationField->getCustomizationFieldId()) {
                $this->handleUpdate($customizationField);
            } else {
                $this->handleCreation($command->getProductId(), $customizationField);
            }
        }

        $this->deleteCustomizationFields($deletableFieldIds);
        $product = $this->getProduct($command->getProductId());

        return array_map(function ($customizationFieldId) {
            return new CustomizationFieldId($customizationFieldId);
        }, $product->getNonDeletedCustomizationFieldIds());
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
            $customizationField->isAddedByModule()
        ));
    }

    /**
     * @param CustomizationField $customizationField
     */
    private function handleUpdate(CustomizationField $customizationField): void
    {
        $command = new UpdateCustomizationFieldCommand($customizationField->getCustomizationFieldId());
        $command->setType($customizationField->getType());
        $command->setRequired($customizationField->isRequired());
        $command->setLocalizedNames($customizationField->getLocalizedNames());
        $command->setAddedByModule($customizationField->isAddedByModule());

        $this->updateCustomizationFieldHandler->handle($command);
    }

    /**
     * @param UpdateProductCustomizationFieldsCommand $command
     *
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function getDeletableFieldIds(UpdateProductCustomizationFieldsCommand $command): array
    {
        $product = $this->getProduct($command->getProductId());

        $existingFieldIds = array_map('intval', $product->getNonDeletedCustomizationFieldIds());
        $providedFieldsIds = array_map(function (CustomizationField $field) {
            return $field->getCustomizationFieldId();
        }, $command->getCustomizationFields());

        return array_diff($existingFieldIds, $providedFieldsIds);
    }

    /**
     * @param int[] $fieldIdsForDeletion
     */
    private function deleteCustomizationFields(array $fieldIdsForDeletion): void
    {
        foreach ($fieldIdsForDeletion as $fieldId) {
            $this->deleteCustomizationFieldHandler->handle(new DeleteCustomizationFieldCommand($fieldId));
        }
    }
}
