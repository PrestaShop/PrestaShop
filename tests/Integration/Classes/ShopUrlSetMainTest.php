<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use PHPUnit\Framework\TestCase;
use ShopUrl;
use Tests\Resources\DatabaseDump;

/**
 * A main shop URL has to be active. AdminShopUrlController::processUpdate() already refuses to save one that
 * is not, but setMain() used to promote a disabled URL as it was, leaving the shop with a main URL that could
 * not be reached and that the back office refused to enable again.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/33305
 */
class ShopUrlSetMainTest extends TestCase
{
    private const TABLES_TO_RESTORE = ['shop_url'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
    }

    public static function tearDownAfterClass(): void
    {
        // A broken main URL takes every later test down with it, so put the table back whatever happened.
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        parent::tearDownAfterClass();
    }

    public function testPromotingADisabledUrlEnablesIt(): void
    {
        $url = $this->createDisabledUrl();

        $url->setMain();

        $stored = $this->readRow((int) $url->id);
        $this->assertSame(1, $stored['main']);
        $this->assertSame(1, $stored['active'], 'A URL promoted to main must not stay disabled.');
        $this->assertTrue((bool) $url->active, 'The in-memory object must agree with the row.');
    }

    public function testPromotingADisabledUrlDemotesThePreviousMain(): void
    {
        $previousMainId = (int) Db::getInstance()->getValue(
            'SELECT id_shop_url FROM ' . _DB_PREFIX_ . 'shop_url WHERE id_shop = 1 AND main = 1'
        );
        $url = $this->createDisabledUrl();

        $url->setMain();

        $this->assertSame(0, $this->readRow($previousMainId)['main']);
    }

    public function testAnAlreadyActiveUrlIsUnaffectedApartFromBecomingMain(): void
    {
        $url = $this->createDisabledUrl();
        $url->active = true;
        $url->update();

        $url->setMain();

        $stored = $this->readRow((int) $url->id);
        $this->assertSame(1, $stored['main']);
        $this->assertSame(1, $stored['active']);
    }

    private function createDisabledUrl(): ShopUrl
    {
        $url = new ShopUrl();
        $url->id_shop = 1;
        $url->domain = 'set-main-test.localhost';
        $url->domain_ssl = 'set-main-test.localhost';
        $url->physical_uri = '/';
        $url->virtual_uri = 'setmaintest' . uniqid() . '/';
        $url->main = false;
        $url->active = false;
        $url->add();

        $stored = $this->readRow((int) $url->id);
        $this->assertSame(0, $stored['active'], 'The fixture must start disabled.');

        return $url;
    }

    private function readRow(int $idShopUrl): array
    {
        $row = Db::getInstance()->getRow(
            'SELECT main, active FROM ' . _DB_PREFIX_ . 'shop_url WHERE id_shop_url = ' . $idShopUrl
        );

        // The driver hands these back as native ints on some setups and as strings on others.
        return ['main' => (int) $row['main'], 'active' => (int) $row['active']];
    }
}
