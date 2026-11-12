<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Http\UntrustedUrl;

/**
 * Decides whether a URL coming from user input (CSV import cells, for instance) may
 * be fetched server side, and which address the connection must be pinned to.
 *
 * Defense against CWE-918: only the ALLOWED_SCHEMES are accepted, so no PHP stream
 * wrapper (phar://, file://, data://, ...) gets through; the host must resolve to
 * public addresses only; the caller connects to the address validated here, so the
 * host name cannot be re-pointed at an internal address afterwards (DNS rebinding).
 * A caller that follows redirects has to validate every hop with this class again.
 */
final class UntrustedUrlValidator
{
    /**
     * Schemes an untrusted URL is allowed to use, mapped to their default port.
     *
     * Must stay in sync with the CURLPROTO_* mask applied by the curl based callers
     * (Tools::file_get_contents_curl()): a scheme accepted here has to be one curl is
     * also allowed to speak, otherwise a URL passes this validation only for curl to
     * refuse it, or, when CURLPROTO_HTTP is not defined and no mask can be applied, to
     * be fetched with a protocol we never meant to allow. That is why sftp is absent.
     */
    public const ALLOWED_SCHEMES = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21,
        'ftps' => 990,
    ];

    /**
     * resolvePublicIps() results, refusals included: importing a few thousand images
     * from the same host would otherwise issue as many DNS lookups.
     *
     * @var array<string, string[]>
     */
    private array $resolvedHosts = [];

    public function __construct(
        private readonly PublicIpChecker $publicIpChecker = new PublicIpChecker(),
        private readonly HostResolverInterface $hostResolver = new DnsHostResolver(),
    ) {
    }

    public function isSchemeAllowed(string $scheme): bool
    {
        return isset(self::ALLOWED_SCHEMES[strtolower($scheme)]);
    }

    /**
     * Validates an untrusted URL and builds the "host:port:ip" entry (the CURLOPT_RESOLVE
     * format) pinning its host to a validated public IP.
     *
     * @return string|null the pin entry, an empty string when the URL is valid but cannot
     *                     be pinned (see below), or null when it must not be fetched
     */
    public function getPinnedResolveEntry(string $url): ?string
    {
        $parts = parse_url($url);
        if ($parts === false || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        $scheme = strtolower($parts['scheme']);
        if (!$this->isSchemeAllowed($scheme)) {
            return null;
        }

        // Strips the brackets of an IPv6 literal.
        $host = $this->hostToAscii(trim($parts['host'], '[]'));
        if (null === $host) {
            return null;
        }

        $publicIps = $this->resolvePublicIps($host);
        if (empty($publicIps)) {
            return null;
        }

        // An outbound proxy does the resolving itself, so a pinned address would be ignored.
        // The host has still been validated above; returning an empty entry keeps the
        // download working on a proxy-only server instead of failing it outright, at the
        // cost of the anti-rebinding guarantee, which is unobtainable through a proxy.
        if ($this->hasOutboundProxy()) {
            return '';
        }

        $port = isset($parts['port']) ? (int) $parts['port'] : self::ALLOWED_SCHEMES[$scheme];
        $ip = $publicIps[0];

        return $host . ':' . $port . ':' . (str_contains($ip, ':') ? '[' . $ip . ']' : $ip);
    }

    /**
     * Resolves a host to its IP addresses and returns them only if ALL of them
     * are public. Returns an empty array when resolution fails or any address is
     * private/reserved (fail-closed).
     *
     * @param string $host hostname or IP literal
     *
     * @return string[] validated public IPs (empty if unsafe/unresolvable)
     */
    public function resolvePublicIps(string $host): array
    {
        // IP literal: validate directly, no lookup and nothing worth caching.
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return $this->publicIpChecker->isPublicIp($host) ? [$host] : [];
        }

        if (isset($this->resolvedHosts[$host])) {
            return $this->resolvedHosts[$host];
        }

        $result = array_values(array_unique($this->hostResolver->resolve($host)));
        foreach ($result as $ip) {
            if (!$this->publicIpChecker->isPublicIp($ip)) {
                $result = [];

                break;
            }
        }

        return $this->resolvedHosts[$host] = $result;
    }

    /**
     * Converts an internationalised host name to its ASCII (punycode) form.
     *
     * Required for two reasons: PHP's resolver functions do not perform the conversion
     * themselves the way libcurl does, so an IDN host resolves partially at best and is
     * then refused; and the pinned entry has to use the ASCII form, which is what libcurl
     * actually looks up.
     *
     * @return string|null null when the host cannot be represented in ASCII
     */
    public function hostToAscii(string $host): ?string
    {
        // Nothing to do for a plain ASCII host, which is the overwhelming majority.
        if (!preg_match('/[^\x20-\x7f]/', $host)) {
            return $host;
        }

        if (!function_exists('idn_to_ascii')) {
            return null;
        }

        $ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        return $ascii === false ? null : $ascii;
    }

    /**
     * Whether an outbound proxy is configured through the environment. libcurl picks these
     * up on its own, and when it does it resolves host names itself.
     */
    public function hasOutboundProxy(): bool
    {
        foreach (['http_proxy', 'HTTP_PROXY', 'https_proxy', 'HTTPS_PROXY', 'all_proxy', 'ALL_PROXY'] as $var) {
            if (!empty(getenv($var))) {
                return true;
            }
        }

        return false;
    }
}
