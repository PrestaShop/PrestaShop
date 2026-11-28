<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\QueryHandler;

use Cache;
use Configuration;
use Context;
use Country;
use Db;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Configuration as ConfigurationAdapter;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Adapter\Product\QueryHandler\GetProductForEditingHandler;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Repository\SpecificPriceRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Repository\VirtualProductFileRepository;
use PrestaShop\PrestaShop\Adapter\SEO\RedirectTargetProvider;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Category\NameBuilder\CategoryDisplayNameBuilder;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Shop;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tax;
use TaxManagerFactory;
use TaxRule;
use TaxRulesGroup;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

class GetProductForEditingHandlerTest extends KernelTestCase
{
    private const TABLES = [
        'configuration', 'configuration_lang', 'product_shop', 'product_lang',
        'product_attribute_shop', 'shop', 'stock_available', 'tax', 'tax_lang',
        'tax_rule', 'tax_rules_group', 'tax_rules_group_shop',
    ];

    private ContextMocker $contextMocker;

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES);
        global $kernel;
        $kernel = self::bootKernel();
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
        ObjectModel::disableCache();
    }

    protected function tearDown(): void
    {
        $this->contextMocker->resetContext();
        DatabaseDump::restoreTables(self::TABLES);
        Configuration::resetStaticCache();
        Shop::resetStaticCache();
        TaxManagerFactory::resetStaticCache();
        Cache::clean('*');
        parent::tearDown();
    }

    /**
     * @dataProvider provideCountryOverrides
     *
     * @runInSeparateProcess
     *
     * @preserveGlobalState disabled
     */
    public function testCrossShopPricesPreserveExplicitCountryOverrides(bool $overrideCountry): void
    {
        $container = self::getContainer();
        $db = Db::getInstance();
        $shopA = (int) Configuration::get('PS_SHOP_DEFAULT');
        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', 1);
        Shop::resetStaticCache();
        $sourceShop = new Shop($shopA);
        $shop = new Shop();
        $shop->name = 'Country scope test';
        $shop->id_category = $sourceShop->id_category;
        $shop->id_shop_group = $sourceShop->id_shop_group;
        $shop->theme_name = $sourceShop->theme_name;
        $shop->active = true;
        self::assertTrue($shop->add());
        $shopB = (int) $shop->id;

        $combination = $db->getRow('SELECT pa.id_product_attribute, pa.id_product FROM ' . _DB_PREFIX_ . 'product_attribute pa JOIN ' . _DB_PREFIX_ . 'product_attribute_shop pas ON pas.id_product_attribute = pa.id_product_attribute WHERE pas.id_shop = ' . $shopA);
        $productId = (int) $combination['id_product'];
        $combinationId = (int) $combination['id_product_attribute'];
        foreach (['product_shop', 'product_lang', 'product_attribute_shop'] as $table) {
            $where = $table === 'product_attribute_shop' ? 'id_product_attribute = ' . $combinationId : 'id_product = ' . $productId;
            foreach ($db->executeS('SELECT * FROM ' . _DB_PREFIX_ . $table . ' WHERE ' . $where . ' AND id_shop = ' . $shopA) as $row) {
                $row['id_shop'] = $shopB;
                $row = array_map(static fn ($value) => is_string($value) ? pSQL($value, true) : $value, $row);
                self::assertTrue($db->insert($table, $row));
            }
        }
        self::assertTrue(StockAvailable::copyStockAvailableFromShopToShop($shopA, $shopB));

        Shop::setContext(Shop::CONTEXT_ALL);
        $group = new TaxRulesGroup();
        $group->name = 'Country scope test';
        $group->active = true;
        self::assertTrue($group->add());
        foreach (['fr' => 20, 'gb' => 10] as $iso => $rate) {
            $tax = new Tax();
            $tax->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Country scope ' . $iso];
            $tax->rate = $rate;
            $tax->active = true;
            self::assertTrue($tax->add());
            $rule = new TaxRule();
            $rule->id_tax_rules_group = $group->id;
            $rule->id_country = Country::getByIso($iso);
            $rule->id_tax = $tax->id;
            $rule->id_state = 0;
            $rule->zipcode_from = 0;
            $rule->zipcode_to = 0;
            $rule->behavior = 0;
            self::assertTrue($rule->add());
        }

        $configuration = $container->get(ConfigurationAdapter::class);
        foreach ([$shopA => 'fr', $shopB => 'gb'] as $shopId => $iso) {
            $scope = ShopConstraint::shop($shopId);
            $configuration->set('PS_TAX', 1, $scope);
            $configuration->set('PS_COUNTRY_DEFAULT', (int) Country::getByIso($iso), $scope);
            $configuration->set('PS_USE_ECOTAX', 0, $scope);
            $configuration->set('PS_ECOTAX_TAX_RULES_GROUP_ID', (int) $group->id, $scope);
        }
        $db->update('product_shop', ['price' => '100', 'unit_price' => '5', 'ecotax' => '3', 'id_tax_rules_group' => (int) $group->id], 'id_product = ' . $productId);
        $db->update('product_attribute_shop', ['price' => '10', 'unit_price_impact' => '2', 'ecotax' => '1'], 'id_product_attribute = ' . $combinationId);
        Configuration::resetStaticCache();
        TaxManagerFactory::resetStaticCache();
        Cache::clean('*');
        Shop::setContext(Shop::CONTEXT_SHOP, $shopA);
        Context::getContext()->shop = new Shop($shopA);

        $query = new GetProductForEditing($productId, ShopConstraint::shop($shopB), (int) Configuration::get('PS_LANG_DEFAULT'));
        if ($overrideCountry) {
            $handler = new GetProductForEditingHandler(
                $container->get(NumberExtractor::class),
                $container->get(ProductRepository::class),
                $container->get(CategoryRepository::class),
                $container->get(StockAvailableRepository::class),
                $container->get(VirtualProductFileRepository::class),
                $container->get(ProductImageRepository::class),
                $container->get(AttachmentRepository::class),
                $container->get(TaxComputer::class),
                (int) Country::getByIso('fr'),
                $container->get(RedirectTargetProvider::class),
                $container->get(ProductImagePathFactory::class),
                $container->get(SpecificPriceRepository::class),
                $configuration,
                $container->get(CategoryDisplayNameBuilder::class),
                $container->get(ProductPackRepository::class)
            );
            $product = $handler->handle($query)->getPricesInformation();
        } else {
            $product = $container->get('prestashop.core.query_bus')->handle($query)->getPricesInformation();
        }

        self::assertSame($overrideCountry ? '120' : '110', (string) $product->getPriceTaxIncluded());
        self::assertSame($overrideCountry ? '6' : '5.5', (string) $product->getUnitPriceTaxIncluded());
        self::assertSame($overrideCountry ? '3.6' : '3.3', (string) $product->getEcotaxTaxIncluded());
        $prices = $container->get('prestashop.core.query_bus')->handle(new GetCombinationForEditing($combinationId, ShopConstraint::shop($shopB)))->getPrices();
        self::assertSame('11', (string) $prices->getImpactOnPriceTaxIncluded());
        self::assertSame('2.2', (string) $prices->getImpactOnUnitPriceTaxIncluded());
        self::assertSame('1.1', (string) $prices->getEcotaxTaxIncluded());
        self::assertSame('10', (string) $prices->getProductTaxRate());
        self::assertSame($shopA, (int) Shop::getContextShopID());
    }

    public static function provideCountryOverrides(): iterable
    {
        yield 'query shop country' => [false];
        yield 'explicit country remains supported' => [true];
    }
}
