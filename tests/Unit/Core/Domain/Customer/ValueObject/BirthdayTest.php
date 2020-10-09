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

namespace Tests\Unit\Core\Domain\Customer\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;

class BirthdayTest extends TestCase
{
    public function testBirthdayCanBeCreatedWithValidDate()
    {
        $birthday = new Birthday('2008-07-31');

        $this->assertEquals('2008-07-31', $birthday->getValue());
    }

    /**
     * @dataProvider getInvalidBirthdays
     */
    public function testItThrowsExceptionWhenCreatingBirthdayWithInvalidData($invalidBirthday)
    {
        $this->expectException(CustomerConstraintException::class);
        $this->expectExceptionCode(CustomerConstraintException::INVALID_BIRTHDAY);

        new Birthday($invalidBirthday);
    }

    public function getInvalidBirthdays()
    {
        yield ['2150-25-100'];
        yield [new \stdClass()];
        yield ['1900-13-33'];
        yield [(new \DateTime('+10 days'))->format('Y-m-d')];
    }
}
