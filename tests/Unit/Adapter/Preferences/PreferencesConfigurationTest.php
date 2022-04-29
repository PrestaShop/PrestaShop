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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Preferences;

use PrestaShop\PrestaShop\Adapter\Preferences\PreferencesConfiguration;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Tests\TestCase\AbstractConfigurationTestCase;

class PreferencesConfigurationTest extends AbstractConfigurationTestCase
{
    private const SHOP_ID = 42;

    /**
     * @dataProvider provideShopConstraints
     *
     * @param ShopConstraint $shopConstraint
     */
    private $mockConfiguration;

    /**
     * @var FeatureFlagRepository|MockObject
     */
    private $featureFlagRepository;

    protected function setUp(): void

    /**
     * @var ShopContext
     */
    private $mockShopConfiguration;

    /**
     * @var FeatureInterface
     */
    private $mockMultistoreFeature;

    protected function setUp(): void
    {
        $this->mockConfiguration = $this->getMockBuilder(Configuration::class)
            ->setMethods(['get', 'getBoolean', 'set'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->featureFlagRepository = $this->getMockBuilder(FeatureFlagRepository::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->featureFlagRepository
            ->method('get')
            ->willReturn(false);
    }

    public function testGetConfiguration(ShopConstraint $shopConstraint): void
    {
        $preferencesConfiguration = new PreferencesConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->mockShopConfiguration
            ->method('getShopConstraint')
            ->willReturn($shopConstraint);

        $this->mockConfiguration
            ->method('get')
            ->willReturnMap(
                [
                    ['PS_SSL_ENABLED', true, $shopConstraint, true],
                    ['PS_SSL_ENABLED_EVERYWHERE', true, $shopConstraint, true],
                    ['PS_TOKEN_ENABLE', true, $shopConstraint, true],
                    ['PS_ALLOW_HTML_IFRAME', true, $shopConstraint, true],
                    ['PS_USE_HTMLPURIFIER', true, $shopConstraint, true],
                    ['PS_PRICE_ROUND_MODE', 1, $shopConstraint, 1],
                    ['PS_ROUND_TYPE', 1, $shopConstraint, 1],
                    ['PS_DISPLAY_SUPPLIERS', true, $shopConstraint, true],
                    ['PS_DISPLAY_MANUFACTURERS', true, $shopConstraint, true],
                    ['PS_DISPLAY_BEST_SELLERS', true, $shopConstraint, true],
                    ['PS_MULTISHOP_FEATURE_ACTIVE', true, $shopConstraint, true],
                    ['PS_SHOP_ACTIVITY', 1, $shopConstraint, 1],
                ]
            );

        $result = $preferencesConfiguration->getConfiguration();

        $this->assertSame(
            [
                'enable_ssl' => true,
                'enable_ssl_everywhere' => true,
                'enable_token' => true,
                'allow_html_iframes' => true,
                'use_htmlpurifier' => true,
                'price_round_mode' => 1,
                'price_round_type' => 1,
                'display_suppliers' => true,
                'display_manufacturers' => true,
                'display_best_sellers' => true,
                'multishop_feature_active' => true,
                'shop_activity' => 1,
            ],
            $result
        );
    }

    /**
     * @dataProvider provideInvalidConfiguration
     *
     * @param string $exception
     * @param array $values
     */
    public function testUpdateConfigurationWithInvalidConfiguration(string $exception, array $values): void
    {
        $maintenanceConfiguration = new PreferencesConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $this->expectException($exception);
        $maintenanceConfiguration->updateConfiguration($values);
    }

    /**
     * @return array[]
     */
    public function provideInvalidConfiguration(): array
    {
        return [
            [UndefinedOptionsException::class, ['does_not_exist' => 'does_not_exist']],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => 'invalid_value_type',
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => 'invalid_value_type',
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => 'invalid_value_type',
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => 'invalid_value_type',
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => 'invalid_value_type',
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 'invalid_value_type',
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 'invalid_value_type',
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => 'invalid_value_type',
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => 'invalid_value_type',
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => 'invalid_value_type',
                    'multishop_feature_active' => true,
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => 'invalid_value_type',
                    'shop_activity' => 1,
                ],
            ],
            [
                InvalidOptionsException::class,
                [
                    'enable_ssl' => true,
                    'enable_ssl_everywhere' => true,
                    'enable_token' => true,
                    'allow_html_iframes' => true,
                    'use_htmlpurifier' => true,
                    'price_round_mode' => 1,
                    'price_round_type' => 1,
                    'display_suppliers' => true,
                    'display_manufacturers' => true,
                    'display_best_sellers' => true,
                    'multishop_feature_active' => true,
                    'shop_activity' => 'invalid_value_type',
                ],
            ],
        ];
    }

    public function testSuccessfulUpdate(): void
    {
        $preferencesConfiguration = new PreferencesConfiguration($this->mockConfiguration, $this->mockShopConfiguration, $this->mockMultistoreFeature);

        $res = $preferencesConfiguration->updateConfiguration([
            'enable_ssl' => true,
            'enable_ssl_everywhere' => true,
            'enable_token' => true,
            'allow_html_iframes' => true,
            'use_htmlpurifier' => true,
            'price_round_mode' => 1,
            'price_round_type' => 1,
            'display_suppliers' => true,
            'display_manufacturers' => true,
            'display_best_sellers' => true,
            'multishop_feature_active' => true,
            'shop_activity' => 1,
        ]);

        $this->assertSame([], $res);
    }

    /**
     * @return array[]
     */
    public function provideShopConstraints(): array
    {
        return [
            [ShopConstraint::shop(self::SHOP_ID)],
            [ShopConstraint::shopGroup(self::SHOP_ID)],
            [ShopConstraint::allShops()],
        ];
    }
}
