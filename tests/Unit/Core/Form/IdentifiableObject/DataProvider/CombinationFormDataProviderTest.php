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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationStock;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CombinationFormDataProvider;
use RuntimeException;

class CombinationFormDataProviderTest extends TestCase
{
    private const DEFAULT_NAME = 'Combination products';
    private const COMBINATION_ID = 42;
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

        $formData = $provider->getData(static::COMBINATION_ID);
        // assertSame is very important here We can't assume null and 0 are the same thing
        $this->assertSame($expectedData, $formData);
    }

    public function getExpectedData(): Generator
    {
        $datasetsByType = [
            $this->getDatasetsForStock(),
            $this->getDatasetsForPriceImpact(),
            $this->getDatasetsForDetails(),
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
        $expectedOutputData['stock']['quantity'] = 42;
        $expectedOutputData['stock']['minimal_quantity'] = 7;
        $expectedOutputData['stock']['low_stock_threshold'] = 5;
        $expectedOutputData['stock']['low_stock_alert'] = true;
        $expectedOutputData['stock']['stock_location'] = 'top shelf';
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
        $expectedOutputData['details']['reference'] = 'reference_bis';
        $expectedOutputData['details']['isbn'] = 'isbn_bis';
        $expectedOutputData['details']['ean_13'] = 'ean13_bis';
        $expectedOutputData['details']['upc'] = 'upc_bis';
        $expectedOutputData['details']['mpn'] = 'mpn_bis';

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
                $this->isInstanceOf(GetCombinationForEditing::class)
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
     * @return CombinationForEditing
     */
    private function createResultBasedOnQuery($query, array $combinationData)
    {
        $queryResultMap = [
            GetCombinationForEditing::class => $this->createCombinationForEditing($combinationData),
        ];

        $queryClass = get_class($query);
        if (array_key_exists($queryClass, $queryResultMap)) {
            return $queryResultMap[$queryClass];
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
            $combination['name'] ?? static::DEFAULT_NAME,
            $this->createDetails($combination),
            $this->createPrices($combination),
            $this->createStock($combination)
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
            $combination['quantity'] ?? static::DEFAULT_QUANTITY,
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
     * @return array
     */
    private function getDefaultOutputData(): array
    {
        return [
            'id' => static::COMBINATION_ID,
            'name' => static::DEFAULT_NAME,
            'stock' => [
                'quantity' => static::DEFAULT_QUANTITY,
                'minimal_quantity' => 0,
                'stock_location' => 'location',
                'low_stock_threshold' => null,
                'low_stock_alert' => false,
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
            'details' => [
                'reference' => 'reference',
                'isbn' => 'isbn',
                'ean_13' => 'ean13',
                'upc' => 'upc',
                'mpn' => 'mpn',
            ],
        ];
    }
}
