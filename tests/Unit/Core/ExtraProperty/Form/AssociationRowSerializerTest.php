<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Form;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\AssociationRowPresenter;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\AssociationRowSerializer;

/**
 * The row serializer is the data handler's inverse of AssociationRowPresenter: rows only carry an
 * entry's EXPLICIT parts, so presenting a stored entry and serializing the rows back must re-emit
 * it byte for byte. Abandoned builder rows (empty identifying field) serialize to nothing.
 */
class AssociationRowSerializerTest extends TestCase
{
    public function testEmptyRowsGiveNoEntries(): void
    {
        $this->assertSame([], AssociationRowSerializer::formEntries([]));
        $this->assertSame([], AssociationRowSerializer::gridEntries([]));
        $this->assertSame([], AssociationRowSerializer::apiEntries([]));
    }

    /**
     * @dataProvider formEntriesProvider
     *
     * @param list<array<string, string|null>> $rows
     * @param list<string> $expected
     */
    public function testFormEntries(array $rows, array $expected): void
    {
        $this->assertSame($expected, AssociationRowSerializer::formEntries($rows));
    }

    /**
     * @return iterable<string, array{list<array<string, string|null>>, list<string>}>
     */
    public static function formEntriesProvider(): iterable
    {
        yield 'bare form id' => [
            [['form_id' => 'product', 'path' => '', 'mode' => '']],
            ['product'],
        ];

        yield 'container path' => [
            [['form_id' => 'product', 'path' => 'options', 'mode' => '']],
            ['product:options'],
        ];

        yield 'anchor path with mode' => [
            [['form_id' => 'product', 'path' => 'options.suppliers', 'mode' => 'before']],
            ['product:options.suppliers:before'],
        ];

        yield 'mode without a path is dropped (no anchor to apply to)' => [
            [['form_id' => 'product', 'path' => '', 'mode' => 'before']],
            ['product'],
        ];

        yield 'empty form id skips the row' => [
            [
                ['form_id' => '', 'path' => 'options', 'mode' => 'after'],
                ['form_id' => 'category', 'path' => '', 'mode' => ''],
            ],
            ['category'],
        ];

        yield 'surrounding spaces are trimmed' => [
            [['form_id' => '  product  ', 'path' => ' options ', 'mode' => ' after ']],
            ['product:options:after'],
        ];

        yield 'missing keys behave as empty values' => [
            [['form_id' => 'product']],
            ['product'],
        ];
    }

    /**
     * @dataProvider gridEntriesProvider
     *
     * @param list<array<string, string|null>> $rows
     * @param list<string> $expected
     */
    public function testGridEntries(array $rows, array $expected): void
    {
        $this->assertSame($expected, AssociationRowSerializer::gridEntries($rows));
    }

    /**
     * @return iterable<string, array{list<array<string, string|null>>, list<string>}>
     */
    public static function gridEntriesProvider(): iterable
    {
        yield 'bare grid id' => [
            [['grid_id' => 'product', 'column_id' => '', 'mode' => '']],
            ['product'],
        ];

        yield 'column without explicit mode' => [
            [['grid_id' => 'product', 'column_id' => 'reference', 'mode' => '']],
            ['product:reference'],
        ];

        yield 'explicit mode' => [
            [['grid_id' => 'country', 'column_id' => 'iso_code', 'mode' => 'before']],
            ['country:iso_code:before'],
        ];

        yield 'empty grid id skips the row' => [
            [['grid_id' => '', 'column_id' => 'reference', 'mode' => 'after']],
            [],
        ];
    }

    /**
     * @dataProvider apiEntriesProvider
     *
     * @param list<array<string, string|null>> $rows
     * @param list<string> $expected
     */
    public function testApiEntries(array $rows, array $expected): void
    {
        $this->assertSame($expected, AssociationRowSerializer::apiEntries($rows));
    }

    /**
     * @return iterable<string, array{list<array<string, string|null>>, list<string>}>
     */
    public static function apiEntriesProvider(): iterable
    {
        yield 'bare uri (all methods)' => [
            [['uri' => '/products', 'methods' => '']],
            ['/products'],
        ];

        yield 'uri template with methods CSV' => [
            [['uri' => '/products/{productId}', 'methods' => 'GET,PATCH']],
            ['/products/{productId}:GET,PATCH'],
        ];

        yield 'empty uri skips the row' => [
            [['uri' => '', 'methods' => 'GET']],
            [],
        ];
    }

    /**
     * The presenter -> serializer round trip re-emits the stored entries byte for byte: this is
     * what keeps an untouched form submission from rewriting the definition.
     *
     * @dataProvider roundTripProvider
     */
    public function testPresenterRoundTrip(string $method, string $stored): void
    {
        $rows = AssociationRowPresenter::{str_replace('Entries', 'Rows', $method)}($stored);

        $this->assertSame(
            explode("\n", $stored),
            AssociationRowSerializer::{$method}($rows)
        );
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function roundTripProvider(): iterable
    {
        yield 'forms' => ['formEntries', "product\ncategory:parent:after\nproduct_combination:combination_details.reference:after\ncustomer:email:before"];
        yield 'grids' => ['gridEntries', "product\nproduct:reference\ncountry:iso_code:before\nproduct:reference:sideways"];
        yield 'apis' => ['apiEntries', "/products\n/products/{productId}:GET,PATCH\nproducts/"];
    }
}
