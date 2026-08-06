<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Country\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CallPrefix;

class CallPrefixTest extends TestCase
{
    /**
     * @dataProvider validCallPrefixProvider
     */
    public function testItAcceptsNonNegativeIntegers(int $callPrefix): void
    {
        $this->assertSame($callPrefix, (new CallPrefix($callPrefix))->getValue());
    }

    /**
     * @return iterable<array{int}>
     */
    public static function validCallPrefixProvider(): iterable
    {
        yield 'zero' => [0];
        yield 'single digit' => [1];
        yield 'two digits' => [33];
        yield 'three digits' => [262];
    }

    /**
     * @dataProvider invalidCallPrefixProvider
     */
    public function testItRejectsNegativeIntegers(int $callPrefix): void
    {
        $this->expectException(CountryConstraintException::class);
        $this->expectExceptionCode(CountryConstraintException::INVALID_CALL_PREFIX);

        new CallPrefix($callPrefix);
    }

    /**
     * @return iterable<array{int}>
     */
    public static function invalidCallPrefixProvider(): iterable
    {
        yield 'negative value' => [-1];
        yield 'large negative value' => [-99];
    }
}
