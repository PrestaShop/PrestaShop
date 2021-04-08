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

use Behat\Gherkin\Node\TableNode;
use Cache;
use Configuration;
use Context;
use Db;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\SearchShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Query\SearchShops;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\FoundShop;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\FoundShopGroup;
use RuntimeException;
use Shop;
use ShopGroup;
use ShopUrl;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class ShopFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given single shop :shopReference context is loaded
     *
     * @param string $shopReference
     */
    public function loadSingleShopContext(string $shopReference): void
    {
        /** @var Shop $shop */
        $shop = SharedStorage::getStorage()->get($shopReference);

        Shop::setContext(Shop::CONTEXT_SHOP, $shop->id);
    }

    /**
     * @Given shop :reference with name :shopName exists
     *
     * @param string $reference
     * @param string $shopName
     */
    public function shopWithNameExists(string $reference, string $shopName): void
    {
        $shopId = Shop::getIdByName($shopName);

        if (false === $shopId) {
            throw new RuntimeException(sprintf('Shop with name "%s" does not exist', $shopName));
        }

        SharedStorage::getStorage()->set($reference, new Shop($shopId));
    }

    /**
     * @Given /^I add a shop group "(.+)" with name "(.+?)"(?: and color "(.+)")?$/
     *
     * @param string $reference
     * @param string $groupName
     * @param string|null $color
     */
    public function addShopGroup(string $reference, string $groupName, string $color = null): void
    {
        $shopGroup = new ShopGroup();
        $shopGroup->name = $groupName;
        $shopGroup->active = true;

        if ($color !== null) {
            $shopGroup->color = $color;
        }

        if (!$shopGroup->add()) {
            throw new RuntimeException(sprintf('Could not create shop group: %s', Db::getInstance()->getMsgError()));
        }

        SharedStorage::getStorage()->set($reference, $shopGroup);
    }

    /**
     * @Given /^I copy "(.+)" shop data from "(.+)" to "(.+)"$/
     *
     * @param string $what
     * @param string $from
     * @param string $to
     */
    public function copyShopData(string $what, string $from, string $to): void
    {
        $shopToId = (int) Shop::getIdByName($to);
        if (empty($shopToId)) {
            throw new RuntimeException(sprintf('Could not find shop: %s', $from));
        }

        $shopFromId = (int) Shop::getIdByName($from);
        if (empty($shopFromId)) {
            throw new RuntimeException(sprintf('Could not find shop: %s', $from));
        }

        $shopTo = new Shop($shopToId);
        $shopTo->copyShopData($shopFromId, [$what => true]);
    }

    /**
     * @Given I add a shop :reference with name :shopName and color :color for the group :shopGroupName
     *
     * @param string $reference
     * @param string $shopName
     * @param string $shopGroupName
     */
    public function addShop(string $reference, string $shopName, string $color, string $shopGroupName): void
    {
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = ShopGroup::getIdByName($shopGroupName);
        // 2 : ID Category for "Home" in database
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = $shopName;
        $shop->color = $color;
        if (!$shop->add()) {
            throw new RuntimeException(sprintf('Could not create shop: %s', Db::getInstance()->getMsgError()));
        }
        $shop->setTheme();

        SharedStorage::getStorage()->set($reference, $shop);
    }

    /**
     * @Given single shop context is loaded
     */
    public function singleShopContextIsLoaded(): void
    {
        $this->setShopContext(Shop::CONTEXT_SHOP, (int) Configuration::get('PS_SHOP_DEFAULT'));
    }

    /**
     * @Given multiple shop context is loaded
     */
    public function multipleShopContextIsLoaded(): void
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
    public function checkShopGroupCount(int $expectedCount): void
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
    public function checkShopCount(int $expectedCount, string $shopGroupName): void
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

    /**
     * @Given I add a shop url to shop :shopReference
     *
     * @param string $shopReference
     */
    public function addShopUrl(string $shopReference): void
    {
        $shop = SharedStorage::getStorage()->get($shopReference);
        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = $shop->id;
        $shopUrl->active = true;
        $shopUrl->main = true;
        $shopUrl->domain = 'localhost';
        $shopUrl->domain_ssl = 'localhost';
        $shopUrl->physical_uri = '/prestatest/';
        $shopUrl->virtual_uri = '/prestatest/';
        if (!$shopUrl->add()) {
            throw new RuntimeException(sprintf('Could not create shop url: %s', Db::getInstance()->getMsgError()));
        }
    }

    /**
     * @Transform table:name,group_name,color,group_color,is_shop_group
     *
     * @param TableNode $tableNode
     *
     * @return array
     */
    public function transformShops(TableNode $shopsTable): array
    {
        $dataRows = $shopsTable->getHash();
        $foundElements = [];

        foreach ($dataRows as $row) {
            $isShopGroup = PrimitiveUtils::castStringBooleanIntoBoolean($row['is_shop_group']);
            if (!$isShopGroup) {
                $foundElements[] = new FoundShop(
                    4, // id not relevant for the test
                    $row['color'],
                    $row['name'],
                    4, // id not relevant for the test
                    $row['group_name'],
                    $row['group_color']
                );
            } else {
                $foundElements[] = new FoundShopGroup(
                    4, // id not relevant for the test
                    $row['color'],
                    $row['name']
                );
            }
        }

        return $foundElements;
    }

    /**
     * @When I search for the term :searchTerm I should get the following results:
     *
     * @param string $searchTerm
     * @param array $expectedShops
     */
    public function assertFoundShops(string $searchTerm, array $expectedShops): void
    {
        $foundShops = $this->getQueryBus()->handle(new SearchShops($searchTerm));

        foreach ($expectedShops as $currentExpectedShop) {
            $wasCurrentExpectedShopFound = false;
            foreach ($foundShops as $currentFoundShop) {
                if ($currentExpectedShop->getName() === $currentFoundShop->getName()) {
                    $wasCurrentExpectedShopFound = true;
                    if ($currentExpectedShop instanceof FoundShop) {
                        Assert::assertEquals(
                            $currentExpectedShop->getGroupName(),
                            $currentFoundShop->getGroupName(),
                            sprintf(
                                'Expected and found shops\'s groups don\'t match (%s and %s)',
                                $currentExpectedShop->getGroupName(),
                                $currentFoundShop->getGroupName()
                            )
                        );
                        Assert::assertEquals(
                            $currentExpectedShop->getGroupColor(),
                            $currentFoundShop->getGroupColor(),
                            sprintf(
                                'Expected and found shop groups\'s colors don\'t match (%s and %s)',
                                $currentExpectedShop->getGroupColor(),
                                $currentFoundShop->getGroupColor()
                            )
                        );
                    }

                    Assert::assertEquals(
                        $currentExpectedShop->getColor(),
                        $currentFoundShop->getColor(),
                        sprintf(
                            'Expected and found shops\'s colors don\'t match (%s and %s)',
                            $currentExpectedShop->getColor(),
                            $currentFoundShop->getColor()
                        )
                    );
                    continue;
                }
            }

            if (!$wasCurrentExpectedShopFound) {
                if ($currentExpectedShop instanceof FoundShop) {
                    throw new RuntimeException(sprintf(
                        'Expected shop with name %s in shop group %s was not found',
                        $currentExpectedShop->getName(),
                        $currentExpectedShop->getGroupName()
                    ));
                } else {
                    throw new RuntimeException(sprintf(
                        'Expected shop group with name %s',
                        $currentExpectedShop->getName()
                    ));
                }
            }
        }
    }

    /**
     * @When I search for the term :searchTerm I should not get any results
     *
     * @param string $searchTerm
     */
    public function assertNoShopWasFound(string $searchTerm)
    {
        $foundShops = $this->getQueryBus()->handle(new SearchShops($searchTerm));
        Assert::assertEmpty($foundShops);
    }

    /**
     * @When I search for the term :searchTerm I should get a SearchShopException
     */
    public function assertShopException(string $searchTerm): void
    {
        $exceptionTriggered = false;
        try {
            $this->getQueryBus()->handle(new SearchShops($searchTerm));
        } catch (SearchShopException $e) {
            $exceptionTriggered = true;
        }

        if (!$exceptionTriggered) {
            throw new RuntimeException('Expected SearchShopException did not happen');
        }
    }
}
