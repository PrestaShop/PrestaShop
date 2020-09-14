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
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;

/**
 * Deletes customization field/fields using legacy object models
 */
final class CustomizationFieldDeleter implements CustomizationFieldDeleterInterface
{
    /**
     * @var CustomizationFieldProvider
     */
    private $customizationFieldProvider;

    /**
     * @var ProductProvider
     */
    private $productProvider;

    /**
     * @var ProductUpdater
     */
    private $productUpdater;

    /**
     * @param CustomizationFieldProvider $customizationFieldProvider
     * @param ProductProvider $productProvider
     * @param ProductUpdater $productUpdater
     */
    public function __construct(
        CustomizationFieldProvider $customizationFieldProvider,
        ProductProvider $productProvider,
        ProductUpdater $productUpdater
    ) {
        $this->customizationFieldProvider = $customizationFieldProvider;
        $this->productProvider = $productProvider;
        $this->productUpdater = $productUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CustomizationFieldId $customizationFieldId): void
    {
        $customizationField = $this->customizationFieldProvider->get($customizationFieldId);
        $this->performDeletion($customizationField, CannotDeleteCustomizationFieldException::FAILED_DELETE);
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $customizationFieldIds): void
    {
        foreach ($customizationFieldIds as $customizationFieldId) {
            $customizationField = $this->customizationFieldProvider->get($customizationFieldId);
            $this->performDeletion($customizationField, CannotDeleteCustomizationFieldException::FAILED_BULK_DELETE);
        }
    }

    /**
     * @param CustomizationField $customizationField
     * @param int $errorCode
     *
     * @return void
     *
     * @throws CannotDeleteCustomizationFieldException
     * @throws CustomizationFieldException
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function performDeletion(CustomizationField $customizationField, int $errorCode): void
    {
        $product = $this->productProvider->get(new ProductId((int) $customizationField->id_product));
        $usedFieldIds = array_map('intval', $product->getUsedCustomizationFieldsIds());
        $fieldId = (int) $customizationField->id;

        try {
            if (in_array($fieldId, $usedFieldIds)) {
                $successfullyDeleted = $customizationField->softDelete();
            } else {
                $successfullyDeleted = $customizationField->delete();
            }

            if (true !== $successfullyDeleted) {
                throw new CannotDeleteCustomizationFieldException(
                    sprintf('Failed deleting customization field #%d', $customizationField->id),
                    $errorCode
                );
            }

            $this->productUpdater->refreshProductCustomizabilityFields($product);
            $this->productUpdater->update($product, CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS);
        } catch (PrestaShopException $e) {
            throw new CustomizationFieldException(
                sprintf(
                    'Error occurred when trying to delete customization field #%d',
                    $fieldId
                ),
                0,
                $e
            );
        }
    }
}
