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

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use Configuration;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\CopyProductToShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use Shop;
use ShopGroup;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\Resetter\ShopResetter;

class MultiShopProductControllerTest extends GridControllerTestCase
{
    protected const DEFAULT_SHOP_ID = 1;
    protected const DEFAULT_SHOP_GROUP_ID = 1;
    protected const DEFAULT_LANG_ID = 1;
    protected const DEFAULT_CATEGORY_ID = 2;

    protected const DEFAULT_SHOP_NAME = 'test_shop';
    protected const INDEPENDENT_SHOP_NAME = 'Independent stock Shop';
    protected const SHARED_SHOP_NAME = 'Shared stock Shop';
    protected const SECOND_SHARED_SHOP_NAME = 'Second shared stock Shop';

    protected const DEFAULT_SHOP_GROUP = 'Default';
    protected const SHARED_STOCK_SHOP_GROUP = 'Shared stock Group';

    protected const SHOPS_DATA = [
        self::DEFAULT_SHOP_NAME => [
            'name' => self::PRODUCT_NAME,
            'reference' => self::PRODUCT_REFERENCE,
            'price' => 51.00,
            'price_display' => '$51.00',
            'quantity' => 51,
        ],
        self::INDEPENDENT_SHOP_NAME => [
            'name' => 'Independent product',
            'reference' => self::PRODUCT_REFERENCE,
            'price' => 69.00,
            'price_display' => '$69.00',
            'quantity' => 69,
        ],
        self::SHARED_SHOP_NAME => [
            'name' => 'Shared stock Product',
            'reference' => self::PRODUCT_REFERENCE,
            'price' => 13.00,
            'price_display' => '$13.00',
            'quantity' => 21,
        ],
        self::SECOND_SHARED_SHOP_NAME => [
            'name' => 'Second shared stock Product',
            'reference' => self::PRODUCT_REFERENCE,
            'price' => 14.00,
            'price_display' => '$14.00',
            'quantity' => 21,
        ],
    ];

    protected const PRODUCT_NAME = 'Multishop Product';

    // This value is common to all shops
    protected const PRODUCT_REFERENCE = 'product-multi';

    protected static $testProductId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
        ProductResetter::resetProducts();
        ShopResetter::resetShops();
        static::initFixtures();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ProductResetter::resetProducts();
        ShopResetter::resetShops();
    }

    public function setUp(): void
    {
        parent::setUp();
        $featureFlagRepository = $this->client->getContainer()->get('prestashop.core.admin.feature_flag.repository');
        $featureFlagRepository->enable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2_MULTI_SHOP);
    }

    public function tearDown(): void
    {
        $featureFlagRepository = $this->client->getContainer()->get('prestashop.core.admin.feature_flag.repository');
        $featureFlagRepository->disable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2_MULTI_SHOP);

        // Call parent tear down later or the kernel will be shut down
        parent::tearDown();
    }

    /**
     * @dataProvider getMultiShopListParameters
     */
    public function testMultiShopList(array $shopContext, array $listFilters, array $productValues): void
    {
        if (!empty($shopContext['shop_name'])) {
            Shop::setContext(Shop::CONTEXT_SHOP, Shop::getIdByName($shopContext['shop_name']));
        } elseif (!empty($shopContext['group_shop_name'])) {
            Shop::setContext(Shop::CONTEXT_GROUP, ShopGroup::getIdByName($shopContext['group_shop_name']));
        } else {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        Shop::resetStaticCache();

        $products = $this->getFilteredEntitiesFromGrid($listFilters);
        $this->assertEquals(1, count($products), sprintf(
            'Expected strictly one product with filters %s',
            var_export($listFilters, true)
        ));
        $this->assertCollectionContainsEntity($products, static::$testProductId);
        /** @var TestEntityDTO $testProductDTO */
        $testProductDTO = $products->get(0);
        foreach ($productValues as $variableName => $variableValue) {
            $this->assertEquals($variableValue, $testProductDTO->getVariable($variableName));
        }
    }

    public function getMultiShopListParameters(): iterable
    {
        $shopData = static::SHOPS_DATA[static::DEFAULT_SHOP_NAME];
        yield 'list for default shop context' => [
            'shop_context' => [
                'shop_name' => static::DEFAULT_SHOP_NAME,
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                'quantity' => $shopData['quantity'],
            ],
        ];

        $shopData = static::SHOPS_DATA[static::INDEPENDENT_SHOP_NAME];
        yield 'list for independent shop context' => [
            'shop_context' => [
                'shop_name' => static::INDEPENDENT_SHOP_NAME,
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                'quantity' => $shopData['quantity'],
            ],
        ];

        $shopData = static::SHOPS_DATA[static::SHARED_SHOP_NAME];
        yield 'list for shared shop context' => [
            'shop_context' => [
                'shop_name' => static::SHARED_SHOP_NAME,
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                // Stock is shared for the group and has been incremented by 21 twice
                'quantity' => 42,
            ],
        ];

        $shopData = static::SHOPS_DATA[static::SECOND_SHARED_SHOP_NAME];
        yield 'list for second shared shop context' => [
            'shop_context' => [
                'shop_name' => static::SECOND_SHARED_SHOP_NAME,
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                // Stock is shared for the group and has been incremented by 21 twice
                'quantity' => 42,
            ],
        ];

        // Display group that shares stock (sto use the stock for the group) and doesn't contain the default shop
        // so display the value from the first shop associated to this shop group
        $shopData = static::SHOPS_DATA[static::SHARED_SHOP_NAME];
        yield 'list for shared shop group context' => [
            'shop_context' => [
                'group_shop_name' => static::SHARED_STOCK_SHOP_GROUP,
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                // Stock is shared for the group and has been incremented by 21 twice
                'quantity' => 42,
            ],
        ];

        // Data shown will be the one from default shop
        $shopData = static::SHOPS_DATA[static::DEFAULT_SHOP_NAME];
        yield 'list for default shop group context' => [
            'shop_context' => [
                'group_shop_name' => static::DEFAULT_SHOP_GROUP,
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                'quantity' => $shopData['quantity'],
            ],
        ];

        // For all shops mode we display the default shop data
        $shopData = static::SHOPS_DATA[static::DEFAULT_SHOP_NAME];
        yield 'list for all shops context' => [
            'shop_context' => [
            ],
            'filters' => ['product[name]' => $shopData['name']],
            'product_values' => [
                'name' => $shopData['name'],
                'reference' => static::PRODUCT_REFERENCE,
                'price_tax_excluded' => $shopData['price_display'],
                'price_tax_included' => $shopData['price_display'],
                'quantity' => $shopData['quantity'],
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_product')->text()),
            [
                'name' => trim($tr->filter('.column-name')->text()),
                'reference' => trim($tr->filter('.column-reference')->text()),
                'category' => trim($tr->filter('.column-category')->text()),
                'price_tax_excluded' => trim($tr->filter('.column-final_price_tax_excluded')->text()),
                'price_tax_included' => trim($tr->filter('.column-price_tax_included')->text()),
                'quantity' => (int) trim($tr->filter('.column-quantity')->text()),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterSearchButtonSelector(): string
    {
        return 'product[actions][search]';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'product[offset]' => 0,
                'product[limit]' => 100,
            ];
        }

        return $this->router->generate('admin_products_v2_index', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGridSelector(): string
    {
        return '#product_grid_table';
    }

    protected static function initFixtures(): void
    {
        // Enable multishop mode
        Configuration::updateGlobalValue(MultistoreConfig::FEATURE_STATUS, 1);

        // Add a shop in the existing shop group, it has its own stock
        $initialShopGroup = new ShopGroup(static::DEFAULT_SHOP_GROUP_ID);
        $independentStockShop = new Shop();
        $independentStockShop->name = static::INDEPENDENT_SHOP_NAME;
        $independentStockShop->id_shop_group = $initialShopGroup->id;
        $independentStockShop->id_category = static::DEFAULT_CATEGORY_ID;
        $independentStockShop->save();

        // Create shop group that shares its stock
        $shopGroup = new ShopGroup();
        $shopGroup->name = self::SHARED_STOCK_SHOP_GROUP;
        $shopGroup->share_stock = true;
        $shopGroup->save();

        $sharedStockShop = new Shop();
        $sharedStockShop->name = static::SHARED_SHOP_NAME;
        $sharedStockShop->id_shop_group = $shopGroup->id;
        $sharedStockShop->id_category = static::DEFAULT_CATEGORY_ID;
        $sharedStockShop->save();

        $secondSharedStockShop = new Shop();
        $secondSharedStockShop->name = static::SECOND_SHARED_SHOP_NAME;
        $secondSharedStockShop->id_shop_group = $shopGroup->id;
        $secondSharedStockShop->id_category = static::DEFAULT_CATEGORY_ID;
        $secondSharedStockShop->save();

        static::createProduct();
    }

    /**
     * Create product that is associated to all shops and groups
     */
    protected static function createProduct(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');

        /** @var ProductId $productId */
        $productId = $commandBus->handle(new AddProductCommand(
            ProductType::TYPE_STANDARD,
            static::DEFAULT_LANG_ID,
            [
                static::DEFAULT_LANG_ID => static::PRODUCT_NAME,
            ]
        ));
        static::$testProductId = $productId->getValue();

        // Copy product to new shops
        foreach (self::SHOPS_DATA as $shopName => $shopData) {
            $shopId = (int) Shop::getIdByName($shopName);

            // Copy product to new shops
            if ($shopId !== static::DEFAULT_SHOP_ID) {
                $commandBus->handle(new CopyProductToShopCommand($productId->getValue(), static::DEFAULT_SHOP_ID, $shopId));
            }
        }

        foreach (self::SHOPS_DATA as $shopName => $shopData) {
            $shopConstraint = ShopConstraint::shop((int) Shop::getIdByName($shopName));
            // Define different names/references for the product
            $updateCommand = new UpdateProductCommand($productId->getValue(), $shopConstraint);
            $updateCommand
                ->setLocalizedNames([static::DEFAULT_LANG_ID => $shopData['name']])
                ->setReference($shopData['reference'])
                ->setPrice((string) $shopData['price'])
            ;
            $commandBus->handle($updateCommand);

            // Define different stock
            $stockCommand = new UpdateProductStockCommand($productId->getValue(), $shopConstraint);
            $stockCommand->setDeltaQuantity($shopData['quantity']);
            $commandBus->handle($stockCommand);
        }
    }
}
