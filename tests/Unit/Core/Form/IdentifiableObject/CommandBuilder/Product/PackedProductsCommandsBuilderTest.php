<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\RemoveAllProductsFromPackCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Command\SetPackProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\PackedProductsCommandsBuilder;

class PackedProductsCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new PackedProductsCommandsBuilder();
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
                'stock' => [],
            ],
            [],
        ];

        yield [
            [
                'stock' => [
                    'packed_products' => [],
                ],
            ],
            [],
        ];

        yield [
            [
                'stock' => [
                    'packed_products' => [
                        [
                            'product_id' => 23,
                            'name' => 'dontcare',
                            'image' => 'notused',
                            'quantity' => 3,
                            'combination_id' => 0,
                        ],
                    ],
                ],
            ],
            [],
        ];

        yield [
            [
                'header' => [
                    'initial_type' => ProductType::TYPE_PACK,
                ],
                'stock' => [
                    'packed_products' => [],
                ],
            ],
            [new RemoveAllProductsFromPackCommand($this->getProductId()->getValue())],
        ];

        $command = new SetPackProductsCommand(
            $this->getProductId()->getValue(),
            [
                [
                    'product_id' => 23,
                    'quantity' => 3,
                    'combination_id' => 0,
                ],
            ]
        );
        yield [
            [
                'header' => [
                    'initial_type' => ProductType::TYPE_PACK,
                ],
                'stock' => [
                    'packed_products' => [
                        [
                            'product_id' => 23,
                            'name' => 'dontcare',
                            'image' => 'notused',
                            'quantity' => 3,
                            'combination_id' => 0,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new SetPackProductsCommand(
            $this->getProductId()->getValue(),
            [
                [
                    'product_id' => 23,
                    'quantity' => 3,
                    'combination_id' => 0,
                ],
                [
                    'product_id' => 42,
                    'quantity' => 5,
                    'combination_id' => 0,
                ],
            ]
        );
        yield [
            [
                'header' => [
                    'initial_type' => ProductType::TYPE_PACK,
                ],
                'stock' => [
                    'packed_products' => [
                        [
                            'product_id' => 23,
                            'name' => 'dontcare',
                            'image' => 'notused',
                            'quantity' => 3,
                            'combination_id' => 0,
                        ],
                        [
                            'product_id' => '42',
                            'name' => 'dontcare',
                            'image' => 'notused',
                            'quantity' => 5,
                            'combination_id' => 0,
                        ],
                    ],
                ],
            ],
            [$command],
        ];
    }
}
