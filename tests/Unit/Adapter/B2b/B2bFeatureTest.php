<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\B2b;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\B2b\B2bFeature;

class B2bFeatureTest extends TestCase
{
    private const CONFIGURATION_FEATURE = 'PS_B2B_ENABLE';

    public function testIsUsedOrIsActiveWhenConfigurationIsFalse(): void
    {
        $configurationMock = $this->createConfigurationMock();
        $configurationMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with(self::CONFIGURATION_FEATURE)
            ->willReturn(0)
        ;

        /** @var Configuration $configurationMock */
        $feature = new B2bFeature($configurationMock);

        $this->assertFalse($feature->isUsed());
        $this->assertFalse($feature->isActive());
    }

    public function testIsUsedOrIsActiveWhenConfigurationIsTrue(): void
    {
        $configurationMock = $this->createConfigurationMock();
        $configurationMock
            ->expects($this->exactly(2))
            ->method('get')
            ->with(self::CONFIGURATION_FEATURE)
            ->willReturn(1)
        ;

        /** @var Configuration $configurationMock */
        $feature = new B2bFeature($configurationMock);

        $this->assertTrue($feature->isUsed());
        $this->assertTrue($feature->isActive());
    }

    public function testEnable(): void
    {
        $configurationMock = $this->createConfigurationMock();
        $configurationMock
            ->expects($this->once())
            ->method('set')
            ->with(self::CONFIGURATION_FEATURE, 1)
        ;

        /** @var Configuration $configurationMock */
        $feature = new B2bFeature($configurationMock);

        $feature->enable();
    }

    public function testDisable(): void
    {
        $configurationMock = $this->createConfigurationMock();
        $configurationMock
            ->expects($this->once())
            ->method('set')
            ->with(self::CONFIGURATION_FEATURE, 0)
        ;

        /** @var Configuration $configurationMock */
        $feature = new B2bFeature($configurationMock);

        $feature->disable();
    }

    public function testUpdateToTrue(): void
    {
        $configurationMock = $this->createConfigurationMock();
        $configurationMock
            ->expects($this->once())
            ->method('set')
            ->with(self::CONFIGURATION_FEATURE, 1)
        ;

        /** @var Configuration $configurationMock */
        $feature = new B2bFeature($configurationMock);

        $feature->update(true);
    }

    public function testUpdateToFalse(): void
    {
        $configurationMock = $this->createConfigurationMock();
        $configurationMock
            ->expects($this->once())
            ->method('set')
            ->with(self::CONFIGURATION_FEATURE, 0)
        ;

        /** @var Configuration $configurationMock */
        $feature = new B2bFeature($configurationMock);

        $feature->update(false);
    }

    private function createConfigurationMock(): MockObject
    {
        return $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
