<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Kpi;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowPresenter;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowPresenterInterface;

class KpiRowPresenterTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $presenter = new KpiRowPresenter();

        $this->assertInstanceOf(KpiRowPresenterInterface::class, $presenter);
    }

    public function testPresentsKpis()
    {
        $presenter = new KpiRowPresenter();

        $kpiRowMock = $this->createMock(KpiRowInterface::class);
        $kpiMock = $this->createMock(KpiInterface::class);

        $kpiRowMock
            ->method('getKpis')
            ->willReturn([$kpiMock]);
        $kpiRowMock
            ->method('getOptions')
            ->willReturn([]);
        $kpiMock
            ->method('render')
            ->willReturn('abcd');

        $result = $presenter->present($kpiRowMock);

        $this->assertArrayHasKey('allowRefresh', $result);
        $this->assertArrayHasKey('kpis', $result);

        $this->assertEquals('abcd', current($result['kpis']));
    }
}
