<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Currency;

use Configuration;
use Currency;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CurrencyDataProviderTest extends KernelTestCase
{
    private int $disabledCurrencyId;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $currency = new Currency();
        $currency->iso_code = 'EUR';
        $currency->numeric_iso_code = '978';
        $currency->precision = 2;
        $currency->conversion_rate = 1;
        $currency->active = false;
        $currency->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Euro'];
        $currency->add();
        $this->disabledCurrencyId = (int) $currency->id;
        Currency::resetStaticCache();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['currency', 'currency_lang', 'currency_shop']);
        Currency::resetStaticCache();
        parent::tearDown();
    }

    public function testADisabledCurrencyIsReturnedWhenNotOnlyActiveOnesAreAskedFor(): void
    {
        /** @var CurrencyDataProvider $provider */
        $provider = self::getContainer()->get('prestashop.adapter.data_provider.currency');

        $this->assertContains($this->disabledCurrencyId, $this->getIds($provider->getCurrencies(false, false, true)));
        $this->assertNotContains($this->disabledCurrencyId, $this->getIds($provider->getCurrencies(false, true, true)));
    }

    /**
     * @param array<int, array<string, mixed>> $currencies
     *
     * @return int[]
     */
    private function getIds(array $currencies): array
    {
        return array_map('intval', array_column($currencies, 'id_currency'));
    }
}
