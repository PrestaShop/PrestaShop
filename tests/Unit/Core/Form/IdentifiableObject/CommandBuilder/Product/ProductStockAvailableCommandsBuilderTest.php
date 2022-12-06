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

use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductStockAvailableCommandsBuilder;

class ProductStockAvailableCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new ProductStockAvailableCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return iterable
     */
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
    }

    /**
     * @dataProvider getExpectedCommandsMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommandMultiShop(array $formData, array $expectedCommands): void
    {
        $builder = new ProductStockAvailableCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommandsMultiShop(): iterable
    {
        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
        ;

        yield [
            [
                'stock' => [
                    'quantities' => [
                        'delta_quantity' => [
                            'delta' => '100',
                        ],
                        'minimal_quantity' => 1,
                    ],
                    'availability' => [
                        'out_of_stock_type' => '2',
                        'available_date' => '2022-10-10',
                    ],
                    // leaving rest of the fields here to make sure they don't have any impact
                    'pack_stock_type' => '2',
                    'options' => [
                        'stock_location' => 'Im in miami...',
                        'low_stock_alert' => '0',
                        'low_stock_threshold' => '5',
                    ],
                ],
            ],
            [$singleShopCommand],
        ];

        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
        ;

        yield [
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
                    'availability' => [
                        'out_of_stock_type' => '2',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'out_of_stock_type' => true,
                        'available_date' => '2022-10-10',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_date' => true,
                        'available_now_label' => [],
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_now_label' => true,
                        'available_later_label' => ['doesnt matter in this case'],
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_later_label' => true,
                    ],
                    // leaving rest of the fields here to make sure they don't have any impact
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
                ],
            ],
            [$allShopsCommand],
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
        ;
        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
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
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand
            ->setOutOfStockType(OutOfStockType::OUT_OF_STOCK_DEFAULT)
        ;
        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setDeltaQuantity(100)
            ->setLocation('Im in miami...')
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
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateProductStockAvailableCommand
    {
        return new UpdateProductStockAvailableCommand($this->getProductId()->getValue(), ShopConstraint::shop(self::SHOP_ID));
    }

    private function getAllShopsCommand(): UpdateProductStockAvailableCommand
    {
        return new UpdateProductStockAvailableCommand($this->getProductId()->getValue(), ShopConstraint::allShops());
    }
}
