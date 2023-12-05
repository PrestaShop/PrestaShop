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
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\RemoveAllFeatureValuesFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Exception\InvalidProductFeatureValuesFormatException;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\FeatureValuesCommandsBuilder;

class FeatureValuesCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new FeatureValuesCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
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
                'details' => [
                    'features' => [],
                ],
            ],
            [],
        ];

        yield 'empty feature values, remove all command' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [],
                    ],
                ],
            ],
            [new RemoveAllFeatureValuesFromProductCommand($this->getProductId()->getValue())],
        ];

        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51],
            ]
        );
        yield 'assign existing feature value' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'feature_value_id' => 51],
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
        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'custom_values' => $localizedValues],
            ]
        );
        yield 'create new custom values' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'is_custom' => 1, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 69],
            ]
        );
        yield 'custom values are not used if is_custom is not specified' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'feature_value_id' => 69, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51, 'custom_values' => $localizedValues],
            ]
        );
        yield 'updating existing custom values' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'feature_value_id' => 51, 'is_custom' => 1, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $localizedValues = [
            1 => '',
            2 => null,
        ];
        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51],
            ]
        );
        yield 'if custom values only contains empty values it is ignored' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'feature_value_id' => 51, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        yield 'if custom values only contains empty values it is ignored even when is_custom is true' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'feature_value_id' => 51, 'is_custom' => 1, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $localizedValues = [
            1 => 'plop',
            2 => '',
        ];
        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'custom_values' => $localizedValues],
            ]
        );
        yield 'one custom value is enough for creation' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'is_custom' => 1, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $localizedValues = [
            1 => 'plop',
        ];
        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'custom_values' => $localizedValues],
            ]
        );
        yield 'one language value is enough for creation' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'is_custom' => 1, 'custom_value' => $localizedValues],
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $localizedValues = [
            1 => 'plop',
            2 => '',
        ];
        $command = new SetProductFeatureValuesCommand(
            $this->getProductId()->getValue(),
            [
                ['feature_id' => 42, 'feature_value_id' => 51, 'custom_values' => $localizedValues],
            ]
        );
        yield 'one custom value is enough for update' => [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42, 'feature_value_id' => 51, 'is_custom' => 1, 'custom_value' => $localizedValues],
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
        $builder = new FeatureValuesCommandsBuilder();
        $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
    }

    public function getInvalidCommands(): Generator
    {
        yield [
            [
                'details' => [
                    'features' => [
                        'feature_values' => [
                            ['feature_id' => 42],
                        ],
                    ],
                ],
            ],
            InvalidProductFeatureValuesFormatException::class,
        ];
    }
}
