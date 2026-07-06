<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\AssociationRowPresenter;

/**
 * The row presenter feeds the definition form's placement collections: one stored entry = one row
 * keeping only the entry's EXPLICIT parts, so a row serialized back (AssociationRowSerializer)
 * re-emits the original entry. Lines that fail the grammar are skipped — the raw edition is gone,
 * so they have no representation left.
 */
class AssociationRowPresenterTest extends TestCase
{
    public function testEmptyValuesGiveNoRows(): void
    {
        $this->assertSame([], AssociationRowPresenter::formRows(null));
        $this->assertSame([], AssociationRowPresenter::formRows(''));
        $this->assertSame([], AssociationRowPresenter::formRows("  \n  \n"));
        $this->assertSame([], AssociationRowPresenter::gridRows(null));
        $this->assertSame([], AssociationRowPresenter::apiRows(null));
    }

    /**
     * @dataProvider formRowsProvider
     *
     * @param list<array{form_id: string, path: string, mode: string}> $expected
     */
    public function testFormRows(?string $raw, array $expected): void
    {
        $this->assertSame($expected, AssociationRowPresenter::formRows($raw));
    }

    /**
     * @return iterable<string, array{string|null, list<array<string, string>>}>
     */
    public static function formRowsProvider(): iterable
    {
        yield 'bare form id' => ['product', [
            ['form_id' => 'product', 'path' => '', 'mode' => ''],
        ]];

        yield 'container path (append inside)' => ['product:options', [
            ['form_id' => 'product', 'path' => 'options', 'mode' => ''],
        ]];

        yield 'nested anchor with mode recombines the raw path' => ['product:options.suppliers:before', [
            ['form_id' => 'product', 'path' => 'options.suppliers', 'mode' => 'before'],
        ]];

        yield 'root-level anchor with mode' => ['customer:email:after', [
            ['form_id' => 'customer', 'path' => 'email', 'mode' => 'after'],
        ]];

        yield 'blank lines are skipped, surrounding spaces trimmed' => ["\n  product  \n\n  category:parent:after\n", [
            ['form_id' => 'product', 'path' => '', 'mode' => ''],
            ['form_id' => 'category', 'path' => 'parent', 'mode' => 'after'],
        ]];

        yield 'line with an empty form id is skipped' => ["product\n:options:before", [
            ['form_id' => 'product', 'path' => '', 'mode' => ''],
        ]];

        yield 'mode with no path canonicalizes to the bare-formId row' => ['product::before', [
            ['form_id' => 'product', 'path' => '', 'mode' => ''],
        ]];
    }

    /**
     * @dataProvider gridRowsProvider
     *
     * @param list<array{grid_id: string, column_id: string, mode: string}> $expected
     */
    public function testGridRows(?string $raw, array $expected): void
    {
        $this->assertSame($expected, AssociationRowPresenter::gridRows($raw));
    }

    /**
     * @return iterable<string, array{string|null, list<array<string, string>>}>
     */
    public static function gridRowsProvider(): iterable
    {
        yield 'bare grid id' => ['product', [
            ['grid_id' => 'product', 'column_id' => '', 'mode' => ''],
        ]];

        yield 'column without explicit mode keeps mode empty (runtime "after" default is not stored)' => ['product:reference', [
            ['grid_id' => 'product', 'column_id' => 'reference', 'mode' => ''],
        ]];

        yield 'explicit after' => ['product:reference:after', [
            ['grid_id' => 'product', 'column_id' => 'reference', 'mode' => 'after'],
        ]];

        yield 'explicit before' => ['country:iso_code:before', [
            ['grid_id' => 'country', 'column_id' => 'iso_code', 'mode' => 'before'],
        ]];

        yield 'unrecognized suffix stays part of the column id (lenient grammar)' => ['product:reference:sideways', [
            ['grid_id' => 'product', 'column_id' => 'reference:sideways', 'mode' => ''],
        ]];

        yield 'line with an empty grid id is skipped' => [':reference:after', []];
    }

    /**
     * @dataProvider apiRowsProvider
     *
     * @param list<array{uri: string, methods: string}> $expected
     */
    public function testApiRows(?string $raw, array $expected): void
    {
        $this->assertSame($expected, AssociationRowPresenter::apiRows($raw));
    }

    /**
     * @return iterable<string, array{string|null, list<array<string, string>>}>
     */
    public static function apiRowsProvider(): iterable
    {
        yield 'bare uri matches all methods' => ['/products', [
            ['uri' => '/products', 'methods' => ''],
        ]];

        yield 'uri template with methods' => ['/products/{productId}:GET,PATCH', [
            ['uri' => '/products/{productId}', 'methods' => 'GET,PATCH'],
        ]];

        yield 'methods are canonicalized to uppercase CSV' => ['/products: get , patch', [
            ['uri' => '/products', 'methods' => 'GET,PATCH'],
        ]];

        yield 'uri is kept as typed (no normalization rewrite)' => ['products/', [
            ['uri' => 'products/', 'methods' => ''],
        ]];

        yield 'line with an unknown method is skipped' => ["/products:FLY\n/categories", [
            ['uri' => '/categories', 'methods' => ''],
        ]];

        yield 'line with an empty uri is skipped' => [':GET', []];
    }
}
