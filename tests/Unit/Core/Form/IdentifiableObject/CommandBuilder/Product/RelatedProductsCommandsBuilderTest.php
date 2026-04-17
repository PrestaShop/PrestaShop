<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\RemoveAllRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetRelatedProductsCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\RelatedProductsCommandsBuilder;

class RelatedProductsCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
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
