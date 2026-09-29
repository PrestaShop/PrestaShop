<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use CMS;
use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CmsTest extends KernelTestCase
{
    private const ID_CMS = 1;
    private const ID_LANG_EN = 1;
    private const ID_LANG_FR = 2;

    private static int $idShop2;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$idShop2 = self::initMultistore();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreAllTables();
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * When $idLang is null, the default language must be resolved for the
     * TARGET shop ($idShop), not for the current context shop.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/29519
     */
    public function testGetCMSContentResolvesDefaultLanguageOfTargetShop(): void
    {
        // Shop 2 uses French as its default language; the current context (shop 1) uses English.
        LegacyConfiguration::updateValue('PS_LANG_DEFAULT', self::ID_LANG_FR, false, null, self::$idShop2);

        // Distinguishable CMS content per language for shop 2.
        $this->insertCmsContent(self::ID_CMS, self::ID_LANG_EN, self::$idShop2, 'EN content for shop 2');
        $this->insertCmsContent(self::ID_CMS, self::ID_LANG_FR, self::$idShop2, 'FR content for shop 2');

        // Browse in shop 1 context (default language English).
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

        // Request the content of shop 2 without specifying a language: it must fall back
        // to shop 2's default language (French), not the current shop's default (English).
        $content = CMS::getCMSContent(self::ID_CMS, null, self::$idShop2);

        $this->assertIsArray($content);
        $this->assertSame('FR content for shop 2', $content['content']);
    }

    private function insertCmsContent(int $idCms, int $idLang, int $idShop, string $content): void
    {
        Db::getInstance()->insert('cms_lang', [
            'id_cms' => $idCms,
            'id_lang' => $idLang,
            'id_shop' => $idShop,
            'meta_title' => 'test',
            'content' => pSQL($content, true),
        ], false, true, Db::REPLACE);
    }

    private static function initMultistore(): int
    {
        DatabaseDump::restoreAllTables();
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        // activate multistore
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        // add a shop in existing group
        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_2');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);

        $entityManager->persist($shop);
        $entityManager->flush();

        LegacyShop::resetStaticCache();

        return (int) $shop->getId();
    }
}
