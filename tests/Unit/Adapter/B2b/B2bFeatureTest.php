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
