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
use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldPersister;
use PrestaShop\PrestaShop\Adapter\Product\CustomizationFieldProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductProvider;
use PrestaShop\PrestaShop\Adapter\Product\ProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CommandHandler\SetProductCustomizationFieldsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField as CustomizationFieldDTO;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationFieldDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;

/**
 * Handles @see  SetProductCustomizationFieldsCommand using legacy object model
 */
class SetProductCustomizationFieldsHandler implements SetProductCustomizationFieldsHandlerInterface
{
    /**
     * @var CustomizationFieldDeleterInterface
     */
    private $customizationFieldDeleter;

    /**
     * @var CustomizationFieldPersister
     */
    private $customizationFieldPersister;

    /**
     * @var CustomizationFieldProvider
     */
    private $customizationFieldProvider;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @param CustomizationFieldPersister $customizationFieldPersister
     * @param CustomizationFieldDeleterInterface $customizationFieldDeleter
     * @param CustomizationFieldProvider $customizationFieldProvider
     * @param ProductUpdater $productUpdater
     * @param ProductProvider $productProvider
     */
    public function __construct(
        CustomizationFieldPersister $customizationFieldPersister,
        CustomizationFieldDeleterInterface $customizationFieldDeleter,
        CustomizationFieldProvider $customizationFieldProvider,
        ProductUpdater $productUpdater,
        ProductProvider $productProvider
    ) {
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->customizationFieldPersister = $customizationFieldPersister;
        $this->customizationFieldProvider = $customizationFieldProvider;
        $this->productUpdater = $productUpdater;
        $this->productProvider = $productProvider;
    }

    /**
     * {@inheritdoc}
     *
     * Creates, updates or deletes customization fields depending on differences of existing and provided fields
     */
    public function handle(SetProductCustomizationFieldsCommand $command): array
    {
        $product = $this->productProvider->get($command->getProductId());
        $providedCustomizationFields = $command->getCustomizationFields();
        $deletableFieldIds = $this->getDeletableFieldIds($providedCustomizationFields, $product);

        foreach ($providedCustomizationFields as $customizationFieldDTO) {
            if ($customizationFieldDTO->getCustomizationFieldId()) {
                $this->update($customizationFieldDTO);
            } else {
                $this->create($command->getProductId(), $customizationFieldDTO);
            }
        }

        $this->deleteCustomizationFields($deletableFieldIds);
        $this->productUpdater->refreshProductCustomizabilityProperties($product);

        return array_map(function (int $customizationFieldId): CustomizationFieldId {
            return new CustomizationFieldId($customizationFieldId);
        }, $product->getNonDeletedCustomizationFieldIds());
    }

    /**
     * @param ProductId $productId
     * @param CustomizationFieldDTO $customizationFieldDTO
     */
    public function create(ProductId $productId, CustomizationFieldDTO $customizationFieldDTO): void
    {
        $customizationField = new CustomizationField();
        $customizationField->id_product = $productId->getValue();
        $customizationField->type = $customizationFieldDTO->getType();
        $customizationField->required = $customizationFieldDTO->isRequired();
        $customizationField->name = $customizationFieldDTO->getLocalizedNames();
        $customizationField->is_module = $customizationFieldDTO->isAddedByModule();

        $this->customizationFieldPersister->add($customizationField);
    }

    /**
     * @param CustomizationFieldDTO $customizationFieldDTO
     *
     * @throws CoreException
     */
    private function update(CustomizationFieldDTO $customizationFieldDTO): void
    {
        $fieldId = $customizationFieldDTO->getCustomizationFieldId();
        $customizationFieldId = new CustomizationFieldId($fieldId);

        $this->customizationFieldPersister->update(
            $this->customizationFieldProvider->get($customizationFieldId),
            [
                'type' => $customizationFieldDTO->getType(),
                'required' => $customizationFieldDTO->isRequired(),
                'name' => $customizationFieldDTO->getLocalizedNames(),
                'is_module' => $customizationFieldDTO->isAddedByModule(),
            ]
        );
    }

    /**
     * @param array $providedCustomizationFields
     * @param Product $product
     *
     * @return array
     */
    private function getDeletableFieldIds(array $providedCustomizationFields, Product $product): array
    {
        $existingFieldIds = $product->getNonDeletedCustomizationFieldIds();
        $providedFieldsIds = array_map(function (CustomizationFieldDTO $field): ?int {
            return $field->getCustomizationFieldId();
        }, $providedCustomizationFields);

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
