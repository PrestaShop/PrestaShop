<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\Service\DataProvider\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;

class CategoriesProviderTest extends TestCase
{
    /**
     * @var CategoriesProvider
     */
    private $provider;

    public function setUp()
    {
        $yamlParser = new YamlParser(_PS_CACHE_DIR_);
        $prestashopAddonsConfig = $yamlParser->parse(_PS_ROOT_DIR_ . '/app/config/addons/categories.yml');

        $this->provider = new CategoriesProvider(
            $prestashopAddonsConfig['prestashop']['addons']['categories'],
            ['cronjobs', 'gamification']
        );
    }

    public function testGetCategoriesMenuWithoutModules()
    {
        $this->assertEquals(
            [
                'categories' => (object) [
                    'tab' => null,
                    'name' => 'Categories',
                    'refMenu' => 'categories',
                    'modules' => [],
                    'subMenu' => [
                        'Administration' => (object) [
                            'tab' => 'administration',
                            'name' => 'Administration',
                            'refMenu' => '440',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Design & Navigation' => (object) [
                            'tab' => 'front_office_features',
                            'name' => 'Design & Navigation',
                            'refMenu' => '507',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Promotions & Marketing' => (object) [
                            'tab' => 'pricing_promotion',
                            'name' => 'Promotions & Marketing',
                            'refMenu' => '496',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Product Page' => (object) [
                            'tab' => 'administration',
                            'name' => 'Product Page',
                            'refMenu' => '460',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Payment' => (object) [
                            'tab' => 'payments_gateways',
                            'name' => 'Payment',
                            'refMenu' => '481',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Shipping & Logistics' => (object) [
                            'tab' => 'shipping_logistics',
                            'name' => 'Shipping & Logistics',
                            'refMenu' => '518',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Traffic & Marketplaces' => (object) [
                            'tab' => 'checkout',
                            'name' => 'Traffic & Marketplaces',
                            'refMenu' => '488',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Customers' => (object) [
                            'tab' => 'administration',
                            'name' => 'Customers',
                            'refMenu' => '475',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Facebook & Social Networks' => (object) [
                            'tab' => 'advertising_marketing',
                            'name' => 'Facebook & Social Networks',
                            'refMenu' => '455',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Specialized Platforms' => (object) [
                            'tab' => 'others',
                            'name' => 'Specialized Platforms',
                            'refMenu' => '469',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'other' => (object) [
                            'tab' => 'other',
                            'name' => 'Other',
                            'refMenu' => 'other',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                    ],
                ],
            ],
            $this->provider->getCategoriesMenu([])
        );
    }

    public function testGetCategoriesMenuWithModules()
    {
        $gamification = $this->mockModule('gamification');
        $this->assertEquals(
            [
                'categories' => (object) [
                    'tab' => null,
                    'name' => 'Categories',
                    'refMenu' => 'categories',
                    'modules' => [],
                    'subMenu' => [
                        'Administration' => (object) [
                            'tab' => 'administration',
                            'name' => 'Administration',
                            'refMenu' => '440',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Design & Navigation' => (object) [
                            'tab' => 'front_office_features',
                            'name' => 'Design & Navigation',
                            'refMenu' => '507',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Promotions & Marketing' => (object) [
                            'tab' => 'pricing_promotion',
                            'name' => 'Promotions & Marketing',
                            'refMenu' => '496',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Product Page' => (object) [
                            'tab' => 'administration',
                            'name' => 'Product Page',
                            'refMenu' => '460',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Payment' => (object) [
                            'tab' => 'payments_gateways',
                            'name' => 'Payment',
                            'refMenu' => '481',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Shipping & Logistics' => (object) [
                            'tab' => 'shipping_logistics',
                            'name' => 'Shipping & Logistics',
                            'refMenu' => '518',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Traffic & Marketplaces' => (object) [
                            'tab' => 'checkout',
                            'name' => 'Traffic & Marketplaces',
                            'refMenu' => '488',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Customers' => (object) [
                            'tab' => 'administration',
                            'name' => 'Customers',
                            'refMenu' => '475',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Facebook & Social Networks' => (object) [
                            'tab' => 'advertising_marketing',
                            'name' => 'Facebook & Social Networks',
                            'refMenu' => '455',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Specialized Platforms' => (object) [
                            'tab' => 'others',
                            'name' => 'Specialized Platforms',
                            'refMenu' => '469',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'other' => (object) [
                            'tab' => 'other',
                            'name' => 'Other',
                            'refMenu' => 'other',
                            'modules' => [$gamification],
                            'subMenu' => [],
                        ],
                    ],
                ],
            ],
            $this->provider->getCategoriesMenu(
                [
                    $gamification,
                ]
            )
        );
    }

    public function testGetCategoriesMenuWithModulesWithCustomTab()
    {
        $gamification = $this->mockModule('gamification', 'administration');
        $cronjobs = $this->mockModule('cronjobs', 'front_office_features');
        $this->assertEquals(
            [
                'categories' => (object) [
                    'tab' => null,
                    'name' => 'Categories',
                    'refMenu' => 'categories',
                    'modules' => [],
                    'subMenu' => [
                        'Administration' => (object) [
                            'tab' => 'administration',
                            'name' => 'Administration',
                            'refMenu' => '440',
                            'modules' => [$gamification],
                            'subMenu' => [],
                        ],
                        'Design & Navigation' => (object) [
                            'tab' => 'front_office_features',
                            'name' => 'Design & Navigation',
                            'refMenu' => '507',
                            'modules' => [$cronjobs],
                            'subMenu' => [],
                        ],
                        'Promotions & Marketing' => (object) [
                            'tab' => 'pricing_promotion',
                            'name' => 'Promotions & Marketing',
                            'refMenu' => '496',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Product Page' => (object) [
                            'tab' => 'administration',
                            'name' => 'Product Page',
                            'refMenu' => '460',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Payment' => (object) [
                            'tab' => 'payments_gateways',
                            'name' => 'Payment',
                            'refMenu' => '481',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Shipping & Logistics' => (object) [
                            'tab' => 'shipping_logistics',
                            'name' => 'Shipping & Logistics',
                            'refMenu' => '518',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Traffic & Marketplaces' => (object) [
                            'tab' => 'checkout',
                            'name' => 'Traffic & Marketplaces',
                            'refMenu' => '488',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Customers' => (object) [
                            'tab' => 'administration',
                            'name' => 'Customers',
                            'refMenu' => '475',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Facebook & Social Networks' => (object) [
                            'tab' => 'advertising_marketing',
                            'name' => 'Facebook & Social Networks',
                            'refMenu' => '455',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Specialized Platforms' => (object) [
                            'tab' => 'others',
                            'name' => 'Specialized Platforms',
                            'refMenu' => '469',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'other' => (object) [
                            'tab' => 'other',
                            'name' => 'Other',
                            'refMenu' => 'other',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                    ],
                ],
            ],
            $this->provider->getCategoriesMenu(
                [
                    $gamification,
                    $cronjobs,
                ]
            )
        );
    }

    public function testGetCategoriesMenuWithModulesWithParentEnglishName()
    {
        $gamification = $this->mockModule('gamification', null, 'Promotions & Marketing');
        $cronjobs = $this->mockModule('cronjobs', null, 'Product Page');
        $noTabsNoCategories = $this->mockModule('example');
        $this->assertEquals(
            [
                'categories' => (object) [
                    'tab' => null,
                    'name' => 'Categories',
                    'refMenu' => 'categories',
                    'modules' => [],
                    'subMenu' => [
                        'Administration' => (object) [
                            'tab' => 'administration',
                            'name' => 'Administration',
                            'refMenu' => '440',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Design & Navigation' => (object) [
                            'tab' => 'front_office_features',
                            'name' => 'Design & Navigation',
                            'refMenu' => '507',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Promotions & Marketing' => (object) [
                            'tab' => 'pricing_promotion',
                            'name' => 'Promotions & Marketing',
                            'refMenu' => '496',
                            'modules' => [$gamification],
                            'subMenu' => [],
                        ],
                        'Product Page' => (object) [
                            'tab' => 'administration',
                            'name' => 'Product Page',
                            'refMenu' => '460',
                            'modules' => [$cronjobs],
                            'subMenu' => [],
                        ],
                        'Payment' => (object) [
                            'tab' => 'payments_gateways',
                            'name' => 'Payment',
                            'refMenu' => '481',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Shipping & Logistics' => (object) [
                            'tab' => 'shipping_logistics',
                            'name' => 'Shipping & Logistics',
                            'refMenu' => '518',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Traffic & Marketplaces' => (object) [
                            'tab' => 'checkout',
                            'name' => 'Traffic & Marketplaces',
                            'refMenu' => '488',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Customers' => (object) [
                            'tab' => 'administration',
                            'name' => 'Customers',
                            'refMenu' => '475',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Facebook & Social Networks' => (object) [
                            'tab' => 'advertising_marketing',
                            'name' => 'Facebook & Social Networks',
                            'refMenu' => '455',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'Specialized Platforms' => (object) [
                            'tab' => 'others',
                            'name' => 'Specialized Platforms',
                            'refMenu' => '469',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                        'other' => (object) [
                            'tab' => 'other',
                            'name' => 'Other',
                            'refMenu' => 'other',
                            'modules' => [$noTabsNoCategories],
                            'subMenu' => [],
                        ],
                    ],
                ],
            ],
            $this->provider->getCategoriesMenu(
                [
                    $gamification,
                    $cronjobs,
                    $noTabsNoCategories,
                ]
            )
        );
    }

    private function mockModule(string $moduleName, string $tab = null, string $categoryName = null)
    {
        $mock = $this->getMockBuilder(Module::class)
              ->getMock();

        if ($tab !== null) {
            $mock->attributes->set('tab', $tab);
        }

        if ($categoryName !== null) {
            $mock->attributes->set('categoryParentEnglishName', $categoryName);
        }

        return $mock;
    }

    public function testGetParentCategoryWithoutResult()
    {
        $this->assertEquals('Test', $this->provider->getParentCategory('Test'));
    }

    public function testGetParentCategory()
    {
        $this->assertEquals('Administration', $this->provider->getParentCategory('Registration & Ordering Process'));
    }
}
