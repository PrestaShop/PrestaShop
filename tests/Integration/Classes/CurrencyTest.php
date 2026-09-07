<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Currency;
use Db;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class CurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreAllTables();
    }

    public function testGetIdByIsoCode(): void
    {
        $this->assertEquals(0, Currency::getIdByIsoCode('ZZZ', 0, false));
        $this->assertEquals(0, Currency::getIdByIsoCode('ZZZ', 0, true));

        $currency = new Currency();
        $currency->name = 'ZZZ';
        $currency->precision = 2;
        $currency->iso_code = 'ZZZ';
        $currency->active = 1;
        $currency->conversion_rate = 1.00;
        $currency->add();

        $idByIsoCode = Currency::getIdByIsoCode('ZZZ', 0, false);
        $this->assertNotEquals(0, $idByIsoCode);
        $this->assertIsInt($idByIsoCode);

        $idByIsoCode = Currency::getIdByIsoCode('ZZZ', 0, true);
        $this->assertNotEquals(0, $idByIsoCode);
        $this->assertIsInt($idByIsoCode);
    }

    public function testGetCurrenciesByIdShopExcludesSoftDeletedCurrencies(): void
    {
        $shopId = (int) Context::getContext()->shop->id;

        $currency = new Currency();
        $currency->name = 'ZZZ';
        $currency->symbol = 'ZZZ';
        $currency->precision = 2;
        $currency->iso_code = 'ZZZ';
        $currency->active = 1;
        $currency->conversion_rate = 1.10;
        $this->assertTrue($currency->add());
        $currencyId = (int) $currency->id;

        $this->assertContains($currencyId, $this->getCurrencyIdsForShop($shopId));

        $currency->delete();

        // The shop association survives the soft delete, so the join alone still matches the row.
        $this->assertNotEmpty(Db::getInstance()->executeS(
            'SELECT `id_currency` FROM `' . _DB_PREFIX_ . 'currency_shop` WHERE `id_currency` = ' . $currencyId
        ));
        $this->assertNotContains($currencyId, $this->getCurrencyIdsForShop($shopId));
        $this->assertNotContains($currencyId, $this->getCurrencyIdsForShop(0));
    }

    /**
     * @return int[]
     */
    private function getCurrencyIdsForShop(int $shopId): array
    {
        return array_map(
            static function (array $currency): int {
                return (int) $currency['id_currency'];
            },
            Currency::getCurrenciesByIdShop($shopId)
        );
    }
}
