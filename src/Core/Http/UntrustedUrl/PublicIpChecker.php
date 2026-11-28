<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Http\UntrustedUrl;

/**
 * Decides whether an IP address is publicly routable, i.e. whether a server-side
 * request to it can stay away from the shop's own network (CWE-918).
 */
final class PublicIpChecker
{
    /**
     * IPv4 ranges that FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE does
     * NOT reject but that are never a legitimate remote source.
     * Each entry is [network, prefix length].
     */
    public const BLOCKED_IPV4_RANGES = [
        ['100.64.0.0', 10],   // RFC 6598 shared address space (CGNAT), used as internal range by some hosts
        ['192.0.0.0', 24],    // RFC 6890 IETF protocol assignments
        ['192.0.2.0', 24],    // RFC 5737 TEST-NET-1
        ['198.18.0.0', 15],   // RFC 2544 benchmarking
        ['198.51.100.0', 24], // RFC 5737 TEST-NET-2
        ['203.0.113.0', 24],  // RFC 5737 TEST-NET-3
        ['224.0.0.0', 4],     // multicast
    ];

    /**
     * IPv6 ranges that FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE does
     * NOT reject but that are never a legitimate remote source.
     * Each entry is [network, prefix length].
     */
    public const BLOCKED_IPV6_RANGES = [
        ['ff00::', 8],   // multicast (fe80::1 is caught by filter_var, ff02::1 is not)
        ['100::', 64],   // RFC 6666 discard-only
        ['fec0::', 10],  // deprecated site-local (filter_var only catches fe80::/10)
        ['2001::', 32],  // Teredo: embeds an obfuscated IPv4 address, reject wholesale
    ];

    /**
     * IPv6 ranges that carry an IPv4 address inside them. Such an address must be
     * range-checked as the IPv4 address it really reaches, because the IPv4 flags of
     * filter_var() do not apply to IPv6 notation: without this, ::ffff:169.254.169.254
     * is reported as a public address and the cloud metadata endpoint stays reachable.
     * Each entry is [network, prefix length, byte offset of the embedded IPv4].
     */
    public const IPV4_IN_IPV6_RANGES = [
        ['::ffff:0:0', 96, 12], // IPv4-mapped
        ['64:ff9b::', 96, 12],  // RFC 6052 NAT64
        ['::', 96, 12],         // deprecated IPv4-compatible
        ['2002::', 16, 2],      // RFC 3056 6to4
    ];

    /**
     * @return bool true when $ip is a valid, non-private, non-reserved address
     */
    public function isPublicIp(string $ip): bool
    {
        // An IPv6 address that embeds an IPv4 one must be judged on the IPv4 address it
        // actually reaches; the IPv4 range flags below are not applied to IPv6 notation.
        $ip = $this->normalizeIpForRangeCheck($ip);
        if ($ip === null) {
            return false;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        // filter_var() leaves a number of non-routable ranges through, complete the job.
        $packed = @inet_pton($ip);
        if ($packed === false) {
            return false;
        }
        $ranges = strlen($packed) === 4 ? self::BLOCKED_IPV4_RANGES : self::BLOCKED_IPV6_RANGES;
        foreach ($ranges as $range) {
            if ($this->isPackedIpInRange($packed, $range[0], $range[1])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Canonicalises an IP address for range checking: IPv6 forms that embed an IPv4
     * address (IPv4-mapped, IPv4-compatible, NAT64, 6to4) are reduced to that IPv4
     * address, everything else is returned unchanged.
     *
     * @return string|null null when $ip is not a valid IP address
     */
    public function normalizeIpForRangeCheck(string $ip): ?string
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return null;
        }

        $packed = @inet_pton($ip);
        if ($packed === false) {
            return null;
        }

        // Only IPv6 addresses can wrap an IPv4 one.
        if (strlen($packed) !== 16) {
            return $ip;
        }

        foreach (self::IPV4_IN_IPV6_RANGES as $range) {
            if (!$this->isPackedIpInRange($packed, $range[0], $range[1])) {
                continue;
            }

            $embedded = @inet_ntop(substr($packed, $range[2], 4));

            return $embedded === false ? null : $embedded;
        }

        return $ip;
    }

    /**
     * Tests a packed IP address (as returned by inet_pton) against a CIDR range.
     * Works for both address families; a family mismatch is never a match.
     */
    public function isPackedIpInRange(string $packedIp, string $network, int $prefixLength): bool
    {
        $packedNetwork = @inet_pton($network);
        if ($packedNetwork === false || strlen($packedNetwork) !== strlen($packedIp)) {
            return false;
        }

        $wholeBytes = intdiv($prefixLength, 8);
        if ($wholeBytes > 0 && strncmp($packedIp, $packedNetwork, $wholeBytes) !== 0) {
            return false;
        }

        $remainingBits = $prefixLength % 8;
        if ($remainingBits === 0) {
            return true;
        }

        $mask = (0xFF << (8 - $remainingBits)) & 0xFF;

        return (ord($packedIp[$wholeBytes]) & $mask) === (ord($packedNetwork[$wholeBytes]) & $mask);
    }
}
