<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Preferences;

use Cookie;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Preferences\PreferencesConfiguration;

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
            ->setMethods(['get', 'getBoolean', 'set'])
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
                    ['PS_SHOP_ACTIVITY', null, null, 'test'],
                ]
            );

        $this->mockConfiguration
            ->method('getBoolean')
            ->willReturnMap(
                [
                    ['PS_SSL_ENABLED', false, true],
                    ['PS_SSL_ENABLED_EVERYWHERE', false, true],
                    ['PS_TOKEN_ENABLE', false, true],
                    ['PS_ALLOW_HTML_IFRAME', false, true],
                    ['PS_USE_HTMLPURIFIER', false, true],
                    ['PS_DISPLAY_SUPPLIERS', false, false],
                    ['PS_DISPLAY_MANUFACTURERS', false, true],
                    ['PS_DISPLAY_BEST_SELLERS', false, false],
                    ['PS_MULTISHOP_FEATURE_ACTIVE', false, true],
                ]
            );

        $result = $this->object->getConfiguration();
        $this->assertSame(
            [
                'enable_ssl' => true,
                'enable_ssl_everywhere' => true,
                'enable_token' => true,
                'allow_html_iframes' => true,
                'use_htmlpurifier' => true,
                'price_round_mode' => 'test',
                'price_round_type' => 'test',
                'display_suppliers' => false,
                'display_manufacturers' => true,
                'display_best_sellers' => false,
                'multishop_feature_active' => true,
                'shop_activity' => 'test',
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
                    ['PS_COOKIE_SAMESITE', null, null, Cookie::SAMESITE_NONE],
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
                    'enable_ssl_everywhere' => false,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 'test',
                    'price_round_type' => 'test',
                    'display_suppliers' => false,
                    'display_manufacturers' => true,
                    'display_best_sellers' => false,
                    'multishop_feature_active' => true,
                    'shop_activity' => 'test',
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
                    ['PS_COOKIE_SAMESITE', null, null, Cookie::SAMESITE_NONE],
                ]
            );
        $this->mockConfiguration
            ->method('set')
            ->willReturnMap(
                [
                    ['PS_SSL_ENABLED', true],
                    ['PS_SSL_ENABLED_EVERYWHERE', true],
                    ['PS_TOKEN_ENABLE', true],
                    ['PS_ALLOW_HTML_IFRAME', true],
                    ['PS_USE_HTMLPURIFIER', true],
                    ['PS_DISPLAY_SUPPLIERS', false],
                    ['PS_DISPLAY_MANUFACTURERS', true],
                    ['PS_DISPLAY_BEST_SELLERS', false],
                    ['PS_MULTISHOP_FEATURE_ACTIVE', true],
                    ['PS_PRICE_ROUND_MODE', 'test'],
                    ['PS_ROUND_TYPE', 'test'],
                    ['PS_SHOP_ACTIVITY', 'test'],
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
                    'enable_ssl_everywhere' => false,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 'test',
                    'price_round_type' => 'test',
                    'display_suppliers' => false,
                    'display_manufacturers' => true,
                    'display_best_sellers' => false,
                    'multishop_feature_active' => true,
                    'shop_activity' => 'test',
                ]
            )
        );
    }
}
