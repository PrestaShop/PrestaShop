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
                'categories' => [
                ],
            ],
            [],
        ];

        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            42,
            [42, 51]
        );
        yield [
            [
                'categories' => [
                    'product_categories' => [
                        42 => [
                            'is_associated' => true,
                            'is_default' => true,
                        ],
                        49 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                        51 => [
                            'is_associated' => true,
                            'is_default' => false,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        // Use last defined as default as default
        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            51,
            [42, 51]
        );
        yield [
            [
                'categories' => [
                    'product_categories' => [
                        42 => [
                            'is_associated' => true,
                            'is_default' => true,
                        ],
                        49 => [
                            'is_associated' => false,
                            'is_default' => true,
                        ],
                        51 => [
                            'is_associated' => true,
                            'is_default' => true,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        // Default is always amongst the list
        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            49,
            [42, 51, 49]
        );
        yield [
            [
                'categories' => [
                    'product_categories' => [
                        42 => [
                            'is_associated' => true,
                            'is_default' => false,
                        ],
                        49 => [
                            'is_associated' => false,
                            'is_default' => true,
                        ],
                        51 => [
                            'is_associated' => true,
                            'is_default' => false,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new RemoveAllAssociatedProductCategoriesCommand($this->getProductId()->getValue());
        yield [
            [
                'categories' => [
                    'product_categories' => [
                    ],
                ],
            ],
            [$command],
        ];

        // Use first associated as default
        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            49,
            [49, 51]
        );
        yield [
            [
                'categories' => [
                    'product_categories' => [
                        42 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                        49 => [
                            'is_associated' => true,
                            'is_default' => false,
                        ],
                        51 => [
                            'is_associated' => true,
                            'is_default' => false,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        // Default is always associated
        $command = new SetAssociatedProductCategoriesCommand(
            $this->getProductId()->getValue(),
            49,
            [49]
        );
        yield [
            [
                'categories' => [
                    'product_categories' => [
                        42 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                        49 => [
                            'is_associated' => false,
                            'is_default' => true,
                        ],
                        51 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        // No associations means remove all
        $command = new RemoveAllAssociatedProductCategoriesCommand($this->getProductId()->getValue());
        yield [
            [
                'categories' => [
                    'product_categories' => [
                        42 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                        49 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                        51 => [
                            'is_associated' => false,
                            'is_default' => false,
                        ],
                    ],
                ],
            ],
            [$command],
        ];
    }
}
