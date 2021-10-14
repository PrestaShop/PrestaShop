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

use Category;
use DateTime;
use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Category\CategoryDataProvider;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Attachment\QueryResult\AttachmentInformation;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\NoManufacturerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Query\GetProductCustomizationFields;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\QueryResult\CustomizationField;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Query\GetProductFeatureValues;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\QueryResult\ProductFeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
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
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetEmployeesStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\EmployeeStockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierInfo;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

// @todo: ProductFormDataProvider needs to be split to multiple classes to allow easier testing
class ProductFormDataProviderTest extends TestCase
{
    private const PRODUCT_ID = 42;
    private const HOME_CATEGORY_ID = 49;
    private const DEFAULT_CATEGORY_ID = 51;
    private const HOME_CATEGORY_NAME = 'Home';
    private const DEFAULT_VIRTUAL_PRODUCT_FILE_ID = 69;
    private const CONTEXT_LANG_ID = 1;
    private const DEFAULT_QUANTITY = 12;
    private const COVER_URL = 'http://localhost/cover.jpg';

    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $provider = $this->buildProvider($queryBusMock, false);

        $expectedDefaultData = [
            'header' => [
                'type' => ProductType::TYPE_STANDARD,
            ],
            'description' => [
                'categories' => [
                    'product_categories' => [
                        [
                            'id' => self::HOME_CATEGORY_ID,
                            'name' => self::HOME_CATEGORY_NAME,
                        ],
                    ],
                    'default_category_id' => self::HOME_CATEGORY_ID,
                ],
                'manufacturer' => NoManufacturerId::NO_MANUFACTURER_ID,
            ],
            'stock' => [
                'quantities' => [
                    'quantity' => 0,
                    'stock_movements' => [],
                    'minimal_quantity' => 0,
                ],
            ],
            'pricing' => [
                'retail_price' => [
                    'price_tax_excluded' => 0,
                    'price_tax_included' => 0,
                ],
                'tax_rules_group_id' => 42,
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
            'footer' => [
                'active' => false,
            ],
            'shortcuts' => [
                'retail_price' => [
                    'price_tax_excluded' => 0,
                    'price_tax_included' => 0,
                    'tax_rules_group_id' => 42,
                ],
                'stock' => [
                    'quantity' => 0,
                ],
            ],
        ];

        $defaultData = $provider->getDefaultData();

        // assertSame is very important here We can't assume null and 0 are the same thing
        $this->assertSame($expectedDefaultData, $defaultData);

        $provider = $this->buildProvider($queryBusMock, true);

        $expectedDefaultData = [
            'header' => [
                'type' => ProductType::TYPE_STANDARD,
            ],
            'description' => [
                'categories' => [
                    'product_categories' => [
                        [
                            'id' => self::HOME_CATEGORY_ID,
                            'name' => self::HOME_CATEGORY_NAME,
                        ],
                    ],
                    'default_category_id' => self::HOME_CATEGORY_ID,
                ],
                'manufacturer' => NoManufacturerId::NO_MANUFACTURER_ID,
            ],
            'stock' => [
                'quantities' => [
                    'quantity' => 0,
                    'stock_movements' => [],
                    'minimal_quantity' => 0,
                ],
            ],
            'pricing' => [
                'retail_price' => [
                    'price_tax_excluded' => 0,
                    'price_tax_included' => 0,
                ],
                'tax_rules_group_id' => 42,
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
            'footer' => [
                'active' => true,
            ],
            'shortcuts' => [
                'retail_price' => [
                    'price_tax_excluded' => 0,
                    'price_tax_included' => 0,
                    'tax_rules_group_id' => 42,
                ],
                'stock' => [
                    'quantity' => 0,
                ],
            ],
        ];

        $defaultData = $provider->getDefaultData();

        // assertSame is very important here We can't assume null and 0 are the same thing
        $this->assertSame($expectedDefaultData, $defaultData);
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
        $provider = $this->buildProvider($queryBusMock, false);

        $formData = $provider->getData(static::PRODUCT_ID);
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
            'type' => ProductType::TYPE_COMBINATIONS,
            'name' => $localizedValues,
            'description' => $localizedValues,
            'description_short' => $localizedValues,
            'cover_thumbnail' => $newCover,
        ];
        $expectedOutputData['header']['name'] = $localizedValues;
        $expectedOutputData['header']['type'] = ProductType::TYPE_COMBINATIONS;
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
            'tax_rules_group_id' => 49,
            'on_sale' => true,
            'wholesale_price' => new DecimalNumber('66.56'),
            'unit_price' => new DecimalNumber('6.656'),
            'unity' => 'candies',
            'unit_price_ratio' => new DecimalNumber('5'),
        ];
        $expectedOutputData['pricing']['retail_price']['price_tax_excluded'] = 42.00;
        $expectedOutputData['pricing']['retail_price']['price_tax_included'] = 50.40;
        $expectedOutputData['pricing']['retail_price']['ecotax'] = 69.51;
        $expectedOutputData['pricing']['tax_rules_group_id'] = 49;
        $expectedOutputData['pricing']['on_sale'] = true;
        $expectedOutputData['pricing']['wholesale_price'] = 66.56;
        $expectedOutputData['pricing']['unit_price']['price'] = 6.656;
        $expectedOutputData['pricing']['unit_price']['unity'] = 'candies';

        // Not handled yet
        // $expectedOutputData['price']['unit_price_ratio'] = 5;

        $expectedOutputData['shortcuts']['retail_price']['price_tax_excluded'] = 42.00;
        $expectedOutputData['shortcuts']['retail_price']['price_tax_included'] = 50.40;
        $expectedOutputData['shortcuts']['retail_price']['tax_rules_group_id'] = 49;

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
            'low_stock_alert' => true,
            'pack_stock_type' => PackStockType::STOCK_TYPE_PACK_ONLY,
            'out_of_stock' => OutOfStockType::OUT_OF_STOCK_AVAILABLE,
            'available_now' => $localizedValues,
            'available_later' => $localizedValues,
            'available_date' => new DateTime('1969/07/20'),
            'stock_movements' => [
                [
                    'id_stock_mvt' => 10,
                    'delta_quantity' => +42,
                    'employee_firstname' => 'Paul',
                    'employee_lastname' => 'Atreide',
                    'date_add' => '2021-05-24 15:24:32',
                ],
                [
                    'id_stock_mvt' => 11,
                    'delta_quantity' => -15,
                    'employee_firstname' => 'Frodo',
                    'employee_lastname' => 'Baggins',
                    'date_add' => '2021-05-22 16:35:48',
                ],
            ],
        ];
        $expectedOutputData['stock']['quantities']['quantity'] = 42;
        $expectedOutputData['stock']['quantities']['minimal_quantity'] = 7;
        $expectedOutputData['stock']['options']['stock_location'] = 'top shelf';
        $expectedOutputData['stock']['options']['low_stock_threshold'] = 5;
        $expectedOutputData['stock']['options']['low_stock_alert'] = true;
        $expectedOutputData['stock']['pack_stock_type'] = PackStockType::STOCK_TYPE_PACK_ONLY;
        $expectedOutputData['stock']['availability']['out_of_stock_type'] = OutOfStockType::OUT_OF_STOCK_AVAILABLE;
        $expectedOutputData['stock']['availability']['available_now_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['available_later_label'] = $localizedValues;
        $expectedOutputData['stock']['availability']['available_date'] = '1969-07-20';

        $expectedOutputData['stock']['quantities']['stock_movements'] = [
            [
                'date_add' => '2021-05-24 15:24:32',
                'employee' => 'Paul Atreide',
                'delta_quantity' => 42,
            ],
            [
                'date_add' => '2021-05-22 16:35:48',
                'employee' => 'Frodo Baggins',
                'delta_quantity' => -15,
            ],
        ];

        $expectedOutputData['shortcuts']['stock']['quantity'] = 42;

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
                ['id' => 42, 'localized_names' => [self::CONTEXT_LANG_ID => 'test1', 2 => 'test2']],
                ['id' => 51, 'localized_names' => [self::CONTEXT_LANG_ID => 'test22', 3 => 'test3']],
            ],
            'default_category' => 51,
        ];

        $expectedOutputData['description']['categories']['product_categories'] = [
            [
                'id' => 42,
                'name' => 'test1',
            ],
        ];
        $expectedOutputData['description']['categories']['product_categories'][] = [
            'id' => 51,
            'name' => 'test22',
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
        $expectedOutputData['footer']['active'] = false;
        $expectedOutputData['options']['visibility']['visibility'] = ProductVisibility::VISIBLE_IN_CATALOG;
        $expectedOutputData['options']['visibility']['available_for_order'] = false;
        $expectedOutputData['options']['visibility']['online_only'] = true;
        $expectedOutputData['options']['visibility']['show_price'] = false;
        $expectedOutputData['options']['condition'] = ProductCondition::USED;
        $expectedOutputData['options']['show_condition'] = true;

        $expectedOutputData['specifications']['references']['isbn'] = 'isbn_2';
        $expectedOutputData['specifications']['references']['upc'] = 'upc_2';
        $expectedOutputData['specifications']['references']['ean_13'] = 'ean13_2';
        $expectedOutputData['specifications']['references']['mpn'] = 'mpn_2';
        $expectedOutputData['specifications']['references']['reference'] = 'reference_2';

        $expectedOutputData['specifications']['attachments']['attached_files'] = [
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
                static::DEFAULT_CATEGORY_ID,
                ProductRedirectTarget::CATEGORY_TYPE,
                $categoryName,
                $categoryImage
            ),
        ];
        $expectedOutputData['seo']['redirect_option']['type'] = RedirectType::TYPE_CATEGORY_TEMPORARY;
        $expectedOutputData['seo']['redirect_option']['target'] = [
            'id' => static::DEFAULT_CATEGORY_ID,
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
        $expectedOutputData['options']['suppliers']['supplier_ids'] = [
            0 => 1,
            1 => 2,
        ];
        $expectedOutputData['options']['suppliers']['product_suppliers'][1] = [
            'supplier_id' => 1,
            'supplier_name' => 'test supplier 1',
            'product_supplier_id' => 1,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 1',
            'currency_id' => 1,
            'combination_id' => 0,
        ];
        $expectedOutputData['options']['suppliers']['product_suppliers'][2] = [
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
        $expectedOutputData['specifications']['features']['feature_values'] = [];
        $expectedOutputData['specifications']['features']['feature_values'][] = [
            'feature_id' => 42,
            'feature_value_id' => 51,
        ];

        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $expectedOutputData['specifications']['features']['feature_values'][] = [
            'feature_id' => 42,
            'feature_value_id' => 69,
            'custom_value' => $localizedValues,
            'custom_value_id' => 69,
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
                    'localized_values' => $localizedValues,
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

        $expectedOutputData['specifications']['customizations']['customization_fields'] = [
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
     * @param array $product
     *
     * @return ProductForEditing
     */
    private function createProductForEditing(array $product): ProductForEditing
    {
        return new ProductForEditing(
            static::PRODUCT_ID,
            $product['type'] ?? ProductType::TYPE_STANDARD,
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
            $product['cover_thumbnail'] ?? static::COVER_URL
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
            return new ProductSupplierOptions(0, []);
        }

        $suppliersInfo = [];
        foreach ($productData['suppliers']['product_suppliers'] as $supplierInfo) {
            $suppliersInfo[] = new ProductSupplierInfo(
                $supplierInfo['supplier_name'],
                $supplierInfo['supplier_id'],
                new ProductSupplierForEditing(
                    $supplierInfo['product_supplier_id'],
                    $supplierInfo['product_id'],
                    $supplierInfo['supplier_id'],
                    $supplierInfo['reference'],
                    $supplierInfo['price'],
                    $supplierInfo['currency_id'],
                    $supplierInfo['combination_id']
                )
            );
        }

        return new ProductSupplierOptions(
            $productData['suppliers']['default_supplier_id'],
            $suppliersInfo
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
     * @return EmployeeStockMovement[]
     */
    private function createProductStockMovements(array $productData): array
    {
        if (!isset($productData['stock_movements'])) {
            return [];
        }

        $stockMovements = [];
        foreach ($productData['stock_movements'] as $stockMovement) {
            $stockMovements[] = new EmployeeStockMovement(
                $stockMovement['id_stock_mvt'],
                42,
                11,
                $stockMovement['delta_quantity'],
                42,
                $stockMovement['employee_firstname'],
                $stockMovement['employee_lastname'],
                new DateTime($stockMovement['date_add'])
            );
        }

        return $stockMovements;
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
            $product['out_of_stock'] ?? OutOfStockType::OUT_OF_STOCK_DEFAULT,
            $product['quantity'] ?? static::DEFAULT_QUANTITY,
            $product['minimal_quantity'] ?? 0,
            $product['low_stock_threshold'] ?? 0,
            $product['low_stock_alert'] ?? false,
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
            $product['active'] ?? true,
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
            $product['tax_rules_group_id'] ?? 1,
            $product['on_sale'] ?? false,
            $product['wholesale_price'] ?? new DecimalNumber('19.86'),
            $product['unit_price'] ?? new DecimalNumber('19.86'),
            $product['unity'] ?? '',
            $product['unit_price_ratio'] ?? new DecimalNumber('1')
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
                $categoriesInfo[] = new CategoryInformation($category['id'], $category['localized_names']);
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
     * @param array $productData
     *
     * @return MockObject|CommandBusInterface
     */
    private function createQueryBusMock(array $productData)
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $queryBusMock
            ->method('handle')
            ->with($this->logicalOr(
                $this->isInstanceOf(GetProductForEditing::class),
                $this->isInstanceOf(GetProductSupplierOptions::class),
                $this->isInstanceOf(GetProductFeatureValues::class),
                $this->isInstanceOf(GetProductCustomizationFields::class),
                $this->isInstanceOf(GetEmployeesStockMovements::class)
            ))
            ->willReturnCallback(function ($query) use ($productData) {
                return $this->createResultBasedOnQuery($query, $productData);
            })
        ;

        return $queryBusMock;
    }

    /**
     * @param mixed $query
     * @param array $productData
     *
     * @return ProductForEditing|ProductSupplierOptions|ProductFeatureValue[]|CustomizationField[]|StockMovement[]
     */
    private function createResultBasedOnQuery($query, array $productData)
    {
        $queryResultMap = [
            GetProductForEditing::class => $this->createProductForEditing($productData),
            GetProductSupplierOptions::class => $this->createProductSupplierOptions($productData),
            GetProductFeatureValues::class => $this->createProductFeatureValueOptions($productData),
            GetProductCustomizationFields::class => $this->createProductCustomizationFields($productData),
            GetEmployeesStockMovements::class => $this->createProductStockMovements($productData),
        ];

        $queryClass = get_class($query);
        if (array_key_exists($queryClass, $queryResultMap)) {
            return $queryResultMap[$queryClass];
        }

        throw new RuntimeException(sprintf('Query "%s" was not expected in query bus mock', $queryClass));
    }

    /**
     * @return array
     */
    private function getDefaultOutputData(): array
    {
        return [
            'id' => static::PRODUCT_ID,
            'header' => [
                'type' => ProductType::TYPE_STANDARD,
                'name' => [],
                'cover_thumbnail' => static::COVER_URL,
            ],
            'description' => [
                'description' => [],
                'description_short' => [],
                'manufacturer' => NoManufacturerId::NO_MANUFACTURER_ID,
                'categories' => [
                    'product_categories' => [],
                    'default_category_id' => self::HOME_CATEGORY_ID,
                ],
            ],
            'specifications' => [
                'references' => [
                    'mpn' => 'mpn',
                    'upc' => 'upc',
                    'ean_13' => 'ean13',
                    'isbn' => 'isbn',
                    'reference' => 'reference',
                ],
                'features' => [],
                'attachments' => [],
                'customizations' => [],
            ],
            'stock' => [
                'quantities' => [
                    'quantity' => static::DEFAULT_QUANTITY,
                    'stock_movements' => [],
                    'minimal_quantity' => 0,
                ],
                'options' => [
                    'stock_location' => 'location',
                    'low_stock_threshold' => null,
                    'low_stock_alert' => false,
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
            ],
            'pricing' => [
                'retail_price' => [
                    'price_tax_excluded' => 19.86,
                    'price_tax_included' => 23.832,
                    'ecotax' => 19.86,
                ],
                'tax_rules_group_id' => 1,
                'on_sale' => false,
                'wholesale_price' => 19.86,
                'unit_price' => [
                    'price' => 19.86,
                    'unity' => '',
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
                'show_condition' => false,
                'condition' => ProductCondition::NEW,
                'suppliers' => [],
            ],
            'footer' => [
                'active' => true,
            ],
            'shortcuts' => [
                'retail_price' => [
                    'price_tax_excluded' => 19.86,
                    'price_tax_included' => 23.832,
                    'tax_rules_group_id' => 1,
                ],
                'stock' => [
                    'quantity' => static::DEFAULT_QUANTITY,
                ],
            ],
        ];
    }

    /**
     * @param CommandBusInterface $queryBusMock
     * @param $activation
     *
     * @return ProductFormDataProvider
     */
    private function buildProvider(CommandBusInterface $queryBusMock, $activation): ProductFormDataProvider
    {
        $urlGeneratorMock = $this->getMockBuilder(UrlGeneratorInterface::class)->getMock();
        $urlGeneratorMock->method('generate')->willReturnArgument(0);

        return new ProductFormDataProvider(
            $queryBusMock,
            $activation,
            42,
            self::HOME_CATEGORY_ID,
            $this->mockCategoryDataProvider(),
            self::CONTEXT_LANG_ID
        );
    }

    private function mockCategoryDataProvider(): CategoryDataProvider
    {
        $defaultCategoryMock = $this->createMock(Category::class);
        $defaultCategoryMock->id = self::HOME_CATEGORY_ID;
        $defaultCategoryMock->name = [
            self::CONTEXT_LANG_ID => self::HOME_CATEGORY_NAME,
        ];

        $categoryDataProvider = $this->createMock(CategoryDataProvider::class);
        $categoryDataProvider->method('getCategory')->willReturn($defaultCategoryMock);

        return $categoryDataProvider;
    }
}
