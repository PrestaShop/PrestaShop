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
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ShippingCommandsBuilder;

class ShippingCommandBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new ShippingCommandsBuilder();
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

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
        yield [
            [
                'shipping' => [
                    'not_handled' => 0,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
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

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
        $command->setAdditionalShippingCost('-0.55');
        yield [
            [
                'shipping' => [
                    'additional_shipping_cost' => '-0.55',
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
        $command->setCarrierReferences(['1', '2', '3']);
        yield [
            [
                'shipping' => [
                    'carriers' => ['1', '2', '3'],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
        $command->setDeliveryTimeNoteType(DeliveryTimeNoteType::TYPE_DEFAULT);
        yield [
            [
                'shipping' => [
                    'delivery_time_note_type' => 1,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
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

        $command = new UpdateProductShippingCommand($this->getProductId()->getValue());
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
}
