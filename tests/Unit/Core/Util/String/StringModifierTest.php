<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Util\String;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\String\StringModifier;

class StringModifierTest extends TestCase
{
    /**
     * @var StringModifier
     */
    private $stringModifier;

    public function setUp(): void
    {
        $this->stringModifier = new StringModifier();
    }

    public function testItTransformsCamelCaseToSplitWords(): void
    {
        $data = [
            [
                'string' => 'oneTwoThreeFour',
                'expects' => 'one Two Three Four',
            ],
            [
                'string' => 'StartsWithCap',
                'expects' => 'Starts With Cap',
            ],
            [
                'string' => 'hasConsecutiveCAPS',
                'expects' => 'has Consecutive CAPS',
            ],
            [
                'string' => 'NewMODULEDevelopment',
                'expects' => 'New MODULE Development',
            ],
            [
                'string' => 'snake_case_word',
                'expects' => 'snake_case_word',
            ],
        ];

        foreach ($data as $item) {
            $result = $this->stringModifier->splitByCamelCase($item['string']);

            $this->assertEquals($item['expects'], $result);
        }
    }

    /**
     * @dataProvider getTooLongStringsForEndCutting
     *
     * @param string $string
     * @param int $length
     * @param string $expectedOutput
     */
    public function testItCutsStringEndIfItIsTooLong(string $string, int $length, string $expectedOutput): void
    {
        $output = $this->stringModifier->cutEnd($string, $length);
        $this->assertEquals($expectedOutput, $output);
    }

    /**
     * @dataProvider getNotTooLongStringsForEndCutting
     *
     * @param string $string
     * @param int $length
     */
    public function testItDoesNotCutStringEndIfItsNotTooLong(string $string, int $length): void
    {
        $output = $this->stringModifier->cutEnd($string, $length);
        $this->assertEquals($string, $output);
    }

    /**
     * @return Generator
     */
    public function getTooLongStringsForEndCutting(): Generator
    {
        yield ['test', 3, 'tes'];
        yield ['testable', 7, 'testabl'];
        yield ['hello world 899', 13, 'hello world 8'];
    }

    /**
     * @return Generator
     */
    public function getNotTooLongStringsForEndCutting(): Generator
    {
        yield ['test', 4];
        yield ['testable', 20];
        yield ['good bye cruel world 10.99', 128];
    }

    /**
     * @dataProvider str2UrlProvider
     */
    public function testStr2url(string $input, string $expected, bool $allow_accented_chars): void
    {
        self::assertSame($expected, $this->stringModifier->str2url($input, $allow_accented_chars));
    }

    public function str2UrlProvider(): Generator
    {
        yield ['!@#$%^&*()_+-={}[]|:;"<>,.?/', '-', false];
        yield ['Some !@#$%^&*()_+-={}[]|:;"<>,.?/ text', 'some-text', false];
        yield ['Some text 123 !@#$%^&*()_+-={}[]|:;"<>,.?/', 'some-text-123-', false];
        yield ['Some text 123 with unicode characters: áéíóú', 'some-text-123-with-unicode-characters-aeiou', false];
        yield ['!@#$%^&*()_+-={}[]|:;"<>,.?/', '-', false];
        yield ['Some !@#$%^&*()_+-={}[]|:;"<>,.?/ text', 'some-text', false];
        yield ['Some text 123 !@#$%^&*()_+-={}[]|:;"<>,.?/', 'some-text-123-', false];
        yield ['Some text 123 with unicode characters: áéíóú', 'some-text-123-with-unicode-characters-aeiou', false];
        yield ['Some text 123 with unicode characters: áéíóú', 'some-text-123-with-unicode-characters-áéíóú', true];
    }

    /**
     * @dataProvider getTestReplaceAccentedCharactersData
     */
    public function testReplaceAccentedCharacters(string $input, string $expected): void
    {
        self::assertSame($expected, $this->stringModifier->replaceAccentedChars($input));
    }

    public function getTestReplaceAccentedCharactersData(): Generator
    {
        yield 'empty string' => ['', ''];
        yield 'Test a variations' => ['aaâæaa', 'aaaaeaa'];
        yield 'Test e variations' => ['éèê', 'eee'];
    }
}
