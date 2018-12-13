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

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\ApeCode;

class ApeCodeTest extends TestCase
{
    /**
     * @dataProvider getValidApeCodes
     */
    public function testItCreatesApeCodeWithValidValue($code)
    {
        $apeCode = new ApeCode($code);

        $this->assertEquals($code, $apeCode->getValue());
    }

    /**
     * @dataProvider getInvalidApeCodes
     */
    public function testItThrowExceptionWhenCreatingApeCodeWithInvalidValue($code)
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_APE_CODE);

        new ApeCode($code);
    }

    public function getValidApeCodes()
    {
        yield ['001A'];
        yield ['1039B'];
    }

    public function getInvalidApeCodes()
    {
        yield ['not_valid'];
        yield ['1236'];
        yield [123];
        yield [''];
        yield [[]];
    }
}
