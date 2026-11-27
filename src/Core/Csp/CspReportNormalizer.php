<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Csp;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;

/**
 * Normalizes the raw fields of a browser CSP report into a bare directive name and a CSP source expression.
 */
final class CspReportNormalizer
{
    /** Browsers report a keyword instead of a URL for eval/inline violations; map each to its source expression. */
    private const BROWSER_KEYWORDS = [
        'eval' => "'unsafe-eval'",
        'inline' => "'unsafe-inline'",
        'wasm-eval' => "'wasm-unsafe-eval'",
        'self' => "'self'",
        'data' => 'data:',
        'blob' => 'blob:',
    ];

    /** Browser/extension-origin sources that are never the shop's: dropped so they can't pollute the log or allow-list. */
    private const JUNK_SCHEMES = [
        'chrome-extension:',
        'chrome-untrusted:',
        'moz-extension:',
        'safari-extension:',
        'safari-web-extension:',
        'about:',
        'chrome:',
        'resource:',
    ];

    /** Reduces a report directive to its bare name; older browsers append the source, so only the first token is kept. */
    public static function normalizeDirective(string $rawDirective): ?string
    {
        $directive = explode(' ', strtolower(trim($rawDirective)))[0];

        if ('' === $directive) {
            return null;
        }

        // Coarsen the granular effective-directive to its parent so the curate -> enforce workflow stays safe.
        $cspDirective = CspDirective::tryFrom($directive);

        return null !== $cspDirective ? $cspDirective->coarsen()->value : $directive;
    }

    /** Maps a report blocked-uri to a canonical CSP source (URLs reduced to their origin), or null when junk. */
    public static function normalizeSource(string $blockedUri): ?string
    {
        $raw = trim($blockedUri);
        if ($raw === '') {
            return null;
        }

        $lower = strtolower($raw);

        if (isset(self::BROWSER_KEYWORDS[$lower])) {
            return self::BROWSER_KEYWORDS[$lower];
        }

        foreach (self::JUNK_SCHEMES as $junk) {
            if (str_starts_with($lower, $junk)) {
                return null;
            }
        }

        // Opaque-origin schemes a browser legitimately reports as a bare token;
        // never http:/https:/… , which would plant a broad source.
        if (in_array($lower, ['data:', 'blob:', 'filesystem:', 'mediastream:'], true)) {
            return $lower;
        }

        if (preg_match('#^[a-z][a-z0-9+.\-]*://#i', $raw)) {
            $parts = parse_url($raw);
            if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
                return null;
            }

            $scheme = strtolower($parts['scheme']);
            // Only real fetch schemes over a concrete host;
            // an exotic scheme or a wildcard host is a forged report planting a broad source.
            if (!in_array($scheme, ['http', 'https', 'ws', 'wss'], true) || str_contains($parts['host'], '*')) {
                return null;
            }

            $origin = $scheme . '://' . strtolower($parts['host']);
            if (isset($parts['port'])) {
                $origin .= ':' . $parts['port'];
            }

            return $origin;
        }

        // A browser only otherwise reports a bare host[:port];
        // reject everything else so an unauthenticated POST can't seed a broad source.
        if (preg_match('#^[a-z0-9]([a-z0-9.\-]*[a-z0-9])?(:[0-9]+)?$#', $lower)) {
            return $lower;
        }

        return null;
    }
}
