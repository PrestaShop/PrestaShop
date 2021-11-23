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
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;

class AbstractMultistoreConfigurationTest extends TestCase
{
    /**
     * @dataProvider provideForGetShopConstraint
     *
     * @param bool $isAllShopContext
     * @param int $shopGroupId
     * @param int $shopId
     * @param bool $isExpectedResultNull
     */
    public function testGetShopConstraint(bool $isAllShopContext, int $shopGroupId, int $shopId, bool $isExpectedResultNull): void
    {
        $abstractMultistoreConfiguration = $this->getTestableClass($isAllShopContext, null, true, $shopGroupId, $shopId);
        $resultShopConstraint = $abstractMultistoreConfiguration->getShopConstraint();

        if ($isExpectedResultNull) {
            $this->assertEquals(null, $resultShopConstraint);

            return;
        }

        // check that result is of the right type
        $this->assertInstanceOf('PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint', $resultShopConstraint);
        if (!empty($resultShopConstraint->getShopGroupId())) {
            $this->assertEquals($shopGroupId, $resultShopConstraint->getShopGroupId()->getValue());
        }
        if (!empty($resultShopConstraint->getShopId())) {
            $this->assertEquals($shopId, $resultShopConstraint->getShopId()->getValue());
        }
    }

    /**
     * @return array[]
     */
    public function provideForGetShopConstraint(): array
    {
        return [
            [true, 1, 1, true],
            [false, 1, 2, false],
            [false, 5, 7, false],
        ];
    }

    /**
     * @dataProvider provideForUpdateConfigurationValue
     *
     * @param string $fieldName
     * @param array $inputValues
     * @param bool $isMultistoreUsed
     * @param string|null $expectedMethodToBeCalled
     */
    public function testUpdateConfigurationValue(string $fieldName, array $inputValues, bool $isMultistoreUsed, ?string $expectedMethodToBeCalled): void
    {
        // this will test that inside the `UpdateConfigurationValue` method, the right update method will be called depending on situation
        $abstractMultistoreConfiguration = $this->getTestableClass(false, $expectedMethodToBeCalled, $isMultistoreUsed);
        $abstractMultistoreConfiguration->updateConfigurationValue('PS_CONF_KEY', $fieldName, $inputValues, $this->getShopConstraintMock());
    }

    /**
     * @return array[]
     */
    public function provideForUpdateConfigurationValue(): array
    {
        $multistorePrefix = MultistoreCheckboxEnabler::MULTISTORE_FIELD_PREFIX;

        return [
            ['toto', ['toto' => 'value'], false, 'set'], // standard value update
            ['toto', ['toto' => 'value', $multistorePrefix . 'toto' => true], true, 'set'], // multistore checkbox is there, method 'set' must be called
            ['toto', ['toto' => 'value'], true, 'deleteFromContext'], // multistore checkbox absent, method 'deleteFromContext' must be called
            ['toto', [], true, null], // do not make any update if the field is not in the input array
        ];
    }

    /**
     * @param bool $isAllShopContext
     * @param string|null $expectedCalledMethod
     * @param bool $isMultistoreUsed
     * @param int $shopGroupId
     * @param int $shopId
     *
     * @return AbstractMultistoreConfiguration
     */
    private function getTestableClass(
        bool $isAllShopContext,
        ?string $expectedCalledMethod,
        bool $isMultistoreUsed = true,
        int $shopGroupId = 1,
        int $shopId = 1
    ): AbstractMultistoreConfiguration {
        return new class($this->createShopConfigurationMock($expectedCalledMethod), $this->createMultistoreContextMock($isAllShopContext, $shopGroupId, $shopId), $this->getMultistoreFeatureMock($isMultistoreUsed)) extends AbstractMultistoreConfiguration {
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
     * @param int $shopGroupId
     * @param int $shopId
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
     * @param string|null $expectedMethodCalled
     *
     * @return MockObject
     */
    private function createShopConfigurationMock(?string $expectedMethodCalled): MockObject
    {
        $stub = $this->createMock(ShopConfiguration::class);
        $stub->method('get')->willReturn(true);

        // check expected method is called if needed
        if (isset($expectedMethodCalled)) {
            $stub->expects($this->once())->method($expectedMethodCalled);
        } else {
            $stub->expects($this->never())->method($this->anything());
        }

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

    /**
     * @return MockObject
     */
    private function getShopConstraintMock(): MockObject
    {
        return $this->createMock(ShopConstraint::class);
    }

    /**
     * @param bool $isUsed
     *
     * @return MockObject
     */
    private function getMultistoreFeatureMock(bool $isUsed = true): MockObject
    {
        $stub = $this->createMock(FeatureInterface::class);
        $stub->method('isUsed')->willReturn($isUsed);

        return $stub;
    }
}
