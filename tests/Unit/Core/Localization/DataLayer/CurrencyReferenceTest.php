<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Localization\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Currency as CldrCurrency;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyData as CldrCurrencyData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\CurrencyInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleInterface as CldrLocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository as CldrLocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\CurrencyData;
use PrestaShop\PrestaShop\Core\Localization\Currency\DataLayer\CurrencyReference as CurrencyReferenceDataLayer;
use PrestaShop\PrestaShop\Core\Localization\Currency\LocalizedCurrencyId;

class CurrencyReferenceTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CurrencyReferenceDataLayer
     */
    protected $currencyReference;

    protected function setUp(): void
    {
        $stubCurrencyData = new CldrCurrencyData();
        $stubCurrencyData
            ->setIsoCode('PCE')
            ->setSymbols([CurrencyInterface::SYMBOL_TYPE_DEFAULT => 'test'])
            ->setDisplayNames([CurrencyInterface::DISPLAY_NAME_COUNT_DEFAULT => 'test'])
        ;
        $stubCldrCurrency = new CldrCurrency($stubCurrencyData);

        $stubLocale = $this->createMock(CldrLocaleInterface::class);
        $stubLocale
            ->method('getCurrency')
            ->willReturnMap([
                ['PCE', $stubCldrCurrency],
                ['unknown', null],
            ]);

        $cldrLocaleRepo = $this->getMockBuilder(CldrLocaleRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getLocale'])
            ->getMock();
        $cldrLocaleRepo->method('getLocale')
            ->willReturnMap([
                ['fr-FR', $stubLocale],
            ]);

        /* @var CldrLocaleRepository $cldrLocaleRepo */
        $this->currencyReference = new CurrencyReferenceDataLayer($cldrLocaleRepo);
    }

    /**
     * Given a valid CurrencyReference data layer
     * When asking for CurrencyData of a valid currency code
     * Then the expected CurrencyData object should be retrieved (or null if not found)
     */
    public function testRead(): void
    {
        /** @phpstan-ignore-next-line */
        $currencyData = $this->currencyReference->read(new LocalizedCurrencyId('PCE', 'fr-FR'));

        /* @var CurrencyData $currencyData */
        $this->assertInstanceOf(
            CurrencyData::class,
            $currencyData
        );

        $this->assertSame(
            'PCE',
            $currencyData->getIsoCode()
        );

        // Same test with unknown cache key
        /** @phpstan-ignore-next-line */
        $currencyData = $this->currencyReference->read(new LocalizedCurrencyId('unknown', 'unknown'));

        $this->assertNull($currencyData);
    }
}
