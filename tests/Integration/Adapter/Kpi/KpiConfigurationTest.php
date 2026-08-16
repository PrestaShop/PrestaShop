<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Kpi;

use Db;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Configuration\KpiConfiguration;
use PrestaShop\PrestaShop\Adapter\Kpi\AbandonedCartKpi;
use PrestaShop\PrestaShop\Adapter\Kpi\AverageOrderValueKpi;
use PrestaShop\PrestaShop\Adapter\Kpi\ConversionRateKpi;
use PrestaShop\PrestaShop\Adapter\Kpi\NetProfitPerVisitKpi;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The KPI widgets read their cache through KpiConfiguration, which is the only configuration adapter
 * that looks at configuration_kpi. If a KPI adapter is handed the ordinary Configuration, or if that
 * adapter cannot reach the KPI table, every lookup answers null and the cache is never a hit.
 */
class KpiConfigurationTest extends KernelTestCase
{
    private const KEY = 'TEST_KPI_CONFIGURATION_KEY';
    private const VALUE = 'cached';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::deleteKey();
        Db::getInstance()->insert('configuration_kpi', [
            'name' => self::KEY,
            'value' => self::VALUE,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        self::deleteKey();
        parent::tearDownAfterClass();
    }

    public function testItReadsAKeyThatOnlyExistsInTheKpiTable(): void
    {
        self::bootKernel();

        $kpiConfiguration = self::getContainer()->get('prestashop.adapter.legacy.kpi_configuration');

        self::assertSame(
            self::VALUE,
            $kpiConfiguration->get(self::KEY),
            'KpiConfiguration must read configuration_kpi, otherwise every KPI cache lookup is a miss'
        );
    }

    /**
     * @dataProvider provideKpiAdapters
     */
    public function testTheKpiAdaptersAreGivenTheKpiConfiguration(string $kpiClass): void
    {
        self::bootKernel();

        $kpi = self::getContainer()->get($kpiClass);
        $configurations = [];
        foreach ((new ReflectionObject($kpi))->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($kpi);
            if ($value instanceof Configuration) {
                $configurations[$property->getName()] = $value;
            }
        }

        self::assertNotEmpty($configurations, $kpiClass . ' holds no configuration to check');
        foreach ($configurations as $name => $configuration) {
            self::assertInstanceOf(
                KpiConfiguration::class,
                $configuration,
                sprintf('%s::$%s must be the KPI configuration, or the cached values are unreachable', $kpiClass, $name)
            );
        }
    }

    public function provideKpiAdapters(): array
    {
        return [
            [ConversionRateKpi::class],
            [AbandonedCartKpi::class],
            [AverageOrderValueKpi::class],
            [NetProfitPerVisitKpi::class],
        ];
    }

    private static function deleteKey(): void
    {
        Db::getInstance()->delete('configuration_kpi', 'name = "' . pSQL(self::KEY) . '"');
    }
}
