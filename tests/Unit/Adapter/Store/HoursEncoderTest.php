<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Store;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Store\HoursEncoder;

class HoursEncoderTest extends TestCase
{
    private HoursEncoder $hoursEncoder;

    protected function setUp(): void
    {
        $this->hoursEncoder = new HoursEncoder();
    }

    /**
     * @dataProvider provideEncodeCases
     *
     * @param array<int, array<int, string>> $localizedHours
     * @param array<int, string> $expected
     */
    public function testItEncodesLocalizedHoursToJson(array $localizedHours, array $expected): void
    {
        $this->assertSame($expected, $this->hoursEncoder->encode($localizedHours));
    }

    /**
     * @return iterable<string, array{array<int, array<int, string>>, array<int, string>}>
     */
    public static function provideEncodeCases(): iterable
    {
        yield 'empty input' => [
            [],
            [],
        ];

        yield 'open/close split on pipe' => [
            [1 => ['09:00 | 18:00']],
            [1 => json_encode([['09:00', '18:00']])],
        ];

        yield 'trims surrounding whitespace' => [
            [1 => ['  09:00  |  18:00  ']],
            [1 => json_encode([['09:00', '18:00']])],
        ];

        yield 'value without pipe is kept as single part' => [
            [1 => ['closed']],
            [1 => json_encode([['closed']])],
        ];

        yield 'multiple languages and days' => [
            [
                1 => ['09:00 | 18:00', ''],
                2 => ['10:00 | 20:00'],
            ],
            [
                1 => json_encode([['09:00', '18:00'], ['']]),
                2 => json_encode([['10:00', '20:00']]),
            ],
        ];

        yield 'multiple languages and days in various formats' => [
            [
                1 => ['09:00 | 18:00', ''],
                2 => ['10:00 | 20:00'],
                3 => ['nope'],
            ],
            [
                1 => json_encode([['09:00', '18:00'], ['']]),
                2 => json_encode([['10:00', '20:00']]),
                3 => json_encode([['nope']]),
            ],
        ];
    }

    /**
     * @dataProvider provideDecodeCases
     *
     * @param array<int, string> $rawHours
     * @param array<int, array<int, string>> $expected
     */
    public function testItDecodesJsonHoursToLocalizedStrings(array $rawHours, array $expected): void
    {
        $this->assertSame($expected, $this->hoursEncoder->decode($rawHours));
    }

    /**
     * @return iterable<string, array{array<int, string>, array<int, array<int, string>>}>
     */
    public static function provideDecodeCases(): iterable
    {
        yield 'empty input' => [
            [],
            [],
        ];

        yield 'empty json string falls back to 7 empty days' => [
            [1 => ''],
            [1 => array_fill(0, 7, '')],
        ];

        yield 'invalid json falls back to 7 empty days' => [
            [1 => 'not-json'],
            [1 => array_fill(0, 7, '')],
        ];

        yield 'new format open/close joined with pipe' => [
            [1 => json_encode([['09:00', '18:00']])],
            [1 => ['09:00 | 18:00']],
        ];

        yield 'new format with empty bound uses open value only' => [
            [1 => json_encode([['09:00', '']])],
            [1 => ['09:00']],
        ];

        yield 'legacy single-element format kept as-is' => [
            [1 => json_encode([['09:00AM - 07:00PM']])],
            [1 => ['09:00AM - 07:00PM']],
        ];

        yield 'unexpected day shape becomes empty string' => [
            [1 => json_encode(['unexpected'])],
            [1 => ['']],
        ];
    }

    /**
     * @dataProvider provideRoundTripCases
     *
     * @param array<int, array<int, string>> $localizedHours
     */
    public function testEncodeThenDecodeIsStable(array $localizedHours): void
    {
        $encoded = $this->hoursEncoder->encode($localizedHours);

        $this->assertSame($localizedHours, $this->hoursEncoder->decode($encoded));
    }

    /**
     * @return iterable<string, array{array<int, array<int, string>>}>
     */
    public static function provideRoundTripCases(): iterable
    {
        yield 'open/close pairs' => [
            [1 => ['09:00 | 18:00', '10:00 | 20:00']],
        ];

        yield 'multiple languages' => [
            [
                1 => ['09:00 | 18:00'],
                2 => ['08:30 | 17:30'],
            ],
        ];
    }
}
