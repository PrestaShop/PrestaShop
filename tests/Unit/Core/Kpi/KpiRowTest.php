<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Kpi;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRow;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;

class KpiRowTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $row = new KpiRow();

        $this->assertInstanceOf(KpiRowInterface::class, $row);
    }

    public function testAddKpi()
    {
        $row = new KpiRow();

        $this->assertEmpty($row->getKpis());

        $kpiMock = $this->createMock(KpiInterface::class);
        $row->addKpi($kpiMock);

        $this->assertEquals(current($row->getKpis()), $kpiMock);
    }

    public function testModifyAllowRefresh()
    {
        $row = new KpiRow();

        $this->assertTrue($row->isRefreshAllowed());

        $row->setAllowRefresh(false);

        $this->assertFalse($row->isRefreshAllowed());
    }
}
