<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\RemoveAllFeatureValuesFromCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\SetCombinationFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidProductFeatureValuesFormatException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationFeatureValuesCommandsBuilder;

class CombinationFeatureValuesCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new CombinationFeatureValuesCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): Generator
    {
        yield 'random useless no commands' => [
            [
                'random' => ['useless'],
            ],
            [],
        ];

        yield 'empty features no commands' => [
            [
                'features' => [],
            ],
            [],
        ];

        yield 'empty feature values, remove all command' => [
            [
                'features' => [
                    'feature_collection' => [],
                ],
            ],
            [new RemoveAllFeatureValuesFromCombinationCommand($this->getCombinationId()->getValue())],
        ];

        $command = new SetCombinationFeatureValuesCommand(
            $this->getCombinationId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51],
            ]
        );
        yield 'assign existing feature value' => [
            [
                'features' => [
                    'feature_collection' => [
                        [
                            'feature_id' => 42,
                            'feature_values' => [
                                ['feature_value_id' => 51],
                            ],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $localizedValues = [
            1 => 'english',
            2 => 'french',
        ];
        $command = new SetCombinationFeatureValuesCommand(
            $this->getCombinationId()->getValue(),
            [
                ['feature_id' => 42, 'custom_values' => $localizedValues],
            ]
        );
        yield 'create new custom values' => [
            [
                'features' => [
                    'feature_collection' => [
                        [
                            'feature_id' => 42,
                            'feature_values' => [
                                ['is_custom' => 1, 'custom_value' => $localizedValues],
                            ],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new SetCombinationFeatureValuesCommand(
            $this->getCombinationId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 69],
            ]
        );
        yield 'custom values are not used if is_custom is not specified' => [
            [
                'features' => [
                    'feature_collection' => [
                        [
                            'feature_id' => 42,
                            'feature_values' => [
                                ['feature_value_id' => 69, 'custom_value' => $localizedValues],
                            ],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new SetCombinationFeatureValuesCommand(
            $this->getCombinationId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51, 'custom_values' => $localizedValues],
            ]
        );
        yield 'updating existing custom values' => [
            [
                'features' => [
                    'feature_collection' => [
                        [
                            'feature_id' => 42,
                            'feature_values' => [
                                ['feature_value_id' => 51, 'is_custom' => 1, 'custom_value' => $localizedValues],
                            ],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $localizedValues = [
            1 => 'french',
            2 => 'english',
        ];
        $newLocalizedValues = [
            1 => 'new french',
            2 => 'new english',
        ];
        $command = new SetCombinationFeatureValuesCommand(
            $this->getCombinationId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51, 'custom_values' => $localizedValues],
                ['feature_id' => 42, 'feature_value_id' => 69],
                ['feature_id' => 42, 'custom_values' => $newLocalizedValues],
                ['feature_id' => 13, 'feature_value_id' => 21],
            ]
        );
        yield 'rich command with multiple different feature values' => [
            [
                'features' => [
                    'feature_collection' => [
                        [
                            'feature_id' => 42,
                            'feature_values' => [
                                ['feature_value_id' => 51, 'is_custom' => 1, 'custom_value' => $localizedValues],
                                ['feature_value_id' => 69],
                                ['is_custom' => 1, 'custom_value' => $newLocalizedValues],
                            ],
                        ],
                        [
                            'feature_id' => 13,
                            'feature_values' => [
                                ['feature_value_id' => 21],
                            ],
                        ],
                    ],
                ],
            ],
            [$command],
        ];
    }

    /**
     * @dataProvider getInvalidCommands
     *
     * @param array $formData
     * @param string $exceptionClass
     */
    public function testInvalidBuildCommand(array $formData, string $exceptionClass): void
    {
        $this->expectException($exceptionClass);
        $builder = new CombinationFeatureValuesCommandsBuilder();
        $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
    }

    public function getInvalidCommands(): Generator
    {
        yield 'feature collection without values' => [
            [
                'features' => [
                    'feature_collection' => [
                        [
                            'feature_id' => 42,
                            'feature_values' => [
                                [],
                            ],
                        ],
                    ],
                ],
            ],
            InvalidProductFeatureValuesFormatException::class,
        ];
    }
}
