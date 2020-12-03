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

namespace Tests\Integration\Behaviour\Features\Context;

use Cache;
use Configuration;
use Context;
use Db;
use RuntimeException;
use Shop;
use ShopGroup;

class ShopFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given shop :reference with name :shopName exists
     *
     * @param string $reference
     * @param string $shopName
     */
    public function shopWithNameExists(string $reference, string $shopName)
    {
        $shopId = Shop::getIdByName($shopName);

        if (false === $shopId) {
            throw new RuntimeException(sprintf('Shop with name "%s" does not exist', $shopName));
        }

        SharedStorage::getStorage()->set($reference, new Shop($shopId));
    }

    /**
     * @Given /I add a shop group "(.+)" with name "(.+)"$/
     *
     * @param string $reference
     * @param string $groupName
     */
    public function addShopGroup(string $reference, string $groupName): void
    {
        $shopGroup = new ShopGroup();
        $shopGroup->name = $groupName;
        $shopGroup->active = true;
        if (!$shopGroup->add()) {
            throw new RuntimeException(sprintf('Could not create shop group: %s', Db::getInstance()->getMsgError()));
        }

        SharedStorage::getStorage()->set($reference, $shopGroup);
    }

    /**
     * @Given /I add a shop "(.+)" with name "(.+)" for the group "(.+)"$/
     *
     * @param string $reference
     * @param string $shopName
     * @param string $shopGroupName
     */
    public function addShop(string $reference, string $shopName, string $shopGroupName): void
    {
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = ShopGroup::getIdByName($shopGroupName);
        // 2 : ID Category for "Home" in database
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = $shopName;
        if (!$shop->add()) {
            throw new RuntimeException(sprintf('Could not create shop: %s', Db::getInstance()->getMsgError()));
        }
        $shop->setTheme();

        // Link Country to new Shop
        Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'country_shop` (`id_country`, `id_shop`) ' .
            'SELECT id_country, ' . $shop->id . ' ' .
            'FROM `' . _DB_PREFIX_ . 'country` '
        );

        SharedStorage::getStorage()->set($reference, $shop);
    }

    /**
     * @Given single shop context is loaded
     */
    public function singleShopContextIsLoaded()
    {
        $this->setShopContext(Shop::CONTEXT_SHOP, (int) Configuration::get('PS_SHOP_DEFAULT'));
    }

    /**
     * @Given multiple shop context is loaded
     */
    public function multipleShopContextIsLoaded()
    {
        $this->setShopContext(Shop::CONTEXT_ALL, (int) Configuration::get('PS_SHOP_DEFAULT'));
    }

    /**
     * @Given /^shop context "(.+)" is loaded$/
     *
     * @param string $shopName
     */
    public function specificShopContextIsLoaded(string $shopName): void
    {
        $this->setShopContext(Shop::CONTEXT_SHOP, (int) Shop::getIdByName($shopName));
    }

    /**
     * @Then /^I should have (\d) shop group(s)$/
     *
     * @param int $expectedCount
     */
    public function checkShopGroupCount(int $expectedCount)
    {
        $countShopGroup = ShopGroup::getTotalShopGroup();

        if ($countShopGroup == $expectedCount) {
            return;
        }
        throw new RuntimeException(
            sprintf(
                'Invalid number of shop groups, expected %s but got %s instead',
                $expectedCount,
                $countShopGroup
            )
        );
    }

    /**
     * @Then /^I should have (\d) shop(?:|s) in group "(.+)"$/
     *
     * @param int $expectedCount
     */
    public function checkShopCount(int $expectedCount, string $shopGroupName)
    {
        $shopGroupId = ShopGroup::getIdByName($shopGroupName);
        if (false === $shopGroupId) {
            throw new RuntimeException(sprintf('Shop Group with name "%s" does not exist', $shopGroupName));
        }

        $shops = ShopGroup::getShopsFromGroup($shopGroupId);
        if (count($shops) == $expectedCount) {
            return;
        }
        throw new RuntimeException(
            sprintf(
                'Invalid number of shop groups, expected %s but got %s instead',
                $expectedCount,
                count($shops)
            )
        );
    }

    /**
     * @param int $context
     * @param int $shopId
     *
     * @throws \PrestaShopException
     */
    private function setShopContext(int $context, int $shopId): void
    {
        Shop::setContext($context, $shopId);
        Context::getContext()->shop = new Shop($shopId);
        // Clean cache
        Cache::clean('Shop::getCompleteListOfShopsID');
        Cache::clean('StockAvailable::*');
    }
}
