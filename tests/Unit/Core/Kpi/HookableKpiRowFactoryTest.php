<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Kpi;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\HookableKpiRowFactory;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowFactoryInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;

/**
 * @doc ./vendor/bin/phpunit
 */
class HookableKpiRowFactoryTest extends TestCase
{
    public function testCanBeConstructedWithValidKpis()
    {
        $kpiMock1 = $this->createMock(KpiInterface::class);
        $kpiMock2 = $this->createMock(KpiInterface::class);
        $kpiMock3 = $this->createMock(KpiInterface::class);

        $factory = new HookableKpiRowFactory(
            [
                $kpiMock1,
                $kpiMock2,
                $kpiMock3,
            ],
            $this->createMock(HookDispatcherInterface::class),
            'fooBar'
        );

        $this->assertInstanceOf(KpiRowFactoryInterface::class, $factory);
    }

    public function testBuild()
    {
        $kpiMock1 = $this->createMock(KpiInterface::class);
        $kpiMock2 = $this->createMock(KpiInterface::class);
        $kpiMock3 = $this->createMock(KpiInterface::class);

        $factory = new HookableKpiRowFactory(
            [
                $kpiMock1,
                $kpiMock2,
                $kpiMock3,
            ],
            $this->createMock(HookDispatcherInterface::class),
            'fooBar'
        );

        /** @var KpiRowInterface $result */
        $result = $factory->build();

        $this->assertInstanceOf(KpiRowInterface::class, $result);

        $kpis = $result->getKpis();

        $this->assertEquals($kpis[0], $kpiMock1);
        $this->assertEquals($kpis[1], $kpiMock2);
        $this->assertEquals($kpis[2], $kpiMock3);
    }
}
