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
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotBulkDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShopException;
use Product;

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
     * @var array<int, Product>
     */
    private $productsById = [];

    /**
     * @param CustomizationFieldProvider $customizationFieldProvider
     * @param ProductProvider $productProvider
     */
    public function __construct(
        CustomizationFieldProvider $customizationFieldProvider,
        ProductProvider $productProvider
    ) {
        $this->customizationFieldProvider = $customizationFieldProvider;
        $this->productProvider = $productProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CustomizationFieldId $customizationFieldId): void
    {
        $customizationField = $this->customizationFieldProvider->get($customizationFieldId);

        if (!$this->performDeletion($customizationField)) {
            throw new CannotDeleteCustomizationFieldException(sprintf(
                'Failed deleting customization field #%d',
                $customizationField->id
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $customizationFieldIds): void
    {
        $failedIds = [];
        foreach ($customizationFieldIds as $customizationFieldId) {
            $customizationField = $this->customizationFieldProvider->get($customizationFieldId);

            if (!$this->performDeletion($customizationField)) {
                $failedIds[] = $customizationFieldId;
            }
        }

        if (empty($failedIds)) {
            return;
        }

        throw new CannotBulkDeleteCustomizationFieldException(
            $failedIds,
            sprintf('Failed deleting following customization fields: "%s"', implode(', ', $failedIds))
        );
    }

    /**
     * @param CustomizationField $customizationField
     *
     * @return bool
     *
     * @throws CustomizationFieldException
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function performDeletion(CustomizationField $customizationField): bool
    {
        $product = $this->getProduct((int) $customizationField->id_product);
        $usedFieldIds = array_map('intval', $product->getUsedCustomizationFieldsIds());
        $fieldId = (int) $customizationField->id;

        try {
            if (in_array($fieldId, $usedFieldIds)) {
                $successfullyDeleted = $customizationField->softDelete();
            } else {
                $successfullyDeleted = $customizationField->delete();
            }

            return (bool) $successfullyDeleted;
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

    /**
     * @param int $productId
     *
     * @return Product
     *
     * @throws ProductException
     * @throws ProductNotFoundException
     */
    private function getProduct(int $productId): Product
    {
        if (!isset($this->productsById[$productId])) {
            $this->productsById[$productId] = $this->productProvider->get(new ProductId($productId));
        }

        return $this->productsById[$productId];
    }
}
