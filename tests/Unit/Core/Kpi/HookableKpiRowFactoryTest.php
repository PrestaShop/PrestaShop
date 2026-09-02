<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Kpi;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Kpi\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\HookableKpiRowFactory;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowFactoryInterface;
use PrestaShop\PrestaShop\Core\Kpi\Row\KpiRowInterface;

/**
 * @doc ./vendor/bin/phpunit -c tests/Unit/phpunit.xml --filter="HookableKpiRowFactoryTest"
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

    public function testCantBeConstructedWithInvalidKpis()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Kpi must be an instance of KpiInterface, got `string`.');

        /** @var KpiInterface $kpiMock1 */
        $kpiMock1 = $this->createMock(KpiInterface::class);
        /** @var KpiInterface $kpiMock2 */
        $kpiMock2 = $this->createMock(KpiInterface::class);

        $factory = new HookableKpiRowFactory(
            /* @phpstan-ignore-next-line */
            [
                $kpiMock1,
                $kpiMock2,
                'kpiMock3',
            ],
            $this->createMock(HookDispatcherInterface::class),
            'fooBar'
        );

        $this->assertInstanceOf(KpiRowFactoryInterface::class, $factory);
    }

    public function testCantBeConstructedWithInvalidIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Identifier must be a string, got `boolean`.');

        /** @var KpiInterface $kpiMock1 */
        $kpiMock1 = $this->createMock(KpiInterface::class);
        /** @var KpiInterface $kpiMock2 */
        $kpiMock2 = $this->createMock(KpiInterface::class);

        $factory = new HookableKpiRowFactory(
            [
                $kpiMock1,
                $kpiMock2,
            ],
            $this->createMock(HookDispatcherInterface::class),
            /* @phpstan-ignore-next-line */
            false
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
