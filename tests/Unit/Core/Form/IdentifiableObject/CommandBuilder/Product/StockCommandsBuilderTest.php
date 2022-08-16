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

use DateTimeImmutable;
use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\StockCommandsBuilder;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

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
        $builder = new StockCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
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
                'stock' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand();
        $command->setDeltaQuantity(100);
        $command->setMinimalQuantity(1);
        yield [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => '100',
                        ],
                        'minimal_quantity' => 1,
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

        $command = $this->getSingleShopCommand();
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

        $command = $this->getSingleShopCommand();
        $command->setLowStockAlert(false);
        yield [
            [
                'stock' => [
                    'options' => [
                        'disabling_switch_low_stock_threshold' => '0',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setPackStockType(PackStockType::STOCK_TYPE_BOTH);
        yield [
            [
                'stock' => [
                    'pack_stock_type' => '2',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
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

        // Handle out_of_stock_type for product with combinations
        $command->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT);
        yield [
            [
                'header' => [
                    'type' => ProductType::TYPE_COMBINATIONS,
                ],
                'combinations' => [
                    'availability' => [
                        'out_of_stock_type' => '2',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAvailableDate(new DateTimeImmutable('2022-10-10'));
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

        $command = $this->getSingleShopCommand();
        $localizedNotes = [
            '1' => 'hello',
            '2' => 'Goodbye',
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

        // Handle available_now_label for product with combinations
        yield [
            [
                'header' => [
                    'type' => ProductType::TYPE_COMBINATIONS,
                ],
                'combinations' => [
                    'availability' => [
                        'available_now_label' => $localizedNotes,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
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

        // Handle available_later_label for product with combinations
        yield [
            [
                'header' => [
                    'type' => ProductType::TYPE_COMBINATIONS,
                ],
                'combinations' => [
                    'availability' => [
                        'available_later_label' => $localizedNotes,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAvailableDate(new DateTimeImmutable('2022-10-10'));
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

        $command = $this->getSingleShopCommand();
        $command->setAvailableDate(new NullDateTime());
        yield [
            [
                'stock' => [
                    'availability' => [
                        'available_date' => '',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAvailableDate(new NullDateTime());
        yield [
            [
                'stock' => [
                    'availability' => [
                        'available_date' => null,
                    ],
                ],
            ],
            [$command],
        ];
    }

    /**
     * @dataProvider getExpectedCommandsMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommandMultiShop(array $formData, array $expectedCommands): void
    {
        $builder = new StockCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommandsMultiShop(): iterable
    {
        $localizedAvailableNowLabels = [
            '1' => 'hello',
            '2', 'Goodbye',
        ];

        $localizedAvailableLaterLabels = [
            '1' => 'welcome',
            '3' => 'cya',
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand
            ->setDeltaQuantity(100)
            ->setMinimalQuantity(1)
            ->setLocation('Im in miami...')
            ->setLowStockAlert(false)
            ->setLowStockThreshold(5)
            ->setPackStockType(PackStockType::STOCK_TYPE_BOTH)
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
            ->setLocalizedAvailableNowLabels($localizedAvailableNowLabels)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLaterLabels)
            ->setAvailableDate(new DateTimeImmutable('2022-10-10'))
        ;

        yield 'full single shop command' => [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => '100',
                        ],
                        'minimal_quantity' => 1,
                    ],
                    'pack_stock_type' => '2',
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        'low_stock_alert' => '0',
                        'low_stock_threshold' => '5',
                    ],
                    'availability' => [
                        'out_of_stock_type' => '2',
                        'available_date' => '2022-10-10',
                        'available_now_label' => $localizedAvailableNowLabels,
                        'available_later_label' => $localizedAvailableLaterLabels,
                    ],
                ],
            ],
            [$singleShopCommand],
        ];

        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setDeltaQuantity(100)
            ->setMinimalQuantity(1)
            ->setLocation('Im in miami...')
            ->setLowStockAlert(false)
            ->setLowStockThreshold(5)
            ->setPackStockType(PackStockType::STOCK_TYPE_BOTH)
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
            ->setLocalizedAvailableNowLabels($localizedAvailableNowLabels)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLaterLabels)
            ->setAvailableDate(new DateTimeImmutable('2022-10-10'))
        ;

        yield 'full all shops command' => [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => '100',
                            self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => true,
                        ],
                        'minimal_quantity' => 1,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'minimal_quantity' => true,
                    ],
                    'pack_stock_type' => '2',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'pack_stock_type' => true,
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'stock_location' => true,
                        'low_stock_alert' => '0',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_alert' => true,
                        'low_stock_threshold' => '5',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_threshold' => true,
                    ],
                    'availability' => [
                        'out_of_stock_type' => '2',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'out_of_stock_type' => true,
                        'available_date' => '2022-10-10',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_date' => true,
                        'available_now_label' => $localizedAvailableNowLabels,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_now_label' => true,
                        'available_later_label' => $localizedAvailableLaterLabels,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_later_label' => true,
                    ],
                ],
            ],
            [$allShopsCommand],
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
            ->setLowStockThreshold(5)
            ->setPackStockType(PackStockType::STOCK_TYPE_BOTH)
            ->setLocalizedAvailableNowLabels($localizedAvailableNowLabels)
        ;
        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setMinimalQuantity(1)
            ->setLowStockAlert(false)
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLaterLabels)
            ->setAvailableDate(new DateTimeImmutable('2022-10-10'))
        ;

        yield 'two commands with missing toggle fields' => [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => '100',
                        ],
                        'minimal_quantity' => 1,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'minimal_quantity' => true,
                    ],
                    'pack_stock_type' => '2',
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        'low_stock_alert' => '0',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_alert' => true,
                        'low_stock_threshold' => '5',
                    ],
                    'availability' => [
                        'out_of_stock_type' => '2',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'out_of_stock_type' => true,
                        'available_date' => '2022-10-10',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_date' => true,
                        'available_now_label' => $localizedAvailableNowLabels,
                        'available_later_label' => $localizedAvailableLaterLabels,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_later_label' => true,
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand
            ->setMinimalQuantity(1)
            ->setLowStockAlert(false)
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLaterLabels)
            ->setAvailableDate(new DateTimeImmutable('2022-10-10'))
        ;
        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
            ->setLowStockThreshold(5)
            ->setPackStockType(PackStockType::STOCK_TYPE_BOTH)
            ->setLocalizedAvailableNowLabels($localizedAvailableNowLabels)
        ;

        yield 'two commands with toggle field false' => [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => '100',
                            self::MODIFY_ALL_SHOPS_PREFIX . 'delta' => true,
                        ],
                        'minimal_quantity' => 1,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'minimal_quantity' => false,
                    ],
                    'pack_stock_type' => '2',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'pack_stock_type' => true,
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'stock_location' => true,
                        'low_stock_alert' => '0',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_alert' => false,
                        'low_stock_threshold' => '5',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'low_stock_threshold' => true,
                    ],
                    'availability' => [
                        'out_of_stock_type' => '2',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'out_of_stock_type' => false,
                        'available_date' => '2022-10-10',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_date' => false,
                        'available_now_label' => $localizedAvailableNowLabels,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_now_label' => true,
                        'available_later_label' => $localizedAvailableLaterLabels,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_later_label' => false,
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateProductStockInformationCommand
    {
        return new UpdateProductStockInformationCommand($this->getProductId()->getValue(), ShopConstraint::shop(self::SHOP_ID));
    }

    private function getAllShopsCommand(): UpdateProductStockInformationCommand
    {
        return new UpdateProductStockInformationCommand($this->getProductId()->getValue(), ShopConstraint::allShops());
    }
}
