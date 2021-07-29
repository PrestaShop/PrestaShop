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

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllAssociatedCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationSuppliersCommandsBuilder;

class CombinationSuppliersCommandsBuilderTest extends AbstractCombinationCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new CombinationSuppliersCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        yield [
            [
                'random' => ['useless'],
            ],
            [],
        ];

        yield [
            [
                'suppliers' => [],
            ],
            [],
        ];

        yield [
            [
                'suppliers' => [
                    'product_suppliers' => [],
                ],
            ],
            [new RemoveAllAssociatedCombinationSuppliersCommand($this->getCombinationId()->getValue())],
        ];

        $suppliersCommand = new SetCombinationSuppliersCommand(
            $this->getCombinationId()->getValue(),
            [
                [
                    'supplier_id' => 5,
                    'currency_id' => 2,
                    'reference' => '',
                    'price_tax_excluded' => '0.5',
                    'product_supplier_id' => 0,
                ],
                [
                    'supplier_id' => 3,
                    'currency_id' => 5,
                    'reference' => '',
                    'price_tax_excluded' => '50.65',
                    'product_supplier_id' => 1,
                ],
            ]
        );
        $defaultSupplierCommand = new SetCombinationDefaultSupplierCommand(
            $this->getCombinationId()->getValue(),
            5
        );

        yield [
            [
                'suppliers' => [
                    'default_supplier_id' => 5,
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'product_supplier_id' => null,
                        ],
                        [
                            'supplier_id' => 3,
                            'currency_id' => 5,
                            'reference' => null,
                            'price_tax_excluded' => '50.65',
                            'product_supplier_id' => 1,
                        ],
                    ],
                ],
            ],
            [$suppliersCommand, $defaultSupplierCommand],
        ];

        $suppliersCommand = new SetCombinationSuppliersCommand(
            $this->getCombinationId()->getValue(),
            [
                [
                    'supplier_id' => 5,
                    'currency_id' => 2,
                    'reference' => '',
                    'price_tax_excluded' => '0.5',
                    'product_supplier_id' => 0,
                ],
            ]
        );

        yield [
            [
                'suppliers' => [
                    'default_supplier_id' => 0,
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'product_supplier_id' => null,
                        ],
                    ],
                ],
            ],
            [$suppliersCommand],
        ];

        yield [
            [
                'suppliers' => [
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'product_supplier_id' => null,
                        ],
                    ],
                ],
            ],
            [$suppliersCommand],
        ];
    }
}
