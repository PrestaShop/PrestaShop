<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ConstraintRowPresenter;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * The constraint row presenter feeds the Validation card's constraint collection: one DSL token =
 * one row carrying the verbatim argument tail; the first top-level All[...] explodes into
 * per_language rows (the "applied to each language" zone). Names are not whitelist-checked here
 * (submit-time row validation owns that); shapeless tokens are skipped.
 */
class ConstraintRowPresenterTest extends TestCase
{
    public function testEmptyValueGivesNoRows(): void
    {
        $this->assertSame([], ConstraintRowPresenter::rows(null));
        $this->assertSame([], ConstraintRowPresenter::rows(''));
        $this->assertSame([], ConstraintRowPresenter::rows("  \n"));
    }

    /**
     * @dataProvider rowsProvider
     *
     * @param list<array{name: string, options: string, per_language: string}> $expected
     */
    public function testRows(string $raw, array $expected): void
    {
        $this->assertSame($expected, ConstraintRowPresenter::rows($raw));
    }

    /**
     * @return iterable<string, array{string, list<array<string, string>>}>
     */
    public static function rowsProvider(): iterable
    {
        yield 'bare name' => ['NotBlank', [
            ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
        ]];

        yield 'positional value keeps the verbatim tail' => ["TypedRegex('generic_name')", [
            ['name' => 'TypedRegex', 'options' => "'generic_name'", 'per_language' => '0'],
        ]];

        yield 'named options keep the verbatim tail' => ['Length(min: 2, max: 64)', [
            ['name' => 'Length', 'options' => 'min: 2, max: 64', 'per_language' => '0'],
        ]];

        yield 'comma-separated tokens on one line' => ['NotBlank, GreaterThan(5)', [
            ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
            ['name' => 'GreaterThan', 'options' => '5', 'per_language' => '0'],
        ]];

        yield 'first All explodes into per-language rows' => ["DefaultLanguage('Video link')\nAll[ Url, Length(max: 255) ]", [
            ['name' => 'DefaultLanguage', 'options' => "'Video link'", 'per_language' => '0'],
            ['name' => 'Url', 'options' => '', 'per_language' => '1'],
            ['name' => 'Length', 'options' => 'max: 255', 'per_language' => '1'],
        ]];

        yield 'multi-line rendered All (toNames output) explodes the same way' => ["All[\n  Url,\n  NotBlank\n]", [
            ['name' => 'Url', 'options' => '', 'per_language' => '1'],
            ['name' => 'NotBlank', 'options' => '', 'per_language' => '1'],
        ]];

        yield 'second All stays an opaque set-level composite row' => ['All[Url], All[NotBlank]', [
            ['name' => 'Url', 'options' => '', 'per_language' => '1'],
            ['name' => 'All', 'options' => 'NotBlank', 'per_language' => '0'],
        ]];

        yield 'non-All composites stay set-level rows with their verbatim inner tail' => ['Collection[name: NotBlank, code: Length(max: 5)]', [
            ['name' => 'Collection', 'options' => 'name: NotBlank, code: Length(max: 5)', 'per_language' => '0'],
        ]];

        yield 'unknown name still presents as a structured row (validated on submit, not here)' => ["Lenght(max: 64)\nNotBlank", [
            ['name' => 'Lenght', 'options' => 'max: 64', 'per_language' => '0'],
            ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
        ]];

        yield 'unknown name inside All presents as a per-language row' => ['All[ Lenght(max: 64), Url ]', [
            ['name' => 'Lenght', 'options' => 'max: 64', 'per_language' => '1'],
            ['name' => 'Url', 'options' => '', 'per_language' => '1'],
        ]];

        yield 'shapeless token is skipped' => ["weird token!\nNotBlank", [
            ['name' => 'NotBlank', 'options' => '', 'per_language' => '0'],
        ]];

        yield 'quoted separators never split a token' => ["Choice(['a,b', 'c'])", [
            ['name' => 'Choice', 'options' => "['a,b', 'c']", 'per_language' => '0'],
        ]];
    }

    /**
     * tokenize() is the grammar authority: same splitting as fromNames(), with 1-based starting lines.
     */
    public function testTokenizeExposesTheMapperSplitting(): void
    {
        $this->assertSame(
            [['NotBlank', 1], ['Length(min: 2, max: 64)', 2], ['All[Url, NotBlank]', 4]],
            ExtraPropertyConstraintMapper::tokenize("NotBlank\nLength(min: 2, max: 64)\n\nAll[Url, NotBlank]")
        );
    }
}
