<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCategoriesCommandsBuilder;

class ProductCategoriesCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
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
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
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
            [42, 49, 51],
            $this->getSingleShopConstraint()
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
            [42, 49, 51],
            $this->getSingleShopConstraint()
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
            [42, 49, 51],
            $this->getSingleShopConstraint()
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
        $command = new RemoveAllAssociatedProductCategoriesCommand($this->getProductId()->getValue(), $this->getSingleShopConstraint());
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
