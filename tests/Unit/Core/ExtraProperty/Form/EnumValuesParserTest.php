<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\EnumValuesParser;

class EnumValuesParserTest extends TestCase
{
    /**
     * @dataProvider parseProvider
     */
    public function testParse(mixed $rawValue, ?array $expected): void
    {
        $this->assertSame($expected, EnumValuesParser::parse($rawValue));
    }

    /**
     * @return array<string, array{mixed, list<string>|null}>
     */
    public static function parseProvider(): array
    {
        return [
            'null' => [null, null],
            'empty string' => ['', null],
            'blank string' => ["  \n  ", null],
            'non-string' => [['S', 'M'], null],
            'one value per line' => ["S\nM\nL", ['S', 'M', 'L']],
            'values are trimmed' => ["  S  \n\tM\n", ['S', 'M']],
            'blank lines are dropped' => ["S\n\n \nM", ['S', 'M']],
        ];
    }
}
