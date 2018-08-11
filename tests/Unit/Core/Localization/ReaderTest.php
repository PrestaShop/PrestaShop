<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Localization\CLDR;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\Reader;

class ReaderTest extends TestCase
{
    /**
     * CLDR Reader to be tested
     *
     * @var Reader
     */
    protected $reader;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->reader = new Reader();
    }

    /**
     * @dataProvider provideLocaleData
     *
     * @param $localeCode
     * @param $expectedData
     *
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    public function testReadLocaleData($localeCode, $expectedData)
    {
        $localeData = $this->reader->readLocaleData($localeCode);

        $this->assertInstanceOf(LocaleData::class, $localeData);

        $dns = $localeData->defaultNumberingSystem;

        $this->assertEquals(
            $expectedData['defaultNumberingSystem'],
            $dns,
            'Wrong group separator'
        );
        $this->assertEquals(
            $expectedData['digitsGroupSeparator'],
            $localeData->numberSymbols[$dns]->group,
            'Wrong group separator'
        );
        $this->assertEquals(
            $expectedData['decimalSeparator'],
            $localeData->numberSymbols[$dns]->decimal,
            'Wrong decimal separator'
        );
        $this->assertEquals(
            $expectedData['decimalPattern'],
            $localeData->decimalPatterns[$dns],
            'Wrong decimal pattern'
        );
        $this->assertEquals(
            $expectedData['currencyPattern'],
            $localeData->currencyPatterns[$dns],
            'Wrong currency pattern'
        );
    }

    public function provideLocaleData()
    {
        return [
            'root' => [
                'localeCode'   => 'root',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator'   => ',',
                    'decimalSeparator'       => '.',
                    'decimalPattern'         => '#,##0.###',
                    'currencyPattern'        => '¤ #,##0.00',
                ],
            ],
            'fr' => [
                'localeCode'   => 'fr',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator'   => ' ',
                    'decimalSeparator'       => ',',
                    'decimalPattern'         => '#,##0.###',
                    'currencyPattern'        => '#,##0.00 ¤',
                ],
            ],
            'fr-FR' => [
                'localeCode'   => 'fr-FR',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator'   => ' ',
                    'decimalSeparator'       => ',',
                    'decimalPattern'         => '#,##0.###',
                    'currencyPattern'        => '#,##0.00 ¤',
                ],
            ],
            'fr-CH' => [
                'localeCode'   => 'fr-CH',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator'   => ' ',
                    'decimalSeparator'       => ',',
                    'decimalPattern'         => '#,##0.###',
                    'currencyPattern'        => '#,##0.00 ¤ ;-#,##0.00 ¤',
                ],
            ],
            'en-GB' => [
                'localeCode'   => 'en-GB',
                'expectedData' => [
                    'defaultNumberingSystem' => 'latn',
                    'digitsGroupSeparator'   => ',',
                    'decimalSeparator'       => '.',
                    'decimalPattern'         => '#,##0.###',
                    'currencyPattern'        => '¤#,##0.00',
                ],
            ],
        ];
    }
}
