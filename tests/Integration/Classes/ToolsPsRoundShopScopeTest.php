<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Db;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use Tools;

class ToolsPsRoundShopScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_group', 'configuration'];

    /**
     * 1.999 separates the two modes used here: rounding half up keeps 2.0, rounding down keeps 1.99.
     */
    private const VALUE = 1.999;
    private const PRECISION = 2;

    private static int $secondShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $configuration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();
        $db->insert('shop_group', [
            'name' => 'test_group_round', 'color' => '', 'share_customer' => 0,
            'share_order' => 0, 'share_stock' => 0, 'active' => 1, 'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId, 'name' => 'test_shop_round', 'color' => '',
            'id_category' => 2, 'theme_name' => 'classic', 'active' => 1, 'deleted' => 0,
        ]);
        self::$secondShopId = (int) $db->Insert_ID();
        LegacyShop::resetStaticCache();

        LegacyConfiguration::updateValue('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_UP, false, null, 1);
        LegacyConfiguration::updateValue('PS_PRICE_ROUND_MODE', PS_ROUND_DOWN, false, null, self::$secondShopId);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        LegacyShop::setContext(LegacyShop::CONTEXT_ALL);
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    protected function setUp(): void
    {
        parent::setUp();
        // Start from the state a fresh process has, so a reintroduced cache is measured on this
        // test's own calls rather than on a value another test left behind.
        Tools::$round_mode = null;
    }

    /**
     * PS_PRICE_ROUND_MODE is shop scoped, so two shops configured differently must round
     * differently in the same process. ps_round() used to resolve the mode once into a static and
     * keep it for the rest of the request, so whichever shop was served first imposed its mode on
     * every shop after it.
     */
    public function testItRoundsWithTheModeOfTheShopInContext(): void
    {
        self::bootKernel();

        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);
        $firstShop = Tools::ps_round(self::VALUE, self::PRECISION);

        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, self::$secondShopId);
        $secondShop = Tools::ps_round(self::VALUE, self::PRECISION);

        // The fixture is only meaningful while the two shops disagree.
        $this->assertSame('2', (string) LegacyConfiguration::get('PS_PRICE_ROUND_MODE', null, null, 1));
        $this->assertSame('1', (string) LegacyConfiguration::get('PS_PRICE_ROUND_MODE', null, null, self::$secondShopId));

        $this->assertSame(2.0, $firstShop, 'Shop 1 rounds half up.');
        $this->assertSame(1.99, $secondShop, 'The second shop rounds down.');
    }

    /**
     * Same defect from the other side: whichever shop is served first must not win. Reversing the
     * order has to reverse the results, otherwise only one of the two shops is actually resolved.
     */
    public function testTheShopServedFirstDoesNotImposeItsMode(): void
    {
        self::bootKernel();

        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, self::$secondShopId);
        $secondShop = Tools::ps_round(self::VALUE, self::PRECISION);

        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);
        $firstShop = Tools::ps_round(self::VALUE, self::PRECISION);

        $this->assertSame(1.99, $secondShop, 'The second shop rounds down.');
        $this->assertSame(2.0, $firstShop, 'Shop 1 rounds half up.');
    }

    /**
     * The mode also has to be re-read after it is changed, rather than staying at the value the
     * process happened to resolve first.
     */
    public function testItFollowsAConfigurationChange(): void
    {
        self::bootKernel();
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

        $this->assertSame(2.0, Tools::ps_round(self::VALUE, self::PRECISION));

        LegacyConfiguration::updateValue('PS_PRICE_ROUND_MODE', PS_ROUND_DOWN, false, null, 1);
        $this->assertSame(1.99, Tools::ps_round(self::VALUE, self::PRECISION));

        LegacyConfiguration::updateValue('PS_PRICE_ROUND_MODE', PS_ROUND_HALF_UP, false, null, 1);
        $this->assertSame(2.0, Tools::ps_round(self::VALUE, self::PRECISION));
    }
}
