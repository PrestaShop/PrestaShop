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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\UpdateCombinationStockAvailableCommandsBuilder;

class UpdateCombinationStockAvailableCommandsBuilderTest extends AbstractCombinationCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new UpdateCombinationStockAvailableCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData);
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

        $command = new UpdateCombinationStockAvailableCommand($this->getCombinationId()->getValue());
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

        $command = new UpdateCombinationStockAvailableCommand($this->getCombinationId()->getValue());
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

        $command = new UpdateCombinationStockAvailableCommand($this->getCombinationId()->getValue());
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
}
