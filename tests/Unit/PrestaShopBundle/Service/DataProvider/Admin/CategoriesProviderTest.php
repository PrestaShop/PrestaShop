<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Service\DataProvider\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShop\PrestaShop\Core\Util\File\YamlParser;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use stdClass;

class CategoriesProviderTest extends TestCase
{
    /**
     * @var CategoriesProvider
     */
    private $provider;

    public function setUp(): void
    {
        $yamlParser = new YamlParser(_PS_CACHE_DIR_);
        $prestashopAddonsConfig = $yamlParser->parse(__DIR__ . '/fixtures/categories.yml');

        $this->provider = new CategoriesProvider(
            $prestashopAddonsConfig['prestashop']['addons']['categories'],
            ['my_theme']
        );
    }

    public function testGetCategories(): void
    {
        $categories = $this->provider->getCategories();
        $this->assertArrayHasKey(
            'categories',
            $categories
        );
        $this->assertInstanceOf(stdClass::class, $categories['categories']);
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
                        'theme_modules' => (object) [
                            'tab' => 'theme_modules',
                            'name' => 'Theme modules',
                            'refMenu' => 'theme_modules',
                            'modules' => [],
                            'subMenu' => [],
                        ],
                    ],
                ],
            ],
            $this->provider->getCategoriesMenu([])
        );
    }

    public function testGetCategoriesMenuWithModulesAndThemes()
    {
        $gamification = $this->mockModule('gamification');
        $myCustomTheme = $this->mockModule('my_theme', null);
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
                        'theme_modules' => (object) [
                            'tab' => 'theme_modules',
                            'name' => 'Theme modules',
                            'refMenu' => 'theme_modules',
                            'modules' => [$myCustomTheme],
                            'subMenu' => [],
                        ],
                    ],
                ],
            ],
            $this->provider->getCategoriesMenu(
                [
                    $gamification,
                    $myCustomTheme,
                ]
            )
        );
    }

    public function testGetCategoriesMenuWithModulesWithCustomTab()
    {
        $gamification = $this->mockModule('gamification', 'administration');
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
                        'theme_modules' => (object) [
                            'tab' => 'theme_modules',
                            'name' => 'Theme modules',
                            'refMenu' => 'theme_modules',
                            'modules' => [],
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

    private function mockModule(string $moduleName, ?string $tab = null)
    {
        $mock = $this->getMockBuilder(Module::class)
            ->getMock();
        $mock->attributes->set('name', $moduleName);

        if ($tab !== null) {
            $mock->attributes->set('tab', $tab);
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
