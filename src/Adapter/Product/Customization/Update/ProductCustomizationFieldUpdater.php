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

use CustomizationField as LegacyCustomizationField;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Repository\CustomizationFieldRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates CustomizationField & Product relation
 */
class ProductCustomizationFieldUpdater
{
    /**
     * @var CustomizationFieldRepository
     */
    private $customizationFieldRepository;

    /**
     * @var CustomizationFieldDeleter
     */
    private $customizationFieldDeleter;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param CustomizationFieldRepository $customizationFieldRepository
     * @param CustomizationFieldDeleter $customizationFieldDeleter
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CustomizationFieldRepository $customizationFieldRepository,
        CustomizationFieldDeleter $customizationFieldDeleter,
        ProductRepository $productRepository
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductId $productId
     * @param LegacyCustomizationField[] $customizationFields
     * @param ShopConstraint $shopConstraint
     */
    public function setProductCustomizationFields(
        ProductId $productId,
        array $customizationFields,
        ShopConstraint $shopConstraint
    ): void {
        $productShops = $this->productRepository->getAssociatedShopIds($productId);
        $deletableFieldIds = $this->getDeletableFieldIds($customizationFields, $productId);

        foreach ($customizationFields as $customizationField) {
            if ($customizationField->id) {
                $this->customizationFieldRepository->update($customizationField, [$shopConstraint->getShopId()]);
            } else {
                $this->customizationFieldRepository->add($customizationField, $productShops);
            }
        }

        $this->customizationFieldDeleter->bulkDelete($deletableFieldIds);
        $this->refreshProductCustomizability($productId);
    }

    /**
     * @param ProductId $productId
     */
    public function refreshProductCustomizability(ProductId $productId): void
    {
        // The modified fields are defined as multishop, but they depend on the association with customization fields
        // which are not multishop, so all those fields must be synced between all associated shops
        $product = $this->productRepository->getByShopConstraint($productId, ShopConstraint::allShops());
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
            ShopConstraint::allShops(),
            CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS
        );
    }

    /**
     * Checks provided customization fields against existing ones to determine which ones to delete
     *
     * @param LegacyCustomizationField[] $providedCustomizationFields
     * @param ProductId $productId
     *
     * @return CustomizationFieldId[] ids of customization fields which should be deleted
     */
    private function getDeletableFieldIds(array $providedCustomizationFields, ProductId $productId): array
    {
        // Get currently associated customization fields (which are not soft deleted already)
        $existingFieldIds = $this->customizationFieldRepository->getCustomizationFieldIds($productId, false);

        // Filter all fields that are still present in the list, the returned list contains the fields that need to be deleted
        return array_filter($existingFieldIds, static function (CustomizationFieldId $customizationFieldId) use ($providedCustomizationFields) {
            foreach ($providedCustomizationFields as $providedCustomizationField) {
                if ($providedCustomizationField->id === $customizationFieldId->getValue()) {
                    return false;
                }
            }

            return true;
        });
    }
}
