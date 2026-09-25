<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter;

use Configuration as LegacyConfiguration;
use ConfigurationKPI;
use Db;
use PrestaShop\PrestaShop\Adapter\Configuration\KpiConfiguration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The KPI values live in their own table, and the legacy side writes them there through
 * ConfigurationKPI. This adapter is the only way the migrated code reaches them, so it has to land on
 * the same table or the two sides stop seeing each other's values.
 */
class KpiConfigurationTest extends KernelTestCase
{
    private const KEY = 'TEST_KPI_VALUE';

    /** @var KpiConfiguration */
    private $kpiConfiguration;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->kpiConfiguration = self::getContainer()->get('prestashop.adapter.legacy.kpi_configuration');
        $this->deleteTestedKey();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['configuration', 'configuration_kpi']);

        parent::tearDown();
    }

    public function testSetWritesToTheKpiTable(): void
    {
        $this->kpiConfiguration->set(self::KEY, '42%');

        $this->assertSame(1, $this->countRowsIn('configuration_kpi'));
        $this->assertSame(0, $this->countRowsIn('configuration'));
    }

    public function testGetReadsWhatTheLegacySideWrote(): void
    {
        ConfigurationKPI::updateValue(self::KEY, '42%');
        // The legacy write leaves the value in the shared static cache, where a reader pointed at the
        // wrong table would still find it, so drop it and make the read go back to the database.
        LegacyConfiguration::resetStaticCache();

        $this->assertSame('42%', $this->kpiConfiguration->get(self::KEY));
    }

    public function testTheRegularConfigurationTableIsUsedAgainAfterwards(): void
    {
        $this->kpiConfiguration->get(self::KEY);

        $configuration = self::getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set(self::KEY, 'plain');

        $this->assertSame(1, $this->countRowsIn('configuration'));
    }

    private function countRowsIn(string $table): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . $table . " WHERE `name` = '" . self::KEY . "'"
        );
    }

    private function deleteTestedKey(): void
    {
        foreach (['configuration', 'configuration_kpi'] as $table) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . $table . " WHERE `name` = '" . self::KEY . "'");
        }
    }
}
