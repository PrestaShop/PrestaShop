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

namespace PrestaShopBundle\Tests\Currency;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Currency\CurrencyFactory;

class CurrencyFactoryTest extends TestCase
{
    const STUB_PARAM_ID             = 123;
    const STUB_PARAM_ISO_CODE       = 'PSD';
    const STUB_PARAM_NUM_ISO_CODE   = 456;
    const STUB_PARAM_SYMBOL_DEFAULT = 'PS$';
    const STUB_PARAM_SYMBOL_NARROW  = '$';
    const STUB_PARAM_NAME_DEFAULT   = 'PrestaShop Dollar';
    const STUB_PARAM_NAME_ONE       = 'PrestaShop dollar';
    const STUB_PARAM_NAME_OTHER     = 'PrestaShop dollars';
    const STUB_PARAM_DIGITS         = 2;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    public function setUp()
    {
        $this->currencyFactory = new CurrencyFactory();
    }

    /**
     * Given a valid CurrencyFactory object
     * When using it to build currency (passing valid currency parameters)
     * The expected currency should be retrieved, with the expected data (coming from the passed currency parameters)
     */
    public function testBuild()
    {
        $currencyParameters = $this->getMockedCurrencyParameters();
        $currency = $this->currencyFactory->build($currencyParameters);
        $this->assertSame(self::STUB_PARAM_ID, $currency->getId(), 'Wrong id');
        $this->assertSame(self::STUB_PARAM_ISO_CODE, $currency->getIsoCode(), 'Wrong ISO code');
        $this->assertSame(self::STUB_PARAM_NUM_ISO_CODE, $currency->getNumericIsoCode(), 'Wrong numeric ISO code');
        $symbol = $currency->getSymbol();
        $this->assertSame(self::STUB_PARAM_SYMBOL_DEFAULT, $symbol->getDefault(), 'Wrong default symbol');
        $this->assertSame(self::STUB_PARAM_SYMBOL_NARROW, $symbol->getNarrow(), 'Wrong narrow symbol');
        $this->assertSame(self::STUB_PARAM_NAME_DEFAULT, $currency->getName('default'), 'Wrong "default" name');
        $this->assertSame(self::STUB_PARAM_NAME_ONE, $currency->getName('one'), 'Wrong "one" name');
        $this->assertSame(self::STUB_PARAM_NAME_OTHER, $currency->getName('other'), 'Wrong "other" name');
        $this->assertSame(self::STUB_PARAM_DIGITS, $currency->getDecimalDigits(), 'Wrong decimal igits number');
    }

    protected function getMockedCurrencyParameters()
    {
        $currencyParameters = $this->getMock('PrestaShopBundle\Currency\CurrencyParameters');
        $currencyParameters->method('getId')
            ->willReturn(self::STUB_PARAM_ID);

        $currencyParameters->method('getIsoCode')
            ->willReturn(self::STUB_PARAM_ISO_CODE);

        $currencyParameters->method('getNumericIsoCode')
            ->willReturn(self::STUB_PARAM_NUM_ISO_CODE);

        $symbol = $this->getMock(
            'PrestaShopBundle\Currency\Symbol',
            array('getDefault', 'getNarrow'),
            array(
                self::STUB_PARAM_SYMBOL_DEFAULT,
                self::STUB_PARAM_SYMBOL_NARROW,
            )
        );
        $symbol->method('getDefault')
            ->willReturn(self::STUB_PARAM_SYMBOL_DEFAULT);
        $symbol->method('getNarrow')
            ->willReturn(self::STUB_PARAM_SYMBOL_NARROW);
        $currencyParameters->method('getSymbol')
            ->willReturn($symbol);

        $currencyParameters->method('getDisplayNameData')
            ->willReturn(array(
                'default' => self::STUB_PARAM_NAME_DEFAULT,
                'one'     => self::STUB_PARAM_NAME_ONE,
                'other'   => self::STUB_PARAM_NAME_OTHER,
            ));

        $currencyParameters->method('getDecimalDigits')
            ->willReturn(self::STUB_PARAM_DIGITS);

        return $currencyParameters;
    }
}
