<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
