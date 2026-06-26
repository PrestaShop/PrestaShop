<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Category;
use Configuration;
use Context;
use Db;
use Dispatcher;
use Link;
use PHPUnit\Framework\TestCase;
use Product;
use ReflectionClass;
use Shop;
use ShopUrl;
use Tests\Resources\DatabaseDump;

/**
 * Reproduces https://github.com/PrestaShop/PrestaShop/issues/36794
 *
 * When a category is shared between two shops with a different link_rewrite for the same
 * language, generating a category/product link for a shop OTHER than the current context shop
 * must use the target shop's link_rewrite, not the one of the context shop.
 */
class LinkMultishopTest extends TestCase
{
    private const TABLES = [
        'configuration',
        'shop',
        'shop_group',
        'shop_url',
        'category',
        'category_shop',
        'category_lang',
    ];

    /**
     * @var int
     */
    private $secondShopId;

    /**
     * @var int
     */
    private $categoryId = 3;

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES);

        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', '1');
        Configuration::updateGlobalValue('PS_REWRITING_SETTINGS', '1');

        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        // Create a second shop in the default group, reusing the default shop's root category.
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = 1;
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = 'Second shop';
        $shop->add();
        $this->secondShopId = (int) $shop->id;

        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = $this->secondShopId;
        $shopUrl->active = true;
        $shopUrl->main = true;
        $shopUrl->domain = 'localhost';
        $shopUrl->domain_ssl = 'localhost';
        $shopUrl->physical_uri = '/second-shop/';
        $shopUrl->virtual_uri = '';
        $shopUrl->add();

        Shop::resetContext();

        // Make the category belong to both shops and give it a distinct slug per shop.
        Db::getInstance()->insert('category_shop', [
            'id_category' => $this->categoryId,
            'id_shop' => $this->secondShopId,
            'position' => 0,
        ], false, true, Db::INSERT_IGNORE);

        Db::getInstance()->update('category_lang', ['link_rewrite' => 'category-shop-one'], 'id_category = ' . $this->categoryId . ' AND id_shop = 1 AND id_lang = ' . $idLang);

        // Ensure a category_lang row exists for the second shop, then set its slug.
        $existing = Db::getInstance()->getValue('SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'category_lang WHERE id_category = ' . $this->categoryId . ' AND id_shop = ' . $this->secondShopId . ' AND id_lang = ' . $idLang);
        if (!$existing) {
            Db::getInstance()->execute(
                'INSERT INTO ' . _DB_PREFIX_ . 'category_lang (id_category, id_shop, id_lang, name, description, additional_description, link_rewrite, meta_title, meta_description)
                 SELECT id_category, ' . $this->secondShopId . ', id_lang, name, description, additional_description, link_rewrite, meta_title, meta_description
                 FROM ' . _DB_PREFIX_ . 'category_lang
                 WHERE id_category = ' . $this->categoryId . ' AND id_shop = 1 AND id_lang = ' . $idLang
            );
        }
        Db::getInstance()->update('category_lang', ['link_rewrite' => 'category-shop-two'], 'id_category = ' . $this->categoryId . ' AND id_shop = ' . $this->secondShopId . ' AND id_lang = ' . $idLang);

        // Bootstrap the request into the FIRST shop.
        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Context::getContext()->shop = new Shop(1);
        Category::resetStaticCache();

        $dispatcher = new ReflectionClass(Dispatcher::class);
        $useRoutes = $dispatcher->getProperty('use_routes');
        $useRoutes->setAccessible(true);
        $useRoutes->setValue(Dispatcher::getInstance(), true);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES);
        Shop::resetContext();
        parent::tearDown();
    }

    public function testCategoryLinkUsesTargetShopLinkRewrite(): void
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $link = new Link();
        $url = $link->getCategoryLink($this->categoryId, null, $idLang, null, $this->secondShopId);

        $this->assertStringContainsString(
            'category-shop-two',
            $url,
            'The category link generated for the second shop must use that shop\'s link_rewrite.'
        );
        $this->assertStringNotContainsString('category-shop-one', $url);
    }

    public function testGetParentsCategoriesResolvesLinkRewriteForTargetShop(): void
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $category = new Category($this->categoryId, $idLang);

        $slugFor = static function (array $rows, int $idCategory): ?string {
            foreach ($rows as $row) {
                if ((int) $row['id_category'] === $idCategory) {
                    return $row['link_rewrite'];
                }
            }

            return null;
        };

        // Context is the first shop; without a target shop we get the context shop's slug.
        $this->assertSame(
            'category-shop-one',
            $slugFor($category->getParentsCategories($idLang), $this->categoryId)
        );

        // Asking for the second shop must resolve that shop's slug.
        $this->assertSame(
            'category-shop-two',
            $slugFor($category->getParentsCategories($idLang, $this->secondShopId), $this->categoryId)
        );
    }

    /**
     * The scenario reported in the issue: a product URL embedding its category path.
     */
    public function testProductGetParentCategoriesResolvesLinkRewriteForTargetShop(): void
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $product = new Product();
        $product->id_category_default = $this->categoryId;

        $slugFor = static function (array $rows, int $idCategory): ?string {
            foreach ($rows as $row) {
                if ((int) $row['id_category'] === $idCategory) {
                    return $row['link_rewrite'];
                }
            }

            return null;
        };

        $this->assertSame(
            'category-shop-one',
            $slugFor($product->getParentCategories($idLang), $this->categoryId)
        );

        $this->assertSame(
            'category-shop-two',
            $slugFor($product->getParentCategories($idLang, $this->secondShopId), $this->categoryId)
        );
    }
}
