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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductPricesCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\PricesCommandsBuilder;

class PricesCommandBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new PricesCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        yield [
            [
                'no_price_data' => ['useless value'],
            ],
            [],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setPrice('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'retail_price' => [
                        'price_tax_excluded' => 45.56,
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setPrice('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'retail_price' => [
                        'price_tax_excluded' => '45.56',
                        'price_tax_included' => '65.56', // Price tax included is ignored
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setEcotax('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'retail_price' => [
                        'ecotax' => '45.56',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setTaxRulesGroupId(42);
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'tax_rules_group_id' => '42',
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setOnSale(true);
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'on_sale' => '42',
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setOnSale(false);
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'on_sale' => '0',
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setWholesalePrice('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'wholesale_price' => '45.56',
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setUnitPrice('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'unit_price' => [
                        'price' => '45.56',
                    ],
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductPricesCommand($this->getProductId()->getValue());
        $command->setUnity('kg');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'unit_price' => [
                        'unity' => 'kg',
                    ],
                ],
            ],
            [$command],
        ];
    }
}
