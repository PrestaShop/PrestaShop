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

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\Update;

use CustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Repository\CustomizationFieldMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationField as CoreCustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Product;

/**
 * Updates CustomizationField & Product relation
 */
class ProductCustomizationFieldUpdater
{
    /**
     * @var CustomizationFieldMultiShopRepository
     */
    private $customizationFieldRepository;

    /**
     * @var CustomizationFieldDeleter
     */
    private $customizationFieldDeleter;

    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @param CustomizationFieldMultiShopRepository $customizationFieldRepository
     * @param CustomizationFieldDeleter $customizationFieldDeleter
     * @param ProductMultiShopRepository $productRepository
     */
    public function __construct(
        CustomizationFieldMultiShopRepository $customizationFieldRepository,
        CustomizationFieldDeleter $customizationFieldDeleter,
        ProductMultiShopRepository $productRepository
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductId $productId
     * @param CoreCustomizationField[] $customizationFields
     * @param ShopConstraint $shopConstraint
     */
    public function setProductCustomizationFields(
        ProductId $productId,
        array $customizationFields,
        ShopConstraint $shopConstraint
    ): void {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        $deletableFieldIds = $this->getDeletableFieldIds($customizationFields, $product);

        foreach ($customizationFields as $customizationField) {
            $calculatedShopConstraint = $this->getCalculatedShopConstraint($customizationField, $shopConstraint);
            $adapterCustomizationField = $this->buildEntityFromDTO($productId, $customizationField);
            if ($customizationField->getCustomizationFieldId() !== null) {
                $this->customizationFieldRepository->update($adapterCustomizationField, $calculatedShopConstraint);
            } else {
                $shopIds = $this->productRepository->getShopIdsByConstraint($product, ShopConstraint::allShops());
                $this->customizationFieldRepository->add($adapterCustomizationField, $shopIds);
            }
        }

        $this->customizationFieldDeleter->bulkDelete($deletableFieldIds);
        $this->refreshProductCustomizability($product, $shopConstraint);
    }

    private function getCalculatedShopConstraint(
        CoreCustomizationField $customizationField,
        ShopConstraint $shopConstraint
    ): ShopConstraint {
        if ($customizationField->isApplyToAllShops() === false) {
            return $shopConstraint;
        }

        return ShopConstraint::allShops();
    }

    /**
     * @param Product $product
     * @param ShopConstraint $shopConstraint
     */
    public function refreshProductCustomizability(Product $product, ShopConstraint $shopConstraint): void
    {
        if ($product->hasActivatedRequiredCustomizableFields()) {
            $product->customizable = ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION;
        } elseif (!empty($product->getNonDeletedCustomizationFieldIds())) {
            $product->customizable = ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION;
        } else {
            $product->customizable = ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
        }

        $product->text_fields = $product->countCustomizationFields(CustomizationFieldType::TYPE_TEXT);
        $product->uploadable_files = $product->countCustomizationFields(CustomizationFieldType::TYPE_FILE);

        $this->productRepository->partialUpdate(
            $product,
            ['customizable', 'text_fields', 'uploadable_files'],
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS
        );
    }

    /**
     * Checks provided customization fields against existing ones to determine which ones to delete
     *
     * @param CoreCustomizationField[] $providedCustomizationFields
     * @param Product $product
     *
     * @return CustomizationFieldId[] ids of customization fields which should be deleted
     */
    private function getDeletableFieldIds(array $providedCustomizationFields, Product $product): array
    {
        $existingFieldIds = $product->getNonDeletedCustomizationFieldIds();
        $deletableIds = [];

        foreach ($existingFieldIds as $existingFieldId) {
            $deletableIds[$existingFieldId] = new CustomizationFieldId($existingFieldId);
        }

        foreach ($providedCustomizationFields as $providedCustomizationField) {
            $providedId = (int) $providedCustomizationField->getCustomizationFieldId();

            if (isset($deletableIds[$providedId])) {
                unset($deletableIds[$providedId]);
            }
        }

        return $deletableIds;
    }

    /**
     * @param ProductId $productId
     * @param CoreCustomizationField $coreCustomizationField
     *
     * @return CustomizationField
     */
    private function buildEntityFromDTO(
        ProductId $productId,
        CoreCustomizationField $coreCustomizationField
    ): CustomizationField {
        $customizationField = new CustomizationField();
        $customizationField->id = $coreCustomizationField->getCustomizationFieldId();
        $customizationField->id_product = $productId->getValue();
        $customizationField->type = $coreCustomizationField->getType();
        $customizationField->required = $coreCustomizationField->isRequired();
        $customizationField->name = $coreCustomizationField->getLocalizedNames();
        $customizationField->is_module = $coreCustomizationField->isAddedByModule();

        return $customizationField;
    }
}
