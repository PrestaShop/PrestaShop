<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ConstraintRowPresenter;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ConstraintRowSerializer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * The constraint row serializer is the data handler's inverse of ConstraintRowPresenter: verbatim
 * tails re-wrapped in their token delimiters, per_language rows folded into ONE All[...] line at
 * the first per-language row's position. What it emits must parse through the mapper into the
 * same constraints the original DSL string produced.
 */
class ConstraintRowSerializerTest extends TestCase
{
    public function testEmptyRowsSerializeToNull(): void
    {
        $this->assertNull(ConstraintRowSerializer::serialize([]));
        $this->assertNull(ConstraintRowSerializer::serialize([
            ['name' => '', 'options' => '', 'per_language' => '0'],
            ['name' => '  ', 'options' => 'max: 5', 'per_language' => '1'],
        ]));
    }

    /**
     * @dataProvider tokenProvider
     *
     * @param array{name?: string|null, options?: string|null} $row
     */
    public function testToken(array $row, string $expected): void
    {
        $this->assertSame($expected, ConstraintRowSerializer::token($row));
    }

    /**
     * @return iterable<string, array{array<string, string|null>, string}>
     */
    public static function tokenProvider(): iterable
    {
        yield 'bare name' => [['name' => 'NotBlank', 'options' => ''], 'NotBlank'];
        yield 'positional tail is kept verbatim' => [['name' => 'TypedRegex', 'options' => "'generic_name'"], "TypedRegex('generic_name')"];
        yield 'named options tail is kept verbatim' => [['name' => 'Length', 'options' => 'min: 2, max: 64'], 'Length(min: 2, max: 64)'];
        yield 'composite uses the bracket shape' => [['name' => 'All', 'options' => 'Url, NotBlank'], 'All[Url, NotBlank]'];
        yield 'keyed composite children stay verbatim' => [['name' => 'Collection', 'options' => 'name: NotBlank'], 'Collection[name: NotBlank]'];
        yield 'empty composite keeps its brackets' => [['name' => 'Sequentially', 'options' => ''], 'Sequentially[]'];
        yield 'unknown name still serializes (row validation rejects it, not the serializer)' => [['name' => 'Lenght', 'options' => 'max: 64'], 'Lenght(max: 64)'];
        yield 'empty name serializes to nothing' => [['name' => '', 'options' => 'max: 64'], ''];
        yield 'missing keys behave as empty values' => [[], ''];
    }

    /**
     * @dataProvider serializeProvider
     *
     * @param list<array<string, string|null>> $rows
     */
    public function testSerialize(array $rows, ?string $expected): void
    {
        $this->assertSame($expected, ConstraintRowSerializer::serialize($rows));
    }

    /**
     * @return iterable<string, array{list<array<string, string|null>>, string|null}>
     */
    public static function serializeProvider(): iterable
    {
        yield 'set-level rows keep their order, one line each' => [
            [
                ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
                ['name' => 'Length', 'options' => 'min: 2, max: 64', 'per_language' => '0'],
            ],
            "NotBlank\nLength(min: 2, max: 64)",
        ];

        yield 'per-language rows fold into one All line at the first per-language position' => [
            [
                ['name' => 'DefaultLanguage', 'options' => "'Video link'", 'per_language' => '0'],
                ['name' => 'Url', 'options' => '', 'per_language' => '1'],
                ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
                ['name' => 'Length', 'options' => 'max: 255', 'per_language' => '1'],
            ],
            "DefaultLanguage('Video link')\nAll[ Url, Length(max: 255) ]\nNotBlank",
        ];

        yield 'only per-language rows give a single All line' => [
            [
                ['name' => 'Url', 'options' => '', 'per_language' => '1'],
            ],
            'All[ Url ]',
        ];

        yield 'abandoned rows are skipped without breaking the fold position' => [
            [
                ['name' => '', 'options' => '', 'per_language' => '0'],
                ['name' => 'Url', 'options' => '', 'per_language' => '1'],
                ['name' => '', 'options' => '', 'per_language' => '1'],
                ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
            ],
            "All[ Url ]\nNotBlank",
        ];

        yield 'set-level All composite row keeps its own brackets' => [
            [
                ['name' => 'All', 'options' => 'NotBlank', 'per_language' => '0'],
            ],
            'All[NotBlank]',
        ];
    }

    /**
     * Present -> serialize -> mapper round trip: the serialized DSL parses into the same
     * constraints as the stored render. Byte equality is not required (toNames renders composites
     * multi-line, the fold emits one line) — semantic equality through fromNames is.
     *
     * @dataProvider roundTripProvider
     */
    public function testMapperRoundTrip(string $stored): void
    {
        $serialized = ConstraintRowSerializer::serialize(ConstraintRowPresenter::rows($stored));

        $this->assertNotNull($serialized);
        $this->assertEquals(
            ExtraPropertyConstraintMapper::fromNames($stored),
            ExtraPropertyConstraintMapper::fromNames($serialized)
        );
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function roundTripProvider(): iterable
    {
        yield 'flat set' => ["NotBlank\nLength(min: 2, max: 64)\nTypedRegex('generic_name')"];
        yield 'per-language fold' => ["DefaultLanguage('Warranty note')\nAll[ Length(max: 64), Url ]"];
        yield 'multi-line toNames render' => ["All[\n  Url,\n  NotBlank\n]"];
        yield 'nested composite' => ['All[ Collection[name: NotBlank, code: Length(max: 5)] ]'];
        yield 'quoted commas and escapes' => ["Choice(['a,b', 'c\\'d'])"];
    }
}
