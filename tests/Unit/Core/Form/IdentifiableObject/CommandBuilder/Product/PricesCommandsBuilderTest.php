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
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\PricesCommandsBuilder;

class PricesCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new PricesCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): iterable
    {
        yield [
            [
                'no_price_data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand();
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

        $command = $this->getSingleShopCommand();
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

        $command = $this->getSingleShopCommand();
        $command->setEcotax('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'retail_price' => [
                        'ecotax_tax_excluded' => '45.56',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command->setTaxRulesGroupId(42);
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'retail_price' => [
                        'tax_rules_group_id' => '42',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
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

        $command = $this->getSingleShopCommand();
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

        $command = $this->getSingleShopCommand();
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

        $command = $this->getSingleShopCommand();
        $command->setUnitPrice('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'unit_price' => [
                        'price_tax_excluded' => '45.56',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
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

    /**
     * @dataProvider getExpectedCommandsMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommandMultiShop(array $formData, array $expectedCommands): void
    {
        $builder = new PricesCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommandsMultiShop(): iterable
    {
        $command = $this->getSingleShopCommand();
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

        $command = $this->getAllShopsCommand();
        $command->setPrice('45.56');
        yield [
            [
                'pricing' => [
                    'not_handled' => 0,
                    'retail_price' => [
                        'price_tax_excluded' => 45.56,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command
            ->setPrice('45.56')
            ->setEcotax('45.56')
            ->setTaxRulesGroupId(42)
            ->setOnSale(true)
            ->setWholesalePrice('45.56')
            ->setUnitPrice('45.56')
            ->setUnity('kg')
        ;
        yield [
            [
                'pricing' => [
                    'retail_price' => [
                        'price_tax_excluded' => 45.56,
                        'ecotax_tax_excluded' => '45.56',
                        'tax_rules_group_id' => '42',
                    ],
                    'on_sale' => true,
                    'wholesale_price' => '45.56',
                    'unit_price' => [
                        'price_tax_excluded' => '45.56',
                        'unity' => 'kg',
                    ],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand();
        $command
            ->setPrice('45.56')
            ->setEcotax('45.56')
            ->setTaxRulesGroupId(42)
            ->setOnSale(true)
            ->setWholesalePrice('45.56')
            ->setUnitPrice('45.56')
            ->setUnity('kg')
        ;
        yield [
            [
                'pricing' => [
                    'retail_price' => [
                        'price_tax_excluded' => 45.56,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => false,
                        'ecotax_tax_excluded' => '45.56',
                        'tax_rules_group_id' => '42',
                    ],
                    'on_sale' => true,
                    'wholesale_price' => '45.56',
                    'unit_price' => [
                        'price_tax_excluded' => '45.56',
                        'unity' => 'kg',
                    ],
                ],
            ],
            [$command],
        ];

        $singleCommand = $this->getSingleShopCommand();
        $singleCommand
            ->setEcotax('45.56')
            ->setTaxRulesGroupId(42)
            ->setOnSale(true)
            ->setWholesalePrice('45.56')
            ->setUnitPrice('45.56')
            ->setUnity('kg')
        ;
        $allShopsCommand = $this->getAllShopsCommand();
        $allShopsCommand
            ->setPrice('45.56')
        ;
        yield [
            [
                'pricing' => [
                    'retail_price' => [
                        'price_tax_excluded' => 45.56,
                        self::MODIFY_ALL_SHOPS_PREFIX . 'price_tax_excluded' => true,
                        'ecotax_tax_excluded' => '45.56',
                        'tax_rules_group_id' => '42',
                    ],
                    'on_sale' => true,
                    'wholesale_price' => '45.56',
                    'unit_price' => [
                        'price_tax_excluded' => '45.56',
                        'unity' => 'kg',
                    ],
                ],
            ],
            [$singleCommand, $allShopsCommand],
        ];
    }

    private function getSingleShopCommand(): UpdateProductPricesCommand
    {
        return new UpdateProductPricesCommand($this->getProductId()->getValue(), $this->getSingleShopConstraint());
    }

    private function getAllShopsCommand(): UpdateProductPricesCommand
    {
        return new UpdateProductPricesCommand($this->getProductId()->getValue(), ShopConstraint::allShops());
    }
}
