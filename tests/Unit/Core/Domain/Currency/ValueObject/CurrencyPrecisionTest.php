<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\Currency\ValueObject;


use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NumericIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;

class CurrencyPrecisionTest extends TestCase
{
    /**
     * @dataProvider getIncorrectPrecision
     */
    public function testItThrowsAnExceptionOnIncorrectIsoCodeRegex($incorrectPrecision)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_PRECISION);

        new Precision($incorrectPrecision);
    }

    public function getIncorrectPrecision()
    {
        return [
            [
                '',
            ],
            [
                'LTUU',
            ],
            [
                'L',
            ],
            [
                null,
            ],
            [
                false,
            ],
            [
                [],
            ],
            [
                '-51',
            ],
            [
                -42,
            ],
        ];
    }

    /**
     * @dataProvider getCorrectNumericIsoCodes
     */
    public function testItReturnsRightPrecision($correctNumericIsoCode, $expectedValue)
    {
        $currencyNumericIsoCode = new Precision($correctNumericIsoCode);

        $this->assertEquals($expectedValue, $currencyNumericIsoCode->getValue());
    }

    public function getCorrectNumericIsoCodes()
    {
        return [
            [
                '008',
                8,
            ],
            [
                '8',
                8,
            ],
            [
                '981',
                981,
            ],
            [
                '49',
                49,
            ],
            [
                '0',
                0,
            ],
            [
                8,
                8,
            ],
            [
                981,
                981,
            ],
            [
                0,
                0,
            ],
        ];
    }
}
