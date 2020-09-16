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
use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldProvider;
use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldUpdater;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\AddCustomizationFieldCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\AddCustomizationFieldHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\SetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationFieldDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Handles @see  SetProductCustomizationFieldsCommand using legacy object model
 */
class SetProductCustomizationFieldsHandler extends AbstractCustomizationFieldHandler implements SetProductCustomizationFieldsHandlerInterface
{
    /**
     * @var AddCustomizationFieldHandlerInterface
     */
    private $addCustomizationFieldHandler;

    /**
     * @var CustomizationFieldDeleterInterface
     */
    private $customizationFieldDeleter;

    /**
     * @var CustomizationFieldUpdater
     */
    private $customizationFieldUpdater;

    /**
     * @var CustomizationFieldProvider
     */
    private $customizationFieldProvider;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @param AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler
     * @param CustomizationFieldUpdater $customizationFieldUpdater
     * @param CustomizationFieldDeleterInterface $customizationFieldDeleter
     * @param CustomizationFieldProvider $customizationFieldProvider
     * @param ProductUpdater $productUpdater
     */
    public function __construct(
        AddCustomizationFieldHandlerInterface $addCustomizationFieldHandler,
        CustomizationFieldUpdater $customizationFieldUpdater,
        CustomizationFieldDeleterInterface $customizationFieldDeleter,
        CustomizationFieldProvider $customizationFieldProvider,
        ProductUpdater $productUpdater
    ) {
        $this->addCustomizationFieldHandler = $addCustomizationFieldHandler;
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->customizationFieldUpdater = $customizationFieldUpdater;
        $this->customizationFieldProvider = $customizationFieldProvider;
        $this->productUpdater = $productUpdater;
    }

    /**
     * {@inheritdoc}
     *
     * Creates, updates or deletes customization fields depending on differences of existing and provided fields
     */
    public function handle(SetProductCustomizationFieldsCommand $command): array
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
        $this->productUpdater->refreshProductCustomizabilityProperties($product);

        return array_map(function (int $customizationFieldId): CustomizationFieldId {
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
     *
     * @throws CoreException
     */
    private function handleUpdate(CustomizationField $customizationField): void
    {
        $fieldId = $customizationField->getCustomizationFieldId();
        $customizationFieldId = new CustomizationFieldId($fieldId);
        $customizationFieldObjectModel = $this->customizationFieldProvider->get($customizationFieldId);

        $this->customizationFieldUpdater->update(
            $customizationFieldObjectModel,
            [
                'type' => $customizationField->getType(),
                'required' => $customizationField->isRequired(),
                'name' => $customizationField->getLocalizedNames(),
                'is_module' => $customizationField->isAddedByModule(),
            ]
        );
    }

    /**
     * @param SetProductCustomizationFieldsCommand $command
     *
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function getDeletableFieldIds(SetProductCustomizationFieldsCommand $command): array
    {
        $product = $this->getProduct($command->getProductId());

        $existingFieldIds = $product->getNonDeletedCustomizationFieldIds();
        $providedFieldsIds = array_map(function (CustomizationField $field): ?int {
            return $field->getCustomizationFieldId();
        }, $command->getCustomizationFields());

        return array_diff($existingFieldIds, $providedFieldsIds);
    }

    /**
     * @param int[] $fieldIdsForDeletion
     */
    private function deleteCustomizationFields(array $fieldIdsForDeletion): void
    {
        $customizationFieldIdsForDeletion = array_map(function (int $fieldId): CustomizationFieldId {
            return new CustomizationFieldId($fieldId);
        }, $fieldIdsForDeletion);

        $this->customizationFieldDeleter->bulkDelete($customizationFieldIdsForDeletion);
    }
}
