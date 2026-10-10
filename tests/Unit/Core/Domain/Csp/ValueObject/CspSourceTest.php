<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Csp\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;

/**
 * Format validation for a single CSP source expression, plus the weakening-keyword flag the grid
 * uses to warn before one is allowed.
 */
class CspSourceTest extends TestCase
{
    /**
     * @dataProvider provideValidSources
     */
    public function testItAcceptsValidSourceExpressions(string $value): void
    {
        $this->assertSame($value, (new CspSource($value))->getValue());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function provideValidSources(): iterable
    {
        // Keywords (stored in their canonical quoted form).
        yield "'self'" => ["'self'"];
        yield "'none'" => ["'none'"];
        yield "'unsafe-inline'" => ["'unsafe-inline'"];
        yield "'unsafe-eval'" => ["'unsafe-eval'"];
        yield "'wasm-unsafe-eval'" => ["'wasm-unsafe-eval'"];
        yield "'strict-dynamic'" => ["'strict-dynamic'"];

        // Schemes.
        yield 'data scheme' => ['data:'];
        yield 'https scheme' => ['https:'];

        // Hosts.
        yield 'bare host' => ['example.com'];
        yield 'wildcard host' => ['*.example.com'];
        yield 'host with scheme and port' => ['https://cdn.example.com:443'];

        // Hashes are static by design, so they may be stored and emitted verbatim.
        yield 'sha256 hash' => ["'sha256-abcDEF123+/='"];
        yield 'sha384 hash' => ["'sha384-abcDEF123+/='"];
        yield 'sha512 hash' => ["'sha512-abcDEF123+/='"];

        // Wildcard.
        yield 'wildcard' => ['*'];
    }

    /**
     * @dataProvider provideCanonicalizableSources
     */
    public function testItLowercasesSchemeAndHostButPreservesThePath(string $input, string $expected): void
    {
        // Scheme/host/port are case-insensitive, so they are lowercased to match the form the recorder
        // stores for a reported violation of the same origin. A path is preserved verbatim so a
        // merchant's path restriction survives to the enforced header instead of being widened.
        $this->assertSame($expected, (new CspSource($input))->getValue());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public function provideCanonicalizableSources(): iterable
    {
        yield 'uppercase host lowercased' => ['https://CDN.Example.com', 'https://cdn.example.com'];
        yield 'path preserved, host lowercased' => ['https://CDN.Example.com/a/B.js?v=1', 'https://cdn.example.com/a/B.js?v=1'];
        yield 'bare host lowercased, path preserved' => ['CDN.Example.com/X', 'cdn.example.com/X'];
        yield 'port kept, path preserved' => ['https://cdn.example.com:8443/X', 'https://cdn.example.com:8443/X'];
        yield 'wildcard host lowercased' => ['*.Example.com', '*.example.com'];
        yield 'uppercase scheme lowercased' => ['DATA:', 'data:'];
        yield 'keyword untouched' => ["'self'", "'self'"];
        yield 'scheme untouched' => ['data:', 'data:'];
    }

    /**
     * @dataProvider provideInvalidSources
     */
    public function testItRejectsInvalidSourceExpressions(string $value): void
    {
        $this->expectException(CspConstraintException::class);
        $this->expectExceptionCode(CspConstraintException::INVALID_SOURCE);

        new CspSource($value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public function provideInvalidSources(): iterable
    {
        yield 'empty string' => [''];
        yield 'string with spaces' => ['example.com evil.com'];
        yield 'internal space' => ["'self' 'unsafe-inline'"];
        yield 'longer than 255 chars' => ['https://cdn.example.com/' . str_repeat('a', 255)];
        // A nonce must be unique per response; a persisted one is always stale or forgeable.
        yield 'nonce' => ["'nonce-2726c7f26c'"];
    }

    /**
     * The weakening rule itself is defined once, in SQL, by CspLogQueryBuilder and
     * CspRuleRepository (keyword/wildcard + broad schemes on script-src/style-src). This only guards
     * that the source lists those queries bind are themselves valid CSP source expressions.
     */
    public function testTheWeakeningAndBroadeningConstantsAreValidSources(): void
    {
        foreach ([...CspSource::WEAKENING_KEYWORDS, ...CspSource::BROADENING_SCHEMES] as $source) {
            $this->assertSame($source, (new CspSource($source))->getValue(), $source . ' must be a valid source');
        }
    }
}
