<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Controllers\Front;

use GetFileControllerCore;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetFileControllerTest extends TestCase
{
    private const FILE_SIZE = 1000;

    /**
     * @dataProvider provideRangeHeaders
     *
     * @param array{0: int, 1: int}|false|null $expected
     */
    public function testItReadsASingleByteRange(?string $header, $expected): void
    {
        self::assertSame($expected, $this->parseRange($header, self::FILE_SIZE));
    }

    public function provideRangeHeaders(): iterable
    {
        // null means "serve the whole file", false means the range cannot be satisfied
        yield 'no header' => [null, null];
        yield 'not a byte range' => ['items=0-10', null];
        yield 'malformed' => ['bytes=abc', null];
        yield 'open ended on both sides' => ['bytes=-', null];
        // Several ranges at once are answered with the whole file rather than a multipart response
        yield 'several ranges' => ['bytes=0-10,20-30', null];

        yield 'first hundred bytes' => ['bytes=0-99', [0, 99]];
        yield 'from an offset to the end' => ['bytes=100-', [100, 999]];
        yield 'a suffix' => ['bytes=-500', [500, 999]];
        yield 'a suffix longer than the file' => ['bytes=-5000', [0, 999]];
        yield 'end past the file is clamped' => ['bytes=0-99999', [0, 999]];
        yield 'the very last byte' => ['bytes=999-999', [999, 999]];
        yield 'surrounding spaces are tolerated' => [' bytes=10-20 ', [10, 20]];

        yield 'start past the end' => ['bytes=1000-1100', false];
        yield 'start after end' => ['bytes=50-10', false];
        yield 'empty suffix' => ['bytes=-0', false];
    }

    public function testAnEmptyFileIsAlwaysServedWhole(): void
    {
        self::assertNull($this->parseRange('bytes=0-10', 0));
    }

    /**
     * @return array{0: int, 1: int}|false|null
     */
    private function parseRange(?string $header, int $fileSize)
    {
        $reflection = new ReflectionClass(GetFileControllerCore::class);
        $controller = $reflection->newInstanceWithoutConstructor();
        $method = $reflection->getMethod('parseRangeHeader');
        $method->setAccessible(true);

        return $method->invoke($controller, $header, $fileSize);
    }
}
