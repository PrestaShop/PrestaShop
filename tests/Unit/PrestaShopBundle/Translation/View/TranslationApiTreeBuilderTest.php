<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\PrestaShopBundle\Translation\View;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\View\TranslationApiTreeBuilder;
use PrestaShopBundle\Translation\View\TreeBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class TranslationApiTreeBuilderTest extends TestCase
{

    /**
     * @param array[] $translationsArray
     * @param array[] $expectedTree
     *
     * @dataProvider provideTestCases
     */
    public function testItBuildsDomainTree(array $translationsArray, array $expectedTree)
    {
        $builder = new TranslationApiTreeBuilder($this->buildFakeRouter(), new TreeBuilder());

        $tree = $builder->buildDomainTreeForApi($translationsArray, 'en-US');

        $this->assertSame($expectedTree, $tree);
    }

    public function provideTestCases()
    {
        return  [
            [
                'translationsArray' => [
                    'AdminFooBar' => [
                        'someMessage' => [],
                        'someotherMessage' => [],
                        '__metadata' => [
                            'count' => 2,
                            'missing_translations' => 1
                        ],
                    ],
                    'AdminFooBaz' => [
                        'someMessage' => [],
                        'someotherMessage' => [],
                        '__metadata' => [
                            'count' => 2,
                            'missing_translations' => 2
                        ],
                    ],
                    'AdminPlop' => [
                        'someMessage' => [],
                        'someotherMessage' => [],
                        '__metadata' => [
                            'count' => 2,
                            'missing_translations' => 1
                        ],
                    ],
                    'AdminPlopFoo' => [
                        'someMessage' => [],
                        'someotherMessage' => [],
                        '__metadata' => [
                            'count' => 2,
                            'missing_translations' => 0
                        ],
                    ],
                    'AdminPlopBar' => [
                        'someMessage' => [],
                        'someotherMessage' => [],
                        'someAnotherMessage' => [],
                        '__metadata' => [
                            'count' => 3,
                            'missing_translations' => 1
                        ],
                    ],
                ],

                'expectedTree' => [
                    'tree' => [
                        'total_translations' => 11,
                        'total_missing_translations' => 5,
                        'children' => [
                            [
                                'name' => 'Admin',
                                'full_name' => 'Admin',
                                'domain_catalog_link' => '/fake-route/en-US/Admin',
                                'total_translations' => 11,
                                'total_missing_translations' => 5,
                                'children' => [
                                    [
                                        'name' => 'Foo',
                                        'full_name' => 'AdminFoo',
                                        'domain_catalog_link' => '/fake-route/en-US/AdminFoo',
                                        'total_translations' => 4,
                                        'total_missing_translations' => 3,
                                        'children' => [
                                            [
                                                'name' => 'Bar',
                                                'full_name' => 'AdminFooBar',
                                                'domain_catalog_link' => '/fake-route/en-US/AdminFooBar',
                                                'total_translations' => 2,
                                                'total_missing_translations' => 1,
                                            ],
                                            [
                                                'name' => 'Baz',
                                                'full_name' => 'AdminFooBaz',
                                                'domain_catalog_link' => '/fake-route/en-US/AdminFooBaz',
                                                'total_translations' => 2,
                                                'total_missing_translations' => 2,
                                            ],
                                        ],
                                    ],
                                    [
                                        'name' => 'Plop',
                                        'full_name' => 'AdminPlop',
                                        'domain_catalog_link' => '/fake-route/en-US/AdminPlop',
                                        'total_translations' => 7,
                                        'total_missing_translations' => 2,
                                        'children' => [
                                            [
                                                'name' => 'Foo',
                                                'full_name' => 'AdminPlopFoo',
                                                'domain_catalog_link' => '/fake-route/en-US/AdminPlopFoo',
                                                'total_translations' => 2,
                                                'total_missing_translations' => 0,
                                            ],
                                            [
                                                'name' => 'Bar',
                                                'full_name' => 'AdminPlopBar',
                                                'domain_catalog_link' => '/fake-route/en-US/AdminPlopBar',
                                                'total_translations' => 3,
                                                'total_missing_translations' => 1,
                                            ]
                                        ],
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Router
     */
    private function buildFakeRouter()
    {
        $mock = $this->createMock(Router::class);
        $mock->method('generate')
            ->willReturnCallback(
                function ($route, $routeParams) {
                    return "/fake-route/{$routeParams['locale']}/{$routeParams['domain']}";
                }
            );

        return $mock;
    }
}
