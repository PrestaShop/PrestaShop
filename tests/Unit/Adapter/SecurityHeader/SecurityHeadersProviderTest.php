<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\SecurityHeader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class SecurityHeadersProviderTest extends TestCase
{
    /** The three low-risk headers that ship on by default. */
    private const DEFAULTS = [
        'PS_SEC_NOSNIFF' => '1',
        'PS_SEC_FRAME_OPTIONS' => 'SAMEORIGIN',
        'PS_SEC_REFERRER_POLICY' => 'strict-origin-when-cross-origin',
        'PS_SEC_HSTS' => '0',
        'PS_SEC_HSTS_MAX_AGE' => '15552000',
        'PS_SEC_HSTS_SUBDOMAINS' => '0',
        'PS_SEC_HSTS_PRELOAD' => '0',
        'PS_SEC_PERMISSIONS_POLICY' => '',
    ];

    public function testItEmitsNothingWhenTheFeatureFlagIsOff(): void
    {
        $this->assertSame([], $this->provider(self::DEFAULTS, flagEnabled: false)->getHeaders(true));
    }

    public function testItEmitsTheThreeSafeHeadersByDefault(): void
    {
        $headers = $this->provider(self::DEFAULTS)->getHeaders(false);

        $this->assertSame([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        ], $headers);
    }

    public function testEachHeaderCanBeTurnedOff(): void
    {
        $headers = $this->provider([
            'PS_SEC_NOSNIFF' => '0',
            'PS_SEC_FRAME_OPTIONS' => '',
            'PS_SEC_REFERRER_POLICY' => '',
        ] + self::DEFAULTS)->getHeaders(true);

        $this->assertSame([], $headers);
    }

    public function testItRejectsAnInvalidFrameOptionOrReferrerPolicy(): void
    {
        $headers = $this->provider([
            'PS_SEC_FRAME_OPTIONS' => 'ALLOWALL',
            'PS_SEC_REFERRER_POLICY' => 'bogus',
        ] + self::DEFAULTS)->getHeaders(true);

        $this->assertArrayNotHasKey('X-Frame-Options', $headers);
        $this->assertArrayNotHasKey('Referrer-Policy', $headers);
    }

    public function testHstsIsSentOnlyOverHttps(): void
    {
        $config = ['PS_SEC_HSTS' => '1', 'PS_SEC_HSTS_SUBDOMAINS' => '1', 'PS_SEC_HSTS_PRELOAD' => '1'] + self::DEFAULTS;

        $this->assertArrayNotHasKey('Strict-Transport-Security', $this->provider($config)->getHeaders(false));

        $secure = $this->provider($config)->getHeaders(true);
        $this->assertSame('max-age=15552000; includeSubDomains; preload', $secure['Strict-Transport-Security']);
    }

    public function testPermissionsPolicyIsEmittedAndCannotInjectAHeader(): void
    {
        $headers = $this->provider(['PS_SEC_PERMISSIONS_POLICY' => "camera=()\r\nSet-Cookie: x=1"] + self::DEFAULTS)->getHeaders(true);

        $this->assertSame('camera=()Set-Cookie: x=1', $headers['Permissions-Policy']);
        $this->assertStringNotContainsString("\n", $headers['Permissions-Policy']);
    }

    /**
     * @param array<string, string> $config
     */
    private function provider(array $config, bool $flagEnabled = true): SecurityHeadersProvider
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn (string $key) => $config[$key] ?? null);

        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn($flagEnabled);

        return new SecurityHeadersProvider($configuration, $flagChecker);
    }
}
