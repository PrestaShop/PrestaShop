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

namespace Tests\Unit\Core\Kpi;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowFactory;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowFactoryInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;

class KpiRowFactoryTest extends TestCase
{
    public function testCanBeConstructedWithValidKpis()
    {
        $kpiMock1 = $this->createMock(KpiInterface::class);
        $kpiMock2 = $this->createMock(KpiInterface::class);
        $kpiMock3 = $this->createMock(KpiInterface::class);

        $factory = new KpiRowFactory($kpiMock1, $kpiMock2, $kpiMock3);

        $this->assertInstanceOf(KpiRowFactoryInterface::class, $factory);
    }

    public function testBuild()
    {
        $kpiMock1 = $this->createMock(KpiInterface::class);
        $kpiMock2 = $this->createMock(KpiInterface::class);
        $kpiMock3 = $this->createMock(KpiInterface::class);

        $factory = new KpiRowFactory($kpiMock1, $kpiMock2, $kpiMock3);

        $result = $factory->build();

        $this->assertInstanceOf(KpiRowInterface::class, $result);

        $kpis = $result->getKpis();

        $this->assertEquals($kpis[0], $kpiMock1);
        $this->assertEquals($kpis[1], $kpiMock2);
        $this->assertEquals($kpis[2], $kpiMock3);
    }
}
