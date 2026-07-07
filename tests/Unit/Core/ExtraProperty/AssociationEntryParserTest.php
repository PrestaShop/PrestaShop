<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\AssociationEntryParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;

/**
 * Direct coverage of the three placement-entry grammars (forms, grids, apis) and of the
 * assertValid*() syntax guards shared by the ExtraPropertyDefinition constructor and the
 * ValidExtraPropertyAssociations form constraint.
 */
class AssociationEntryParserTest extends TestCase
{
    // -------------------------------------------------------------------------
    // parseFormEntry: "formId[:path[:before|after]]"
    // -------------------------------------------------------------------------

    /**
     * @dataProvider formEntryProvider
     *
     * @param array{formId: string, mode: string|null, path: string|null, anchor: string|null} $expected
     */
    public function testParseFormEntry(string $entry, array $expected): void
    {
        $this->assertSame($expected, AssociationEntryParser::parseFormEntry($entry));
    }

    /**
     * @return array<string, array{string, array{formId: string, mode: string|null, path: string|null, anchor: string|null}}>
     */
    public static function formEntryProvider(): array
    {
        return [
            'no colon: fallback section' => [
                'product',
                ['formId' => 'product', 'mode' => null, 'path' => null, 'anchor' => null],
            ],
            'trailing colon behaves like no path' => [
                'product:',
                ['formId' => 'product', 'mode' => null, 'path' => null, 'anchor' => null],
            ],
            'container placement: single segment path' => [
                'product:options',
                ['formId' => 'product', 'mode' => null, 'path' => 'options', 'anchor' => null],
            ],
            'container placement: dot-separated path' => [
                'product:options.suppliers',
                ['formId' => 'product', 'mode' => null, 'path' => 'options.suppliers', 'anchor' => null],
            ],
            'anchor placement: before, anchor at root' => [
                'product:reference:before',
                ['formId' => 'product', 'mode' => 'before', 'path' => '', 'anchor' => 'reference'],
            ],
            'anchor placement: after, dot path splits into parent + anchor' => [
                'product:options.suppliers:after',
                ['formId' => 'product', 'mode' => 'after', 'path' => 'options', 'anchor' => 'suppliers'],
            ],
            'anchor placement: deep dot path keeps parent path intact' => [
                'product:a.b.c:before',
                ['formId' => 'product', 'mode' => 'before', 'path' => 'a.b', 'anchor' => 'c'],
            ],
            'mode with no path: mode consumed, no anchor' => [
                'product::after',
                ['formId' => 'product', 'mode' => 'after', 'path' => null, 'anchor' => null],
            ],
            'empty formId is parsed (validation is the assert method\'s job)' => [
                ':options',
                ['formId' => '', 'mode' => null, 'path' => 'options', 'anchor' => null],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // parseGridEntry: "gridId[:columnId[:before|after]]"
    // -------------------------------------------------------------------------

    /**
     * @dataProvider gridEntryProvider
     *
     * @param array{gridId: string, columnId: string|null, mode: string|null} $expected
     */
    public function testParseGridEntry(string $entry, array $expected): void
    {
        $this->assertSame($expected, AssociationEntryParser::parseGridEntry($entry));
    }

    /**
     * @return array<string, array{string, array{gridId: string, columnId: string|null, mode: string|null}}>
     */
    public static function gridEntryProvider(): array
    {
        return [
            'no colon: appended at the end' => [
                'product',
                ['gridId' => 'product', 'columnId' => null, 'mode' => null],
            ],
            'column without mode defaults to after' => [
                'product:reference',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'after'],
            ],
            'explicit before mode' => [
                'product:reference:before',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'before'],
            ],
            'explicit after mode' => [
                'product:reference:after',
                ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'after'],
            ],
            'trailing colon behaves like no column' => [
                'product:',
                ['gridId' => 'product', 'columnId' => null, 'mode' => null],
            ],
            'mode with no column: both dropped' => [
                'product::before',
                ['gridId' => 'product', 'columnId' => null, 'mode' => null],
            ],
            'empty gridId is parsed (validation is the assert method\'s job)' => [
                ':reference',
                ['gridId' => '', 'columnId' => 'reference', 'mode' => 'after'],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // parseApiEntry: "uriPath[:METHOD[,METHOD...]]" — split on the FIRST colon
    // -------------------------------------------------------------------------

    /**
     * @dataProvider apiEntryProvider
     *
     * @param array{path: string, methods: list<string>|null} $expected
     */
    public function testParseApiEntry(string $entry, array $expected): void
    {
        $this->assertSame($expected, AssociationEntryParser::parseApiEntry($entry));
    }

    /**
     * @return array<string, array{string, array{path: string, methods: list<string>|null}}>
     */
    public static function apiEntryProvider(): array
    {
        return [
            'no colon: matches every method' => [
                '/products',
                ['path' => '/products', 'methods' => null],
            ],
            'URI template with placeholder and method list' => [
                '/products/{productId}:GET,PATCH',
                ['path' => '/products/{productId}', 'methods' => ['GET', 'PATCH']],
            ],
            'methods are case-insensitive (uppercased)' => [
                '/products:get,patch',
                ['path' => '/products', 'methods' => ['GET', 'PATCH']],
            ],
            'methods are trimmed' => [
                '/products: GET , POST ',
                ['path' => '/products', 'methods' => ['GET', 'POST']],
            ],
            'trailing colon: no method restriction' => [
                '/products:',
                ['path' => '/products', 'methods' => null],
            ],
            'missing leading slash is normalized' => [
                'products:DELETE',
                ['path' => '/products', 'methods' => ['DELETE']],
            ],
            'trailing slash is dropped' => [
                '/products/',
                ['path' => '/products', 'methods' => null],
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // normalizeApiPath
    // -------------------------------------------------------------------------

    /**
     * @dataProvider normalizeApiPathProvider
     */
    public function testNormalizeApiPath(string $path, string $expected): void
    {
        $this->assertSame($expected, AssociationEntryParser::normalizeApiPath($path));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function normalizeApiPathProvider(): array
    {
        return [
            'already normalized' => ['/products', '/products'],
            'missing leading slash' => ['products', '/products'],
            'trailing slash dropped' => ['/products/', '/products'],
            'surrounding whitespace trimmed' => ['  /products  ', '/products'],
            'root stays root' => ['/', '/'],
            'empty becomes root' => ['', '/'],
        ];
    }

    // -------------------------------------------------------------------------
    // assertValid*: syntax guards
    // -------------------------------------------------------------------------

    public function testAssertValidFormEntryReturnsParsedEntry(): void
    {
        $this->assertSame(
            ['formId' => 'product', 'mode' => 'before', 'path' => 'options', 'anchor' => 'suppliers'],
            AssociationEntryParser::assertValidFormEntry('product:options.suppliers:before')
        );
    }

    /**
     * @dataProvider emptyFormIdProvider
     */
    public function testAssertValidFormEntryRejectsEmptyFormId(string $entry): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/formId must not be empty/');

        AssociationEntryParser::assertValidFormEntry($entry);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emptyFormIdProvider(): array
    {
        return [
            'empty entry' => [''],
            'path only' => [':options'],
            'path and mode only' => [':options:before'],
        ];
    }

    public function testAssertionErrorsKeepTheBareMessageRetrievable(): void
    {
        try {
            AssociationEntryParser::assertValidFormEntry(':options');
            $this->fail('An InvalidExtraPropertyDefinitionException was expected.');
        } catch (InvalidExtraPropertyDefinitionException $e) {
            // Consumers already pointing at the offending entry (e.g. a placement form row)
            // display the message without the "ExtraPropertyDefinition: " locator.
            $this->assertStringStartsWith('ExtraPropertyDefinition: ', $e->getMessage());
            $this->assertSame('ExtraPropertyDefinition: ' . $e->getBareMessage(), $e->getMessage());
        }
    }

    public function testAssertValidGridEntryReturnsParsedEntry(): void
    {
        $this->assertSame(
            ['gridId' => 'product', 'columnId' => 'reference', 'mode' => 'after'],
            AssociationEntryParser::assertValidGridEntry('product:reference')
        );
    }

    /**
     * @dataProvider emptyGridIdProvider
     */
    public function testAssertValidGridEntryRejectsEmptyGridId(string $entry): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/gridId must not be empty/');

        AssociationEntryParser::assertValidGridEntry($entry);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emptyGridIdProvider(): array
    {
        return [
            'empty entry' => [''],
            'column only' => [':reference'],
        ];
    }

    public function testAssertValidApiEntryReturnsParsedEntry(): void
    {
        $this->assertSame(
            ['path' => '/products/{productId}', 'methods' => ['GET', 'PATCH']],
            AssociationEntryParser::assertValidApiEntry('/products/{productId}:get,PATCH')
        );
    }

    /**
     * @dataProvider emptyApiPathProvider
     */
    public function testAssertValidApiEntryRejectsEmptyPath(string $entry): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/URI path must not be empty/');

        AssociationEntryParser::assertValidApiEntry($entry);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emptyApiPathProvider(): array
    {
        return [
            'empty entry' => [''],
            'whitespace only' => ['   '],
            // Checked on the RAW path: normalization would turn "" into "/" and hide the error.
            'methods only' => [':GET'],
        ];
    }

    public function testAssertValidApiEntryRejectsUnknownHttpMethod(): void
    {
        $this->expectException(InvalidExtraPropertyDefinitionException::class);
        $this->expectExceptionMessageMatches('/invalid HTTP method "FETCH"/');

        AssociationEntryParser::assertValidApiEntry('/products:GET,FETCH');
    }

    public function testAssertValidApiEntryAcceptsEveryWhitelistedMethodCaseInsensitively(): void
    {
        $entry = '/products:' . implode(',', array_map('strtolower', AssociationEntryParser::ALLOWED_HTTP_METHODS));

        $this->assertSame(
            ['path' => '/products', 'methods' => AssociationEntryParser::ALLOWED_HTTP_METHODS],
            AssociationEntryParser::assertValidApiEntry($entry)
        );
    }
}
