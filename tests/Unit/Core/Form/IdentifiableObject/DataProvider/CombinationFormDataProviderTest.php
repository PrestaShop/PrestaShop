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
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationSuppliers;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationStock;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Query\GetProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierInfo;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\QueryResult\ProductSupplierOptions;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CombinationFormDataProvider;
use RuntimeException;

class CombinationFormDataProviderTest extends TestCase
{
    private const DEFAULT_NAME = 'Combination products';
    private const COMBINATION_ID = 42;
    private const PRODUCT_ID = 69;
    private const DEFAULT_QUANTITY = 51;

    public function testGetDefaultData(): void
    {
        $queryBusMock = $this->createMock(CommandBusInterface::class);
        $provider = new CombinationFormDataProvider($queryBusMock);

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
        $provider = new CombinationFormDataProvider($queryBusMock);

        $formData = $provider->getData(self::COMBINATION_ID);
        // assertSame is very important here We can't assume null and 0 are the same thing
        $this->assertSame($expectedData, $formData);
    }

    public function getExpectedData(): Generator
    {
        $datasetsByType = [
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

        $expectedOutputData = $this->getDefaultOutputData();
        $combinationData = [
            'quantity' => 42,
            'minimal_quantity' => 7,
            'low_stock_threshold' => 5,
            'low_stock_alert' => true,
            'location' => 'top shelf',
            'available_date' => new DateTime('1969/07/20'),
        ];
        $expectedOutputData['stock']['quantities']['quantity'] = 42;
        $expectedOutputData['stock']['quantities']['minimal_quantity'] = 7;
        $expectedOutputData['stock']['options']['low_stock_threshold'] = 5;
        $expectedOutputData['stock']['options']['low_stock_alert'] = true;
        $expectedOutputData['stock']['options']['stock_location'] = 'top shelf';
        $expectedOutputData['stock']['available_date'] = '1969-07-20';

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
            'wholesale_price' => new DecimalNumber('69.00'),
            'price_impact_tax_excluded' => new DecimalNumber('47.00'),
            'price_impact_tax_included' => new DecimalNumber('56.40'),
            'eco_tax' => new DecimalNumber('11.00'),
            'unit_price_impact' => new DecimalNumber('0.50'),
            'combination_weight' => new DecimalNumber('1.45'),
        ];
        $expectedOutputData['price_impact']['wholesale_price'] = 69.00;
        $expectedOutputData['price_impact']['price_tax_excluded'] = 47.00;
        $expectedOutputData['price_impact']['price_tax_included'] = 56.40;
        $expectedOutputData['price_impact']['ecotax'] = 11.00;
        $expectedOutputData['price_impact']['unit_price'] = 0.50;
        $expectedOutputData['price_impact']['weight'] = 1.45;

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
        $expectedOutputData['suppliers']['default_supplier_id'] = 1;
        $expectedOutputData['suppliers']['supplier_ids'] = [
            0 => 1,
            1 => 2,
        ];
        $expectedOutputData['suppliers']['product_suppliers'][1] = [
            'supplier_id' => 1,
            'supplier_name' => 'test supplier 1',
            'product_supplier_id' => 1,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 1',
            'currency_id' => 1,
            'combination_id' => self::COMBINATION_ID,
        ];
        $expectedOutputData['suppliers']['product_suppliers'][2] = [
            'supplier_id' => 2,
            'supplier_name' => 'test supplier 2',
            'product_supplier_id' => 2,
            'price_tax_excluded' => '0',
            'reference' => 'test supp ref 2',
            'currency_id' => 3,
            'combination_id' => self::COMBINATION_ID,
        ];

        $combinationData = [
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

        $combinationData = [
            'image_ids' => [42, 51],
        ];

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
                $this->isInstanceOf(GetProductSupplierOptions::class),
                $this->isInstanceOf(GetCombinationSuppliers::class)
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
     * @return CombinationForEditing|ProductSupplierOptions|ProductSupplierInfo[]
     */
    private function createResultBasedOnQuery($query, array $combinationData)
    {
        $queryClass = get_class($query);
        switch ($queryClass) {
            case GetCombinationForEditing::class:
                return $this->createCombinationForEditing($combinationData);
            case GetProductSupplierOptions::class:
                return $this->createProductSupplierOptions($combinationData);
            case GetCombinationSuppliers::class:
                return $this->createCombinationSupplierInfos($combinationData);
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
            $combination['image_ids'] ?? []
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
            $combination['eco_tax'] ?? new DecimalNumber('42.00'),
            $combination['price_impact_tax_excluded'] ?? new DecimalNumber('51.00'),
            $combination['price_impact_tax_included'] ?? new DecimalNumber('61.20'),
            $combination['unit_price_impact'] ?? new DecimalNumber('69.00'),
            $combination['wholesale_price'] ?? new DecimalNumber('99.00')
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
            $combination['low_stock_alert'] ?? false,
            $combination['location'] ?? 'location',
            $combination['available_date'] ?? null
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
     * @return ProductSupplierOptions
     */
    private function createProductSupplierOptions(array $combinationData): ProductSupplierOptions
    {
        return new ProductSupplierOptions(
            $combinationData['suppliers']['default_supplier_id'] ?? 0,
            []
        );
    }

    /**
     * @param array $combinationData
     *
     * @return ProductSupplierInfo[]
     */
    private function createCombinationSupplierInfos(array $combinationData): array
    {
        if (empty($combinationData['suppliers']['product_suppliers'])) {
            return [];
        }

        $suppliersInfo = [];
        foreach ($combinationData['suppliers']['product_suppliers'] as $supplierInfo) {
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

        return $suppliersInfo;
    }

    /**
     * @return array
     */
    private function getDefaultOutputData(): array
    {
        return [
            'id' => self::COMBINATION_ID,
            'product_id' => self::PRODUCT_ID,
            'name' => self::DEFAULT_NAME,
            'stock' => [
                'quantities' => [
                    'quantity' => self::DEFAULT_QUANTITY,
                    'minimal_quantity' => 0,
                ],
                'options' => [
                    'stock_location' => 'location',
                    'low_stock_threshold' => null,
                    'low_stock_alert' => false,
                ],
                'available_date' => '',
            ],
            'price_impact' => [
                'wholesale_price' => 99.00,
                'price_tax_excluded' => 51.00,
                'price_tax_included' => 61.20,
                'ecotax' => 42.00,
                'unit_price' => 69.00,
                'weight' => 42.00,
            ],
            'references' => [
                'reference' => 'reference',
                'isbn' => 'isbn',
                'ean_13' => 'ean13',
                'upc' => 'upc',
                'mpn' => 'mpn',
            ],
            'suppliers' => [],
            'images' => [],
        ];
    }
}
