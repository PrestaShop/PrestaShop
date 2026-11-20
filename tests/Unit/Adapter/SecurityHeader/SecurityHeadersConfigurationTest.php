<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\SecurityHeader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersConfiguration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;

class SecurityHeadersConfigurationTest extends TestCase
{
    private const SHOP_ID = 1;

    public function testItReadsTheStoredSettingsForTheCurrentShopScope(): void
    {
        $seen = [];
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('get')->willReturnCallback(
            function (string $key, $default = null, ?ShopConstraint $shopConstraint = null) use (&$seen) {
                $seen[] = $shopConstraint;

                return [
                    'PS_SEC_NOSNIFF' => '1',
                    'PS_SEC_FRAME_OPTIONS' => 'SAMEORIGIN',
                    'PS_SEC_REFERRER_POLICY' => 'strict-origin',
                    'PS_SEC_HSTS' => '0',
                    'PS_SEC_HSTS_MAX_AGE' => '15552000',
                    'PS_SEC_HSTS_SUBDOMAINS' => '0',
                    'PS_SEC_HSTS_PRELOAD' => '0',
                    'PS_SEC_PERMISSIONS_POLICY' => 'camera=()',
                ][$key] ?? $default;
            }
        );

        $this->assertSame([
            'nosniff' => true,
            'frame_options' => 'SAMEORIGIN',
            'referrer_policy' => 'strict-origin',
            'hsts' => false,
            'hsts_max_age' => 15552000,
            'hsts_subdomains' => false,
            'hsts_preload' => false,
            'permissions_policy' => 'camera=()',
        ], $this->securityHeadersConfiguration($configuration)->getConfiguration());

        // Every read is scoped to the current shop (not the global/all-shops value).
        $this->assertNotEmpty($seen);
        foreach ($seen as $shopConstraint) {
            $this->assertEquals(ShopConstraint::shop(self::SHOP_ID), $shopConstraint);
        }
    }

    public function testItWritesEveryKeyAndClampsANegativeMaxAge(): void
    {
        $written = [];
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('set')->willReturnCallback(function (string $key, $value) use (&$written): void {
            $written[$key] = $value;
        });

        $this->securityHeadersConfiguration($configuration)->updateConfiguration([
            'nosniff' => true,
            'frame_options' => 'DENY',
            'referrer_policy' => 'no-referrer',
            'hsts' => true,
            'hsts_max_age' => -5,
            'hsts_subdomains' => true,
            'hsts_preload' => false,
            'permissions_policy' => "  geolocation=()\r\nSet-Cookie: x=1  ",
        ]);

        $this->assertTrue($written['PS_SEC_NOSNIFF']);
        $this->assertSame('DENY', $written['PS_SEC_FRAME_OPTIONS']);
        $this->assertTrue($written['PS_SEC_HSTS']);
        $this->assertSame(0, $written['PS_SEC_HSTS_MAX_AGE'], 'A negative max-age is clamped to 0');
        $this->assertSame('geolocation=()Set-Cookie: x=1', $written['PS_SEC_PERMISSIONS_POLICY'], 'The policy is stripped of CR/LF and trimmed');
    }

    private function securityHeadersConfiguration(Configuration $configuration): SecurityHeadersConfiguration
    {
        $shopContext = $this->createMock(Context::class);
        $shopContext->method('isAllShopContext')->willReturn(false);
        $shopContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(self::SHOP_ID));

        $multistoreFeature = $this->createMock(FeatureInterface::class);
        $multistoreFeature->method('isUsed')->willReturn(false);

        return new SecurityHeadersConfiguration($configuration, $shopContext, $multistoreFeature);
    }
}
