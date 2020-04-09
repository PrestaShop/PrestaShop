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
use PrestaShopBundle\Translation\View\TreeBuilder;

class TreeBuilderTest extends TestCase
{

    /**
     * @param array[] $translationsArray
     * @param array[] $expectedTree
     *
     * @dataProvider provideTestCases
     */
    public function testItBuildsAMetadataTree(array $translationsArray, array $expectedTree)
    {
        $builder = new TreeBuilder();

        $tree = $builder->buildDomainMetadataTree($translationsArray);

        $this->assertSame($expectedTree, $tree);
    }

    public function provideTestCases()
    {
        return [
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
                    '__metadata' => [
                        'count' => 11,
                        'missing_translations' => 5
                    ],
                    'Admin' => [
                        '__metadata' => [
                            'count' => 11,
                            'missing_translations' => 5
                        ],
                        'Foo' => [
                            '__metadata' => [
                                'count' => 4,
                                'missing_translations' => 3
                            ],
                            'Bar' => [
                                '__metadata' => [
                                    'count' => 2,
                                    'missing_translations' => 1
                                ],
                            ],
                            'Baz' => [
                                '__metadata' => [
                                    'count' => 2,
                                    'missing_translations' => 2
                                ],
                            ],
                        ],
                        'Plop' => [
                            '__metadata' => [
                                'count' => 7,
                                'missing_translations' => 2
                            ],
                            'Foo' => [
                                '__metadata' => [
                                    'count' => 2,
                                    'missing_translations' => 0
                                ],
                            ],
                            'Bar' => [
                                '__metadata' => [
                                    'count' => 3,
                                    'missing_translations' => 1
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
