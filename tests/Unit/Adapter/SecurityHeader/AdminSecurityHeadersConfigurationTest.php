<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\SecurityHeader;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\SecurityHeader\AdminSecurityHeadersConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * The back office is a single global surface, so the admin security-header settings are read and written
 * at the all-shops scope (the PS_SEC_ADMIN_* keys), independent of the shop selected in the back office.
 */
class AdminSecurityHeadersConfigurationTest extends TestCase
{
    public function testItReadsTheAdminKeysAtTheAllShopsScope(): void
    {
        $store = [
            'PS_SEC_ADMIN_NOSNIFF' => '1',
            'PS_SEC_ADMIN_FRAME_OPTIONS' => 'SAMEORIGIN',
            'PS_SEC_ADMIN_REFERRER_POLICY' => 'strict-origin',
            'PS_SEC_ADMIN_PERMISSIONS_POLICY' => 'camera=()',
        ];
        $scopes = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(
            function (string $key, $default = null, ?ShopConstraint $scope = null) use ($store, &$scopes) {
                $scopes[] = $scope;

                return $store[$key] ?? $default;
            }
        );

        $config = (new AdminSecurityHeadersConfiguration($configuration))->getConfiguration();

        $this->assertSame([
            'nosniff' => true,
            'frame_options' => 'SAMEORIGIN',
            'referrer_policy' => 'strict-origin',
            'permissions_policy' => 'camera=()',
        ], $config);
        $this->assertNotEmpty($scopes);
        foreach ($scopes as $scope) {
            $this->assertEquals(ShopConstraint::allShops(), $scope);
        }
    }

    public function testItFallsBackToTheSecureDefaultsWhenNoKeysAreSet(): void
    {
        // Every key absent (never seeded): the form must still reflect the hardened defaults.
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturnCallback(fn (string $key, $default = null) => $default);

        $config = (new AdminSecurityHeadersConfiguration($configuration))->getConfiguration();

        $this->assertSame([
            'nosniff' => true,
            'frame_options' => 'SAMEORIGIN',
            'referrer_policy' => 'strict-origin',
            'permissions_policy' => 'camera=(), microphone=(), geolocation=()',
        ], $config);
    }

    public function testItWritesTheAdminKeysAtTheAllShopsScopeAndStripsNewlines(): void
    {
        $written = [];
        $scopes = [];
        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('set')->willReturnCallback(
            function (string $key, $value, ?ShopConstraint $scope = null) use (&$written, &$scopes) {
                $written[$key] = $value;
                $scopes[] = $scope;
            }
        );

        (new AdminSecurityHeadersConfiguration($configuration))->updateConfiguration([
            'nosniff' => true,
            'frame_options' => 'SAMEORIGIN',
            'referrer_policy' => 'strict-origin',
            'permissions_policy' => "camera=()\r\nSet-Cookie: x=1",
        ]);

        $this->assertSame('1', $written['PS_SEC_ADMIN_NOSNIFF']);
        $this->assertSame('SAMEORIGIN', $written['PS_SEC_ADMIN_FRAME_OPTIONS']);
        $this->assertSame('strict-origin', $written['PS_SEC_ADMIN_REFERRER_POLICY']);
        $this->assertSame('camera=()Set-Cookie: x=1', $written['PS_SEC_ADMIN_PERMISSIONS_POLICY']);
        $this->assertStringNotContainsString("\n", $written['PS_SEC_ADMIN_PERMISSIONS_POLICY']);
        foreach ($scopes as $scope) {
            $this->assertEquals(ShopConstraint::allShops(), $scope);
        }
    }
}
