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

namespace PrestaShop\PrestaShop\Adapter\Product;

use CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationFieldDeleterInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;

/**
 * Updates CustomizationField & Product relation
 */
class ProductCustomizationFieldUpdater
{
    /**
     * @var CustomizationFieldPersister
     */
    private $customizationFieldPersister;

    /**
     * @var CustomizationFieldDeleterInterface
     */
    private $customizationFieldDeleter;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductPersister
     */
    private $productPersister;

    /**
     * @param CustomizationFieldPersister $customizationFieldPersister
     * @param CustomizationFieldDeleterInterface $customizationFieldDeleter
     * @param ProductProvider $productProvider
     * @param ProductPersister $productPersister
     */
    public function __construct(
        CustomizationFieldPersister $customizationFieldPersister,
        CustomizationFieldDeleterInterface $customizationFieldDeleter,
        ProductProvider $productProvider,
        ProductPersister $productPersister
    ) {
        $this->customizationFieldPersister = $customizationFieldPersister;
        $this->customizationFieldDeleter = $customizationFieldDeleter;
        $this->productPersister = $productPersister;
        $this->productProvider = $productProvider;
    }

    /**
     * @param ProductId $productId
     * @param CustomizationField[] $customizationFields
     */
    public function setProductCustomizationFields(ProductId $productId, array $customizationFields): void
    {
        $product = $this->productProvider->get($productId);
        $deletableFieldIds = $this->getDeletableFieldIds($customizationFields, $product);

        foreach ($customizationFields as $customizationField) {
            if ($customizationField->id) {
                $this->customizationFieldPersister->update($customizationField);
            } else {
                $this->customizationFieldPersister->add($customizationField);
            }
        }

        $this->customizationFieldDeleter->bulkDelete($deletableFieldIds);
        $this->refreshProductCustomizability($product);
    }

    /**
     * @param Product $product
     */
    public function refreshProductCustomizability(Product $product): void
    {
        if ($product->hasActivatedRequiredCustomizableFields()) {
            $customizable = ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION;
        } elseif (!empty($product->getNonDeletedCustomizationFieldIds())) {
            $customizable = ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION;
        } else {
            $customizable = ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
        }

        $this->productPersister->update(
            $product,
            [
                'customizable' => $customizable,
                'text_fields' => $product->countCustomizationFields(CustomizationFieldType::TYPE_TEXT),
                'uploadable_files' => $product->countCustomizationFields(CustomizationFieldType::TYPE_FILE),
            ], CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS
        );
    }

    /**
     * Checks provided customization fields against existing ones to determine which ones to delete
     *
     * @param CustomizationField[] $providedCustomizationFields
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
            $providedId = (int) $providedCustomizationField->id;

            if (isset($deletableIds[$providedId])) {
                unset($deletableIds[$providedId]);
            }
        }

        return $deletableIds;
    }
}
