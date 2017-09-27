<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Localization;

use PrestaShopBundle\Localization\Locale;
use PrestaShopBundle\Localization\Manager as LocaleManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LocaleIntegrationTest extends KernelTestCase
{
    /**
     * @var LocaleManager
     */
    protected $localeManager;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->localeManager = $kernel->getContainer()->get('prestashop.cldr.locale.manager');
    }

    /**
     * @param $floatNumber
     * @param $expectedFormats
     *
     * @dataProvider provideValidNumberFormats
     */
    public function testFormatNumber($floatNumber, $expectedFormats)
    {
        $cachedLocales = array();

        foreach ($expectedFormats as $localeCode => $format) {
            if (!isset($cachedLocales[$localeCode])) {
                $cachedLocales[$localeCode] = $this->localeManager->getLocaleByIsoCode($localeCode);
            }
            /** @var Locale $locale */
            $locale = $cachedLocales[$localeCode];
            $this->assertSame(
                $format,
                $locale->formatNumber($floatNumber),
                "For locale $localeCode."
            );
        }
    }

    /**
     * @param $floatNumber
     * @param $currencyCode
     * @param $expectedFormats
     *
     * @dataProvider provideValidCurrencyFormats
     */
    public function testFormatCurrency($floatNumber, $currencyCode, $expectedFormats)
    {
        $cachedLocales    = array();
        $cachedCurrencies = array();

        foreach ($expectedFormats as $localeCode => $format) {
            if (!isset($cachedLocales[$localeCode])) {
                $cachedLocales[$localeCode] = $this->localeManager->getLocaleByIsoCode($localeCode);
            }

            if (!isset($cachedCurrencies[$currencyCode])) {
                $cachedCurrencies[$currencyCode] = $cachedLocales[$localeCode]->getCurrencyByIsoCode($currencyCode);
            }

            /** @var Locale $locale */
            $locale = $cachedLocales[$localeCode];
            $this->assertSame(
                $format,
                $locale->formatCurrency($floatNumber, $currencyCode),
                "For locale $localeCode."
            );
        }
    }

    public function provideValidNumberFormats()
    {
        return array(
            array(
                'number'  => 1234560.1234,
                'formats' => array(
                    'ar-IL' => '1,234,560.123', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,560.123', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.123',
                    'en-US' => '1,234,560.123',
                    'es-AR' => '1.234.560,123',
                    'fr-FR' => '1 234 560,123',
                ),
            ),
            array(
                'number'  => 1234560.9876,
                'formats' => array(
                    'ar-IL' => '1,234,560.988', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,560.988', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.988',
                    'en-US' => '1,234,560.988',
                    'es-AR' => '1.234.560,988',
                    'fr-FR' => '1 234 560,988',
                ),
            ),
            array(
                'number'  => 0.7,
                'formats' => array(
                    'ar-IL' => '0.7', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '0.7', // Needs to be changed when numbering system is configurable
                    'de-CH' => '0.7',
                    'en-US' => '0.7',
                    'es-AR' => '0,7',
                    'fr-FR' => '0,7',
                ),
            ),
            array(
                'number'  => '0.7000',
                'formats' => array(
                    'ar-IL' => '0.7', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '0.7', // Needs to be changed when numbering system is configurable
                    'de-CH' => '0.7',
                    'en-US' => '0.7',
                    'es-AR' => '0,7',
                    'fr-FR' => '0,7',
                ),
            ),
            array(
                'number'  => '1234560.78389',
                'formats' => array(
                    'ar-IL' => '1,234,560.784', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,560.784', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.784',
                    'en-US' => '1,234,560.784',
                    'es-AR' => '1.234.560,784',
                    'fr-FR' => '1 234 560,784',
                ),
            ),
            array(
                'number'  => '1234560.7831111',
                'formats' => array(
                    'ar-IL' => '1,234,560.783', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,560.783', // Needs to be changed when numbering system is configurable
                    'de-CH' => '1’234’560.783',
                    'en-US' => '1,234,560.783',
                    'es-AR' => '1.234.560,783',
                    'fr-FR' => '1 234 560,783',
                ),
            ),
        );
    }

    public function provideValidCurrencyFormats()
    {
        return array(
            array(
                'number'   => 1234560.123,
                'currency' => 'INR',
                'formats'  => array(
                    'ar-IL' => '1,234,560.12 ₹', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,560.12₹', // Needs to be changed when numbering system is configurable
                    'de-CH' => '₹ 1’234’560.12',
                    'en-US' => '₹1,234,560.12',
                    'es-AR' => '₹ 1.234.560,12',
                    'fr-FR' => '1 234 560,12 ₹',
                ),
            ),
            array(
                'number'   => 1234560.789,
                'currency' => 'JPY',
                'formats'  => array(
                    'ar-IL' => '1,234,561 ¥', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,561¥', // Needs to be changed when numbering system is configurable
                    'de-CH' => '¥ 1’234’561',
                    'en-US' => '¥1,234,561',
                    'es-AR' => '¥ 1.234.561',
                    'fr-FR' => '1 234 561 ¥',
                ),
            ),
            array(
                'number'   => '0.7000',
                'currency' => 'GBP',
                'formats'  => array(
                    'ar-IL' => '0.70 £', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '0.70£', // Needs to be changed when numbering system is configurable
                    'de-CH' => '£ 0.70',
                    'en-US' => '£0.70',
                    'es-AR' => '£ 0,70',
                    'fr-FR' => '0,70 £',
                ),
            ),
            array(
                'number'   => '1234560.789898989898123',
                'currency' => 'CHF',
                'formats'  => array(
                    'ar-IL' => '1,234,560.79 CHF', // To be changed when numbering system is configurable
                    'bn-IN' => '1,234,560.79CHF', // To be changed when numbering system is configurable
                    'de-CH' => 'CHF 1’234’560.79',
                    'en-US' => 'CHF1,234,560.79',
                    'es-AR' => 'CHF 1.234.560,79',
                    'fr-FR' => '1 234 560,79 CHF',
                ),
            ),
            array(
                'number'   => 1234560.789,
                'currency' => 'COP',
                'formats'  => array(
                    'ar-IL' => '1,234,561 $', // Needs to be changed when numbering system is configurable
                    'bn-IN' => '1,234,561$', // Needs to be changed when numbering system is configurable
                    'de-CH' => '$ 1’234’561',
                    'en-US' => '$1,234,561',
                    'es-AR' => '$ 1.234.561',
                    'fr-FR' => '1 234 561 $',
                ),
            ),
        );
    }
}
