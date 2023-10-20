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

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use Configuration;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Product;
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

    protected const ALL_SHOPS_PRODUCT_DATA = [
        self::DEFAULT_SHOP_NAME => [
            'name' => 'All shops Product - Default',
            'reference' => 'product-all-shops',
            'price_tax_excluded' => '$51.00',
            'quantity' => 51,
        ],
        self::INDEPENDENT_SHOP_NAME => [
            'name' => 'All shops Product - independent shop',
            'reference' => 'product-all-shops',
            'price_tax_excluded' => '$69.00',
            'quantity' => 69,
        ],
        self::SHARED_SHOP_NAME => [
            'name' => 'All shops Product - Shared stock',
            'reference' => 'product-all-shops',
            'price_tax_excluded' => '$13.00',
            'quantity' => 21,
        ],
        self::SECOND_SHARED_SHOP_NAME => [
            'name' => 'All shops Product - Second shared stock',
            'reference' => 'product-all-shops',
            'price_tax_excluded' => '$14.00',
            'quantity' => 21,
        ],
    ];

    protected const PARTIAL_SHOPS_PRODUCT_DATA = [
        self::DEFAULT_SHOP_NAME => [
            'name' => 'Partial shops Product - Default',
            'reference' => 'product-partial-shops',
            'price_tax_excluded' => '$99.00',
            'quantity' => 99,
        ],
        self::SECOND_SHARED_SHOP_NAME => [
            'name' => 'Partial shops Product - Second shared stock',
            'reference' => 'product-partial-shops',
            'price_tax_excluded' => '$44.00',
            'quantity' => 44,
        ],
    ];

    protected const FIXTURE_PRODUCT_DATA = [
        self::DEFAULT_SHOP_NAME => [
            'name' => 'Customizable mug',
            'reference' => 'demo_14',
            'price_tax_excluded' => '$13.90',
            'quantity' => 300,
        ],
        self::INDEPENDENT_SHOP_NAME => [
            'name' => 'Customizable mug',
            'reference' => 'demo_14',
            'price_tax_excluded' => '$13.90',
            'quantity' => 300,
        ],
        self::SHARED_SHOP_NAME => [
            'name' => 'Customizable mug',
            'reference' => 'demo_14',
            'price_tax_excluded' => '$13.90',
            'quantity' => 300,
        ],
        self::SECOND_SHARED_SHOP_NAME => [
            'name' => 'Customizable mug',
            'reference' => 'demo_14',
            'price_tax_excluded' => '$13.90',
            'quantity' => 300,
        ],
    ];

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
        $featureFlagRepository = $this->client->getContainer()->get(FeatureFlagRepository::class);
        $featureFlagRepository->enable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
    }

    public function tearDown(): void
    {
        $featureFlagRepository = $this->client->getContainer()->get(FeatureFlagRepository::class);
        $featureFlagRepository->disable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);

        // Call parent tear down later or the kernel will be shut down
        parent::tearDown();
    }

    /**
     * @dataProvider getMultiShopListParameters
     */
    public function testMultiShopList(array $shopContext, array $listFilters, int $totalCount, array $productsValues): void
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
        $this->assertEquals($totalCount, $products->getTotalCount(), sprintf(
            'Expected %d product(s) with filters %s but got %d instead',
            $totalCount,
            var_export($listFilters, true),
            $products->getTotalCount()
        ));
        $this->assertCollectionContainsEntity($products, static::$testProductId);

        foreach ($productsValues as $productIndex => $productValues) {
            /** @var TestEntityDTO $testProductDTO */
            $testProductDTO = $products->get($productIndex);
            $this->assertNotNull($testProductDTO, sprintf('Expected product at index %d', $productIndex));
            foreach ($productValues as $variableName => $variableValue) {
                $this->assertEquals($variableValue, $testProductDTO->getVariable($variableName));
            }
        }
    }

    public function getMultiShopListParameters(): iterable
    {
        yield 'list for default shop context' => [
            'shop_context' => [
                'shop_name' => static::DEFAULT_SHOP_NAME,
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 21,
            'products_values' => [
                0 => static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
                1 => static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
                2 => static::FIXTURE_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
            ],
        ];

        yield 'list for default shop context with filter' => [
            'shop_context' => [
                'shop_name' => static::DEFAULT_SHOP_NAME,
            ],
            'filters' => ['product[name]' => static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME]['name']],
            'total_count' => 1,
            'products_values' => [
                0 => static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
            ],
        ];

        yield 'list for independent shop context' => [
            'shop_context' => [
                'shop_name' => static::INDEPENDENT_SHOP_NAME,
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 2,
            'products_values' => [
                0 => static::ALL_SHOPS_PRODUCT_DATA[static::INDEPENDENT_SHOP_NAME],
                1 => static::FIXTURE_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
            ],
        ];

        yield 'list for shared shop context' => [
            'shop_context' => [
                'shop_name' => static::SHARED_SHOP_NAME,
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 2,
            'products_values' => [
                0 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::SHARED_SHOP_NAME], [
                    // Stock is shared for the group and has been incremented by 21 twice
                    'quantity' => 42,
                ]),
                1 => static::FIXTURE_PRODUCT_DATA[static::SHARED_SHOP_NAME],
            ],
        ];

        yield 'list for second shared shop context' => [
            'shop_context' => [
                'shop_name' => static::SECOND_SHARED_SHOP_NAME,
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 3,
            'products_values' => [
                0 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME], [
                    // Stock is shared for the group and has been incremented by 21 twice
                    'quantity' => 42,
                ]),
                1 => static::PARTIAL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME],
                2 => static::FIXTURE_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME],
            ],
        ];

        // Data shown will be the one from default shop (first shop in the first shop group)
        yield 'list for default shop group context' => [
            'shop_context' => [
                'group_shop_name' => static::DEFAULT_SHOP_GROUP,
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 21,
            'products_values' => [
                0 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME], [
                    'associated_shops' => implode(', ', [static::DEFAULT_SHOP_NAME, static::INDEPENDENT_SHOP_NAME]),
                ]),
                1 => array_merge(static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME], [
                    'associated_shops' => static::DEFAULT_SHOP_NAME,
                ]),
                2 => array_merge(static::FIXTURE_PRODUCT_DATA[static::DEFAULT_SHOP_NAME], [
                    'associated_shops' => implode(', ', [static::DEFAULT_SHOP_NAME, static::INDEPENDENT_SHOP_NAME]),
                ]),
            ],
        ];

        // Display group that shares stock (so use the stock for the group) display the value from the first shop associated to this shop group
        yield 'list for shared shop group context' => [
            'shop_context' => [
                'group_shop_name' => static::SHARED_STOCK_SHOP_GROUP,
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 3,
            'products_values' => [
                0 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::SHARED_SHOP_NAME], [
                    // Stock is shared for the group and has been incremented by 21 twice
                    'quantity' => 42,
                    'associated_shops' => implode(', ', [static::SHARED_SHOP_NAME, static::SECOND_SHARED_SHOP_NAME]),
                ]),
                // Partial product is part of the group, but it only belongs to the second shared shop (so the data from this shop are displayed)
                1 => array_merge(static::PARTIAL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME], [
                    'associated_shops' => static::SECOND_SHARED_SHOP_NAME,
                ]),
                2 => array_merge(static::FIXTURE_PRODUCT_DATA[static::SHARED_SHOP_NAME], [
                    'associated_shops' => implode(', ', [static::SHARED_SHOP_NAME, static::SECOND_SHARED_SHOP_NAME]),
                ]),
            ],
        ];

        // For all shops mode we display the default shop data
        yield 'list for all shops context' => [
            'shop_context' => [
            ],
            'filters' => ['product[name]' => ''],
            'total_count' => 21,
            'products_values' => [
                0 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME], [
                    'associated_shops' => implode(', ', [static::DEFAULT_SHOP_NAME, static::INDEPENDENT_SHOP_NAME, static::SHARED_SHOP_NAME, static::SECOND_SHARED_SHOP_NAME]),
                ]),
                1 => array_merge(static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME], [
                    'associated_shops' => implode(', ', [static::DEFAULT_SHOP_NAME, static::SECOND_SHARED_SHOP_NAME]),
                ]),
                2 => array_merge(static::FIXTURE_PRODUCT_DATA[static::DEFAULT_SHOP_NAME], [
                    'associated_shops' => implode(', ', [static::DEFAULT_SHOP_NAME, static::INDEPENDENT_SHOP_NAME, static::SHARED_SHOP_NAME, static::SECOND_SHARED_SHOP_NAME]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider getProductShopPreviewsParameters
     */
    public function testProductShopPreviews(array $shopContext, array $listFilters, array $shopPreviews): void
    {
        if (!empty($shopContext['shop_name'])) {
            Shop::setContext(Shop::CONTEXT_SHOP, Shop::getIdByName($shopContext['shop_name']));
        } elseif (!empty($shopContext['group_shop_name'])) {
            Shop::setContext(Shop::CONTEXT_GROUP, ShopGroup::getIdByName($shopContext['group_shop_name']));
        } else {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        $products = $this->getFilteredEntitiesFromGrid($listFilters);
        $this->assertEquals(1, $products->count(), 'Provided filters must match one product only');
        /** @var TestEntityDTO $filteredProduct */
        $filteredProduct = $products->get(0);

        $routeParams = ['productId' => $filteredProduct->getId()];
        if (!empty($shopContext['group_shop_name'])) {
            $routeParams['shopGroupId'] = ShopGroup::getIdByName($shopContext['group_shop_name']);
        }
        $shopPreviewsUrl = $this->router->generate('admin_products_grid_shop_previews', $routeParams);
        $crawler = $this->client->request('GET', $shopPreviewsUrl);
        $entitiesRows = $crawler->filter('tr:not(.empty_row)');
        $productShopPreviews = $this->parseEntitiesFromRows($entitiesRows);

        foreach ($shopPreviews as $shopPreviewIndex => $shopPreviewData) {
            /** @var TestEntityDTO $productShopPreview */
            $productShopPreview = $productShopPreviews->get($shopPreviewIndex);
            $this->assertNotNull($productShopPreview, sprintf('Expected shop preview at index %d', $shopPreviewIndex));
            // The check at the shop preview values
            foreach ($shopPreviewData as $variableName => $variableValue) {
                $this->assertEquals($variableValue, $productShopPreview->getVariable($variableName));
            }
        }
    }

    public function getProductShopPreviewsParameters(): iterable
    {
        yield 'test previews for product on all shops with all shops context' => [
            'shop_context' => [],
            'filters' => ['product[name]' => static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME]['name']],
            'shop_previews' => [
                0 => array_merge(
                    static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
                    ['shop_name' => static::DEFAULT_SHOP_NAME]
                ),
                1 => array_merge(
                    static::ALL_SHOPS_PRODUCT_DATA[static::INDEPENDENT_SHOP_NAME],
                    ['shop_name' => static::INDEPENDENT_SHOP_NAME]
                ),
                2 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::SHARED_SHOP_NAME],
                    ['shop_name' => static::SHARED_SHOP_NAME, 'quantity' => 42]
                ),
                3 => array_merge(
                    static::ALL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME],
                    ['shop_name' => static::SECOND_SHARED_SHOP_NAME, 'quantity' => 42]
                ),
            ],
        ];

        yield 'test previews for product on all shops with default shop group context' => [
            'shop_context' => ['group_shop_name' => static::DEFAULT_SHOP_GROUP],
            'filters' => ['product[name]' => static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME]['name']],
            'shop_previews' => [
                0 => array_merge(
                    static::ALL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
                    ['shop_name' => static::DEFAULT_SHOP_NAME]
                ),
                1 => array_merge(
                    static::ALL_SHOPS_PRODUCT_DATA[static::INDEPENDENT_SHOP_NAME],
                    ['shop_name' => static::INDEPENDENT_SHOP_NAME]
                ),
            ],
        ];

        yield 'test previews for product on all shops with second shop group context' => [
            'shop_context' => ['group_shop_name' => static::SHARED_STOCK_SHOP_GROUP],
            'filters' => ['product[name]' => static::ALL_SHOPS_PRODUCT_DATA[static::SHARED_SHOP_NAME]['name']],
            'shop_previews' => [
                0 => array_merge(static::ALL_SHOPS_PRODUCT_DATA[static::SHARED_SHOP_NAME],
                    ['shop_name' => static::SHARED_SHOP_NAME, 'quantity' => 42]
                ),
                1 => array_merge(
                    static::ALL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME],
                    ['shop_name' => static::SECOND_SHARED_SHOP_NAME, 'quantity' => 42]
                ),
            ],
        ];

        yield 'test previews for partial product with all shop contexts' => [
            'shop_context' => [],
            'filters' => ['product[name]' => static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME]['name']],
            'shop_previews' => [
                0 => array_merge(
                    static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
                    ['shop_name' => static::DEFAULT_SHOP_NAME]
                ),
                1 => array_merge(
                    static::PARTIAL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME],
                    ['shop_name' => static::SECOND_SHARED_SHOP_NAME]
                ),
            ],
        ];

        yield 'test previews for partial product with default group shop context' => [
            'shop_context' => ['group_shop_name' => static::DEFAULT_SHOP_GROUP],
            'filters' => ['product[name]' => static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME]['name']],
            'shop_previews' => [
                0 => array_merge(
                    static::PARTIAL_SHOPS_PRODUCT_DATA[static::DEFAULT_SHOP_NAME],
                    ['shop_name' => static::DEFAULT_SHOP_NAME]
                ),
            ],
        ];

        yield 'test previews for partial product with second shop group context' => [
            'shop_context' => ['group_shop_name' => static::SHARED_STOCK_SHOP_GROUP],
            'filters' => ['product[name]' => static::PARTIAL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME]['name']],
            'shop_previews' => [
                0 => array_merge(
                    static::PARTIAL_SHOPS_PRODUCT_DATA[static::SECOND_SHARED_SHOP_NAME],
                    ['shop_name' => static::SECOND_SHARED_SHOP_NAME]
                ),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        $shopListNode = $tr->filter('.column-associated_shops .product-shop-list');
        $associatedShops = $shopListNode->count() ? $shopListNode->attr('title') : '';

        $shopNameNode = $tr->filter('.column-shop_name .shop-name-text');
        $shopName = $shopNameNode->count() ? $shopNameNode->text() : '';

        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_product')->text()),
            [
                'name' => trim($tr->filter('.column-name')->text()),
                'reference' => trim($tr->filter('.column-reference')->text()),
                'category' => trim($tr->filter('.column-category')->text()),
                'price_tax_excluded' => trim($tr->filter('.column-final_price_tax_excluded')->text()),
                'price_tax_included' => trim($tr->filter('.column-price_tax_included')->text()),
                'quantity' => (int) trim($tr->filter('.column-quantity')->text()),
                'associated_shops' => $associatedShops,
                'shop_name' => $shopName,
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

        return $this->router->generate('admin_products_index', $routeParams);
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

        static::createProducts();
    }

    /**
     * Create product that is associated to all shops and groups
     */
    protected static function createProducts(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');

        static::createProduct($commandBus, self::PARTIAL_SHOPS_PRODUCT_DATA);
        static::createProduct($commandBus, self::ALL_SHOPS_PRODUCT_DATA);

        $shopIds = array_map(static function (string $shopName): int {
            return (int) Shop::getIdByName($shopName);
        }, array_keys(self::FIXTURE_PRODUCT_DATA));

        // copy product to new shops
        $commandBus->handle(new SetProductShopsCommand((int) Product::getIdByReference('demo_14'), static::DEFAULT_SHOP_ID, $shopIds));
    }

    protected static function createProduct(CommandBusInterface $commandBus, array $multiShopProductData): void
    {
        /** @var ProductId $productId */
        $productId = $commandBus->handle(new AddProductCommand(
            ProductType::TYPE_STANDARD,
            static::DEFAULT_LANG_ID,
            [
                static::DEFAULT_LANG_ID => $multiShopProductData[self::DEFAULT_SHOP_NAME]['name'],
            ]
        ));
        static::$testProductId = $productId->getValue();

        $shopIds = array_map(static function (string $shopName): int {
            return (int) Shop::getIdByName($shopName);
        }, array_keys($multiShopProductData));

        $commandBus->handle(new SetProductShopsCommand($productId->getValue(), static::DEFAULT_SHOP_ID, $shopIds));

        foreach ($multiShopProductData as $shopName => $shopProductData) {
            $shopConstraint = ShopConstraint::shop((int) Shop::getIdByName($shopName));
            // Define different names/references for the product
            $updateCommand = new UpdateProductCommand($productId->getValue(), $shopConstraint);
            $updateCommand
                ->setLocalizedNames([static::DEFAULT_LANG_ID => $shopProductData['name']])
                ->setReference($shopProductData['reference'])
                ->setPrice(str_replace('$', '', $shopProductData['price_tax_excluded']))
            ;
            $commandBus->handle($updateCommand);

            // Define different stock
            $stockCommand = new UpdateProductStockAvailableCommand($productId->getValue(), $shopConstraint);
            $stockCommand->setDeltaQuantity($shopProductData['quantity']);
            $commandBus->handle($stockCommand);
        }
    }
}
