<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Http\UntrustedUrl;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Http\UntrustedUrl\PublicIpChecker;

class PublicIpCheckerTest extends TestCase
{
    /**
     * Also used by ToolsTest, so that the legacy Tools::isPublicIp() entry point is
     * checked against the same vectors.
     */
    public static function providerIsPublicIp(): array
    {
        return [
            // Routable addresses, the only ones that must be accepted.
            'ipv4 public' => [true, '8.8.8.8'],
            'ipv4 public 2' => [true, '1.1.1.1'],
            'ipv6 public' => [true, '2606:4700::1111'],
            'ipv4-mapped public' => [true, '::ffff:8.8.8.8'],
            '6to4 wrapping public ipv4' => [true, '2002:0808:0808::'],

            // Plain IPv4 private/reserved ranges.
            'loopback' => [false, '127.0.0.1'],
            'rfc1918 10/8' => [false, '10.0.0.1'],
            'rfc1918 172.16/12' => [false, '172.16.0.1'],
            'rfc1918 192.168/16' => [false, '192.168.1.1'],
            'cloud metadata' => [false, '169.254.169.254'],
            'unspecified' => [false, '0.0.0.0'],
            'broadcast' => [false, '255.255.255.255'],

            // Plain IPv6 private/reserved ranges.
            'ipv6 loopback' => [false, '::1'],
            'ipv6 unique local' => [false, 'fc00::1'],
            'ipv6 link local' => [false, 'fe80::1'],

            // IPv6 notations that tunnel an IPv4 address: the IPv4 range flags of
            // filter_var() do not apply to them, so they must be unwrapped first.
            'ipv4-mapped cloud metadata' => [false, '::ffff:169.254.169.254'],
            'ipv4-mapped loopback' => [false, '::ffff:127.0.0.1'],
            'ipv4-mapped rfc1918' => [false, '::ffff:10.0.0.1'],
            'ipv4-mapped rfc1918 2' => [false, '::ffff:192.168.1.1'],
            'ipv4-mapped loopback hex' => [false, '0:0:0:0:0:ffff:7f00:1'],
            'ipv4-compatible loopback' => [false, '::127.0.0.1'],
            'ipv4-compatible rfc1918' => [false, '::10.0.0.1'],
            'nat64 loopback' => [false, '64:ff9b::7f00:1'],
            'nat64 rfc1918' => [false, '64:ff9b::a00:1'],
            '6to4 loopback' => [false, '2002:7f00:0001::'],

            // Non-routable ranges that filter_var() alone lets through.
            'shared address space' => [false, '100.64.0.1'],
            'ietf protocol assignments' => [false, '192.0.0.1'],
            'test-net-1' => [false, '192.0.2.1'],
            'benchmarking' => [false, '198.18.0.1'],
            'test-net-2' => [false, '198.51.100.1'],
            'test-net-3' => [false, '203.0.113.1'],
            'ipv4 multicast' => [false, '224.0.0.1'],
            'ipv4 multicast top' => [false, '239.255.255.255'],
            'ipv6 multicast' => [false, 'ff02::1'],
            'ipv6 discard only' => [false, '100::1'],
            'ipv6 site local' => [false, 'fec0::1'],
            'teredo' => [false, '2001::1'],

            // Not addresses at all.
            'hostname' => [false, 'not-an-ip'],
            'empty' => [false, ''],
            'out of range octets' => [false, '999.999.999.999'],
        ];
    }

    #[DataProvider('providerIsPublicIp')]
    public function testIsPublicIp(bool $expected, string $ip): void
    {
        $this->assertSame($expected, (new PublicIpChecker())->isPublicIp($ip));
    }

    public static function providerNormalizeIpForRangeCheck(): array
    {
        return [
            'ipv4 unchanged' => ['8.8.8.8', '8.8.8.8'],
            'plain ipv6 unchanged' => ['2606:4700::1111', '2606:4700::1111'],
            'ipv4-mapped' => ['169.254.169.254', '::ffff:169.254.169.254'],
            'ipv4-mapped hex' => ['127.0.0.1', '0:0:0:0:0:ffff:7f00:1'],
            'ipv4-compatible' => ['10.0.0.1', '::10.0.0.1'],
            'nat64' => ['127.0.0.1', '64:ff9b::7f00:1'],
            '6to4' => ['127.0.0.1', '2002:7f00:0001::'],
            'not an address' => [null, 'not-an-ip'],
            'empty' => [null, ''],
        ];
    }

    #[DataProvider('providerNormalizeIpForRangeCheck')]
    public function testNormalizeIpForRangeCheck(?string $expected, string $ip): void
    {
        $this->assertSame($expected, (new PublicIpChecker())->normalizeIpForRangeCheck($ip));
    }

    public static function providerIsPackedIpInRange(): array
    {
        return [
            'whole byte prefix, inside' => [true, '10.1.2.3', '10.0.0.0', 8],
            'whole byte prefix, outside' => [false, '11.0.0.1', '10.0.0.0', 8],
            'partial byte prefix, last address inside' => [true, '100.127.255.255', '100.64.0.0', 10],
            'partial byte prefix, first address outside' => [false, '100.128.0.0', '100.64.0.0', 10],
            'partial byte prefix, just below' => [false, '100.63.255.255', '100.64.0.0', 10],
            'zero prefix matches the whole family' => [true, '203.0.113.7', '0.0.0.0', 0],
            'full prefix, same address' => [true, '192.0.2.1', '192.0.2.1', 32],
            'full prefix, other address' => [false, '192.0.2.2', '192.0.2.1', 32],
            'ipv6 inside' => [true, 'ff02::1', 'ff00::', 8],
            'ipv6 outside' => [false, 'fe80::1', 'ff00::', 8],
            'family mismatch never matches' => [false, '::1', '0.0.0.0', 0],
            'invalid network never matches' => [false, '10.0.0.1', 'not-a-network', 8],
        ];
    }

    #[DataProvider('providerIsPackedIpInRange')]
    public function testIsPackedIpInRange(bool $expected, string $ip, string $network, int $prefixLength): void
    {
        $this->assertSame(
            $expected,
            (new PublicIpChecker())->isPackedIpInRange(inet_pton($ip), $network, $prefixLength)
        );
    }
}
