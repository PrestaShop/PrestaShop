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
use PrestaShop\PrestaShop\Core\Configuration\MultistoreConfigurator;

class MultistoreConfiguratorTest extends TestCase
{
    protected function setUp()
    {
        $this->mockedShopConfiguration = $this->createShopConfigurationMock();
        parent::setUp();
    }

    /**
     * @dataProvider provideForTestRemoveDisabledFields
     *
     * @param array $fields
     * @param bool $isAllShopContext
     * @param array $expectedResult
     */
    public function testRemoveDisabledFields(array $fields, bool $isAllShopContext, array $expectedResult): void
    {
        $multistoreConfigurator = $this->getTestableClass($isAllShopContext);
        $result = $multistoreConfigurator->removeDisabledFields($fields);

        $this->assertEquals($expectedResult, $result);
    }

    public function provideForTestRemoveDisabledFields()
    {
        return [
            [
                ['test_field' => 'test_value'],
                true,
                ['test_field' => 'test_value'],
            ],
            [
                ['test_field' => 'test_value'],
                false,
                ['test_field' => 'test_value'],
            ],
            [
                ['test_field' => 'test_value', 'multistore_test_field' => true],
                false,
                ['test_field' => 'test_value', 'multistore_test_field' => true],
            ],
            [
                ['test_field' => 'test_value', 'multistore_test_field' => false],
                false,
                ['multistore_test_field' => false],
            ],
            [
                ['test_field' => 'test_value', 'multistore_test_field' => false],
                true,
                ['multistore_test_field' => false, 'test_field' => 'test_value'],
            ],
        ];
    }

    /**
     * @param bool $isAllShopContext
     *
     * @return MultistoreConfigurator
     */
    private function getTestableClass(bool $isAllShopContext): MultistoreConfigurator
    {
        return new class($this->mockedShopConfiguration, $this->createMultistoreContextMock($isAllShopContext)) extends MultistoreConfigurator {
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
    private function createMultistoreContextMock(bool $isAllShopContext): MockObject
    {
        $stub = $this->createMock(ShopContext::class);
        $stub->method('isAllShopContext')->willReturn($isAllShopContext);

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
}
