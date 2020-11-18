<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\Language\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;

class IsoCodeTest extends TestCase
{
    /**
     * @dataProvider getValidTwoLetterIsoCodes
     */
    public function testIsoCodeCanBeCreatedWithValidTwoLetterIsoCode($twoLetterIsoCode, $expectedIsoCodeValue)
    {
        $isoCode = new IsoCode($twoLetterIsoCode);

        $this->assertEquals($expectedIsoCodeValue, $isoCode->getValue());
    }

    /**
     * @dataProvider getInvalidCodes
     */
    public function testIsoCodeCannotBeCreatedWithInvalidValue($invalidIsoCode)
    {
        $this->expectException(LanguageConstraintException::class);

        new IsoCode($invalidIsoCode);
    }

    public function getValidTwoLetterIsoCodes()
    {
        yield ['lt', 'lt'];
        yield ['fr', 'fr'];
        yield ['GB', 'gb'];
        yield ['SW', 'sw'];
    }

    public function getInvalidCodes()
    {
        yield [''];
        yield ['12'];
        yield [23];
        yield ['?!'];
        yield [null];
    }
}
