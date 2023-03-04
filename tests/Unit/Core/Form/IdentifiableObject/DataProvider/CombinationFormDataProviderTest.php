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
use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationStock;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetAssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\AssociatedSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\NoSupplierId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CombinationFormDataProvider;
use PrestaShopBundle\Form\Extension\DisablingSwitchExtension;
use RuntimeException;

class CombinationFormDataProviderTest extends TestCase
{
    private const DEFAULT_NAME = 'Combination products';
    private const IS_DEFAULT = false;
    private const COMBINATION_ID = 42;
    private const PRODUCT_ID = 69;
    private const DEFAULT_QUANTITY = 51;
    private const COVER_URL = 'http://localhost/cover.jpg';
    private const SHOP_ID = 1;

    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = $this->createFormDataProvider($queryBusMock);

        $this->assertEquals([], $provider->getDefaultData());
    }

    /**
     * @dataProvider getExpectedData
     *
     * @param array $combinationData
     * @param array $expectedData
     */
    public function testGetData(array $combinationData, array $expectedData): void
    {
        $queryBusMock = $this->createQueryBusMock($combinationData);
        $formDataProvider = $this->createFormDataProvider(
            $queryBusMock
        );

        $formData = $formDataProvider->getData(self::COMBINATION_ID);
        // assertSame is very important here We can't assume null and 0 are the same thing
        $this->assertSame($expectedData, $formData);
    }

    public function getExpectedData(): Generator
    {
        $datasetsByType = [
            $this->getDatasetsForIsDefault(),
            $this->getDatasetsForStock(),
            $this->getDatasetsForPriceImpact(),
            $this->getDatasetsForDetails(),
            $this->getDatasetsForProductSuppliers(),
            $this->getDatasetsForImages(),
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
    private function getDatasetsForStock(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [
            'quantity' => 42,
            'minimal_quantity' => 7,
            'low_stock_threshold' => 5,
            sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => true,
            'location' => 'top shelf',
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
            'available_now' => $localizedValues,
            'available_later' => $localizedValues,
        ];
        $expectedOutputData['stock']['quantities']['delta_quantity']['quantity'] = 42;
        $expectedOutputData['stock']['quantities']['minimal_quantity'] = 7;
        $expectedOutputData['stock']['options']['low_stock_threshold'] = 5;
        $expectedOutputData['stock']['options'][sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX)] = true;
        $expectedOutputData['stock']['options']['stock_location'] = 'top shelf';
        $expectedOutputData['stock']['available_date'] = '1969-07-20';
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
        $expectedOutputData['stock']['available_now_label'] = $localizedValues;
        $expectedOutputData['stock']['available_later_label'] = $localizedValues;

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForPriceImpact(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [
            'price_impact_tax_excluded' => new DecimalNumber('47.00'),
            'price_impact_tax_included' => new DecimalNumber('56.40'),
            'unit_price_tax_excluded' => new DecimalNumber('0.50'),
            'unit_price_tax_included' => new DecimalNumber('0.70'),
            'ecotax_tax_excluded' => new DecimalNumber('11.00'),
            'ecotax_tax_included' => new DecimalNumber('12.00'),
            'wholesale_price' => new DecimalNumber('69.00'),
            'combination_weight' => new DecimalNumber('1.45'),
            'product_tax_rate' => new DecimalNumber('0.05'),
            'product_price' => new DecimalNumber('69.00'),
            'product_ecotax' => new DecimalNumber('5.00'),
        ];

        $expectedOutputData['price_impact']['price_tax_excluded'] = 47.00;
        $expectedOutputData['price_impact']['price_tax_included'] = 56.40;
        $expectedOutputData['price_impact']['unit_price_tax_excluded'] = 0.50;
        $expectedOutputData['price_impact']['unit_price_tax_included'] = 0.70;
        $expectedOutputData['price_impact']['ecotax_tax_excluded'] = 11.00;
        $expectedOutputData['price_impact']['ecotax_tax_included'] = 12.00;
        $expectedOutputData['price_impact']['wholesale_price'] = 69.00;
        $expectedOutputData['price_impact']['weight'] = 1.45;
        $expectedOutputData['price_impact']['product_tax_rate'] = 0.05;
        $expectedOutputData['price_impact']['product_price_tax_excluded'] = 69.00;
        $expectedOutputData['price_impact']['product_ecotax_tax_excluded'] = 5.00;

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForDetails(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [
            'reference' => 'reference_bis',
            'isbn' => 'isbn_bis',
            'ean13' => 'ean13_bis',
            'upc' => 'upc_bis',
            'mpn' => 'mpn_bis',
        ];
        $expectedOutputData['references']['reference'] = 'reference_bis';
        $expectedOutputData['references']['isbn'] = 'isbn_bis';
        $expectedOutputData['references']['ean_13'] = 'ean13_bis';
        $expectedOutputData['references']['upc'] = 'upc_bis';
        $expectedOutputData['references']['mpn'] = 'mpn_bis';

        $datasets[] = [
            $combinationData,
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
        $expectedOutputData['default_supplier_id'] = 1;
        $expectedOutputData['product_suppliers'][1] = [
            'supplier_id' => 1,
            'supplier_name' => 'test supplier 1',
            'product_supplier_id' => 1,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 1',
            'currency_id' => 1,
            'combination_id' => self::COMBINATION_ID,
        ];
        $expectedOutputData['product_suppliers'][2] = [
            'supplier_id' => 2,
            'supplier_name' => 'test supplier 2',
            'product_supplier_id' => 2,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 2',
            'currency_id' => 3,
            'combination_id' => self::COMBINATION_ID,
        ];

        $combinationData = [
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
                    'combination_id' => self::COMBINATION_ID,
                ],
                [
                    'product_id' => self::PRODUCT_ID,
                    'supplier_id' => 2,
                    'supplier_name' => 'test supplier 2',
                    'product_supplier_id' => 2,
                    'price' => '0',
                    'reference' => 'test supp ref 2',
                    'currency_id' => 3,
                    'combination_id' => self::COMBINATION_ID,
                ],
            ],
        ];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @return array
     */
    private function getDatasetsForImages(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $expectedOutputData['images'] = [42, 51];
        $expectedOutputData['cover_thumbnail_url'] = 'http://custom.combination.url';

        $combinationData = [
            'image_ids' => [42, 51],
            'cover_url' => 'http://custom.combination.url',
        ];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    private function getDatasetsForIsDefault(): array
    {
        $datasets = [];

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = ['is_default' => false];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        $expectedOutputData['header']['is_default'] = true;
        $combinationData = ['is_default' => true];

        $datasets[] = [
            $combinationData,
            $expectedOutputData,
        ];

        return $datasets;
    }

    /**
     * @param array $combinationData
     *
     * @return MockObject|CommandBusInterface
     */
    private function createQueryBusMock(array $combinationData)
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);

        $queryBusMock
            ->method('handle')
            ->with($this->logicalOr(
                $this->isInstanceOf(GetCombinationForEditing::class),
                $this->isInstanceOf(GetAssociatedSuppliers::class),
                $this->isInstanceOf(GetCombinationSuppliers::class),
                $this->isInstanceOf(GetCombinationStockMovements::class)
            ))
            ->willReturnCallback(function ($query) use ($combinationData) {
                return $this->createResultBasedOnQuery($query, $combinationData);
            })
        ;

        return $queryBusMock;
    }

    /**
     * @param GetCombinationForEditing $query
     * @param array $combinationData
     *
     * @return CombinationForEditing|AssociatedSuppliers|ProductSupplierForEditing[]|StockMovement[]
     */
    private function createResultBasedOnQuery($query, array $combinationData)
    {
        switch ($queryClass = get_class($query)) {
            case GetCombinationForEditing::class:
                return $this->createCombinationForEditing($combinationData);
            case GetAssociatedSuppliers::class:
                return $this->createAssociatedSuppliers($combinationData);
            case GetCombinationSuppliers::class:
                return $this->createCombinationSupplierInfos($combinationData);
            case GetCombinationStockMovements::class:
                return $this->createStockMovementHistories($combinationData);
        }

        throw new RuntimeException(sprintf('Query "%s" was not expected in query bus mock', $queryClass));
    }

    /**
     * @param array $combination
     *
     * @return CombinationForEditing
     */
    private function createCombinationForEditing(array $combination): CombinationForEditing
    {
        return new CombinationForEditing(
            self::COMBINATION_ID,
            self::PRODUCT_ID,
            $combination['name'] ?? self::DEFAULT_NAME,
            $this->createDetails($combination),
            $this->createPrices($combination),
            $this->createStock($combination),
            $combination['image_ids'] ?? [],
            $combination['cover_url'] ?? self::COVER_URL,
            $combination['is_default'] ?? self::IS_DEFAULT
        );
    }

    /**
     * @param array $combination
     *
     * @return CombinationPrices
     */
    private function createPrices(array $combination): CombinationPrices
    {
        return new CombinationPrices(
            $combination['price_impact_tax_excluded'] ?? new DecimalNumber('51.00'),
            $combination['price_impact_tax_included'] ?? new DecimalNumber('61.20'),
            $combination['unit_price_tax_excluded'] ?? new DecimalNumber('69.00'),
            $combination['unit_price_tax_included'] ?? new DecimalNumber('72.00'),
            $combination['ecotax_tax_excluded'] ?? new DecimalNumber('42.00'),
            $combination['ecotax_tax_included'] ?? new DecimalNumber('51.00'),
            $combination['wholesale_price'] ?? new DecimalNumber('99.00'),
            $combination['product_tax_rate'] ?? new DecimalNumber('0.20'),
            $combination['product_price'] ?? new DecimalNumber('42.00'),
            $combination['product_ecotax'] ?? new DecimalNumber('4.00')
        );
    }

    /**
     * @param array $combination
     *
     * @return CombinationStock
     */
    private function createStock(array $combination): CombinationStock
    {
        return new CombinationStock(
            $combination['quantity'] ?? self::DEFAULT_QUANTITY,
            $combination['minimal_quantity'] ?? 0,
            $combination['low_stock_threshold'] ?? 0,
            $combination[sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX)] ?? false,
            $combination['location'] ?? 'location',
            $combination['available_date'] ?? null,
            $combination['available_now'] ?? [],
            $combination['available_later'] ?? []
        );
    }

    /**
     * @param array $combination
     *
     * @return CombinationDetails
     */
    private function createDetails(array $combination): CombinationDetails
    {
        return new CombinationDetails(
            $combination['ean13'] ?? 'ean13',
            $combination['isbn'] ?? 'isbn',
            $combination['mpn'] ?? 'mpn',
            $combination['reference'] ?? 'reference',
            $combination['upc'] ?? 'upc',
            $combination['combination_weight'] ?? new DecimalNumber('42.00')
        );
    }

    /**
     * @param array $combinationData
     *
     * @return AssociatedSuppliers
     */
    private function createAssociatedSuppliers(array $combinationData): AssociatedSuppliers
    {
        return new AssociatedSuppliers(
            $combinationData['default_supplier_id'] ?? 0,
            []
        );
    }

    /**
     * @param array $combinationData
     *
     * @return ProductSupplierForEditing[]
     */
    private function createCombinationSupplierInfos(array $combinationData): array
    {
        if (empty($combinationData['product_suppliers'])) {
            return [];
        }

        $suppliersInfo = [];
        foreach ($combinationData['product_suppliers'] as $supplierInfo) {
            $suppliersInfo[] = new ProductSupplierForEditing(
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

        return $suppliersInfo;
    }

    /**
     * @param array $combinationData
     *
     * @return StockMovement[]
     */
    private function createStockMovementHistories(array $combinationData): array
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
            $combinationData['stock_movements'] ?? []
        );
    }

    /**
     * @return array
     */
    private function getDefaultOutputData(): array
    {
        return [
            'id' => self::COMBINATION_ID,
            'product_id' => self::PRODUCT_ID,
            'cover_thumbnail_url' => self::COVER_URL,
            'header' => [
                'name' => self::DEFAULT_NAME,
                'is_default' => self::IS_DEFAULT,
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
                    sprintf('%slow_stock_threshold', DisablingSwitchExtension::FIELD_PREFIX) => false,
                ],
                'available_date' => '',
                'available_now_label' => [],
                'available_later_label' => [],
            ],
            'price_impact' => [
                'price_tax_excluded' => 51.00,
                'price_tax_included' => 61.20,
                'unit_price_tax_excluded' => 69.00,
                'unit_price_tax_included' => 72.00,
                'ecotax_tax_excluded' => 42.00,
                'ecotax_tax_included' => 51.00,
                'wholesale_price' => 99.00,
                'weight' => 42.00,
                'product_tax_rate' => 0.20,
                'product_price_tax_excluded' => 42.00,
                'product_ecotax_tax_excluded' => 4.00,
            ],
            'references' => [
                'reference' => 'reference',
                'isbn' => 'isbn',
                'ean_13' => 'ean13',
                'upc' => 'upc',
                'mpn' => 'mpn',
            ],
            'default_supplier_id' => NoSupplierId::NO_SUPPLIER_ID,
            'product_suppliers' => [],
            'images' => [],
        ];
    }

    private function createFormDataProvider(
        CommandBusInterface $queryBusMock
    ): CombinationFormDataProvider {
        return new CombinationFormDataProvider(
            $queryBusMock,
            $this->mockShopContext()
        );
    }

    /**
     * @return Context
     */
    private function mockShopContext(): Context
    {
        $shopContext = $this->getMockBuilder(Context::class)
            ->onlyMethods(['getShopConstraint'])
            ->getMock()
        ;
        $shopContext
            ->method('getShopConstraint')
            ->willReturn(ShopConstraint::shop(self::SHOP_ID))
        ;

        return $shopContext;
    }
}
