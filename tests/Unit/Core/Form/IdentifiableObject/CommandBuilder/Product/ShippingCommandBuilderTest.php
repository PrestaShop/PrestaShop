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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductShippingCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\DeliveryTimeNoteType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ShippingCommandsBuilder;

class ShippingCommandBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedSingleShopCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildSingleShopCommands(array $formData, array $expectedCommands): void
    {
        $builder = new ShippingCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @dataProvider getExpectedMultiShopCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildMultiShopCommands(array $formData, array $expectedCommands): void
    {
        $builder = new ShippingCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedMultiShopCommands(): Generator
    {
        $command = $this->getAllShopsCommand();
        $command->setCarrierReferenceIds([1, 2, 3]);
        yield [
            [
                'shipping' => [
                    'carriers' => ['1', '2', '3'],
                    self::MODIFY_ALL_SHOPS_PREFIX . 'carriers' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $localizedNotes = [
            '1' => 'test4',
            '3' => 'test5',
        ];
        $command->setLocalizedDeliveryTimeOutOfStockNotes($localizedNotes);
        yield [
            [
                'shipping' => [
                    'delivery_time_notes' => [
                        'out_of_stock' => $localizedNotes,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'out_of_stock' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand();
        $command->setAdditionalShippingCost('-0.55');
        yield [
            [
                'shipping' => [
                    'additional_shipping_cost' => '-0.55',
                    self::MODIFY_ALL_SHOPS_PREFIX . 'additional_shipping_cost' => true,
                ],
            ],
            [$command],
        ];

        $singleShopCommand = $this->getSingleShopCommand();
        $singleShopCommand->setDeliveryTimeNoteType(DeliveryTimeNoteType::TYPE_DEFAULT);
        $allShopsCommand = $this->getAllShopsCommand();
        $localizedNotes = [
            '1' => 'test9',
            '3' => 'test19',
        ];
        $allShopsCommand->setLocalizedDeliveryTimeOutOfStockNotes($localizedNotes);
        yield [
            [
                'shipping' => [
                    'delivery_time_note_type' => 1,
                    'delivery_time_notes' => [
                        'out_of_stock' => $localizedNotes,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'out_of_stock' => true,
                    ],
                ],
            ],
            [$singleShopCommand, $allShopsCommand],
        ];
    }

    /**
     * @return Generator
     */
    public function getExpectedSingleShopCommands(): Generator
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'shipping' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand();
        $command->setWeight('10');
        $command->setWidth('10.5');
        $command->setHeight('109');
        $command->setDepth('0');
        yield [
            [
                'shipping' => [
                    'dimensions' => [
                        'weight' => '10',
                        'width' => 10.5,
                        'height' => '109',
                        'depth' => 0,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setAdditionalShippingCost('-0.55');
        yield [
            [
                'shipping' => [
                    'additional_shipping_cost' => '-0.55',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setCarrierReferenceIds([1, 2, 3]);
        yield [
            [
                'shipping' => [
                    'carriers' => ['1', '2', '3'],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setDeliveryTimeNoteType(DeliveryTimeNoteType::TYPE_DEFAULT);
        yield [
            [
                'shipping' => [
                    'delivery_time_note_type' => 1,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $localizedNotes = [
            '1' => 'test1',
            '2' => 'test2',
        ];
        $command->setLocalizedDeliveryTimeInStockNotes($localizedNotes);
        yield [
            [
                'shipping' => [
                    'delivery_time_notes' => [
                        'in_stock' => $localizedNotes,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $localizedNotes = [
            '1' => 'test4',
            '3' => 'test5',
        ];
        $command->setLocalizedDeliveryTimeOutOfStockNotes($localizedNotes);
        yield [
            [
                'shipping' => [
                    'delivery_time_notes' => [
                        'out_of_stock' => $localizedNotes,
                    ],
                ],
            ],
            [$command],
        ];
    }

    private function getSingleShopCommand(): UpdateProductShippingCommand
    {
        return new UpdateProductShippingCommand($this->getProductId()->getValue(), $this->getSingleShopConstraint());
    }

    private function getAllShopsCommand(): UpdateProductShippingCommand
    {
        return new UpdateProductShippingCommand($this->getProductId()->getValue(), ShopConstraint::allShops());
    }
}
