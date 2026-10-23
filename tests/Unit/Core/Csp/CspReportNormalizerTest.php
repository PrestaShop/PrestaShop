<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Csp;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Csp\CspReportNormalizer;

/**
 * The pure report-field normalization the front-office collector and the back-office handler share.
 * The inputs below are the real shapes Chrome, Firefox and Safari put in a CSP report's
 * "blocked-uri" / "blockedURL" and "violated-directive" / "effective-directive" fields.
 */
class CspReportNormalizerTest extends TestCase
{
    /**
     * @dataProvider provideSources
     */
    public function testItNormalizesBlockedUriToACanonicalSource(string $blockedUri, ?string $expected): void
    {
        $this->assertSame($expected, CspReportNormalizer::normalizeSource($blockedUri));
    }

    /**
     * @return iterable<string, array{string, string|null}>
     */
    public function provideSources(): iterable
    {
        // Browser keyword forms. Chrome/Firefox report a bare keyword instead of a URL for
        // eval/inline/data/blob violations; each maps to the real source expression.
        yield 'eval keyword' => ['eval', "'unsafe-eval'"];
        yield 'inline keyword' => ['inline', "'unsafe-inline'"];
        yield 'wasm-eval keyword' => ['wasm-eval', "'wasm-unsafe-eval'"];
        yield 'data keyword' => ['data', 'data:'];
        yield 'blob keyword' => ['blob', 'blob:'];
        yield 'self keyword' => ['self', "'self'"];
        yield 'keyword is case-insensitive' => ['EVAL', "'unsafe-eval'"];

        // Full URLs (Chrome "blocked-uri", Firefox/Safari "blockedURL") are reduced to their origin.
        yield 'url reduced to origin, query dropped' => ['https://cdn.example.com/app.js?v=1', 'https://cdn.example.com'];
        yield 'url path dropped' => ['https://cdn.example.com/a/b/c.js', 'https://cdn.example.com'];
        yield 'url port preserved' => ['https://cdn.example.com:8443/app.js', 'https://cdn.example.com:8443'];
        yield 'url host and scheme lowercased' => ['HTTPS://CDN.Example.COM/App.js', 'https://cdn.example.com'];
        yield 'http url reduced to origin' => ['http://analytics.example.org/collect', 'http://analytics.example.org'];
        // Reducing to the origin drops any embedded credentials, so a userinfo in the report never
        // reaches the visible log / allow-list.
        yield 'url credentials stripped' => ['https://user:pass@cdn.example.com/app.js', 'https://cdn.example.com'];
        // IPv6 literal hosts keep their brackets (and port) in the origin.
        yield 'ipv6 host with port' => ['https://[2001:db8::1]:8080/path', 'https://[2001:db8::1]:8080'];
        yield 'ipv6 host without port' => ['https://[2001:DB8::1]/path', 'https://[2001:db8::1]'];

        // Junk sources injected by the browser or an installed extension, never by the shop.
        yield 'chrome extension dropped' => ['chrome-extension://abcdef/inject.js', null];
        yield 'moz extension dropped' => ['moz-extension://abcdef/inject.js', null];
        yield 'safari extension dropped' => ['safari-extension://abcdef/inject.js', null];
        yield 'safari web extension dropped' => ['safari-web-extension://abcdef/inject.js', null];
        yield 'about dropped' => ['about:blank', null];
        yield 'chrome scheme dropped' => ['chrome://settings', null];
        yield 'resource dropped' => ['resource://gre/modules/x.js', null];

        // Empty / whitespace-only.
        yield 'empty string' => ['', null];
        yield 'whitespace only' => ['   ', null];

        // Opaque-origin scheme tokens a browser legitimately reports (Safari sometimes reports "data").
        yield 'data scheme token' => ['data:', 'data:'];
        yield 'blob scheme token' => ['blob:', 'blob:'];

        // Bare host fallback (no scheme): lower-cased like the URL branch so the same host in a
        // different case does not create a duplicate log row / allow-list rule.
        yield 'bare host lowercased' => ['CDN.Example.com', 'cdn.example.com'];
        yield 'bare host with port' => ['cdn.example.com:8443', 'cdn.example.com:8443'];

        // Forged inputs a browser never reports as a blocked-uri: rejected, so an unauthenticated
        // POST cannot plant a broad or weakening source for a merchant to one-click "Allow".
        yield 'wildcard rejected' => ['*', null];
        yield 'wildcard host rejected' => ['*.evil.example.com', null];
        yield 'wildcard host with scheme rejected' => ['https://*.com/x.js', null];
        yield 'wildcard subdomain with scheme rejected' => ['https://*.evil.example.com/a', null];
        yield 'exotic scheme rejected' => ['ftp://evil.example.com/x', null];
        yield 'file scheme rejected' => ['file:///etc/passwd', null];
        yield 'ws scheme kept' => ['ws://socket.example.com/live', 'ws://socket.example.com'];
        yield 'quoted keyword rejected' => ["'strict-dynamic'", null];
        yield 'quoted unsafe-inline rejected' => ["'unsafe-inline'", null];
        yield 'bare https scheme rejected' => ['https:', null];
        yield 'bare http scheme rejected' => ['http:', null];
        yield 'javascript scheme rejected' => ['javascript:', null];
        yield 'nonce rejected' => ["'nonce-abc123'", null];
    }

    /**
     * @dataProvider provideDirectives
     */
    public function testItNormalizesTheViolatedDirectiveToItsBareName(string $rawDirective, ?string $expected): void
    {
        $this->assertSame($expected, CspReportNormalizer::normalizeDirective($rawDirective));
    }

    /**
     * @return iterable<string, array{string, string|null}>
     */
    public function provideDirectives(): iterable
    {
        yield 'plain directive' => ['script-src', 'script-src'];
        yield 'case-insensitive' => ['SCRIPT-SRC', 'script-src'];
        yield 'trimmed' => ['  style-src  ', 'style-src'];
        // Older browsers append the offending source to "violated-directive".
        yield 'source appended is dropped' => ['script-src https://evil.example.com', 'script-src'];
        // Granular script/style directives are coarsened to their parent so a curated rule augments
        // the base 'self' instead of overriding it (browsers report these for <script>/<style>).
        yield 'script-src-elem coarsened' => ['script-src-elem', 'script-src'];
        yield 'script-src-attr coarsened' => ['script-src-attr', 'script-src'];
        yield 'style-src-elem coarsened' => ['style-src-elem', 'style-src'];
        yield 'style-src-attr coarsened' => ['style-src-attr', 'style-src'];
        yield 'granular coarsened with source appended' => ['script-src-elem https://cdn.example.com', 'script-src'];
        yield 'empty string' => ['', null];
        yield 'whitespace only' => ['   ', null];
    }
}
