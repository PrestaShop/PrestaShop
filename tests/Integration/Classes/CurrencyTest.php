<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Currency;
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
}
