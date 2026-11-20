<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;

/**
 * A single CSP source expression (keyword, scheme, host, or hash); validation only.
 */
final class CspSource
{
    /** Keywords/wildcard that weaken the policy for the whole shop, so the grid warns before one is added. */
    public const WEAKENING_KEYWORDS = ["'unsafe-eval'", "'unsafe-inline'", "'wasm-unsafe-eval'", '*'];

    /** Schemes that weaken script-src/style-src (e.g. `script-src data:`); ordinary on other directives. */
    public const BROADENING_SCHEMES = ['https:', 'http:', 'data:', 'blob:', 'ws:', 'wss:', 'filesystem:', 'mediastream:'];

    private const MAX_LENGTH = 255;

    private const KEYWORDS = [
        "'self'",
        "'none'",
        "'unsafe-inline'",
        "'unsafe-eval'",
        "'wasm-unsafe-eval'",
        "'strict-dynamic'",
        "'unsafe-hashes'",
        "'report-sample'",
    ];

    private const SCHEMES = ['data:', 'blob:', 'filesystem:', 'mediastream:', 'https:', 'http:', 'ws:', 'wss:'];

    /**
     * Hashes are legitimately static, so they may be stored and emitted verbatim. A nonce must be
     * unique per response; a persisted 'nonce-...' would be stale or forgeable, and v1 ships no
     * per-request nonce service, so nonce values are rejected rather than stored.
     */
    private const HASH_PATTERN = "/^'(sha256|sha384|sha512)-[A-Za-z0-9+\/\-_=]+'$/";

    private const HOST_PATTERN = '#^(?:[a-z][a-z0-9+.\-]*://)?(?:\*\.)?[a-z0-9\-]+(?:\.[a-z0-9\-]+)*(?::(?:[0-9]+|\*))?(?:/[^\s,;\'"]*)?$#i';

    private readonly string $value;

    /**
     * @throws CspConstraintException when the value is not a valid CSP source expression
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (!self::isValid($value)) {
            throw new CspConstraintException(sprintf('Invalid CSP source "%s".', $value), CspConstraintException::INVALID_SOURCE);
        }

        $this->value = self::canonicalize($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /** Lowercases the case-insensitive scheme/host/port but keeps the case-sensitive path, so a path restriction survives to the header. */
    private static function canonicalize(string $value): string
    {
        if ($value === '*'
            || in_array($value, self::KEYWORDS, true)
            || preg_match(self::HASH_PATTERN, $value)
        ) {
            return $value;
        }

        if (in_array(strtolower($value), self::SCHEMES, true)) {
            return strtolower($value);
        }

        // Lowercase the optional scheme:// prefix and the host[:port]; leave the path (if any) as typed.
        if (preg_match('#^([a-z][a-z0-9+.\-]*://)?([^/]+)(/.*)?$#i', $value, $matches)) {
            return strtolower($matches[1] . $matches[2]) . ($matches[3] ?? '');
        }

        return $value;
    }

    private static function isValid(string $value): bool
    {
        if ($value === '' || mb_strlen($value) > self::MAX_LENGTH) {
            return false;
        }

        if ($value === '*' || in_array($value, self::KEYWORDS, true)) {
            return true;
        }

        if (in_array(strtolower($value), self::SCHEMES, true)) {
            return true;
        }

        if (preg_match(self::HASH_PATTERN, $value)) {
            return true;
        }

        return (bool) preg_match(self::HOST_PATTERN, $value);
    }
}
