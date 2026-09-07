<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\tax;

use Address;
use Cache;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\EntityMapper;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Foundation\IoC\Container;
use Tax;
use TaxCalculator;
use TaxRulesTaxManager;

class TaxRulesTaxManagerTest extends TestCase
{
    /**
     * @var array<array<string, int|float>>
     */
    private $tax_rows = [
        [
            'id_tax' => 1,
            'behavior' => TaxCalculator::COMBINE_METHOD,
            'rate' => 20.6,
        ],
        [
            'id_tax' => 2,
            'behavior' => TaxCalculator::ONE_AFTER_ANOTHER_METHOD,
            'rate' => 5.5,
        ],
    ];

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Container
     */
    private $savedContainer;

    /**
     * @var array<string> SQL of every query issued through the mocked Db
     */
    private $executedQueries = [];

    public function setUp(): void
    {
        parent::setUp();

        // Every address in this class resolves to the same TaxRulesTaxManager cache id, so without this
        // the first test to run answers for all the others and their queries are never issued.
        Cache::clean('*');
        $this->executedQueries = [];

        $this->configuration = $this->createMock(ConfigurationInterface::class);
        $this->configuration->method('get')->willReturn(1);

        $mockDatabase = $this->createMock(Db::class);
        $mockDatabase->method('executeS')->withAnyParameters()->willReturn($this->tax_rows);

        Db::setInstanceForTesting($mockDatabase);

        $this->savedContainer = ServiceLocator::getContainer();
        $container = new Container();
        ServiceLocator::setServiceContainerInstance($container);

        $entity_mapper = $this->createMock(EntityMapper::class);
        $tax_rows = [];
        foreach ($this->tax_rows as $tax_row) {
            $tax_rows[$tax_row['id_tax']] = new Tax();
            $tax_rows[$tax_row['id_tax']]->id = $tax_row['id_tax'];
            $tax_rows[$tax_row['id_tax']]->rate = $tax_row['rate'];
        }
        $entity_mapper->method('load')->willReturnCallback(function ($id, $id_lang, $entity, $entity_defs, $id_shop, $should_cache_objects) use ($tax_rows) {
            $entity->id = $tax_rows[$id]->id;
            $entity->rate = $tax_rows[$id]->rate;
        });

        $container->bind(
            '\\PrestaShop\\PrestaShop\\Adapter\\EntityMapper',
            $entity_mapper
        );
        $container->bind(
            '\\PrestaShop\\PrestaShop\\Core\\ConfigurationInterface',
            $this->configuration
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        ServiceLocator::setServiceContainerInstance($this->savedContainer);
        Db::deleteTestingInstance();
    }

    public function testGetTaxCalculatorShouldUseFirstComputationMethodFromTaxes()
    {
        $tax_rules_tax_manager = new TaxRulesTaxManager(new Address(), null, $this->configuration);
        $tax_calculator = $tax_rules_tax_manager->getTaxCalculator();

        $this->assertEquals(TaxCalculator::COMBINE_METHOD, $tax_calculator->computation_method);
        $this->assertIsArray($tax_calculator->taxes);

        foreach ($tax_calculator->taxes as $key => $tax) {
            $this->assertTrue($tax instanceof Tax);
            $this->assertEquals($this->tax_rows[$key]['id_tax'], $tax->id);
            $this->assertEquals($this->tax_rows[$key]['rate'], $tax->rate);
        }
    }

    /**
     * A shop whose rules are all restricted to a zipcode range showed untaxed prices to visitors who
     * were not logged in, because the postcode predicate is a string comparison and an address with no
     * postcode compares as '0', which does not sort inside any range.
     */
    public function testAnAddressWithoutPostcodeFallsBackToTheCountryRule(): void
    {
        $this->mockDatabaseWithNoZipcodeRestrictedMatch();

        $tax_calculator = (new TaxRulesTaxManager(new Address(), null, $this->configuration))->getTaxCalculator();

        $this->assertCount(2, $this->executedQueries, 'the fallback query must have been issued');
        $this->assertStringContainsString('BETWEEN', $this->executedQueries[0]);
        $this->assertStringNotContainsString('BETWEEN', $this->executedQueries[1]);
        $this->assertStringContainsString('LIMIT 1', $this->executedQueries[1]);

        $this->assertCount(1, $tax_calculator->taxes);
        $this->assertEquals(20.6, $tax_calculator->taxes[0]->rate);
    }

    /**
     * The fallback is for the address we know nothing about. A real postcode that simply matches no
     * rule must keep returning no tax, otherwise a customer would be charged a rate for a zone they
     * are not in.
     */
    public function testAnAddressWithAPostcodeDoesNotFallBack(): void
    {
        $this->mockDatabaseWithNoZipcodeRestrictedMatch();

        $address = new Address();
        $address->postcode = '75001';

        $tax_calculator = (new TaxRulesTaxManager($address, null, $this->configuration))->getTaxCalculator();

        $this->assertCount(1, $this->executedQueries, 'no fallback query may be issued for a known postcode');
        $this->assertEmpty($tax_calculator->taxes);
    }

    public function testTheFallbackIsSkippedWhenARuleAlreadyMatched(): void
    {
        $rows = $this->tax_rows;
        $mockDatabase = $this->createMock(Db::class);
        $mockDatabase->method('executeS')->willReturnCallback(function ($sql) use ($rows) {
            $this->executedQueries[] = (string) $sql;

            return $rows;
        });
        Db::setInstanceForTesting($mockDatabase);

        $tax_calculator = (new TaxRulesTaxManager(new Address(), null, $this->configuration))->getTaxCalculator();

        $this->assertCount(1, $this->executedQueries, 'the fallback must not run when the normal query matched');
        $this->assertCount(2, $tax_calculator->taxes);
    }

    /**
     * Mocks the database so the zipcode restricted query matches nothing, which is the situation of a
     * shop whose rules all carry a zipcode range, and records the SQL actually issued.
     */
    private function mockDatabaseWithNoZipcodeRestrictedMatch(): void
    {
        $firstRow = [$this->tax_rows[0]];
        $mockDatabase = $this->createMock(Db::class);
        $mockDatabase->method('executeS')->willReturnCallback(function ($sql) use ($firstRow) {
            $this->executedQueries[] = (string) $sql;

            return str_contains((string) $sql, 'BETWEEN') ? [] : $firstRow;
        });
        Db::setInstanceForTesting($mockDatabase);
    }
}
