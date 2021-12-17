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

use DateTime;
use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\StockCommandsBuilder;

class StockCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new StockCommandsBuilder();
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

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        yield [
            [
                'stock' => [
                    'not_handled' => 0,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $command->setDeltaQuantity(100);
        $command->setMinimalQuantity(1);
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'quantity' => '100',
                        'minimal_quantity' => 1,
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
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

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $command->setLowStockThreshold(5);
        yield [
            [
                'stock' => [
                    'options' => [
                        'low_stock_threshold' => '5',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $command->setLowStockAlert(false);
        yield [
            [
                'stock' => [
                    'options' => [
                        'low_stock_alert' => '0',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $command->setPackStockType(PackStockType::STOCK_TYPE_BOTH);
        yield [
            [
                'stock' => [
                    'pack_stock_type' => '2',
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $command->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT);
        yield [
            [
                'stock' => [
                    'availability' => [
                        'out_of_stock_type' => '2',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $localizedNotes = [
            '1' => 'hello',
            '2', 'Goodbye',
        ];
        $command->setLocalizedAvailableNowLabels($localizedNotes);
        yield [
            [
                'stock' => [
                    'availability' => [
                        'available_now_label' => $localizedNotes,
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $localizedNotes = [
            '1' => 'welcome',
            '3' => 'cya',
        ];
        $command->setLocalizedAvailableLaterLabels($localizedNotes);
        yield [
            [
                'stock' => [
                    'availability' => [
                        'available_later_label' => $localizedNotes,
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductStockInformationCommand($this->getProductId()->getValue());
        $command->setAvailableDate(new DateTime('2022-10-10'));
        yield [
            [
                'stock' => [
                    'availability' => [
                        'available_date' => '2022-10-10',
                    ],
                ],
            ],
            [$command],
        ];
    }
}
