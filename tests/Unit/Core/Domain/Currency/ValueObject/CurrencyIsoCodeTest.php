<?php
/**
 * 2007-2018 PrestaShop.
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

namespace Tests\Unit\Core\Domain\Currency\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\IsoCode;

/**
 * Class CurrencyIsoCodeTest
 */
class CurrencyIsoCodeTest extends TestCase
{
    /**
     * @dataProvider getIncorrectIsoCodeTypes
     */
    public function testItThrowsAnExceptionOnIncorrectIsoCodeTypePassed($incorrectIsoCodeTypes)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_ISO_CODE_TYPE);

        $currencyIsoCode = new IsoCode($incorrectIsoCodeTypes);
    }

    public function getIncorrectIsoCodeTypes()
    {
        return [
            [
                [],
            ],
            [
                null,
            ],
            [
                false,
            ]
        ];
    }

    /**
     * @dataProvider getIncorrectIsoCodes
     */
    public function testItThrowsAnExceptionOnIncorrectIsoCodeRegex($incorrectIsoCode)
    {
        $this->expectException(CurrencyConstraintException::class);
        $this->expectExceptionCode(CurrencyConstraintException::INVALID_ISO_CODE);

        $currencyIsoCode = new IsoCode($incorrectIsoCode);
    }

    public function getIncorrectIsoCodes()
    {
        return [
            [
                '',
            ],
            [
                'LTUU',
            ],
            [
                '12345',
            ],
            [
                'L',
            ]
        ];
    }

    public function testItReturnsRightIsoCode()
    {
        $currencyIsoCode = new IsoCode('LTU');

        $this->assertEquals('LTU', $currencyIsoCode->getValue());
    }
}
