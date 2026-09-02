<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
