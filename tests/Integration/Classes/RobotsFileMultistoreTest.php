<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use ShopUrl;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use Tools;

class RobotsFileMultistoreTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_url', 'configuration'];

    private const SHOP2_DOMAIN = 'shop-b.robots-test.example';

    private static int $secondShopId;

    /** @var list<string> */
    private static array $createdFiles = [];

    private static ?string $robotsTxtBackup = null;

    private static bool $robotsTxtExisted = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        ShopUrl::resetMainDomainCache();

        $robotsTxt = _PS_ROOT_DIR_ . '/robots.txt';
        self::$robotsTxtExisted = is_file($robotsTxt);
        self::$robotsTxtBackup = self::$robotsTxtExisted ? (string) file_get_contents($robotsTxt) : null;

        self::bootKernel();

        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);
        $configuration->set('PS_REWRITING_SETTINGS', 1);

        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_robots');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('blue');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();
        self::$secondShopId = (int) $shop->getId();

        LegacyShop::resetStaticCache();

        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = self::$secondShopId;
        $shopUrl->domain = self::SHOP2_DOMAIN;
        $shopUrl->domain_ssl = self::SHOP2_DOMAIN;
        $shopUrl->physical_uri = '/';
        $shopUrl->virtual_uri = '';
        $shopUrl->main = true;
        $shopUrl->active = true;
        $shopUrl->add();

        ShopUrl::resetMainDomainCache();
        LegacyShop::resetStaticCache();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        foreach (self::$createdFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        foreach (glob(_PS_ROOT_DIR_ . '/robots-*.txt') ?: [] as $file) {
            if (str_contains(basename($file), 'robots-test.example') && is_file($file)) {
                @unlink($file);
            }
        }

        $robotsTxt = _PS_ROOT_DIR_ . '/robots.txt';
        if (self::$robotsTxtExisted) {
            file_put_contents($robotsTxt, (string) self::$robotsTxtBackup);
        } elseif (is_file($robotsTxt)) {
            @unlink($robotsTxt);
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
        ShopUrl::resetMainDomainCache();
    }

    public function testGenerateRobotsFileWritesPerDomainFilesWithCorrectSitemaps(): void
    {
        self::bootKernel();

        $idShop1 = (int) LegacyConfiguration::get('PS_SHOP_DEFAULT');
        $domain1 = ShopUrl::getMainShopDomain($idShop1);
        $this->assertNotEmpty($domain1);

        $sitemap1 = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . $idShop1 . '_index_sitemap.xml';
        $sitemap2 = _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR . self::$secondShopId . '_index_sitemap.xml';
        file_put_contents($sitemap1, '<?xml version="1.0"?><urlset></urlset>');
        file_put_contents($sitemap2, '<?xml version="1.0"?><urlset></urlset>');
        self::$createdFiles[] = $sitemap1;
        self::$createdFiles[] = $sitemap2;

        $this->assertTrue(Tools::generateRobotsFile(false));

        $file1 = _PS_ROOT_DIR_ . '/' . Tools::getRobotsFileNameForDomain($domain1);
        $file2 = _PS_ROOT_DIR_ . '/' . Tools::getRobotsFileNameForDomain(self::SHOP2_DOMAIN);
        self::$createdFiles[] = $file1;
        self::$createdFiles[] = $file2;

        $this->assertFileExists($file1);
        $this->assertFileExists($file2);

        $content1 = (string) file_get_contents($file1);
        $content2 = (string) file_get_contents($file2);

        $sitemapLine1 = 'Sitemap: ' . Tools::getProtocol((bool) LegacyConfiguration::get('PS_SSL_ENABLED', null, null, $idShop1))
            . $domain1 . '/' . $idShop1 . '_index_sitemap.xml';
        $sitemapLine2 = 'Sitemap: ' . Tools::getProtocol((bool) LegacyConfiguration::get('PS_SSL_ENABLED', null, null, self::$secondShopId))
            . self::SHOP2_DOMAIN . '/' . self::$secondShopId . '_index_sitemap.xml';

        $this->assertStringContainsString($sitemapLine1, $content1);
        $this->assertStringNotContainsString(self::SHOP2_DOMAIN, $content1);
        $this->assertStringNotContainsString($sitemapLine2, $content1);

        $this->assertStringContainsString($sitemapLine2, $content2);
        $this->assertStringNotContainsString($sitemapLine1, $content2);

        $this->assertStringContainsString('Disallow: ', $content1);
        $this->assertStringContainsString('Disallow: ', $content2);
        $this->assertStringContainsString('# Directories blocked from crawling', $content1);
        $this->assertStringContainsString('# Directories blocked from crawling', $content2);
    }

    public function testInactiveShopUrlIsIgnored(): void
    {
        self::bootKernel();

        $inactiveDomain = 'inactive.robots-test.example';
        Db::getInstance()->insert('shop_url', [
            'id_shop' => self::$secondShopId,
            'domain' => $inactiveDomain,
            'domain_ssl' => $inactiveDomain,
            'physical_uri' => '/',
            'virtual_uri' => '',
            'main' => 0,
            'active' => 0,
        ]);
        ShopUrl::resetMainDomainCache();
        LegacyShop::resetStaticCache();

        Tools::generateRobotsFile(false);

        $inactiveFile = _PS_ROOT_DIR_ . '/' . Tools::getRobotsFileNameForDomain($inactiveDomain);
        $this->assertFileDoesNotExist($inactiveFile);

        Db::getInstance()->delete('shop_url', 'domain = \'' . pSQL($inactiveDomain) . '\'');
        ShopUrl::resetMainDomainCache();
        LegacyShop::resetStaticCache();
    }
}
