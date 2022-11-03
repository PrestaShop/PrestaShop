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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
