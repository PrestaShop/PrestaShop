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
use PrestaShopBundle\Currency\CurrencyParameters;
use PrestaShopBundle\Currency\Exception\Exception;

class CurrencyParametersTest extends TestCase
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
     * @var CurrencyParameters
     */
    protected $currencyParameters;

    public function setUp()
    {
        $this->currencyParameters = new CurrencyParameters();

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

        $this->currencyParameters->setId(self::STUB_PARAM_ID)
            ->setIsoCode(self::STUB_PARAM_ISO_CODE)
            ->setNumericIsoCode(self::STUB_PARAM_NUM_ISO_CODE)
            ->setSymbol($symbol)
            ->setDisplayNameData(array(
                'default' => self::STUB_PARAM_NAME_DEFAULT,
                'one'     => self::STUB_PARAM_NAME_ONE,
                'other'   => self::STUB_PARAM_NAME_OTHER,
            ))
            ->setDecimalDigits(self::STUB_PARAM_DIGITS);
    }

    /**
     * Given a valid CurrencyParameters object
     * When validating this data
     * No exception should be raised
     */
    public function testValidateProperties()
    {
        $exception = null;
        try {
            $this->currencyParameters->validateProperties();
        } catch (Exception $exception) {
            // Nothing here. We just want to assert things on (not) caught $exception
            // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171#issuecomment-67645583
        }

        $this->assertNull($exception);
    }

    /**
     * Given a valid CurrencyParameters object, having missing data (that is required)
     * When validating this data
     * An exception should be raised
     *
     * @expectedException Exception
     */
    public function testValidatePropertiesWhenMissingData()
    {
        $incompleteParameters = new CurrencyParameters(); // Obviously, some required data will be missing.
        $incompleteParameters->validateProperties();
    }

    /**
     * Given a valid CurrencyParameters object, having missing (but optional) data
     * When validating this data
     * No exception should be raised
     */
    public function testValidatePropertiesWhenMissingOptionalData()
    {
        $incompleteParameters = new CurrencyParameters();
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
        // We are not setting the id here. id is optional.
        $incompleteParameters->setIsoCode(self::STUB_PARAM_ISO_CODE)
            ->setNumericIsoCode(self::STUB_PARAM_NUM_ISO_CODE)
            ->setSymbol($symbol)
            ->setDisplayNameData(array(
                'default' => self::STUB_PARAM_NAME_DEFAULT,
                'one'     => self::STUB_PARAM_NAME_ONE,
                'other'   => self::STUB_PARAM_NAME_OTHER,
            ))
            ->setDecimalDigits(self::STUB_PARAM_DIGITS);

        $exception = null;
        try {
            $incompleteParameters->validateProperties();
        } catch (Exception $exception) {
            // Nothing here. We just want to assert things on (not) caught $exception
            // @see https://github.com/sebastianbergmann/phpunit-documentation/issues/171#issuecomment-67645583
        }

        $this->assertNull($exception);
    }
}
