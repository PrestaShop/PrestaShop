<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            ->onlyMethods(['get', 'getBoolean', 'set'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return MockObject|ShopContext
     */
    protected function createShopContextMock()
    {
        return $this->getMockBuilder(ShopContext::class)
            ->onlyMethods(['getContextShopGroup', 'getContextShopID', 'isAllShopContext', 'getShopConstraint'])
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
