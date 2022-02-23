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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductOptionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\OptionsCommandsBuilder;

class ProductOptionsCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommandsForSingleShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildSingleShopCommand(array $formData, array $expectedCommands)
    {
        $builder = new OptionsCommandsBuilder(self::MODIFY_ALL_NAME_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommandsForSingleShop()
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
                'options' => [
                    'condition' => 'new',
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setShowCondition(false);
        yield [
            [
                'options' => [
                    'not_handled' => 0,
                    'show_condition' => 0,
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

    private function getSingleShopCommand(): UpdateProductOptionsCommand
    {
        return new UpdateProductOptionsCommand($this->getProductId()->getValue(), $this->getSingleShopConstraint());
    }
}
