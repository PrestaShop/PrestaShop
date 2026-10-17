<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\SecurityHeader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersProvider;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class SecurityHeadersProviderTest extends TestCase
{
    /** A representative storefront config with the three low-risk headers set; the install ships them all empty/off. */
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

    public function testTheBackOfficeReadsItsOwnAdminKeysNotTheStorefrontOnes(): void
    {
        $config = [
            'PS_SEC_ADMIN_NOSNIFF' => '1',
            'PS_SEC_ADMIN_FRAME_OPTIONS' => 'SAMEORIGIN',
            'PS_SEC_ADMIN_REFERRER_POLICY' => 'strict-origin',
            'PS_SEC_ADMIN_PERMISSIONS_POLICY' => 'camera=()',
            // Storefront keys set to different values: they must be ignored on the admin surface.
            'PS_SEC_NOSNIFF' => '0',
            'PS_SEC_FRAME_OPTIONS' => 'DENY',
            'PS_SEC_REFERRER_POLICY' => 'unsafe-url',
            'PS_SEC_PERMISSIONS_POLICY' => '',
        ];

        $admin = $this->provider($config)->getHeaders(true, null, true);
        $this->assertSame([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin',
            'Permissions-Policy' => 'camera=()',
        ], $admin);

        // The storefront reads the PS_SEC_* keys, not the admin ones.
        $storefront = $this->provider($config)->getHeaders(true);
        $this->assertArrayNotHasKey('X-Content-Type-Options', $storefront);
        $this->assertSame('DENY', $storefront['X-Frame-Options']);
        $this->assertSame('unsafe-url', $storefront['Referrer-Policy']);
    }

    public function testTheBackOfficeShipsSecureDefaultsEvenWithNoKeysSet(): void
    {
        // No PS_SEC_ADMIN_* set at all (e.g. the keys were never seeded): the back office must still be
        // hardened. The storefront, with no PS_SEC_* set, sends nothing.
        $admin = $this->provider([])->getHeaders(true, null, true);
        $this->assertSame([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ], $admin);

        $this->assertSame([], $this->provider([])->getHeaders(true));
    }

    public function testAnExplicitBackOfficeValueOverridesTheSecureDefault(): void
    {
        // Turning a back-office header off (empty string stored) must win over the secure default.
        $config = ['PS_SEC_ADMIN_NOSNIFF' => '0', 'PS_SEC_ADMIN_FRAME_OPTIONS' => '', 'PS_SEC_ADMIN_REFERRER_POLICY' => ''];

        $admin = $this->provider($config)->getHeaders(true, null, true);
        $this->assertArrayNotHasKey('X-Content-Type-Options', $admin);
        $this->assertArrayNotHasKey('X-Frame-Options', $admin);
        $this->assertArrayNotHasKey('Referrer-Policy', $admin);
        // Permissions-Policy was not overridden, so its secure default still applies.
        $this->assertSame('camera=(), microphone=(), geolocation=()', $admin['Permissions-Policy']);
    }

    public function testHstsIsSharedAcrossBothSurfaces(): void
    {
        $config = ['PS_SEC_HSTS' => '1', 'PS_SEC_HSTS_MAX_AGE' => '100'];

        $this->assertSame('max-age=100', $this->provider($config)->getHeaders(true)['Strict-Transport-Security']);
        $this->assertSame('max-age=100', $this->provider($config)->getHeaders(true, null, true)['Strict-Transport-Security']);
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

    public function testItReadsForTheGivenShopScope(): void
    {
        $seen = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            function (string $key, $default = null, ?ShopConstraint $shopConstraint = null) use (&$seen) {
                $seen[] = $shopConstraint;

                return self::DEFAULTS[$key] ?? $default;
            }
        );
        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn(true);

        $constraint = ShopConstraint::shop(2);
        (new SecurityHeadersProvider($configuration, $flagChecker))->getHeaders(true, $constraint);

        $this->assertNotEmpty($seen);
        foreach ($seen as $passed) {
            $this->assertSame($constraint, $passed);
        }
    }

    /**
     * @param array<string, string> $config
     */
    private function provider(array $config, bool $flagEnabled = true): SecurityHeadersProvider
    {
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn (string $key, $default = null) => $config[$key] ?? $default);

        $flagChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $flagChecker->method('isEnabled')->willReturn($flagEnabled);

        return new SecurityHeadersProvider($configuration, $flagChecker);
    }
}
