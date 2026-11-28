<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Validate;

/**
 * Casting a small float to string gives scientific notation, so 0.00002 arrived at the price patterns
 * as "2.0E-5" and was refused while 0.0002 was accepted.
 */
class ValidatePriceNotationTest extends TestCase
{
    /**
     * @dataProvider providePrices
     */
    public function testIsPrice(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, (bool) Validate::isPrice($value));
    }

    /**
     * @dataProvider provideNegativePrices
     */
    public function testIsNegativePrice(mixed $value, bool $expected): void
    {
        $this->assertSame($expected, (bool) Validate::isNegativePrice($value));
    }

    /**
     * @return iterable<string, array{0: mixed, 1: bool}>
     */
    public static function providePrices(): iterable
    {
        yield 'a float small enough to stringify as an exponent' => [0.00002, true];
        yield 'the same value written as a string' => ['0.00002', true];
        yield 'a float just above the exponent threshold' => [0.0002, true];
        yield 'zero' => [0.0, true];
        yield 'an ordinary float' => [1.5, true];
        yield 'a negative value' => [-0.00002, false];
        yield 'more decimals than the pattern allows' => [0.0000000001, false];
        yield 'more integer digits than the pattern allows' => [12345678901.0, false];
        yield 'not a number at all' => ['abc', false];
    }

    /**
     * @return iterable<string, array{0: mixed, 1: bool}>
     */
    public static function provideNegativePrices(): iterable
    {
        yield 'a float small enough to stringify as an exponent' => [0.00002, true];
        yield 'its negative counterpart' => [-0.00002, true];
        yield 'the same value written as a string' => ['-0.00002', true];
        yield 'zero' => [0.0, true];
        yield 'more decimals than the pattern allows' => [-0.0000000001, false];
        yield 'not a number at all' => ['abc', false];
    }
}
