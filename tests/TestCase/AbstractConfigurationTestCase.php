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

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;

abstract class AbstractConfigurationTestCase extends TestCase
{
    /**
     * @var Configuration
     */
    protected $mockConfiguration;

    /**
     * @var ShopContext
     */
    protected $mockShopConfiguration;

    /**
     * @var FeatureInterface
     */
    protected $mockMultistoreFeature;

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
     * @return ShopContext
     */
    protected function createShopContextMock(): ShopContext
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
}
