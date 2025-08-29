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

namespace Tests\Unit\PrestaShopBundle\Utils;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Utils\Tree;

class TreeTest extends TestCase
{
    public function testItListsChildrenIds(): void
    {
        $tree = [
            [
                'element_id' => 1,
                'children' => [
                    [
                        'element_id' => 2,
                        'children' => [
                            [
                                'element_id' => '4',
                                'children' => [],
                            ],
                            [
                                'element_id' => 6,
                                'children' => [],
                            ],
                            [
                                'element_id' => 7,
                            ],
                        ],
                    ],
                    [
                        'element_id' => 10,
                        'children' => [
                            [
                                'element_id' => 12,
                                'children' => [],
                            ],
                            [
                                'element_id' => 13,
                                'children' => [],
                            ],
                        ],
                    ],
                    [
                        'element_id' => 15,
                        'children' => [
                            [
                                'element_id' => 13,
                                'children' => [],
                            ],
                            [
                                'element_id' => 20,
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $idList = [
            1 => 1,
            2 => 2,
            4 => 4,
            6 => 6,
            7 => 7,
            10 => 10,
            12 => 12,
            13 => 13,
            15 => 15,
            20 => 20,
        ];

        $getChildren = function (array $element) {
            return isset($element['children']) ? $element['children'] : [];
        };

        $getId = function ($element) {
            return (int) $element['element_id'];
        };

        $this->assertSame($idList, Tree::extractChildrenId($tree, $getChildren, $getId));
    }
}
