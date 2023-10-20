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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\RelatedProductsCommandsBuilder;

class RelatedProductsCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new RelatedProductsCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        yield [
            [
                'random' => ['useless'],
            ],
            [],
        ];

        yield [
            [
                'description' => [],
            ],
            [],
        ];

        yield [
            [
                'description' => [
                    'related_products' => [],
                ],
            ],
            [new RemoveAllRelatedProductsCommand($this->getProductId()->getValue())],
        ];

        $command = new SetRelatedProductsCommand($this->getProductId()->getValue(), [23]);
        yield [
            [
                'description' => [
                    'related_products' => [
                        [
                            'id' => 23,
                            'name' => 'dontcare',
                            'image' => 'notused',
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new SetRelatedProductsCommand($this->getProductId()->getValue(), [23, 42]);
        yield [
            [
                'description' => [
                    'related_products' => [
                        [
                            'id' => 23,
                            'name' => 'dontcare',
                            'image' => 'notused',
                        ],
                        [
                            'id' => '42',
                            'name' => 'dontcare',
                            'image' => 'notused',
                        ],
                        [
                            'id' => 23,
                            'name' => 'dontcare',
                            'image' => 'notused',
                        ],
                    ],
                ],
            ],
            [$command],
        ];
    }
}
