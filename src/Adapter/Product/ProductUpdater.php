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
use PrestaShop\PrestaShop\Adapter\Supplier\SupplierProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductCustomizabilitySettings;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Exception\ProductSupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\ValueObject\ProductSupplierId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
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
     * @var SupplierProvider
     */
    private $supplierProvider;

    /**
     * @var ProductSupplierProvider
     */
    private $productSupplierProvider;

    /**
     * @param ProductValidator $productValidator
     * @param SupplierProvider $supplierProvider
     * @param ProductSupplierProvider $productSupplierProvider
     */
    public function __construct(
        ProductValidator $productValidator,
        SupplierProvider $supplierProvider,
        ProductSupplierProvider $productSupplierProvider
    ) {
        $this->productValidator = $productValidator;
        $this->supplierProvider = $supplierProvider;
        $this->productSupplierProvider = $productSupplierProvider;
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
     *
     * @throws CoreException
     */
    public function resetDefaultSupplier(Product $product): void
    {
        $this->update($product, [
            'supplier_reference' => '',
            'wholesale_price' => 0,
            'id_supplier' => 0,
        ], CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);
    }

    /**
     * @param Product $product
     * @param int $defaultSupplierId
     */
    public function updateDefaultSupplier(Product $product, int $defaultSupplierId): void
    {
        if ($defaultSupplierId && !$product->hasCombinations()) {
            $this->resetDefaultSupplierIfNotExists($product, new SupplierId($defaultSupplierId));
            $this->update($product, [
                'supplier_reference' => ProductSupplier::getProductSupplierReference($product->id, 0, $defaultSupplierId),
                'wholesale_price' => ProductSupplier::getProductSupplierPrice($product->id, 0, $defaultSupplierId),
                'id_supplier' => $defaultSupplierId,
            ], CannotUpdateProductException::FAILED_UPDATE_DEFAULT_SUPPLIER);

            return;
        }

        $this->resetDefaultSupplier($product);
    }

    /**
     * @param Product $product
     * @param SupplierId $supplierId
     *
     * @throws CoreException
     * @throws ProductSupplierNotFoundException
     * @throws SupplierNotFoundException
     */
    private function resetDefaultSupplierIfNotExists(Product $product, SupplierId $supplierId): void
    {
        try {
            $this->supplierProvider->assertSupplierExists($supplierId);
            $this->productSupplierProvider->assertProductSupplierExists(new ProductSupplierId(
                (int) ProductSupplier::getIdByProductAndSupplier(
                    $product->id,
                    0,
                    $supplierId->getValue())
            ));
        } catch (SupplierNotFoundException | ProductSupplierNotFoundException $e) {
            $this->resetDefaultSupplier($product);

            throw $e;
        }
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
