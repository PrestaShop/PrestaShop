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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCategoriesCommandsBuilder;

class ProductCategoriesCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new ProductCategoriesCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedCommands(): Generator
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'description' => [
                    'categories' => [
                    ],
                ],
            ],
            [],
        ];

        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            42,
            [42, 49, 51]
        );
        yield [
            [
                'description' => [
                    'categories' => [
                        'product_categories' => [
                            0 => [
                                'name' => 'name is not important its only for presentation',
                                'id' => 42,
                            ],
                            1 => [
                                'id' => 49,
                            ],
                            2 => [
                                'id' => 51,
                            ],
                        ],
                        'default_category_id' => 42,
                    ],
                ],
            ],
            [$command],
        ];

        // default category which is not one of selected categories
        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            51,
            [42, 49, 51]
        );
        yield [
            [
                'description' => [
                    'categories' => [
                        'product_categories' => [
                            0 => [
                                'name' => 'name is not important its only for presentation',
                                'id' => 42,
                            ],
                            1 => [
                                'id' => 49,
                            ],
                        ],
                        'default_category_id' => 51,
                    ],
                ],
            ],
            [$command],
        ];

        // no default category id provided. First one taken as default
        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            42,
            [42, 49, 51]
        );
        yield [
            [
                'description' => [
                    'categories' => [
                        'product_categories' => [
                            0 => [
                                'name' => 'name is not important its only for presentation',
                                'id' => 42,
                            ],
                            1 => [
                                'id' => 49,
                            ],
                            2 => [
                                'id' => 51,
                            ],
                        ],
                        'default_category_id' => null,
                    ],
                ],
            ],
            [$command],
        ];

        // No associations means remove all
        $command = new RemoveAllAssociatedProductCategoriesCommand($this->getProductId()->getValue());
        yield [
            [
                'description' => [
                    'categories' => [
                        'product_categories' => [],
                    ],
                ],
            ],
            [$command],
        ];
    }
}
