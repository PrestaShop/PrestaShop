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
