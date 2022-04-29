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

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

class DateTimeTest extends TestCase
{
    private const DATE_SAMPLE = '1969-08-16';
    private const DATE_TIME_SAMPLE = '1969-08-16 15:45:18';

    /**
     * @dataProvider getNullableDates
     *
     * @param string|null $input
     */
    public function testBuildNullDateTime(?string $input): void
    {
        $output = DateTime::buildNullableDateTime($input);
        $this->assertInstanceOf(NullDateTime::class, $output);
    }

    /**
     * @dataProvider getNullableDates
     *
     * @param string|null $input
     */
    public function testBuildDateTimeOrNull(?string $input): void
    {
        $output = DateTime::buildDateTimeOrNull($input);
        $this->assertNull($output);
    }

    public function getNullableDates(): iterable
    {
        yield [null];
        yield [''];
        yield ['0'];
        yield [DateTime::NULL_DATE];
        yield [DateTime::NULL_DATETIME];
    }

    /**
     * @dataProvider getDates
     *
     * @param string|null $input
     * @param DateTimeImmutable $expectedOutput
     */
    public function testBuildDateTime(?string $input, DateTimeImmutable $expectedOutput): void
    {
        $output = DateTime::buildNullableDateTime($input);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getDates(): iterable
    {
        yield [self::DATE_SAMPLE, new DateTimeImmutable(self::DATE_SAMPLE)];
        yield [self::DATE_TIME_SAMPLE, new DateTimeImmutable(self::DATE_TIME_SAMPLE)];
    }

    /**
     * @dataProvider getCheckedValues
     *
     * @param string|null $input
     * @param bool $isNull
     */
    public function testIsNull($input, bool $isNull): void
    {
        $this->assertEquals($isNull, DateTime::isNull($input));
    }

    public function getCheckedValues(): iterable
    {
        yield [null, true];
        yield ['', true];
        yield ['0', true];
        yield [0, true];
        yield [new NullDateTime(), true];
        yield [new DateTimeImmutable(self::DATE_SAMPLE), false];
        yield [new DateTimeImmutable(self::DATE_TIME_SAMPLE), false];
    }
}
