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

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelPersister;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use Product;
use ProductSupplier;

/**
 * Performs update of provided Product properties
 */
class ProductUpdater extends AbstractObjectModelPersister
{
    /**
     * @var ProductValidator
     */
    private $productValidator;

    /**
     * @param ProductValidator $productValidator
     */
    public function __construct(
        ProductValidator $productValidator
    ) {
        $this->productValidator = $productValidator;
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     * @param int $errorCode
     *
     * @throws CoreException
     */
    public function update(Product $product, array $propertiesToUpdate, int $errorCode): void
    {
        $this->fillProperties($product, $propertiesToUpdate);
        $this->productValidator->validate($product);
        $this->updateObjectModel($product, CannotUpdateProductException::class, $errorCode);
    }

    /**
     * @param Product $product
     */
    public function refreshProductCustomizabilityProperties(Product $product): void
    {
        if ($product->hasActivatedRequiredCustomizableFields()) {
            $customizable = ProductCustomizabilitySettings::REQUIRES_CUSTOMIZATION;
        } elseif (!empty($product->getNonDeletedCustomizationFieldIds())) {
            $customizable = ProductCustomizabilitySettings::ALLOWS_CUSTOMIZATION;
        } else {
            $customizable = ProductCustomizabilitySettings::NOT_CUSTOMIZABLE;
        }

        $this->update(
            $product,
            [
                'customizable' => $customizable,
                'text_fields' => $product->countCustomizationFields(CustomizationFieldType::TYPE_TEXT),
                'uploadable_files' => $product->countCustomizationFields(CustomizationFieldType::TYPE_FILE),
            ], CannotUpdateProductException::FAILED_UPDATE_CUSTOMIZATION_FIELDS
        );
    }

    /**
     * @param Product $product
     * @param int $defaultSupplierId
     */
    public function updateProductDefaultSupplier(Product $product, int $defaultSupplierId): void
    {
        if ($product->hasCombinations() || !$defaultSupplierId) {
            $fieldsToUpdate['supplier_reference'] = '';
            $fieldsToUpdate['wholesale_price'] = 0;
        } elseif ($defaultSupplierId && !$product->hasCombinations()) {
            $fieldsToUpdate['supplier_reference'] = ProductSupplier::getProductSupplierReference(
                $product->id,
                0,
                $defaultSupplierId
            );
            $fieldsToUpdate['wholesale_price'] = ProductSupplier::getProductSupplierPrice($product->id, 0, $defaultSupplierId);
        }

        $fieldsToUpdate['id_supplier'] = $defaultSupplierId;

        $this->update($product, $fieldsToUpdate, CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);
    }

    /**
     * @param Product $product
     * @param array $propertiesToUpdate
     */
    private function fillProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillCustomizabilityProperties($product, $propertiesToUpdate);
        $this->fillSupplierProperties($product, $propertiesToUpdate);
    }

    /**
     * @param Product $product
     * @param array<string, mixed> $propertiesToUpdate
     */
    private function fillCustomizabilityProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillProperty($product, 'customizable', $propertiesToUpdate);
        $this->fillProperty($product, 'text_fields', $propertiesToUpdate);
        $this->fillProperty($product, 'uploadable_files', $propertiesToUpdate);
    }

    /**
     * @param Product $product
     * @param array<string, mixed> $propertiesToUpdate
     */
    private function fillSupplierProperties(Product $product, array $propertiesToUpdate): void
    {
        $this->fillProperty($product, 'supplier_reference', $propertiesToUpdate);
        $this->fillProperty($product, 'id_supplier', $propertiesToUpdate);
        $this->fillProperty($product, 'wholesale_price', $propertiesToUpdate);
    }
}
