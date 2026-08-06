<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Preferences;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Preferences\PreferencesConfiguration;
use PrestaShop\PrestaShop\Core\Feature\Enum\ShopModeEnum;
use PrestaShop\PrestaShop\Core\Feature\ShopModeFeature;
use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\General\PreferencesType;

class PreferencesConfigurationTest extends TestCase
{
    /**
     * @var PreferencesConfiguration
     */
    private $object;

    /**
     * @var Configuration|MockObject
     */
    private $mockConfiguration;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->getMockBuilder(Configuration::class)
            ->onlyMethods(['get', 'getBoolean', 'set', 'getEnum'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new PreferencesConfiguration($this->mockConfiguration);
    }

    public function testGetConfiguration()
    {
        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_PRICE_ROUND_MODE', null, null, 'test'],
                    ['PS_ROUND_TYPE', null, null, 'test'],
                ]
            );

        $this->mockConfiguration
            ->method('getBoolean')
            ->willReturnMap(
                [
                    ['PS_SSL_ENABLED', false, true],
                    ['PS_TOKEN_ENABLE', false, true],
                    ['PS_ALLOW_HTML_IFRAME', false, true],
                    ['PS_USE_HTMLPURIFIER', false, true],
                    ['PS_DISPLAY_SUPPLIERS', false, false],
                    ['PS_DISPLAY_MANUFACTURERS', false, true],
                    ['PS_DISPLAY_BEST_SELLERS', false, false],
                    ['PS_MULTISHOP_FEATURE_ACTIVE', false, true],
                ]
            );

        $this->mockConfiguration
            ->method('getEnum')
            ->willReturnMap(
                [
                    [ShopModeFeature::CONFIGURATION_NAME, ShopModeEnum::class, ShopModeFeature::DEFAULT_SHOP_MODE, ShopModeEnum::SHOP_MODE_B2C_ONLY],
                ]
            );

        $result = $this->object->getConfiguration();
        $this->assertSame(
            [
                'enable_ssl' => true,
                'enable_token' => true,
                PreferencesType::SHOP_MODE => ShopModeEnum::SHOP_MODE_B2C_ONLY,
                'allow_html_iframes' => true,
                'use_htmlpurifier' => true,
                'price_round_mode' => 'test',
                'price_round_type' => 'test',
                'display_suppliers' => false,
                'display_manufacturers' => true,
                'display_best_sellers' => false,
                'multishop_feature_active' => true,
            ],
            $result
        );
    }

    public function testUpdateConfigurationWithInvalidConfiguration()
    {
        $this->assertSame(
            [
                [
                    'key' => 'Invalid configuration',
                    'domain' => 'Admin.Notifications.Warning',
                    'parameters' => [],
                ],
            ],
            $this->object->updateConfiguration([])
        );
    }

    public function testUpdateConfigurationWithInvalidSSLConfiguration()
    {
        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_COOKIE_SAMESITE', null, null, CookieOptions::SAMESITE_NONE],
                ]
            );

        $this->assertSame(
            [
                [
                    'key' => 'Cannot disable SSL configuration due to the Cookie SameSite=None.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ],
            ],

            $this->object->updateConfiguration(
                [
                    'enable_ssl' => false,
                    'enable_token' => true,
                    PreferencesType::SHOP_MODE => ShopModeEnum::SHOP_MODE_B2C_ONLY,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 'test',
                    'price_round_type' => 'test',
                    'display_suppliers' => false,
                    'display_manufacturers' => true,
                    'display_best_sellers' => false,
                    'multishop_feature_active' => true,
                ]
            )
        );
    }

    public function testUpdateConfiguration()
    {
        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_COOKIE_SAMESITE', null, null, CookieOptions::SAMESITE_NONE],
                ]
            );

        $this->mockConfiguration
            ->method('set')
            ->willReturnMap(
                [
                    ['PS_SSL_ENABLED', true],
                    ['PS_TOKEN_ENABLE', true],
                    [ShopModeFeature::CONFIGURATION_NAME, ShopModeEnum::SHOP_MODE_B2C_ONLY],
                    ['PS_ALLOW_HTML_IFRAME', true],
                    ['PS_USE_HTMLPURIFIER', true],
                    ['PS_DISPLAY_SUPPLIERS', false],
                    ['PS_DISPLAY_MANUFACTURERS', true],
                    ['PS_DISPLAY_BEST_SELLERS', false],
                    ['PS_MULTISHOP_FEATURE_ACTIVE', true],
                    ['PS_PRICE_ROUND_MODE', 'test'],
                    ['PS_ROUND_TYPE', 'test'],
                ]
            );

        $this->assertSame(
            [],
            $this->object->updateConfiguration(
                [
                    'enable_ssl' => true,
                    'enable_token' => true,
                    PreferencesType::SHOP_MODE => ShopModeEnum::SHOP_MODE_B2C_ONLY,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 'test',
                    'price_round_type' => 'test',
                    'display_suppliers' => false,
                    'display_manufacturers' => true,
                    'display_best_sellers' => false,
                    'multishop_feature_active' => true,
                ]
            )
        );
    }
}
