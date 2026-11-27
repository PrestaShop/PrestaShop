<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Csp\CspReportParser;

class CspReportParserTest extends TestCase
{
    public function testItParsesALegacyCspReport(): void
    {
        // application/csp-report — the body a browser POSTs for a report-uri endpoint.
        $body = json_encode([
            'csp-report' => [
                'document-uri' => 'https://shop.example.com/',
                'effective-directive' => 'script-src-elem',
                'blocked-uri' => 'https://cdn.example.com/app.js',
            ],
        ]);

        $this->assertSame(
            [['directive' => 'script-src-elem', 'blockedUri' => 'https://cdn.example.com/app.js', 'documentUri' => 'https://shop.example.com/', 'sample' => null, 'sourceFile' => null, 'lineNumber' => null]],
            CspReportParser::parse('application/csp-report', (string) $body)
        );
    }

    public function testItParsesTheInlineSampleSourceFileAndLineFromALegacyReport(): void
    {
        // For an inline violation the browser adds a short code sample plus the source file and line when
        // the policy carries 'report-sample'. These make the keyword ('unsafe-inline') source identifiable.
        $body = json_encode([
            'csp-report' => [
                'document-uri' => 'https://shop.example.com/',
                'effective-directive' => 'script-src',
                'blocked-uri' => 'inline',
                'script-sample' => 'window.dashboard_data = {',
                'source-file' => 'https://shop.example.com/',
                'line-number' => 1481,
            ],
        ]);

        $this->assertSame(
            [['directive' => 'script-src', 'blockedUri' => 'inline', 'documentUri' => 'https://shop.example.com/', 'sample' => 'window.dashboard_data = {', 'sourceFile' => 'https://shop.example.com/', 'lineNumber' => 1481]],
            CspReportParser::parse('application/csp-report', (string) $body)
        );
    }

    public function testItCapsTheNumberOfReportsFromASingleReportingApiPayload(): void
    {
        $entry = [
            'type' => 'csp-violation',
            'body' => ['effectiveDirective' => 'script-src', 'blockedURL' => 'https://cdn.example.com/app.js'],
        ];
        $body = json_encode(array_fill(0, CspReportParser::MAX_REPORTS + 25, $entry));

        $this->assertCount(
            CspReportParser::MAX_REPORTS,
            CspReportParser::parse('application/reports+json', (string) $body),
            'One request must not fan out into an unbounded number of writes'
        );
    }

    public function testItFallsBackToViolatedDirectiveAndParsesWithACharset(): void
    {
        $body = json_encode([
            'csp-report' => [
                'violated-directive' => 'img-src',
                'blocked-uri' => 'data',
            ],
        ]);

        $this->assertSame(
            [['directive' => 'img-src', 'blockedUri' => 'data', 'documentUri' => null, 'sample' => null, 'sourceFile' => null, 'lineNumber' => null]],
            CspReportParser::parse('application/csp-report; charset=utf-8', (string) $body)
        );
    }

    public function testItParsesTheReportingApiFormat(): void
    {
        // application/reports+json — the Reporting API delivers an array of reports.
        $body = json_encode([
            [
                'type' => 'csp-violation',
                'body' => [
                    'documentURL' => 'https://shop.example.com/cart',
                    'effectiveDirective' => 'style-src',
                    'blockedURL' => 'inline',
                    'sample' => '.price{color:red}',
                    'sourceFile' => 'https://shop.example.com/cart',
                    'lineNumber' => 219,
                ],
            ],
            [
                'type' => 'deprecation',
                'body' => ['id' => 'ignored'],
            ],
        ]);

        $this->assertSame(
            [['directive' => 'style-src', 'blockedUri' => 'inline', 'documentUri' => 'https://shop.example.com/cart', 'sample' => '.price{color:red}', 'sourceFile' => 'https://shop.example.com/cart', 'lineNumber' => 219]],
            CspReportParser::parse('application/reports+json', (string) $body)
        );
    }

    public function testItMatchesTheReportTypeCaseInsensitively(): void
    {
        $body = json_encode([
            ['type' => 'CSP-Violation', 'body' => ['effectiveDirective' => 'script-src', 'blockedURL' => 'eval']],
        ]);

        $this->assertSame(
            [['directive' => 'script-src', 'blockedUri' => 'eval', 'documentUri' => null, 'sample' => null, 'sourceFile' => null, 'lineNumber' => null]],
            CspReportParser::parse('application/reports+json', (string) $body)
        );
    }

    public function testItReturnsEveryViolationInAReportingApiBatch(): void
    {
        $body = json_encode([
            ['type' => 'csp-violation', 'body' => ['effectiveDirective' => 'script-src', 'blockedURL' => 'eval']],
            ['type' => 'csp-violation', 'body' => ['effectiveDirective' => 'font-src', 'blockedURL' => 'https://fonts.example.com']],
        ]);

        $this->assertCount(2, CspReportParser::parse('application/reports+json', (string) $body));
    }

    /**
     * @dataProvider provideEmptyResultBodies
     */
    public function testItReturnsNoViolationForUnusableInput(string $contentType, string $body): void
    {
        $this->assertSame([], CspReportParser::parse($contentType, $body));
    }

    public static function provideEmptyResultBodies(): iterable
    {
        yield 'empty body' => ['application/csp-report', ''];
        yield 'invalid json' => ['application/csp-report', 'not json'];
        yield 'json but not an object' => ['application/csp-report', '"a string"'];
        yield 'missing csp-report key' => ['application/csp-report', '{"foo":"bar"}'];
        yield 'missing blocked-uri' => ['application/csp-report', '{"csp-report":{"effective-directive":"script-src"}}'];
        yield 'missing directive' => ['application/csp-report', '{"csp-report":{"blocked-uri":"https://x"}}'];
        yield 'reporting api without csp-violation' => ['application/reports+json', '[{"type":"deprecation","body":{}}]'];
    }
}
