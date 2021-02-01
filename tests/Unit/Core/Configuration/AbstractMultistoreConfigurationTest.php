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

declare(strict_types=1);

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration as ShopConfiguration;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;

class AbstractMultistoreConfigurationTest extends TestCase
{
    protected function setUp()
    {
        $this->mockedShopConfiguration = $this->createShopConfigurationMock();
        parent::setUp();
    }

    /**
     * @dataProvider provideForGetShopConstraint
     *
     * @param bool $isAllShopContext
     * @param int $shopGroupId
     * @param bool $isExpectedResultNull
     */
    public function testGetShopConstraint(bool $isAllShopContext, int $shopGroupId, int $shopId, bool $isExpectedResultNull): void
    {
        $abstractMultistoreConfiguration = $this->getTestableClass($isAllShopContext, $shopGroupId, $shopId);
        $resultShopConstraint = $abstractMultistoreConfiguration->getShopConstraint();

        if ($isExpectedResultNull) {
            $this->assertEquals(null, $resultShopConstraint);

            return;
        }

        // check that result is of the right type
        $this->assertInstanceOf('PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint', $resultShopConstraint);
        $this->assertEquals($shopGroupId, $resultShopConstraint->getShopGroupId()->getValue());
        $this->assertEquals($shopId, $resultShopConstraint->getShopId()->getValue());
    }

    public function provideForGetShopConstraint(): array
    {
        return [
            [true, 1, 1, true],
            [false, 1, 2, false],
            [false, 5, 7, false],
        ];
    }

    /**
     * @param bool $isAllShopContext
     *
     * @return AbstractMultistoreConfiguration
     */
    private function getTestableClass(bool $isAllShopContext, int $shopGroupId, int $shopId): AbstractMultistoreConfiguration
    {
        return new class($this->mockedShopConfiguration, $this->createMultistoreContextMock($isAllShopContext, $shopGroupId, $shopId)) extends AbstractMultistoreConfiguration {
            public function getConfiguration()
            {
                return [];
            }

            public function validateConfiguration(array $configuration)
            {
                return true;
            }

            public function updateConfiguration(array $configuration)
            {
                return [];
            }
        };
    }

    /**
     * @param bool $isAllShopContext
     *
     * @return MockObject
     */
    private function createMultistoreContextMock(bool $isAllShopContext, int $shopGroupId, int $shopId): MockObject
    {
        $stub = $this->createMock(ShopContext::class);
        $stub->method('isAllShopContext')->willReturn($isAllShopContext);
        $stub->method('getContextShopGroup')->willReturn($this->getShopGroupMock($shopGroupId));
        $stub->method('getContextShopID')->willReturn($shopId);

        return $stub;
    }

    /**
     * @return MockObject
     */
    private function createShopConfigurationMock(): MockObject
    {
        $stub = $this->createMock(ShopConfiguration::class);
        $stub->method('get')->willReturn(true);

        return $stub;
    }

    /**
     * @param int $shopGroupId
     *
     * @return MockObject
     */
    private function getShopGroupMock(int $shopGroupId): MockObject
    {
        $stub = $this->createMock(stdClass::class);
        $stub->id = $shopGroupId;

        return $stub;
    }
}
