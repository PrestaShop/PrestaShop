<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            } catch (CannotDeleteCustomizationFieldException) {
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
