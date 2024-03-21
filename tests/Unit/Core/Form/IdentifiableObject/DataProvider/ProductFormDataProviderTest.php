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

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use DateTime;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Constraint\LogicalOr;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\FeaturesChoiceProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Query\GetPackedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\QueryResult\PackedProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetRelatedProducts;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\CategoriesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\CategoryInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductBasicInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductCustomizationOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductPricesInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductRedirectTarget;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductSeoOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductShippingInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductStockInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\RelatedProduct;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetProductStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\QueryResult\VirtualProductFileForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\ProductFormDataProvider;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;
use RuntimeException;

// @todo: ProductFormDataProvider needs to be split to multiple classes to allow easier testing
class ProductFormDataProviderTest extends TestCase
{
    private const PRODUCT_ID = 42;
    private const HOME_CATEGORY_ID = 49;
    private const DEFAULT_CATEGORY_ID = 51;
    private const DEFAULT_VIRTUAL_PRODUCT_FILE_ID = 69;
    private const CONTEXT_LANG_ID = 1;
    private const DEFAULT_QUANTITY = 12;
    private const DEFAULT_SHOP_ID = 99;
    private const COVER_URL = 'http://localhost/cover.jpg';
    private const DEFAULT_PRIORITY_LIST = [
        PriorityList::PRIORITY_GROUP,
        PriorityList::PRIORITY_CURRENCY,
        PriorityList::PRIORITY_COUNTRY,
        PriorityList::PRIORITY_SHOP,
    ];

    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = $this->buildProvider($queryBusMock);

        $expectedDefaultData = [
            'type' => ProductType::TYPE_STANDARD,
        ];
        $defaultData = $provider->getDefaultData();
        $this->assertSame($expectedDefaultData, $defaultData);
    }

    public function testSwitchDefaultContextShop(): void
    {
        $configurationMock = $this->getDefaultConfigurationMock();
        // The real test is performed by the mock here, which assert the correct shopId is used
        $queryBusMock = $this->createQueryBusCheckingShopMock(self::DEFAULT_SHOP_ID);
        $provider = new ProductFormDataProvider(
            $queryBusMock,
            $configurationMock,
            self::CONTEXT_LANG_ID,
            self::DEFAULT_SHOP_ID,
            null,
            $this->getFeaturesProvider()
        );

        $formData = $provider->getData(self::PRODUCT_ID);
        $this->assertNotNull($formData);
        $contextShopId = 51;
        $queryBusMock = $this->createQueryBusCheckingShopMock($contextShopId);
        $provider = new ProductFormDataProvider(
            $queryBusMock,
            $configurationMock,
            self::CONTEXT_LANG_ID,
            self::DEFAULT_SHOP_ID,
            $contextShopId,
            $this->getFeaturesProvider()
        );

        $formData = $provider->getData(self::PRODUCT_ID);
        $this->assertNotNull($formData);
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param array $productData
     * @param array $expectedData
     */
    public function testGetData(array $productData, array $expectedData)
    {
        $queryBusMock = $this->createQueryBusMock($productData);
        $provider = $this->buildProvider($queryBusMock);

        $formData = $provider->getData(self::PRODUCT_ID);
        Assert::assertSame($expectedData, $formData);
    }

    public function getExpectedData(): Generator
    {
        $datasetsByType = [
            $this->getDatasetsForDescription(),
            $this->getDatasetsForSeo(),
            $this->getDatasetsForRedirectOption(),
            $this->getDatasetsForProductSuppliers(),
            $this->getDataSetsForFeatures(),
            $this->getDataSetsForManufacturer(),
            $this->getDatasetsForCustomizations(),
            $this->getDatasetsForVirtualProductFile(),
            $this->getDatasetsForPrices(),
            $this->getDatasetsForStock(),
            $this->getDatasetsForShipping(),
            $this->getDatasetsForOptions(),
            $this->getDatasetsForCategories(),
            $this->getDatasetsForPackedProducts(),
            $this->getDatasetsForRelatedProducts(),
            $this->getDatasetsForCombinations(),
        ];

        foreach ($datasetsByType as $datasetByType) {
            foreach ($datasetByType as $dataset) {
                yield $dataset;
            }
        }
    }

    /**
     * @return array
     */
    private function getDatasetsForVirtualProductFile(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $expectedOutputData['stock']['virtual_product_file'] = [
            'has_file' => true,
            'virtual_product_file_id' => self::DEFAULT_VIRTUAL_PRODUCT_FILE_ID,
            'name' => 'heh logo.jpg',
            'download_times_limit' => 0,
            'access_days_limit' => 0,
            'expiration_date' => null,
        ];

        $productData = [
            'virtual_product_file' => [
                'filename' => 'logo.jpg',
                'display_filename' => 'heh logo.jpg',
                'nb_days_accessible' => 0,
                'nb_downloadable' => 0,
                'date_expiration' => null,
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        // test case providing expiration date
        $expirationDate = new DateTimeImmutable();
        $expectedOutputData['stock']['virtual_product_file']['expiration_date'] = $expirationDate->format('Y-m-d');
        $productData['virtual_product_file']['date_expiration'] = $expirationDate;

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        // test case providing NullDateTime expiration date
        $expirationDate = new NullDateTime();
        $expectedOutputData['stock']['virtual_product_file']['expiration_date'] = $expirationDate->format('Y-m-d');
        $productData['virtual_product_file']['date_expiration'] = $expirationDate;

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        // test case has no virtual product file
        $expectedOutputData['stock']['virtual_product_file'] = [
            'has_file' => false,
        ];

        $datasets[] = [
            [],
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForSeo(): array
    {
        $datasets = [];
        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'meta_title' => $localizedValues,
            'meta_description' => $localizedValues,
            'link_rewrite' => $localizedValues,
        ];
        $expectedOutputData['seo']['meta_title'] = $localizedValues;
        $expectedOutputData['seo']['meta_description'] = $localizedValues;
        $expectedOutputData['seo']['link_rewrite'] = $localizedValues;

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForDescription(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $newCover = 'http://localhost/super_cover.jpg';
        $productData = [
            'type' => ProductType::TYPE_VIRTUAL,
            'name' => $localizedValues,
            'description' => $localizedValues,
            'description_short' => $localizedValues,
            'cover_thumbnail' => $newCover,
        ];
        $expectedOutputData['header']['name'] = $localizedValues;
        $expectedOutputData['header']['initial_type'] = ProductType::TYPE_VIRTUAL;
        $expectedOutputData['header']['type'] = ProductType::TYPE_VIRTUAL;
        $expectedOutputData['header']['initial_type'] = ProductType::TYPE_VIRTUAL;
        $expectedOutputData['header']['cover_thumbnail'] = $newCover;

        $expectedOutputData['description']['description'] = $localizedValues;
        $expectedOutputData['description']['description_short'] = $localizedValues;

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForPrices(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        $expectedOutputData = $this->getDefaultOutputData();

        $productData = [
            'price_tax_excluded' => new DecimalNumber('42.00'),
            'price_tax_included' => new DecimalNumber('50.40'),
            'ecotax' => new DecimalNumber('69.51'),
            'ecotax_tax_included' => new DecimalNumber('72.2904'),
            'tax_rules_group_id' => 49,
            'on_sale' => true,
            'wholesale_price' => new DecimalNumber('66.56'),
            'unit_price' => new DecimalNumber('6.656'),
            'unit_price_tax_included' => new DecimalNumber('7.9872'),
            'unity' => 'candies',
            'unit_price_ratio' => new DecimalNumber('5'),
        ];
        $expectedOutputData['pricing']['retail_price']['price_tax_excluded'] = 42.00;
        $expectedOutputData['pricing']['retail_price']['price_tax_included'] = 50.40;
        $expectedOutputData['pricing']['retail_price']['tax_rules_group_id'] = 49;
        $expectedOutputData['pricing']['retail_price']['ecotax_tax_excluded'] = 69.51;
        $expectedOutputData['pricing']['retail_price']['ecotax_tax_included'] = 72.2904;
        $expectedOutputData['pricing']['on_sale'] = true;
        $expectedOutputData['pricing']['wholesale_price'] = 66.56;
        $expectedOutputData['pricing']['unit_price']['price_tax_excluded'] = 6.656;
        $expectedOutputData['pricing']['unit_price']['price_tax_included'] = 7.9872;
        $expectedOutputData['pricing']['unit_price']['unity'] = 'candies';

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        // dataset 2
        $priorities = [
            PriorityList::PRIORITY_CURRENCY,
            PriorityList::PRIORITY_COUNTRY,
            PriorityList::PRIORITY_GROUP,
            PriorityList::PRIORITY_SHOP,
        ];
        $productData['priority_list'] = new PriorityList($priorities);
        $expectedOutputData['pricing']['priority_management'] = [
            'use_custom_priority' => true,
            'priorities' => $priorities,
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForStock(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'quantity' => 42,
            'minimal_quantity' => 7,
            'location' => 'top shelf',
            'low_stock_threshold' => 5,
            'disabling_switch_low_stock_threshold' => true,
            'pack_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY,
            'out_of_stock_type' => OutOfStockType::OUT_OF_STOCK_AVAILABLE,
            'available_now' => $localizedValues,
            'available_later' => $localizedValues,
            'available_date' => new DateTime('1969/07/20'),
            'stock_movements' => [
                [
                    'type' => StockMovement::ORDERS_TYPE,
                    'from_date' => '2022-01-13 18:20:58',
                    'to_date' => '2021-05-24 15:24:32',
                    'stock_movement_ids' => [321, 322, 323, 324, 325],
                    'stock_ids' => [42],
                    'order_ids' => [311, 312, 313, 314, 315],
                    'employee_ids' => [11, 12, 13, 14, 15],
                    'delta_quantity' => -19,
                ],
                [
                    'type' => StockMovement::EDITION_TYPE,
                    'date_add' => '2021-05-24 15:24:32',
                    'stock_movement_id' => 320,
                    'stock_id' => 42,
                    'order_id' => 310,
                    'employee_id' => 12,
                    'employee_name' => 'Paul Atreide',
                    'delta_quantity' => +20,
                ],
                [
                    'type' => StockMovement::ORDERS_TYPE,
                    'from_date' => '2021-05-24 15:24:32',
                    'to_date' => '2021-05-22 16:35:48',
                    'stock_movement_ids' => [221, 222, 223, 224, 225],
                    'stock_ids' => [42],
                    'order_ids' => [211, 212, 213, 214, 215],
                    'employee_ids' => [11, 12, 13, 14, 15],
                    'delta_quantity' => -23,
                ],
                [
                    'type' => StockMovement::EDITION_TYPE,
                    'date_add' => '2021-05-22 16:35:48',
                    'stock_movement_id' => 220,
                    'stock_id' => 42,
                    'order_id' => 210,
                    'employee_id' => 11,
                    'employee_name' => 'Frodo Baggins',
                    'delta_quantity' => +20,
                ],
                [
                    'type' => StockMovement::ORDERS_TYPE,
                    'from_date' => '2021-05-22 16:35:48',
                    'to_date' => '2021-01-24 15:24:32',
                    'stock_movement_ids' => [121, 122, 123, 124, 125],
                    'stock_ids' => [42],
                    'order_ids' => [111, 112, 113, 114, 115],
                    'employee_ids' => [11, 12, 13, 14, 15],
                    'delta_quantity' => -17,
                ],
            ],
        ];
        $expectedOutputData['stock']['quantities']['delta_quantity']['quantity'] = 42;
        $expectedOutputData['stock']['quantities']['minimal_quantity'] = 7;
        $expectedOutputData['stock']['options']['stock_location'] = 'top shelf';
        $expectedOutputData['stock']['options']['low_stock_threshold'] = 5;
        $expectedOutputData['stock']['options']['disabling_switch_low_stock_threshold'] = true;
        $expectedOutputData['stock']['pack_stock_type'] = PackStockType::STOCK_TYPE_PACK_ONLY;
        $expectedOutputData['stock']['availability']['out_of_stock_type'] = OutOfStockType::OUT_OF_STOCK_AVAILABLE;
        $expectedOutputData['stock']['availability']['available_now_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['available_later_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['available_date'] = '1969-07-20';
        $expectedOutputData['stock']['quantities']['stock_movements'] = [
            [
                'type' => 'orders',
                'date' => null,
                'employee_name' => null,
                'delta_quantity' => -19,
            ],
            [
                'type' => 'edition',
                'date' => '2021-05-24 15:24:32',
                'employee_name' => 'Paul Atreide',
                'delta_quantity' => +20,
            ],
            [
                'type' => 'orders',
                'date' => null,
                'employee_name' => null,
                'delta_quantity' => -23,
            ],
            [
                'type' => 'edition',
                'date' => '2021-05-22 16:35:48',
                'employee_name' => 'Frodo Baggins',
                'delta_quantity' => +20,
            ],
            [
                'type' => 'orders',
                'date' => null,
                'employee_name' => null,
                'delta_quantity' => -17,
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForCategories(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'categories' => [
                ['id' => 42, 'name' => 'test1', 'display_name' => 'test > test1', 'removable' => true],
                ['id' => 51, 'name' => 'test22', 'display_name' => 'test > test22', 'removable' => false],
            ],
            'default_category' => 51,
        ];

        $expectedOutputData['description']['categories']['product_categories'] = [
            [
                'id' => 42,
                'name' => 'test1',
                'display_name' => 'test > test1',
                'removable' => true,
            ],
        ];
        $expectedOutputData['description']['categories']['product_categories'][] = [
            'id' => 51,
            'name' => 'test22',
            'display_name' => 'test > test22',
            'removable' => false,
        ];

        $expectedOutputData['description']['categories']['default_category_id'] = 51;

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForPackedProducts(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'packed_products' => [
                0 => [
                    'product_id' => 15,
                    'productName' => 'wicked snowboard',
                    'quantity' => 3,
                    'reference' => 'demo_15',
                    'combination_id' => 1,
                    'image' => 'http://myshop.com/img/p/no_picture-small_default.jpg',
                ],
                1 => [
                    'product_id' => 42,
                    'productName' => 'cool glasses',
                    'quantity' => 5,
                    'reference' => 'demo_42',
                    'combination_id' => 2,
                    'image' => 'http://myshop.com/img/p/no_picture-small_default.jpg',
                ],
            ],
        ];

        $expectedOutputData['stock']['packed_products'] = [
            0 => [
                'product_id' => 15,
                'name' => 'wicked snowboard',
                'reference' => 'demo_15',
                'combination_id' => 1,
                'image' => 'http://myshop.com/img/p/no_picture-small_default.jpg',
                'quantity' => 3,
                'unique_identifier' => '15_1',
            ],
            1 => [
                'product_id' => 42,
                'name' => 'cool glasses',
                'reference' => 'demo_42',
                'combination_id' => 2,
                'image' => 'http://myshop.com/img/p/no_picture-small_default.jpg',
                'quantity' => 5,
                'unique_identifier' => '42_2',
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForRelatedProducts(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'related_products' => [
                0 => [
                    'id' => 15,
                    'name' => 'wicked snowboard',
                    'reference' => 'zebest',
                    'image' => 'http://awesome.jpg',
                ],
                1 => [
                    'id' => 42,
                    'name' => 'cool glasses',
                    'reference' => '',
                    'image' => 'http://awesome.jpg',
                ],
            ],
        ];

        $expectedOutputData['description']['related_products'] = [
            0 => [
                'id' => 15,
                'name' => 'wicked snowboard (ref: zebest)',
                'image' => 'http://awesome.jpg',
            ],
            1 => [
                'id' => 42,
                'name' => 'cool glasses',
                'image' => 'http://awesome.jpg',
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForShipping(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'width' => new DecimalNumber('45.87'),
            'height' => new DecimalNumber('46.87'),
            'depth' => new DecimalNumber('47.87'),
            'weight' => new DecimalNumber('48.87'),
            'additional_shipping_cost' => new DecimalNumber('49.87'),
            'carrier_references' => [69, 99],
            'delivery_time_note_type' => DeliveryTimeNoteType::TYPE_SPECIFIC,
            'delivery_time_in_stock_note' => $localizedValues,
            'delivery_time_out_stock_note' => $localizedValues,
        ];
        $expectedOutputData['shipping']['dimensions']['width'] = '45.87';
        $expectedOutputData['shipping']['dimensions']['height'] = '46.87';
        $expectedOutputData['shipping']['dimensions']['depth'] = '47.87';
        $expectedOutputData['shipping']['dimensions']['weight'] = '48.87';
        $expectedOutputData['shipping']['additional_shipping_cost'] = '49.87';
        $expectedOutputData['shipping']['delivery_time_note_type'] = DeliveryTimeNoteType::TYPE_SPECIFIC;
        $expectedOutputData['shipping']['delivery_time_notes']['in_stock'] = $localizedValues;
        $expectedOutputData['shipping']['delivery_time_notes']['out_of_stock'] = $localizedValues;
        $expectedOutputData['shipping']['carriers'] = [69, 99];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForOptions(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        $localizedNames = [
            1 => 'english name',
            2 => 'french name',
        ];
        $localizedDescriptions = [
            1 => 'english description',
            2 => 'french description',
        ];
        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'active' => false,
            'visibility' => ProductVisibility::VISIBLE_IN_CATALOG,
            'available_for_order' => false,
            'online_only' => true,
            'show_price' => false,
            'condition' => ProductCondition::USED,
            'show_condition' => true,
            'isbn' => 'isbn_2',
            'upc' => 'upc_2',
            'ean13' => 'ean13_2',
            'mpn' => 'mpn_2',
            'reference' => 'reference_2',
            'date_new' => date('Y-m-d'),
            'attachments' => [
                new AttachmentInformation(
                    1,
                    $localizedNames,
                    $localizedDescriptions,
                    'test1',
                    'image/jpeg',
                    1042
                ),
            ],
        ];
        $expectedOutputData['header']['active'] = false;
        $expectedOutputData['options']['visibility']['visibility'] = ProductVisibility::VISIBLE_IN_CATALOG;
        $expectedOutputData['options']['visibility']['available_for_order'] = false;
        $expectedOutputData['options']['visibility']['online_only'] = true;
        $expectedOutputData['options']['visibility']['show_price'] = false;
        $expectedOutputData['options']['date_new'] = date('Y-m-d');

        $expectedOutputData['details']['references']['isbn'] = 'isbn_2';
        $expectedOutputData['details']['references']['upc'] = 'upc_2';
        $expectedOutputData['details']['references']['ean_13'] = 'ean13_2';
        $expectedOutputData['details']['references']['mpn'] = 'mpn_2';
        $expectedOutputData['details']['references']['reference'] = 'reference_2';

        $expectedOutputData['details']['condition'] = ProductCondition::USED;
        $expectedOutputData['details']['show_condition'] = true;

        $expectedOutputData['details']['attachments']['attached_files'] = [
            [
                'attachment_id' => 1,
                'name' => 'english name',
                'file_name' => 'test1',
                'mime_type' => 'image/jpeg',
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForRedirectOption(): array
    {
        $datasets = [];

        $categoryName = 'Category 1';
        $categoryImage = '/path/to/category/img.jpg';

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'redirect_type' => RedirectType::TYPE_CATEGORY_TEMPORARY,
            'redirect_target' => new ProductRedirectTarget(
                self::DEFAULT_CATEGORY_ID,
                ProductRedirectTarget::CATEGORY_TYPE,
                $categoryName,
                $categoryImage
            ),
        ];
        $expectedOutputData['seo']['redirect_option']['type'] = RedirectType::TYPE_CATEGORY_TEMPORARY;
        $expectedOutputData['seo']['redirect_option']['target'] = [
            'id' => self::DEFAULT_CATEGORY_ID,
            'name' => $categoryName,
            'image' => $categoryImage,
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForProductSuppliers(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $expectedOutputData['options']['suppliers']['default_supplier_id'] = 1;
        $expectedOutputData['options']['suppliers']['supplier_ids'] = [1, 2];
        $expectedOutputData['options']['product_suppliers'][1] = [
            'supplier_id' => 1,
            'supplier_name' => 'test supplier 1',
            'product_supplier_id' => 1,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 1',
            'currency_id' => 1,
            'combination_id' => 0,
        ];
        $expectedOutputData['options']['product_suppliers'][2] = [
            'supplier_id' => 2,
            'supplier_name' => 'test supplier 2',
            'product_supplier_id' => 2,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 2',
            'currency_id' => 3,
            'combination_id' => 0,
        ];

        $productData = [
            'suppliers' => [
                'default_supplier_id' => 1,
                'supplier_ids' => [1, 2],
            ],
            'product_suppliers' => [
                [
                    'product_id' => self::PRODUCT_ID,
                    'supplier_id' => 1,
                    'supplier_name' => 'test supplier 1',
                    'product_supplier_id' => 1,
                    'price' => '0',
                    'reference' => 'test supp ref 1',
                    'currency_id' => 1,
                    'combination_id' => 0,
                ],
                [
                    'product_id' => self::PRODUCT_ID,
                    'supplier_id' => 2,
                    'supplier_name' => 'test supplier 2',
                    'product_supplier_id' => 2,
                    'price' => '0',
                    'reference' => 'test supp ref 2',
                    'currency_id' => 3,
                    'combination_id' => 0,
                ],
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        // We can have only the list of suppliers with no product suppliers infos (for product with combinations)
        $expectedOutputData = $this->getDefaultOutputData();
        $expectedOutputData['options']['suppliers']['default_supplier_id'] = 1;
        $expectedOutputData['options']['suppliers']['supplier_ids'] = [1, 2];
        $expectedOutputData['options']['product_suppliers'] = [];

        $productData = [
            'suppliers' => [
                'default_supplier_id' => 1,
                'supplier_ids' => [1, 2],
            ],
            'product_suppliers' => [],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDataSetsForManufacturer(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $expectedOutputData['description']['manufacturer'] = 42;

        $productData = [
            'manufacturer_id' => 42,
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDataSetsForFeatures(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $expectedOutputData['details']['features']['feature_collection'] = [];

        $customLocalizedValues = [
            1 => 'english custom feature',
            2 => 'propriété personnalisée française',
        ];
        $expectedOutputData['details']['features']['feature_collection'][] = [
            'feature_id' => 42,
            'feature_name' => 'Test feature',
            'feature_values' => [
                [
                    'feature_value_id' => 51,
                    'feature_value_name' => 'english feature',
                    'is_custom' => false,
                ],
                [
                    'feature_value_id' => 69,
                    'feature_value_name' => 'english custom feature',
                    'is_custom' => true,
                    'custom_value' => $customLocalizedValues,
                ],
            ],
        ];
        $expectedOutputData['details']['features']['feature_collection'][] = [
            'feature_id' => 51,
            'feature_name' => 'Test feature 2',
            'feature_values' => [
                [
                    'feature_value_id' => 99,
                    'feature_value_name' => 'other english feature',
                    'is_custom' => false,
                ],
            ],
        ];

        $localizedValues = [
            1 => 'english feature',
            2 => 'propriété française',
        ];
        $otherLocalizedValues = [
            1 => 'other english feature',
            2 => 'autre propriété française',
        ];

        $productData = [
            'feature_values' => [
                [
                    'feature_id' => 42,
                    'feature_value_id' => 51,
                    'custom' => false,
                    'localized_values' => $localizedValues,
                ],
                [
                    'feature_id' => 42,
                    'feature_value_id' => 69,
                    'custom' => true,
                    'localized_values' => $customLocalizedValues,
                ],
                [
                    'feature_id' => 51,
                    'feature_value_id' => 99,
                    'custom' => false,
                    'localized_values' => $otherLocalizedValues,
                ],
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForCustomizations(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $localizedNames = [
            1 => 'test1',
            2 => 'test2',
        ];

        $productData = [
            'customizations' => [
                [
                    'id' => 1,
                    'name' => $localizedNames,
                    'type' => 1,
                    'required' => false,
                ],
                [
                    'id' => 2,
                    'name' => $localizedNames,
                    'type' => 0,
                    'required' => true,
                ],
            ],
        ];

        $expectedOutputData['details']['customizations']['customization_fields'] = [
            [
                'id' => 1,
                'name' => $localizedNames,
                'type' => CustomizationFieldType::TYPE_TEXT,
                'required' => false,
            ],
            [
                'id' => 2,
                'name' => $localizedNames,
                'type' => CustomizationFieldType::TYPE_FILE,
                'required' => true,
            ],
        ];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForCombinations(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [];

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $expectedOutputData = $this->getDefaultOutputData();
        $productData = [
            'type' => ProductType::TYPE_COMBINATIONS,
            'out_of_stock_type' => OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE,
            'available_now' => $localizedValues,
            'available_later' => $localizedValues,
        ];

        $expectedOutputData['header']['type'] = ProductType::TYPE_COMBINATIONS;
        $expectedOutputData['header']['initial_type'] = ProductType::TYPE_COMBINATIONS;
        $expectedOutputData['combinations']['availability']['out_of_stock_type'] = OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE;
        $expectedOutputData['combinations']['availability']['available_now_label'] = $localizedValues;
        $expectedOutputData['combinations']['availability']['available_later_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['available_now_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['available_later_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['out_of_stock_type'] = OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE;

        $datasets[] = [
            $productData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @param array $product
     *
     * @return ProductForEditing
     */
    private function createProductForEditing(array $product): ProductForEditing
    {
        return new ProductForEditing(
            self::PRODUCT_ID,
            $product['type'] ?? ProductType::TYPE_STANDARD,
            $product['active'] ?? true,
            ProductCustomizationOptions::createNotCustomizable(),
            $this->createBasic($product),
            $this->createCategories($product),
            $this->createPricesInformation($product),
            $this->createOptions($product),
            $this->createDetails($product),
            $this->createShippingInformation($product),
            $this->createSeoOptions($product),
            $product['attachments'] ?? [],
            $this->createProductStockInformation($product),
            $this->createVirtualProductFile($product),
            $product['cover_thumbnail'] ?? self::COVER_URL,
            new DateTime('now')
        );
    }

    /**
     * @param array $productData
     *
     * @return ProductSupplierOptions
     */
    private function createProductSupplierOptions(array $productData): ProductSupplierOptions
    {
        if (empty($productData['suppliers'])) {
            return new ProductSupplierOptions(0, [], []);
        }

        $productSuppliers = [];
        if (!empty($productData['product_suppliers'])) {
            foreach ($productData['product_suppliers'] as $supplierInfo) {
                $productSuppliers[] = new ProductSupplierForEditing(
                    $supplierInfo['product_supplier_id'],
                    $supplierInfo['product_id'],
                    $supplierInfo['supplier_id'],
                    $supplierInfo['supplier_name'],
                    $supplierInfo['reference'],
                    $supplierInfo['price'],
                    $supplierInfo['currency_id'],
                    $supplierInfo['combination_id']
                );
            }
        }

        return new ProductSupplierOptions(
            $productData['suppliers']['default_supplier_id'],
            $productData['suppliers']['supplier_ids'],
            $productSuppliers
        );
    }

    /**
     * @param array $productData
     *
     * @return ProductFeatureValue[]
     */
    private function createProductFeatureValueOptions(array $productData): array
    {
        if (empty($productData['feature_values'])) {
            return [];
        }

        $productFeatureValues = [];
        foreach ($productData['feature_values'] as $featureValue) {
            $productFeatureValues[] = new ProductFeatureValue(
                $featureValue['feature_id'],
                $featureValue['feature_value_id'],
                $featureValue['localized_values'],
                $featureValue['custom']
            );
        }

        return $productFeatureValues;
    }

    /**
     * @param array $productData
     *
     * @return PackedProductDetails[]
     */
    private function createPackedProductsDetails(array $productData): array
    {
        if (empty($productData['packed_products'])) {
            return [];
        }

        $packedProducts = [];
        foreach ($productData['packed_products'] as $packedProduct) {
            $packedProducts[] = new PackedProductDetails(
                (int) $packedProduct['product_id'],
                (int) $packedProduct['quantity'],
                $packedProduct['combination_id'],
                $packedProduct['productName'],
                $packedProduct['reference'],
                $packedProduct['image']
            );
        }

        return $packedProducts;
    }

    /**
     * @param array $productData
     *
     * @return RelatedProduct[]
     */
    private function createRelatedProducts(array $productData): array
    {
        if (empty($productData['related_products'])) {
            return [];
        }

        $relatedProducts = [];
        foreach ($productData['related_products'] as $relatedProduct) {
            $relatedProducts[] = new RelatedProduct(
                (int) $relatedProduct['id'],
                $relatedProduct['name'],
                $relatedProduct['reference'],
                $relatedProduct['image']
            );
        }

        return $relatedProducts;
    }

    /**
     * @param array $productData
     *
     * @return CustomizationField[]
     */
    private function createProductCustomizationFields(array $productData): array
    {
        if (!isset($productData['customizations'])) {
            return [];
        }

        $customizationFields = [];
        foreach ($productData['customizations'] as $customization) {
            $customizationFields[] = new CustomizationField(
                $customization['id'],
                $customization['type'],
                $customization['name'],
                $customization['required'],
                false
            );
        }

        return $customizationFields;
    }

    /**
     * @param array $productData
     *
     * @return StockMovement[]
     */
    private function createStockMovementHistories(array $productData): array
    {
        return array_map(
            static function (array $historyData): StockMovement {
                if (StockMovement::EDITION_TYPE === $historyData['type']) {
                    return StockMovement::createEditionMovement(
                        $historyData['date_add'],
                        $historyData['stock_movement_id'],
                        $historyData['stock_id'],
                        $historyData['order_id'],
                        $historyData['employee_id'],
                        $historyData['employee_name'],
                        $historyData['delta_quantity']
                    );
                }
                if (StockMovement::ORDERS_TYPE === $historyData['type']) {
                    return StockMovement::createOrdersMovement(
                        $historyData['from_date'],
                        $historyData['to_date'],
                        $historyData['stock_movement_ids'],
                        $historyData['stock_ids'],
                        $historyData['order_ids'],
                        $historyData['employee_ids'],
                        $historyData['delta_quantity']
                    );
                }
                throw new RuntimeException(
                    sprintf('Unsupported stock movement event type "%s"', $historyData['type'])
                );
            },
            $productData['stock_movements'] ?? []
        );
    }

    /**
     * @param array $product
     *
     * @return VirtualProductFileForEditing|null
     */
    private function createVirtualProductFile(array $product): ?VirtualProductFileForEditing
    {
        if (!isset($product['virtual_product_file'])) {
            return null;
        }

        return new VirtualProductFileForEditing(
            $product['virtual_product_file']['virtual_product_file_id'] ?? self::DEFAULT_VIRTUAL_PRODUCT_FILE_ID,
            $product['virtual_product_file']['filename'] ?? 'filename',
            $product['virtual_product_file']['display_filename'] ?? 'display_filename',
            $product['virtual_product_file']['nb_days_accessible'] ?? 0,
            $product['virtual_product_file']['nb_downloadable'] ?? 0,
            $product['virtual_product_file']['date_expiration'] ?? null
        );
    }

    /**
     * @param array $product
     *
     * @return ProductStockInformation
     */
    private function createProductStockInformation(array $product): ProductStockInformation
    {
        return new ProductStockInformation(
            $product['pack_stock_type'] ?? PackStockType::STOCK_TYPE_DEFAULT,
            $product['out_of_stock_type'] ?? OutOfStockType::OUT_OF_STOCK_DEFAULT,
            $product['quantity'] ?? self::DEFAULT_QUANTITY,
            $product['minimal_quantity'] ?? 0,
            $product['low_stock_threshold'] ?? 0,
            $product['disabling_switch_low_stock_threshold'] ?? false,
            $product['available_now'] ?? [],
            $product['available_later'] ?? [],
            $product['location'] ?? 'location',
            $product['available_date'] ?? null
        );
    }

    /**
     * @param array $product
     *
     * @return ProductSeoOptions
     */
    private function createSeoOptions(array $product): ProductSeoOptions
    {
        return new ProductSeoOptions(
            $product['meta_title'] ?? [],
            $product['meta_description'] ?? [],
            $product['link_rewrite'] ?? [],
            $product['redirect_type'] ?? RedirectType::TYPE_NOT_FOUND,
            $product['redirect_target'] ?? null
        );
    }

    /**
     * @param array $product
     *
     * @return ProductShippingInformation
     */
    private function createShippingInformation(array $product): ProductShippingInformation
    {
        return new ProductShippingInformation(
            $product['width'] ?? new DecimalNumber('19.86'),
            $product['height'] ?? new DecimalNumber('19.86'),
            $product['depth'] ?? new DecimalNumber('19.86'),
            $product['weight'] ?? new DecimalNumber('19.86'),
            $product['additional_shipping_cost'] ?? new DecimalNumber('19.86'),
            $product['carrier_references'] ?? [],
            $product['delivery_time_note_type'] ?? DeliveryTimeNoteType::TYPE_DEFAULT,
            $product['delivery_time_in_stock_note'] ?? [],
            $product['delivery_time_out_stock_note'] ?? []
        );
    }

    /**
     * @param array $product
     *
     * @return ProductDetails
     */
    private function createDetails(array $product): ProductDetails
    {
        return new ProductDetails(
            $product['isbn'] ?? 'isbn',
            $product['upc'] ?? 'upc',
            $product['ean13'] ?? 'ean13',
            $product['mpn'] ?? 'mpn',
            $product['reference'] ?? 'reference'
        );
    }

    /**
     * @param array $product
     *
     * @return ProductOptions
     */
    private function createOptions(array $product): ProductOptions
    {
        return new ProductOptions(
            $product['visibility'] ?? ProductVisibility::VISIBLE_EVERYWHERE,
            $product['available_for_order'] ?? true,
            $product['online_only'] ?? false,
            $product['show_price'] ?? true,
            $product['condition'] ?? ProductCondition::NEW,
            $product['show_condition'] ?? false,
            $product['manufacturer_id'] ?? 0
        );
    }

    /**
     * @param array $product
     *
     * @return ProductPricesInformation
     */
    private function createPricesInformation(array $product): ProductPricesInformation
    {
        return new ProductPricesInformation(
            $product['price_tax_excluded'] ?? new DecimalNumber('19.86'),
            $product['price_tax_included'] ?? new DecimalNumber('23.832'),
            $product['ecotax'] ?? new DecimalNumber('19.86'),
            $product['ecotax_tax_included'] ?? new DecimalNumber('20.6544'),
            $product['tax_rules_group_id'] ?? 1,
            $product['on_sale'] ?? false,
            $product['wholesale_price'] ?? new DecimalNumber('19.86'),
            $product['unit_price'] ?? new DecimalNumber('19.86'),
            $product['unit_price_tax_included'] ?? new DecimalNumber('23.832'),
            $product['unity'] ?? '',
            $product['unit_price_ratio'] ?? new DecimalNumber('1'),
            $product['priority_list'] ?? null
        );
    }

    /**
     * @param array $product
     *
     * @return CategoriesInformation
     */
    private function createCategories(array $product): CategoriesInformation
    {
        $categoriesInfo = [];
        if (isset($product['categories'])) {
            foreach ($product['categories'] as $category) {
                $categoriesInfo[] = new CategoryInformation($category['id'], $category['name'], $category['display_name']);
            }
        }

        return new CategoriesInformation(
            $categoriesInfo,
            $product['default_category'] ?? self::HOME_CATEGORY_ID
        );
    }

    /**
     * @param array $product
     *
     * @return ProductBasicInformation
     */
    private function createBasic(array $product): ProductBasicInformation
    {
        return new ProductBasicInformation(
            $product['name'] ?? [],
            $product['description'] ?? [],
            $product['description_short'] ?? [],
            $product['tags'] ?? []
        );
    }

    /**
     * @param int $expectedShopId
     *
     * @return CommandBusInterface
     */
    private function createQueryBusCheckingShopMock(int $expectedShopId): CommandBusInterface
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $queryBusMock
            ->method('handle')
            ->with($this->getHandledQueries())
            ->willReturnCallback(function ($query) use ($expectedShopId) {
                if ($query instanceof GetProductForEditing) {
                    $this->assertEquals($expectedShopId, $query->getShopConstraint()->getShopId()->getValue());
                }

                return $this->createResultBasedOnQuery($query, []);
            })
        ;

        return $queryBusMock;
    }

    /**
     * @param array $productData
     *
     * @return CommandBusInterface
     */
    private function createQueryBusMock(array $productData): CommandBusInterface
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $queryBusMock
            ->method('handle')
            ->with($this->getHandledQueries())
            ->willReturnCallback(function ($query) use ($productData) {
                return $this->createResultBasedOnQuery($query, $productData);
            })
        ;

        return $queryBusMock;
    }

    /**
     * @return LogicalOr
     */
    private function getHandledQueries(): LogicalOr
    {
        return $this->logicalOr(
            $this->isInstanceOf(GetProductForEditing::class),
            $this->isInstanceOf(GetProductSupplierOptions::class),
            $this->isInstanceOf(GetProductFeatureValues::class),
            $this->isInstanceOf(GetProductCustomizationFields::class),
            $this->isInstanceOf(GetProductStockMovements::class),
            $this->isInstanceOf(GetRelatedProducts::class),
            $this->isInstanceOf(GetPackedProducts::class)
        );
    }

    /**
     * @param mixed $query
     * @param array $productData
     *
     * @return ProductForEditing|ProductSupplierOptions|ProductFeatureValue[]|CustomizationField[]|StockMovement[]|RelatedProduct[]|PackedProductDetails[]
     */
    private function createResultBasedOnQuery($query, array $productData)
    {
        switch ($queryClass = get_class($query)) {
            case GetProductForEditing::class:
                return $this->createProductForEditing($productData);
            case GetProductSupplierOptions::class:
                return $this->createProductSupplierOptions($productData);
            case GetProductFeatureValues::class:
                return $this->createProductFeatureValueOptions($productData);
            case GetProductCustomizationFields::class:
                return $this->createProductCustomizationFields($productData);
            case GetProductStockMovements::class:
                return $this->createStockMovementHistories($productData);
            case GetRelatedProducts::class:
                return $this->createRelatedProducts($productData);
            case GetPackedProducts::class:
                return $this->createPackedProductsDetails($productData);
        }

        throw new RuntimeException(sprintf('Query "%s" was not expected in query bus mock', $queryClass));
    }

    /**
     * @return array
     */
    private function getDefaultOutputData(): array
    {
        return [
            'id' => self::PRODUCT_ID,
            'header' => [
                'type' => ProductType::TYPE_STANDARD,
                'initial_type' => ProductType::TYPE_STANDARD,
                'name' => [],
                'cover_thumbnail' => self::COVER_URL,
                'active' => true,
            ],
            'description' => [
                'description' => [],
                'description_short' => [],
                'categories' => [
                    'product_categories' => [],
                    'default_category_id' => self::HOME_CATEGORY_ID,
                ],
                'manufacturer' => NoManufacturerId::NO_MANUFACTURER_ID,
                'related_products' => [],
            ],
            'details' => [
                'references' => [
                    'mpn' => 'mpn',
                    'upc' => 'upc',
                    'ean_13' => 'ean13',
                    'isbn' => 'isbn',
                    'reference' => 'reference',
                ],
                'features' => [],
                'attachments' => [],
                'show_condition' => false,
                'condition' => ProductCondition::NEW,
                'customizations' => [],
            ],
            'stock' => [
                'quantities' => [
                    'delta_quantity' => [
                        'quantity' => self::DEFAULT_QUANTITY,
                        'delta' => 0,
                    ],
                    'stock_movements' => [],
                    'minimal_quantity' => 0,
                ],
                'options' => [
                    'stock_location' => 'location',
                    'low_stock_threshold' => 0,
                    'disabling_switch_low_stock_threshold' => false,
                ],
                'virtual_product_file' => [
                    'has_file' => false,
                ],
                'pack_stock_type' => PackStockType::STOCK_TYPE_DEFAULT,
                'availability' => [
                    'out_of_stock_type' => OutOfStockType::OUT_OF_STOCK_DEFAULT,
                    'available_now_label' => [],
                    'available_later_label' => [],
                    'available_date' => '',
                ],
                'packed_products' => [],
            ],
            'pricing' => [
                'retail_price' => [
                    'price_tax_excluded' => 19.86,
                    'price_tax_included' => 23.832,
                    'tax_rules_group_id' => 1,
                    'ecotax_tax_excluded' => 19.86,
                    'ecotax_tax_included' => 20.6544,
                ],
                'on_sale' => false,
                'wholesale_price' => 19.86,
                'unit_price' => [
                    'price_tax_excluded' => 19.86,
                    'price_tax_included' => 23.832,
                    'unity' => '',
                ],
                'priority_management' => [
                    'use_custom_priority' => false,
                    'priorities' => self::DEFAULT_PRIORITY_LIST,
                ],
            ],
            'seo' => [
                'meta_title' => [],
                'meta_description' => [],
                'link_rewrite' => [],
                'redirect_option' => [
                    'type' => RedirectType::TYPE_NOT_FOUND,
                    'target' => null,
                ],
                'tags' => [],
            ],
            'shipping' => [
                'dimensions' => [
                    'width' => '19.86',
                    'height' => '19.86',
                    'depth' => '19.86',
                    'weight' => '19.86',
                ],
                'additional_shipping_cost' => '19.86',
                'delivery_time_note_type' => DeliveryTimeNoteType::TYPE_DEFAULT,
                'delivery_time_notes' => [
                    'in_stock' => [],
                    'out_of_stock' => [],
                ],
                'carriers' => [],
            ],
            'options' => [
                'visibility' => [
                    'visibility' => ProductVisibility::VISIBLE_EVERYWHERE,
                    'available_for_order' => true,
                    'show_price' => true,
                    'online_only' => false,
                ],
                'date_new' => date('Y-m-d'),
                'suppliers' => [
                    'default_supplier_id' => 0,
                    'supplier_ids' => [],
                ],
                'product_suppliers' => [],
            ],
        ];
    }

    /**
     * @param CommandBusInterface $queryBusMock
     *
     * @return ProductFormDataProvider
     */
    private function buildProvider(CommandBusInterface $queryBusMock): ProductFormDataProvider
    {
        return new ProductFormDataProvider(
            $queryBusMock,
            $this->getDefaultConfigurationMock(),
            self::CONTEXT_LANG_ID,
            self::DEFAULT_SHOP_ID,
            null,
            $this->getFeaturesProvider()
        );
    }

    /**
     * @return ConfigurationInterface
     */
    private function getDefaultConfigurationMock(): ConfigurationInterface
    {
        $configurationMock = $this->getMockBuilder(ConfigurationInterface::class)->getMock();

        $configurationMock->method('get')->willReturnMap([
            ['PS_SPECIFIC_PRICE_PRIORITIES', implode(';', self::DEFAULT_PRIORITY_LIST)],
        ]);

        return $configurationMock;
    }

    private function getFeaturesProvider(): FeaturesChoiceProvider
    {
        $featureProviderMock = $this->getMockBuilder(FeaturesChoiceProvider::class)->disableOriginalConstructor()->getMock();
        $featureProviderMock->method('getChoices')->willReturn([
            'Feature A' => 1,
            'Feature B' => 2,
            'Test feature' => 42,
            'Test feature 2' => 51,
        ]);

        return $featureProviderMock;
    }
}
