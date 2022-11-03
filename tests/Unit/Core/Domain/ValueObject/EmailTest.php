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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Core\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Class EmailTest
 */
class EmailTest extends TestCase
{
    /**
     * @dataProvider getValidEmailValues
     */
    public function testItCreatesEmailWithValidValues($validEmail)
    {
        $email = new Email($validEmail);

        $this->assertEquals($validEmail, $email->getValue());
    }

    /**
     * @dataProvider getInvalidEmailValues
     */
    public function testItThrowsExceptionWhenCreatingEmailWithInvalidValue($invalidEmail)
    {
        $this->expectException(DomainConstraintException::class);
        $this->expectExceptionCode(DomainConstraintException::INVALID_EMAIL);

        new Email($invalidEmail);
    }

    /**
     * @dataProvider getEmailCompareValues
     */
    public function testEmailComparesValuesCorrectly($firstEmail, $secondEmail, $expectedCompareResult)
    {
        $this->assertEquals($expectedCompareResult, (new Email($firstEmail))->isEqualTo(new Email($secondEmail)));
    }

    public function getValidEmailValues()
    {
        yield ['demo.demo@prestashop.com'];
        yield ['12312321@123.com'];
        yield ['abc_123o@a.eu'];
    }

    public function getInvalidEmailValues()
    {
        yield [''];
        yield [123];
        yield [sprintf('very_long_email_%s@demo.com', str_repeat('A', 231))];
    }

    public function getEmailCompareValues()
    {
        yield ['demo@demo.com', 'demo@demo.com', true];
        yield ['demo@demo.com', 'no_the_same@demo.com', false];
    }
}
