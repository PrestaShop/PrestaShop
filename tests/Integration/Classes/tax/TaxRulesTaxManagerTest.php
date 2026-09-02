<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\tax;

use Address;
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

    public function setUp(): void
    {
        parent::setUp();

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
}
