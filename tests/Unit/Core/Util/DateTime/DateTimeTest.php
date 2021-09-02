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

declare(strict_types=1);

namespace Tests\Unit\Core\Util\DateTime;

use DateTime as NativeDateTime;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

class DateTimeTest extends TestCase
{
    private const DATE_SAMPLE = '1969-08-16';

    /**
     * @dataProvider getDates
     *
     * @param string|null $input
     * @param NativeDateTime|null $expectedOutput
     */
    public function testGetNullableDate(?string $input, ?NativeDateTime $expectedOutput): void
    {
        $output = DateTime::getNullableDate($input);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getDates(): iterable
    {
        yield [null, null];
        yield ['', null];
        yield ['0', null];
        yield [DateTime::NULL_DATE, null];
        yield [DateTime::NULL_DATETIME, null];
        yield [self::DATE_SAMPLE, new NativeDateTime(self::DATE_SAMPLE)];
    }

    /**
     * @dataProvider getDateTimes
     *
     * @param string|null $input
     * @param NativeDateTime|null $expectedOutput
     */
    public function testGetNullableDateTime(?string $input, ?NativeDateTime $expectedOutput): void
    {
        $output = DateTime::getNullableDateTime($input);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getDateTimes(): iterable
    {
        yield [null, null];
        yield ['', null];
        yield ['0', null];
        yield [DateTime::NULL_DATE, null];
        yield [DateTime::NULL_DATETIME, null];
        yield [self::DATE_SAMPLE, new NativeDateTime(self::DATE_SAMPLE)];
    }
}
