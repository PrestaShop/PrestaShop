<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Customer;
use DateTime;
use DateTimeZone;
use Db;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreConfig;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\Resetter\ShopResetter;
use Tools;

/**
 * The timezone is bound once during bootstrap, from the shop resolved out of the URL. Every other
 * shop context - the back office shop selector, the order's shop when an invoice is rendered, the
 * webservice's requested shop - is decided afterwards, so Shop::setContext() has to re-apply it.
 *
 * The two clocks asserted here are the ones every write actually uses: PHP's date(), which is what
 * ObjectModel::add() stores into date_add (classes/ObjectModel.php:529), and the database session
 * offset, which is what NOW() resolves against.
 */
class ShopContextTimezoneTest extends KernelTestCase
{
    private const DEFAULT_SHOP_ID = 1;
    private const SHOP_TIMEZONE = 'Europe/Paris';
    /** +14:00, so it can never coincide with the shop or the runner's timezone. */
    private const OTHER_SHOP_TIMEZONE = 'Pacific/Kiritimati';

    private static int $secondShopId;
    private static string $initialTimezone;
    private static ?int $initialContext;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        // Global var read by legacy code resolving the container (SymfonyContainer::getInstance).
        global $kernel;
        $kernel = self::$kernel;

        self::$initialTimezone = date_default_timezone_get();
        self::$initialContext = Shop::getContext();

        ShopResetter::resetShops();
        Configuration::updateGlobalValue(MultistoreConfig::FEATURE_STATUS, 1);

        $secondShop = new Shop();
        $secondShop->name = 'Shop Context Timezone Shop 2';
        $secondShop->id_shop_group = (int) Shop::getGroupFromShop(self::DEFAULT_SHOP_ID, true);
        $secondShop->id_category = 2;
        $secondShop->active = true;
        $secondShop->save();
        self::$secondShopId = (int) $secondShop->id;

        Shop::resetStaticCache();
        Shop::resetContext();
    }

    public static function tearDownAfterClass(): void
    {
        self::removeShopTimezone(self::$secondShopId);
        ShopResetter::resetShops();

        // Restore the process globals this test moves on purpose, or every later date assertion in
        // the same PHPUnit process inherits them.
        date_default_timezone_set(self::$initialTimezone);
        Db::getInstance()->setTimeZone();
        if (null !== self::$initialContext) {
            Shop::setContext(self::$initialContext, self::DEFAULT_SHOP_ID);
        }

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Configuration::updateGlobalValue('PS_TIMEZONE', self::SHOP_TIMEZONE);
        self::removeShopTimezone(self::$secondShopId);
        Configuration::clearConfigurationCacheForTesting();
        Configuration::loadConfiguration();

        date_default_timezone_set(self::SHOP_TIMEZONE);
        Db::getInstance()->setTimeZone();
    }

    public function testSwitchingToAShopWithItsOwnTimezoneMovesTheProcessClock(): void
    {
        $this->setShopTimezone(self::$secondShopId, self::OTHER_SHOP_TIMEZONE);

        Shop::setContext(Shop::CONTEXT_SHOP, self::DEFAULT_SHOP_ID);
        $this->assertSame(self::SHOP_TIMEZONE, date_default_timezone_get());

        Shop::setContext(Shop::CONTEXT_SHOP, self::$secondShopId);
        $this->assertSame(self::OTHER_SHOP_TIMEZONE, date_default_timezone_get());

        Shop::setContext(Shop::CONTEXT_SHOP, self::DEFAULT_SHOP_ID);
        $this->assertSame(self::SHOP_TIMEZONE, date_default_timezone_get());
    }

    public function testAllShopsContextFallsBackToTheGlobalTimezone(): void
    {
        $this->setShopTimezone(self::$secondShopId, self::OTHER_SHOP_TIMEZONE);

        Shop::setContext(Shop::CONTEXT_SHOP, self::$secondShopId);
        $this->assertSame(self::OTHER_SHOP_TIMEZONE, date_default_timezone_get());

        Shop::setContext(Shop::CONTEXT_ALL);
        $this->assertSame(self::SHOP_TIMEZONE, date_default_timezone_get());
    }

    public function testAShopWithoutItsOwnTimezoneKeepsTheGlobalOne(): void
    {
        Shop::setContext(Shop::CONTEXT_SHOP, self::$secondShopId);

        $this->assertSame(self::SHOP_TIMEZONE, date_default_timezone_get());
    }

    public function testTheDatabaseSessionOffsetFollowsTheShopTimezone(): void
    {
        $this->setShopTimezone(self::$secondShopId, self::OTHER_SHOP_TIMEZONE);

        Shop::setContext(Shop::CONTEXT_SHOP, self::DEFAULT_SHOP_ID);
        $this->assertSame($this->offsetOf(self::SHOP_TIMEZONE), $this->databaseSessionOffset());

        Shop::setContext(Shop::CONTEXT_SHOP, self::$secondShopId);
        $this->assertSame($this->offsetOf(self::OTHER_SHOP_TIMEZONE), $this->databaseSessionOffset());
    }

    public function testAnUnusableTimezoneLeavesTheProcessOnTheOneAlreadyInPlace(): void
    {
        $this->setShopTimezone(self::$secondShopId, 'Not/AZone');

        Shop::setContext(Shop::CONTEXT_SHOP, self::$secondShopId);

        $this->assertSame(self::SHOP_TIMEZONE, date_default_timezone_get());
        $this->assertSame($this->offsetOf(self::SHOP_TIMEZONE), $this->databaseSessionOffset());
    }

    /**
     * The last hop: what a row written under that shop context is actually stamped with.
     */
    public function testARowWrittenUnderAShopContextCarriesThatShopsWallClock(): void
    {
        $this->setShopTimezone(self::$secondShopId, self::OTHER_SHOP_TIMEZONE);
        Shop::setContext(Shop::CONTEXT_SHOP, self::$secondShopId);

        $customer = new Customer();
        $customer->firstname = 'Shop';
        $customer->lastname = 'Timezone';
        $customer->email = 'shop-timezone-' . uniqid() . '@prestashop.test';
        $customer->passwd = Tools::hash('Pr3st4Sh0P');
        $customer->add();

        $writtenAt = $this->asWallClock($customer->date_add);
        $customer->delete();

        $this->assertLessThan(
            60,
            abs($writtenAt->getTimestamp() - $this->wallClockOf(self::OTHER_SHOP_TIMEZONE)->getTimestamp()),
            'date_add should carry the wall clock of the shop the row was written under'
        );
        // And it is genuinely that shop's clock, not the global one it used to be stamped with.
        $this->assertGreaterThan(
            60,
            abs($writtenAt->getTimestamp() - $this->wallClockOf(self::SHOP_TIMEZONE)->getTimestamp()),
            'the two timezones must be far enough apart for the assertion above to mean anything'
        );
    }

    /**
     * date_add is a naive wall-clock string. Anchoring it and the expected clocks to the same
     * fixed zone is what makes them comparable - reading it back in the current default timezone
     * would compare instants, which are equal whatever timezone produced them.
     */
    private function asWallClock(string $naiveDateTime): DateTime
    {
        return new DateTime($naiveDateTime, new DateTimeZone('UTC'));
    }

    private function wallClockOf(string $timezone): DateTime
    {
        return $this->asWallClock((new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d H:i:s'));
    }

    private function setShopTimezone(int $shopId, string $timezone): void
    {
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . "configuration (name, id_shop_group, id_shop, value, date_add, date_upd)
             VALUES ('PS_TIMEZONE', NULL, " . $shopId . ", '" . pSQL($timezone) . "', NOW(), NOW())"
        );
        Configuration::clearConfigurationCacheForTesting();
        Configuration::loadConfiguration();
    }

    private static function removeShopTimezone(int $shopId): void
    {
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . "configuration WHERE name = 'PS_TIMEZONE' AND id_shop = " . $shopId
        );
        Configuration::clearConfigurationCacheForTesting();
        Configuration::loadConfiguration();
    }

    private function databaseSessionOffset(): string
    {
        return (string) Db::getInstance()->getValue('SELECT @@session.time_zone');
    }

    private function offsetOf(string $timezone): string
    {
        return (new DateTime('now', new DateTimeZone($timezone)))->format('P');
    }
}
