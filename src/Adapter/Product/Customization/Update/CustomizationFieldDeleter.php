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
use PrestaShop\PrestaShop\Adapter\Product\Customization\Repository\CustomizationFieldRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotBulkDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;

/**
 * Deletes customization field/fields using legacy object models
 */
class CustomizationFieldDeleter
{
    /**
     * @var CustomizationFieldRepository
     */
    private $customizationFieldRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var array<int, Product>
     */
    private $productsById = [];

    /**
     * @param CustomizationFieldRepository $customizationFieldRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CustomizationFieldRepository $customizationFieldRepository,
        ProductRepository $productRepository
    ) {
        $this->customizationFieldRepository = $customizationFieldRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param CustomizationFieldId $customizationFieldId
     */
    public function delete(CustomizationFieldId $customizationFieldId): void
    {
        $customizationField = $this->customizationFieldRepository->get($customizationFieldId);
        $this->performDeletion($customizationField);
    }

    /**
     * @param array $customizationFieldIds
     *
     * @throws CannotBulkDeleteCustomizationFieldException
     */
    public function bulkDelete(array $customizationFieldIds): void
    {
        $failedIds = [];
        foreach ($customizationFieldIds as $customizationFieldId) {
            $customizationField = $this->customizationFieldRepository->get($customizationFieldId);

            try {
                $this->performDeletion($customizationField);
            } catch (CannotDeleteCustomizationFieldException $e) {
                $failedIds[] = $customizationFieldId->getValue();
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
     */
    private function performDeletion(CustomizationField $customizationField): void
    {
        $product = $this->getProduct((int) $customizationField->id_product);
        $usedFieldIds = array_map('intval', $product->getUsedCustomizationFieldsIds());
        $fieldId = (int) $customizationField->id;

        if (in_array($fieldId, $usedFieldIds)) {
            $this->customizationFieldRepository->softDelete($customizationField);
        } else {
            $this->customizationFieldRepository->delete($customizationField);
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
            $this->productsById[$productId] = $this->productRepository->getProductByDefaultShop(new ProductId($productId));
        }

        return $this->productsById[$productId];
    }
}
