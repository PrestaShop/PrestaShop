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

namespace Tests\TestCase;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DummyMultistoreConfiguration;

abstract class AbstractConfigurationTestCase extends KernelTestCase
{
    /**
     * @var Configuration|MockObject
     */
    protected $mockConfiguration;

    /**
     * @var ShopContext|MockObject
     */
    protected $mockShopConfiguration;

    /**
     * @var FeatureInterface
     */
    protected $mockMultistoreFeature;

    /**
     * @var Context
     */
    protected $shopContext;

    /**
     * @var Configuration
     */
    protected $legacyConfigurationAdapter;

    /**
     * @var FeatureInterface
     */
    protected $multistoreFeature;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->createConfigurationMock();
        $this->mockShopConfiguration = $this->createShopContextMock();
        $this->mockMultistoreFeature = $this->createMultistoreFeatureMock();
    }

    /**
     * @return Configuration
     */
    protected function createConfigurationMock(): Configuration
    {
        return $this->getMockBuilder(Configuration::class)
            ->setMethods(['get', 'getBoolean', 'set'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|ShopContext
     */
    protected function createShopContextMock()
    {
        return $this->getMockBuilder(ShopContext::class)
            ->setMethods(['getContextShopGroup', 'getContextShopID', 'isAllShopContext', 'getShopConstraint'])
            ->getMock();
    }

    /**
     * @return FeatureInterface
     */
    protected function createMultistoreFeatureMock(): FeatureInterface
    {
        return $this->getMockForAbstractClass(FeatureInterface::class);
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return DummyMultistoreConfiguration
     */
    protected function getDummyMultistoreConfiguration(ShopConstraint $shopConstraint): DummyMultistoreConfiguration
    {
        // we mock the shop context so that its `getShopConstraint` method returns the ShopConstraint from our provider
        $this->shopContext = $this->createShopContextMock();
        $this->shopContext
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->shopContext
            ->method('isAllShopContext')
            ->willReturn($shopConstraint->forAllShops());

        return new DummyMultistoreConfiguration(
            $this->legacyConfigurationAdapter,
            $this->shopContext,
            $this->multistoreFeature
        );
    }
}
