<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Service\Multistore;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopBundle\Service\Multistore\CustomizedConfigurationChecker;

class CustomizedConfigurationCheckerTest extends TestCase
{
    public function testIsConfigurationCustomizedForThisShop(): void
    {
        $customizedConfigurationChecker = new CustomizedConfigurationChecker($this->mockShopConfiguration(true));
        $this->assertTrue($customizedConfigurationChecker->isConfigurationCustomizedForThisShop('FAKE_CONFIG_KEY', $this->prophesizeShopEntity(), true));
        $this->assertTrue($customizedConfigurationChecker->isConfigurationCustomizedForThisShop('FAKE_CONFIG_KEY', $this->prophesizeShopEntity(), false));

        $customizedConfigurationChecker = new CustomizedConfigurationChecker($this->mockShopConfiguration(false));
        $this->assertFalse($customizedConfigurationChecker->isConfigurationCustomizedForThisShop('FAKE_CONFIG_KEY', $this->prophesizeShopEntity(), true));
        $this->assertFalse($customizedConfigurationChecker->isConfigurationCustomizedForThisShop('FAKE_CONFIG_KEY', $this->prophesizeShopEntity(), false));
    }

    /**
     * @param bool $hasConfig
     *
     * @return ShopConfigurationInterface
     */
    private function mockShopConfiguration(bool $hasConfig): ShopConfigurationInterface
    {
        $shopConfigurationMock = $this->createMock(ShopConfigurationInterface::class);
        $shopConfigurationMock->method('has')->willReturn($hasConfig);

        return $shopConfigurationMock;
    }

    /**
     * @return Shop
     */
    private function prophesizeShopEntity(): Shop
    {
        $shopGroupMock = $this->createMock(ShopGroup::class);
        $shopGroupMock->method('getId')->willReturn(3); // id not important

        $shopMock = $this->createMock(Shop::class);
        $shopMock->method('getShopGroup')->willReturn($shopGroupMock);
        $shopMock->method('getId')->willReturn(3); // id not important

        return $shopMock;
    }
}
