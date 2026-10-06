<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\SecurityHeader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\SecurityHeadersConfiguration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

class SecurityHeadersConfigurationTest extends TestCase
{
    public function testItReadsTheStoredSettings(): void
    {
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn (string $key) => [
            'PS_SEC_NOSNIFF' => '1',
            'PS_SEC_FRAME_OPTIONS' => 'SAMEORIGIN',
            'PS_SEC_REFERRER_POLICY' => 'strict-origin',
            'PS_SEC_HSTS' => '0',
            'PS_SEC_HSTS_MAX_AGE' => '15552000',
            'PS_SEC_HSTS_SUBDOMAINS' => '0',
            'PS_SEC_HSTS_PRELOAD' => '0',
            'PS_SEC_PERMISSIONS_POLICY' => 'camera=()',
        ][$key] ?? null);

        $this->assertSame([
            'nosniff' => true,
            'frame_options' => 'SAMEORIGIN',
            'referrer_policy' => 'strict-origin',
            'hsts' => false,
            'hsts_max_age' => 15552000,
            'hsts_subdomains' => false,
            'hsts_preload' => false,
            'permissions_policy' => 'camera=()',
        ], (new SecurityHeadersConfiguration($configuration))->getConfiguration());
    }

    public function testItWritesEveryKeyAndClampsANegativeMaxAge(): void
    {
        $written = [];
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('set')->willReturnCallback(function (string $key, $value) use (&$written): void {
            $written[$key] = $value;
        });

        (new SecurityHeadersConfiguration($configuration))->updateConfiguration([
            'nosniff' => true,
            'frame_options' => 'DENY',
            'referrer_policy' => 'no-referrer',
            'hsts' => true,
            'hsts_max_age' => -5,
            'hsts_subdomains' => true,
            'hsts_preload' => false,
            'permissions_policy' => '  geolocation=()  ',
        ]);

        $this->assertSame('1', $written['PS_SEC_NOSNIFF']);
        $this->assertSame('DENY', $written['PS_SEC_FRAME_OPTIONS']);
        $this->assertSame('1', $written['PS_SEC_HSTS']);
        $this->assertSame('0', $written['PS_SEC_HSTS_MAX_AGE'], 'A negative max-age is clamped to 0');
        $this->assertSame('geolocation=()', $written['PS_SEC_PERMISSIONS_POLICY'], 'The policy is trimmed');
    }
}
