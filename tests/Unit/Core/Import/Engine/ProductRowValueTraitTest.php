<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\ProductRowValueTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;

class ProductRowValueTraitTest extends TestCase
{
    /**
     * @dataProvider providesRowValues
     *
     * @param array<string, string> $row
     */
    public function testHasValueOnlySkipsUnmappedAndBlankCells(array $row, bool $expected): void
    {
        $this->assertSame($expected, $this->buildUser()->exposedHasValue($row, 'field'));
    }

    /**
     * @return iterable<string, array{0: array<string, string>, 1: bool}>
     */
    public static function providesRowValues(): iterable
    {
        yield 'unmapped column' => [[], false];
        yield 'blank cell' => [['field' => ''], false];
        // the reason hasValue() must not be !empty(): "0" disables a boolean
        // field or carries a zero price/dimension — it is a real value
        yield 'zero' => [['field' => '0'], true];
        yield 'plain value' => [['field' => 'value'], true];
        yield 'whitespace' => [['field' => ' '], true];
    }

    /**
     * @dataProvider providesVirtualCells
     *
     * @param array<string, string> $row
     */
    public function testIsVirtualRequiresAnExplicitTruthyCell(array $row, bool $expected): void
    {
        $this->assertSame($expected, $this->buildUser()->exposedIsVirtual($row));
    }

    /**
     * @return iterable<string, array{0: array<string, string>, 1: bool}>
     */
    public static function providesVirtualCells(): iterable
    {
        yield 'unmapped column' => [[], false];
        yield 'blank cell' => [['is_virtual' => ''], false];
        yield 'truthy' => [['is_virtual' => '1'], true];
        yield 'falsy' => [['is_virtual' => '0'], false];
        // an unparseable boolean is null, never true
        yield 'garbage' => [['is_virtual' => 'maybe'], false];
    }

    private function buildUser(): object
    {
        return new class(new ValueParser()) {
            use ProductRowValueTrait;

            public function __construct(
                protected readonly ValueParser $valueParser,
            ) {
            }

            /**
             * @param array<string, string> $row
             */
            public function exposedHasValue(array $row, string $field): bool
            {
                return $this->hasValue($row, $field);
            }

            /**
             * @param array<string, string> $row
             */
            public function exposedIsVirtual(array $row): bool
            {
                return $this->isVirtual($row);
            }
        };
    }
}
