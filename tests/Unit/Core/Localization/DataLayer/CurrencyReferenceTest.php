<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            ->setMethods(['getLocale'])
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
