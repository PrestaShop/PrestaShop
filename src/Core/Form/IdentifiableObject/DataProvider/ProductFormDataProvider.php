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
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

/**
 * Provides the data that is used to prefill the Product form
 */
final class ProductFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $defaultProductActivation;

    /**
     * @var int
     */
    private $mostUsedTaxRulesGroupId;

    /**
     * @var int
     */
    private $defaultCategoryId;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $defaultProductActivation
     * @param int $mostUsedTaxRulesGroupId
     * @param int $defaultCategoryId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        bool $defaultProductActivation,
        int $mostUsedTaxRulesGroupId,
        int $defaultCategoryId
    ) {
        $this->queryBus = $queryBus;
        $this->defaultProductActivation = $defaultProductActivation;
        $this->mostUsedTaxRulesGroupId = $mostUsedTaxRulesGroupId;
        $this->defaultCategoryId = $defaultCategoryId;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $productId = (int) $id;
        /** @var ProductForEditing $productForEditing */
        $productForEditing = $this->queryBus->handle(new GetProductForEditing($productId));

        $productData = [
            'id' => $productId,
            'header' => $this->extractHeaderData($productForEditing),
            'basic' => $this->extractBasicData($productForEditing),
            'stock' => $this->extractStockData($productForEditing),
            'pricing' => $this->extractPricingData($productForEditing),
            'seo' => $this->extractSEOData($productForEditing),
            'shipping' => $this->extractShippingData($productForEditing),
            'options' => $this->extractOptionsData($productForEditing),
            'categories' => $this->extractCategoriesData($productForEditing),
            'footer' => [
                'active' => $productForEditing->getOptions()->isActive(),
            ],
        ];

        return $this->addShortcutData($productData);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return $this->addShortcutData([
            'header' => [
                'type' => ProductType::TYPE_STANDARD,
            ],
            'basic' => [
                'manufacturer' => NoManufacturerId::NO_MANUFACTURER_ID,
            ],
            'stock' => [
                'quantities' => [
                    'quantity' => 0,
                    'minimal_quantity' => 0,
                ],
            ],
            'pricing' => [
                'retail_price' => [
                    'price_tax_excluded' => 0,
                    'price_tax_included' => 0,
                ],
                'tax_rules_group_id' => $this->mostUsedTaxRulesGroupId,
                'wholesale_price' => 0,
                'unit_price' => [
                    'price' => 0,
                ],
            ],
            'shipping' => [
                'dimensions' => [
                    'width' => 0,
                    'height' => 0,
                    'depth' => 0,
                    'weight' => 0,
                ],
                'additional_shipping_cost' => 0,
                'delivery_time_note_type' => DeliveryTimeNoteType::TYPE_DEFAULT,
            ],
            'options' => [
                'visibility' => [
                    'visibility' => ProductVisibility::VISIBLE_EVERYWHERE,
                ],
                'condition' => ProductCondition::NEW,
            ],
            'categories' => [
                'product_categories' => [
                    $this->defaultCategoryId => [
                        'is_associated' => true,
                        'is_default' => true,
                    ],
                ],
            ],
            'footer' => [
                'active' => $this->defaultProductActivation,
            ],
        ]);
    }

    /**
     * Returned product data with shortcut data that is picked from existing data.
     *
     * @param array $productData
     *
     * @return array
     */
    private function addShortcutData(array $productData): array
    {
        $productData['shortcuts'] = [
            'retail_price' => [
                'price_tax_excluded' => $productData['pricing']['retail_price']['price_tax_excluded'],
                'price_tax_included' => $productData['pricing']['retail_price']['price_tax_included'],
                'tax_rules_group_id' => $productData['pricing']['tax_rules_group_id'],
            ],
            'stock' => [
                'quantity' => $productData['stock']['quantities']['quantity'],
            ],
        ];

        return $productData;
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array
     */
    private function extractCategoriesData(ProductForEditing $productForEditing): array
    {
        $categoriesInformation = $productForEditing->getCategoriesInformation();
        $categories = [];
        foreach ($categoriesInformation->getCategoryIds() as $categoryId) {
            $categories[$categoryId] = [
                'is_associated' => true,
                'is_default' => $categoryId === $categoriesInformation->getDefaultCategoryId(),
            ];
        }

        return [
            'product_categories' => $categories,
        ];
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
            'name' => $productForEditing->getBasicInformation()->getLocalizedNames(),
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, mixed>
     */
    private function extractBasicData(ProductForEditing $productForEditing): array
    {
        return [
            'description' => $productForEditing->getBasicInformation()->getLocalizedDescriptions(),
            'description_short' => $productForEditing->getBasicInformation()->getLocalizedShortDescriptions(),
            'features' => $this->extractFeatureValues($productForEditing->getProductId()),
            'manufacturer' => $productForEditing->getOptions()->getManufacturerId(),
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
     *
     * @return array<string, mixed>
     */
    private function extractStockData(ProductForEditing $productForEditing): array
    {
        $stockInformation = $productForEditing->getStockInformation();
        $availableDate = $stockInformation->getAvailableDate();

        return [
            'quantities' => [
                'quantity' => $stockInformation->getQuantity(),
                'minimal_quantity' => $stockInformation->getMinimalQuantity(),
            ],
            'options' => [
                'stock_location' => $stockInformation->getLocation(),
                'low_stock_threshold' => $stockInformation->getLowStockThreshold() ?: null,
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
        ];
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
                'ecotax' => (float) (string) $productForEditing->getPricesInformation()->getEcotax(),
            ],
            'tax_rules_group_id' => $productForEditing->getPricesInformation()->getTaxRulesGroupId(),
            'on_sale' => $productForEditing->getPricesInformation()->isOnSale(),
            'wholesale_price' => (float) (string) $productForEditing->getPricesInformation()->getWholesalePrice(),
            'unit_price' => [
                'price' => (float) (string) $productForEditing->getPricesInformation()->getUnitPrice(),
                'unity' => $productForEditing->getPricesInformation()->getUnity(),
            ],
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
        ];
    }

    /**
     * @param ProductForEditing $productForEditing
     *
     * @return array<string, int|string>
     */
    private function extractRedirectOptionData(ProductForEditing $productForEditing): array
    {
        $seoOptions = $productForEditing->getProductSeoOptions();

        return [
            'type' => $seoOptions->getRedirectType(),
            'target' => $seoOptions->getRedirectTargetId(),
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
        $details = $productForEditing->getDetails();

        return [
            'visibility' => [
                'visibility' => $options->getVisibility(),
                'available_for_order' => $options->isAvailableForOrder(),
                'show_price' => $options->showPrice(),
                'online_only' => $options->isOnlineOnly(),
            ],
            'tags' => $this->presentTags($productForEditing->getBasicInformation()->getLocalizedTags()),
            'show_condition' => $options->showCondition(),
            'condition' => $options->getCondition(),
            'references' => [
                'mpn' => $details->getMpn(),
                'upc' => $details->getUpc(),
                'ean_13' => $details->getEan13(),
                'isbn' => $details->getIsbn(),
                'reference' => $details->getReference(),
            ],
            'customizations' => $this->extractCustomizationsData($productForEditing),
            'suppliers' => $this->extractSuppliersData($productForEditing),
        ];
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
     * @return array<string, int|array<int, int|array<string, string|int>>>
     */
    private function extractSuppliersData(ProductForEditing $productForEditing): array
    {
        /** @var ProductSupplierOptions $productSupplierOptions */
        $productSupplierOptions = $this->queryBus->handle(new GetProductSupplierOptions($productForEditing->getProductId()));

        if (empty($productSupplierOptions->getSuppliersInfo())) {
            return [];
        }

        $defaultSupplierId = $productSupplierOptions->getDefaultSupplierId();
        $suppliersData = [
            'default_supplier_id' => $defaultSupplierId,
        ];

        foreach ($productSupplierOptions->getSuppliersInfo() as $supplierOption) {
            $supplierForEditing = $supplierOption->getProductSupplierForEditing();
            $supplierId = $supplierOption->getSupplierId();

            $suppliersData['supplier_ids'][] = $supplierId;
            $suppliersData['product_suppliers'][$supplierId] = [
                'supplier_id' => $supplierId,
                'supplier_name' => $supplierOption->getSupplierName(),
                'product_supplier_id' => $supplierForEditing->getProductSupplierId(),
                'price_tax_excluded' => $supplierForEditing->getPriceTaxExcluded(),
                'reference' => $supplierForEditing->getReference(),
                'currency_id' => $supplierForEditing->getCurrencyId(),
                'combination_id' => $supplierForEditing->getCombinationId(),
            ];
        }

        return $suppliersData;
    }
}
