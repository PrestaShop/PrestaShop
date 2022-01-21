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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetRelatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RelatedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetEmployeesStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\EmployeeStockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

/**
 * Provides the data that is used to prefill the Product form
 */
class ProductFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param CommandBusInterface $queryBus
     * @param ConfigurationInterface $configuration
     * @param int $contextLangId
     * @param int $defaultShopId
     * @param int|null $contextShopId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        ConfigurationInterface $configuration,
        int $contextLangId,
        int $defaultShopId,
        ?int $contextShopId
    ) {
        $this->queryBus = $queryBus;
        $this->contextLangId = $contextLangId;
        $this->defaultShopId = $defaultShopId;
        $this->contextShopId = $contextShopId;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        $productId = (int) $id;
        $shopConstraint = null !== $this->contextShopId ? ShopConstraint::shop($this->contextShopId) : ShopConstraint::shop($this->defaultShopId);
        /** @var ProductForEditing $productForEditing */
        $productForEditing = $this->queryBus->handle(new GetProductForEditing($productId, $shopConstraint));

        $productData = [
            'id' => $productId,
            'header' => $this->extractHeaderData($productForEditing),
            'description' => $this->extractDescriptionData($productForEditing),
            'specifications' => $this->extractSpecificationsData($productForEditing),
            'stock' => $this->extractStockData($productForEditing, $shopConstraint),
            'pricing' => $this->extractPricingData($productForEditing),
            'seo' => $this->extractSEOData($productForEditing),
            'shipping' => $this->extractShippingData($productForEditing),
            'options' => $this->extractOptionsData($productForEditing),
        ];

        if ($productForEditing->getType() === ProductType::TYPE_COMBINATIONS) {
            $productData['combinations'] = [
                'availability' => [
                    'out_of_stock_type' => $productData['stock']['availability']['out_of_stock_type'],
                    'available_now_label' => $productData['stock']['availability']['available_now_label'] ?? [],
                    'available_later_label' => $productData['stock']['availability']['available_later_label'] ?? [],
                ],
            ];
        }

        return $productData;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'type' => ProductType::TYPE_STANDARD,
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array
     */
    private function extractCategoriesData(ProductForEditing $productForEditing): array
    {
        $categoriesInformation = $productForEditing->getCategoriesInformation();
        $defaultCategoryId = $categoriesInformation->getDefaultCategoryId();

        $categories = [];
        foreach ($categoriesInformation->getCategoriesInformation() as $categoryInformation) {
            $localizedNames = $categoryInformation->getLocalizedNames();
            $categoryId = $categoryInformation->getId();

            $categories[] = [
                'id' => $categoryId,
                'name' => $localizedNames[$this->contextLangId],
            ];
        }

        return [
            'product_categories' => $categories,
            'default_category_id' => $defaultCategoryId,
        ];
    }

    /**
     * @param int $productId
     *
     * @return array<int, array<string, int|string>>
     */
    private function extractRelatedProducts(int $productId): array
    {
        /** @var RelatedProduct[] $relatedProducts */
        $relatedProducts = $this->queryBus->handle(new GetRelatedProducts($productId, $this->contextLangId));

        $relatedProductsData = [];
        foreach ($relatedProducts as $relatedProduct) {
            $productName = $relatedProduct->getName();

            if (!empty($relatedProduct->getReference())) {
                $productName .= sprintf(
                    ' (ref: %s)',
                    $relatedProduct->getReference()
                );
            }

            $relatedProductsData[] = [
                'id' => $relatedProduct->getProductId(),
                'name' => $productName,
                'image' => $relatedProduct->getImageUrl(),
            ];
        }

        return $relatedProductsData;
    }

    /**
     * @param int $productId
     *
     * @return array<int, array<string, int|string>>
     */
    protected function extractPackedProducts(int $productId): array
    {
        /** @var PackedProductDetails[] $packedProductsDetails
         */
        $packedProductsDetails = $this->queryBus->handle(
            new GetPackedProducts(
                $productId,
                $this->contextLangId
            )
        );
        $packedProductsData = [];
        foreach ($packedProductsDetails as $packedProductDetails) {
            $packedProductsData[] = [
                'product_id' => $packedProductDetails->getProductId(),
                'name' => $packedProductDetails->getProductName(),
                'reference' => $packedProductDetails->getReference(),
                'combination_id' => $packedProductDetails->getCombinationId(),
                'image' => $packedProductDetails->getImageUrl(),
                'quantity' => $packedProductDetails->getQuantity(),
                'unique_identifier' => $packedProductDetails->getProductId() . '_' . $packedProductDetails->getCombinationId(),
            ];
        }

        return $packedProductsData;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractVirtualProductFileData(ProductForEditing $productForEditing): array
    {
        $data = [
            'has_file' => false,
        ];
        $virtualProductFile = $productForEditing->getVirtualProductFile();

        if (null !== $virtualProductFile) {
            $data = [
                'has_file' => true,
                'virtual_product_file_id' => $virtualProductFile->getId(),
                'name' => $virtualProductFile->getDisplayName(),
                'download_times_limit' => $virtualProductFile->getDownloadTimesLimit(),
                'access_days_limit' => $virtualProductFile->getAccessDays(),
                'expiration_date' => $virtualProductFile->getExpirationDate() ?
                    $virtualProductFile->getExpirationDate()->format(DateTime::DEFAULT_DATE_FORMAT) :
                    null,
            ];
        }

        return $data;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractHeaderData(ProductForEditing $productForEditing): array
    {
        return [
            'type' => $productForEditing->getType(),
            'initial_type' => $productForEditing->getType(),
            'name' => $productForEditing->getBasicInformation()->getLocalizedNames(),
            'cover_thumbnail' => $productForEditing->getCoverThumbnailUrl(),
            'active' => $productForEditing->isActive(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractDescriptionData(ProductForEditing $productForEditing): array
    {
        return [
            'description' => $productForEditing->getBasicInformation()->getLocalizedDescriptions(),
            'description_short' => $productForEditing->getBasicInformation()->getLocalizedShortDescriptions(),
            'categories' => $this->extractCategoriesData($productForEditing),
            'manufacturer' => $productForEditing->getOptions()->getManufacturerId(),
            'related_products' => $this->extractRelatedProducts($productForEditing->getProductId()),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractSpecificationsData(ProductForEditing $productForEditing): array
    {
        $details = $productForEditing->getDetails();
        $options = $productForEditing->getOptions();

        return [
            'references' => [
                'mpn' => $details->getMpn(),
                'upc' => $details->getUpc(),
                'ean_13' => $details->getEan13(),
                'isbn' => $details->getIsbn(),
                'reference' => $details->getReference(),
            ],
            'features' => $this->extractFeatureValues($productForEditing->getProductId()),
            'attachments' => $this->extractAttachmentsData($productForEditing),
            'show_condition' => $options->showCondition(),
            'condition' => $options->getCondition(),
            'customizations' => $this->extractCustomizationsData($productForEditing),
        ];
    }

    /**
     * @param int $productId
     *
     * @return array<string, array<int, array<string, int|array<int, string>>>>
     */
    private function extractFeatureValues(int $productId): array
    {
        /** @var ProductFeatureValue[] $featureValues */
        $featureValues = $this->queryBus->handle(new GetProductFeatureValues($productId));
        if (empty($featureValues)) {
            return [];
        }

        $productFeatureValues = [];
        foreach ($featureValues as $featureValue) {
            $productFeatureValue = [
                'feature_id' => $featureValue->getFeatureId(),
                'feature_value_id' => $featureValue->getFeatureValueId(),
            ];
            if ($featureValue->isCustom()) {
                $productFeatureValue['custom_value'] = $featureValue->getLocalizedValues();
                $productFeatureValue['custom_value_id'] = $featureValue->getFeatureValueId();
            }

            $productFeatureValues[] = $productFeatureValue;
        }

        return [
            'feature_values' => $productFeatureValues,
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     * @param ShopConstraint $shopConstraint
     *
     * @return array<string, mixed>
     */
    private function extractStockData(ProductForEditing $productForEditing, ShopConstraint $shopConstraint): array
    {
        $stockInformation = $productForEditing->getStockInformation();
        $availableDate = $stockInformation->getAvailableDate();

        return [
            'quantities' => [
                'delta_quantity' => [
                    'quantity' => $stockInformation->getQuantity(),
                    'delta' => 0,
                ],
                'stock_movements' => $this->getStockMovements($productForEditing->getProductId(), $shopConstraint),
                'minimal_quantity' => $stockInformation->getMinimalQuantity(),
            ],
            'options' => [
                'stock_location' => $stockInformation->getLocation(),
                'low_stock_threshold' => $stockInformation->getLowStockThreshold(),
                'low_stock_alert' => $stockInformation->isLowStockAlertEnabled(),
            ],
            'virtual_product_file' => $this->extractVirtualProductFileData($productForEditing),
            'pack_stock_type' => $stockInformation->getPackStockType(),
            'availability' => [
                'out_of_stock_type' => $stockInformation->getOutOfStockType(),
                'available_now_label' => $stockInformation->getLocalizedAvailableNowLabels(),
                'available_later_label' => $stockInformation->getLocalizedAvailableLaterLabels(),
                'available_date' => $availableDate ? $availableDate->format(DateTime::DEFAULT_DATE_FORMAT) : '',
            ],
            'packed_products' => $this->extractPackedProducts($productForEditing->getProductId()),
        ];
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    private function getStockMovements(int $productId, ShopConstraint $shopConstraint): array
    {
        /** @var EmployeeStockMovement[] $stockMovements */
        $stockMovements = $this->queryBus->handle(new GetEmployeesStockMovements($productId, $shopConstraint->getShopId()->getValue()));

        $movementData = [];
        foreach ($stockMovements as $stockMovement) {
            $movementData[] = [
                'date_add' => $stockMovement->getDateAdd()->format(DateTime::DEFAULT_DATETIME_FORMAT),
                'employee' => $stockMovement->getFirstName() . ' ' . $stockMovement->getLastName(),
                'delta_quantity' => $stockMovement->getDeltaQuantity(),
            ];
        }

        return $movementData;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractPricingData(ProductForEditing $productForEditing): array
    {
        return [
            'retail_price' => [
                'price_tax_excluded' => (float) (string) $productForEditing->getPricesInformation()->getPrice(),
                'price_tax_included' => (float) (string) $productForEditing->getPricesInformation()->getPriceTaxIncluded(),
                'tax_rules_group_id' => $productForEditing->getPricesInformation()->getTaxRulesGroupId(),
                'ecotax_tax_excluded' => (float) (string) $productForEditing->getPricesInformation()->getEcotax(),
                'ecotax_tax_included' => (float) (string) $productForEditing->getPricesInformation()->getEcotaxTaxIncluded(),
            ],
            'on_sale' => $productForEditing->getPricesInformation()->isOnSale(),
            'wholesale_price' => (float) (string) $productForEditing->getPricesInformation()->getWholesalePrice(),
            'unit_price' => [
                'price_tax_excluded' => (float) (string) $productForEditing->getPricesInformation()->getUnitPrice(),
                'price_tax_included' => (float) (string) $productForEditing->getPricesInformation()->getUnitPriceTaxIncluded(),
                'unity' => $productForEditing->getPricesInformation()->getUnity(),
            ],
            'priority_management' => $this->getPriorityManagement($productForEditing),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, bool|string[]>
     */
    private function getPriorityManagement(ProductForEditing $productForEditing): array
    {
        $priorities = $productForEditing->getPricesInformation()->getSpecificPricePriorities();

        if (!$priorities) {
            return [
                'use_custom_priority' => false,
                'priorities' => $this->getDefaultPrioritiesData(),
            ];
        }

        return [
            'use_custom_priority' => true,
            'priorities' => $priorities->getPriorities(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array
     */
    private function extractSEOData(ProductForEditing $productForEditing): array
    {
        $seoOptions = $productForEditing->getProductSeoOptions();

        return [
            'meta_title' => $seoOptions->getLocalizedMetaTitles(),
            'meta_description' => $seoOptions->getLocalizedMetaDescriptions(),
            'link_rewrite' => $seoOptions->getLocalizedLinkRewrites(),
            'redirect_option' => $this->extractRedirectOptionData($productForEditing),
            'tags' => $this->presentTags($productForEditing->getBasicInformation()->getLocalizedTags()),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array{type: string, target: null|array}
     */
    private function extractRedirectOptionData(ProductForEditing $productForEditing): array
    {
        $seoOptions = $productForEditing->getProductSeoOptions();

        // It is important to return null when nothing is selected this way the transformer and therefore
        // the form field have no value to try and display
        $redirectTarget = null;
        if (null !== $seoOptions->getRedirectTarget()) {
            $redirectTarget = [
                'id' => $seoOptions->getRedirectTarget()->getId(),
                'name' => $seoOptions->getRedirectTarget()->getName(),
                'image' => $seoOptions->getRedirectTarget()->getImage(),
            ];
        }

        return [
            'type' => $seoOptions->getRedirectType(),
            'target' => $redirectTarget,
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractShippingData(ProductForEditing $productForEditing): array
    {
        $shipping = $productForEditing->getShippingInformation();

        return [
            'dimensions' => [
                'width' => (string) $shipping->getWidth(),
                'height' => (string) $shipping->getHeight(),
                'depth' => (string) $shipping->getDepth(),
                'weight' => (string) $shipping->getWeight(),
            ],
            'additional_shipping_cost' => (string) $shipping->getAdditionalShippingCost(),
            'delivery_time_note_type' => $shipping->getDeliveryTimeNoteType(),
            'delivery_time_notes' => [
                'in_stock' => $shipping->getLocalizedDeliveryTimeInStockNotes(),
                'out_of_stock' => $shipping->getLocalizedDeliveryTimeOutOfStockNotes(),
            ],
            'carriers' => $shipping->getCarrierReferences(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractOptionsData(ProductForEditing $productForEditing): array
    {
        $options = $productForEditing->getOptions();
        $suppliersData = $this->extractSuppliersData($productForEditing);

        return array_merge([
            'visibility' => [
                'visibility' => $options->getVisibility(),
                'available_for_order' => $options->isAvailableForOrder(),
                'show_price' => $options->showPrice(),
                'online_only' => $options->isOnlineOnly(),
            ],
        ], $suppliersData);
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function extractAttachmentsData(ProductForEditing $productForEditing): array
    {
        $productAttachments = $productForEditing->getAssociatedAttachments();

        $attachmentsData = [];
        foreach ($productAttachments as $productAttachment) {
            $localizedNames = $productAttachment->getLocalizedNames();
            $attachmentsData['attached_files'][] = [
                'attachment_id' => $productAttachment->getAttachmentId(),
                'name' => $localizedNames[$this->contextLangId] ?? reset($localizedNames),
                'file_name' => $productAttachment->getFilename(),
                'mime_type' => $productAttachment->getMimeType(),
            ];
        }

        return $attachmentsData;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, array<int, mixed>>
     */
    private function extractCustomizationsData(ProductForEditing $productForEditing): array
    {
        /** @var CustomizationField[] $customizationFields */
        $customizationFields = $this->queryBus->handle(
            new GetProductCustomizationFields($productForEditing->getProductId())
        );

        if (empty($customizationFields)) {
            return [];
        }

        $fields = [];
        foreach ($customizationFields as $customizationField) {
            $fields[] = [
                'id' => $customizationField->getCustomizationFieldId(),
                'name' => $customizationField->getLocalizedNames(),
                'type' => $customizationField->getType(),
                'required' => $customizationField->isRequired(),
            ];
        }

        return [
            'customization_fields' => $fields,
        ];
    }

    /**
     * @param LocalizedTags[] $localizedTagsList
     *
     * @return array<int, string>
     */
    private function presentTags(array $localizedTagsList): array
    {
        $tags = [];
        foreach ($localizedTagsList as $localizedTags) {
            $tags[$localizedTags->getLanguageId()] = implode(',', $localizedTags->getTags());
        }

        return $tags;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array{suppliers: array{default_supplier_id: int, supplier_ids: int[]}, product_suppliers: array<int, array{supplier_id: int, supplier_name: string, product_supplier_id: int, price_tax_excluded: string, reference: string, currency_id: int, combination_id: int}>}
     */
    private function extractSuppliersData(ProductForEditing $productForEditing): array
    {
        $suppliersData = [
            'suppliers' => [
                'default_supplier_id' => 0,
                'supplier_ids' => [],
            ],
            'product_suppliers' => [],
        ];

        /** @var ProductSupplierOptions $productSupplierOptions */
        $productSupplierOptions = $this->queryBus->handle(new GetProductSupplierOptions($productForEditing->getProductId()));
        $suppliersData['suppliers']['default_supplier_id'] = $productSupplierOptions->getDefaultSupplierId();
        $suppliersData['suppliers']['supplier_ids'] = $productSupplierOptions->getSupplierIds();

        if (empty($productSupplierOptions->getProductSuppliers())) {
            return $suppliersData;
        }

        foreach ($productSupplierOptions->getProductSuppliers() as $supplierForEditing) {
            $supplierId = $supplierForEditing->getSupplierId();

            if ($productForEditing->getType() !== ProductType::TYPE_COMBINATIONS) {
                $suppliersData['product_suppliers'][$supplierId] = [
                    'supplier_id' => $supplierId,
                    'supplier_name' => $supplierForEditing->getSupplierName(),
                    'product_supplier_id' => $supplierForEditing->getProductSupplierId(),
                    'price_tax_excluded' => $supplierForEditing->getPriceTaxExcluded(),
                    'reference' => $supplierForEditing->getReference(),
                    'currency_id' => $supplierForEditing->getCurrencyId(),
                    'combination_id' => $supplierForEditing->getCombinationId(),
                ];
            }
        }

        return $suppliersData;
    }

    /**
     * @return string[]
     */
    private function getDefaultPrioritiesData(): array
    {
        if (!empty($this->configuration->get('PS_SPECIFIC_PRICE_PRIORITIES'))) {
            return explode(';', $this->configuration->get('PS_SPECIFIC_PRICE_PRIORITIES'));
        }

        return array_values(PriorityList::AVAILABLE_PRIORITIES);
    }
}
