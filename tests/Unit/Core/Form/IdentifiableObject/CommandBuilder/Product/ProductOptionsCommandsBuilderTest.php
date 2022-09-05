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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\OptionsCommandsBuilder;

class ProductOptionsCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommandsForSingleShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildSingleShopCommand(array $formData, array $expectedCommands): void
    {
        $builder = new OptionsCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @dataProvider getExpectedCommandsForMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildMultiShopCommand(array $formData, array $expectedCommands): void
    {
        $builder = new OptionsCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedCommandsForSingleShop(): Generator
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'options' => [],
            ],
            [],
        ];

        yield [
            [
                'options' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand();
        $command->setManufacturerId(1);
        yield [
            [
                'description' => [
                    'manufacturer' => '1',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setCondition(ProductCondition::NEW);
        yield [
            [
                'specifications' => [
                    'condition' => 'new',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setShowCondition(true);
        yield [
            [
                'specifications' => [
                    'not_handled' => 0,
                    'show_condition' => 1,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setOnlineOnly(true);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'visibility' => [
                        'online_only' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setShowPrice(false);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'visibility' => [
                        'show_price' => false,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAvailableForOrder(true);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'visibility' => [
                        'available_for_order' => '1',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setVisibility(ProductVisibility::INVISIBLE);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'visibility' => [
                        'visibility' => ProductVisibility::INVISIBLE,
                    ],
                ],
            ],
            [$command],
        ];
    }

    /**
     * @return Generator
     */
    public function getExpectedCommandsForMultiShop(): Generator
    {
        $command = $this->getAllShopsCommand();
        $command->setCondition(ProductCondition::NEW);
        yield [
            [
                'specifications' => [
                    'condition' => 'new',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'condition' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $command->setShowCondition(false);
        yield [
            [
                'specifications' => [
                    'not_handled' => 0,
                    'show_condition' => 0,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'show_condition' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $command->setShowPrice(false);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'visibility' => [
                        'show_price' => false,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'show_price' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $command->setAvailableForOrder(true);
        yield [
            [
                'options' => [
                    'visibility' => [
                        'available_for_order' => '1',
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_for_order' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $command->setVisibility(ProductVisibility::INVISIBLE);
        yield [
            [
                'options' => [
                    'visibility' => [
                        'visibility' => ProductVisibility::INVISIBLE,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'visibility' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $command->setOnlineOnly(false);
        yield [
            [
                'options' => [
                    'visibility' => [
                        'online_only' => false,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'online_only' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand->setVisibility(ProductVisibility::VISIBLE_EVERYWHERE);
        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand->setAvailableForOrder(true);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'visibility' => [
                        'visibility' => ProductVisibility::VISIBLE_EVERYWHERE,
                        'available_for_order' => true,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'available_for_order' => true,
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateProductOptionsCommand
    {
        return new UpdateProductOptionsCommand($this->getProductId()->getValue(), $this->getSingleShopConstraint());
    }

    private function getAllShopsCommand(): UpdateProductOptionsCommand
    {
        return new UpdateProductOptionsCommand($this->getProductId()->getValue(), ShopConstraint::allShops());
    }
}
