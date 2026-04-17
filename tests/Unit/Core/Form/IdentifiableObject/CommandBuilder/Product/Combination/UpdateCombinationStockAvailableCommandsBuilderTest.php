<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\UpdateCombinationStockAvailableCommandsBuilder;

class UpdateCombinationStockAvailableCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     * @dataProvider getExpectedCommandsMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommands(array $formData, array $expectedCommands): void
    {
        $builder = new UpdateCombinationStockAvailableCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'stock' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand();
        $command->setDeltaQuantity(100);
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            // quantity is used for view only, the real value that is used now is delta
                            'quantity' => 1000000,
                            'delta' => 100,
                        ],
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setFixedQuantity(134);
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'fixed_quantity' => 134,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setLocation('Im in miami...');
        yield [
            [
                'stock' => [
                    'options' => [
                        'stock_location' => 'Im in miami...',
                    ],
                ],
            ],
            [$command],
        ];
    }

    public function getExpectedCommandsMultiShop(): iterable
    {
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
        ;
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            // quantity is used for view only, the real value that is used now is delta
                            'quantity' => 1000000,
                            'delta' => 100,
                            self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => true,
                        ],
                    ],
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'stock_location' => true,
                    ],
                ],
            ],
            [$allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setDeltaQuantity(100)
        ;
        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setLocation('Im in miami...')
        ;
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            // quantity is used for view only, the real value that is used now is delta
                            'quantity' => 1000000,
                            'delta' => 100,
                            self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => false,
                        ],
                    ],
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'stock_location' => true,
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $allShopsCommand = $this
            ->getAllShopsCommand()
            ->setFixedQuantity(134)
        ;
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'fixed_quantity' => 134,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'fixed_quantity' => true,
                    ],
                ],
            ],
            [$allShopsCommand],
        ];

        $singleShopCommand = $this
            ->getSingleShopCommand()
            ->setFixedQuantity(134)
        ;
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'fixed_quantity' => 134,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'fixed_quantity' => false,
                    ],
                ],
            ],
            [$singleShopCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateCombinationStockAvailableCommand
    {
        return new UpdateCombinationStockAvailableCommand($this->getCombinationId()->getValue(), $this->getSingleShopConstraint());
    }

    private function getAllShopsCommand(): UpdateCombinationStockAvailableCommand
    {
        return new UpdateCombinationStockAvailableCommand($this->getCombinationId()->getValue(), ShopConstraint::allShops());
    }
}
