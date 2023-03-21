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

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductSuppliersCommandsBuilder;

class ProductSuppliersCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new ProductSuppliersCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
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
                'options' => [
                    'suppliers' => [],
                ],
            ],
            [],
        ];

        yield [
            [
                'options' => [
                    'suppliers' => [
                    ],
                    'product_suppliers' => [],
                ],
            ],
            [new RemoveAllAssociatedProductSuppliersCommand($this->getProductId()->getValue())],
        ];

        yield [
            [
                'options' => [
                    'suppliers' => [
                        'supplier_ids' => [],
                    ],
                ],
            ],
            [new RemoveAllAssociatedProductSuppliersCommand($this->getProductId()->getValue())],
        ];

        yield [
            [
                'options' => [
                    'suppliers' => [
                        'default_supplier_id' => 5,
                        // No supplier IDs means no associations even if product suppliers data are present
                    ],
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'combination_id' => 0,
                            'product_supplier_id' => null,
                        ],
                        [
                            'supplier_id' => 3,
                            'currency_id' => 5,
                            'reference' => null,
                            'price_tax_excluded' => '50.65',
                            'combination_id' => null,
                            'product_supplier_id' => 1,
                        ],
                    ],
                ],
            ],
            [new RemoveAllAssociatedProductSuppliersCommand($this->getProductId()->getValue())],
        ];

        $suppliersCommand = new SetSuppliersCommand(
            $this->getProductId()->getValue(),
            [5, 3]
        );
        $defaultSupplierCommand = new SetProductDefaultSupplierCommand(
            $this->getProductId()->getValue(),
            5
        );

        yield [
            [
                'options' => [
                    'suppliers' => [
                        'default_supplier_id' => 5,
                        'supplier_ids' => [5, 3],
                    ],
                ],
            ],
            [$suppliersCommand, $defaultSupplierCommand],
        ];

        $suppliersCommand = new SetSuppliersCommand(
            $this->getProductId()->getValue(),
            [3, 5]
        );

        yield [
            [
                'options' => [
                    'suppliers' => [
                        'supplier_ids' => [3, 5],
                    ],
                ],
            ],
            [$suppliersCommand],
        ];

        $suppliersCommand = new SetSuppliersCommand(
            $this->getProductId()->getValue(),
            [5, 3]
        );
        $updateSuppliersCommand = new UpdateProductSuppliersCommand(
            $this->getProductId()->getValue(),
            [
                [
                    'supplier_id' => 5,
                    'currency_id' => 2,
                    'reference' => '',
                    'price_tax_excluded' => '0.5',
                    'combination_id' => 0,
                    // Product supplier ID can be 0 when not yet created
                    'product_supplier_id' => 0,
                ],
                [
                    'supplier_id' => 3,
                    'currency_id' => 5,
                    'reference' => '',
                    'price_tax_excluded' => '50.65',
                    'combination_id' => 0,
                    // Product supplier ID can also be indicated if it exists
                    'product_supplier_id' => 1,
                ],
            ]
        );
        $defaultSupplierCommand = new SetProductDefaultSupplierCommand(
            $this->getProductId()->getValue(),
            5
        );

        yield [
            [
                'options' => [
                    'suppliers' => [
                        'default_supplier_id' => 5,
                        'supplier_ids' => [5, 3],
                    ],
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'combination_id' => 0,
                            'product_supplier_id' => null,
                        ],
                        [
                            'supplier_id' => 3,
                            'currency_id' => 5,
                            'reference' => null,
                            'price_tax_excluded' => '50.65',
                            'combination_id' => null,
                            'product_supplier_id' => 1,
                        ],
                    ],
                ],
            ],
            [$suppliersCommand, $defaultSupplierCommand, $updateSuppliersCommand],
        ];

        $suppliersCommand = new SetSuppliersCommand(
            $this->getProductId()->getValue(),
            [5]
        );
        $updateSuppliersCommand = new UpdateProductSuppliersCommand(
            $this->getProductId()->getValue(),
            [
                [
                    'supplier_id' => 5,
                    'currency_id' => 2,
                    'reference' => '',
                    'price_tax_excluded' => '0.5',
                    'combination_id' => 0,
                    'product_supplier_id' => 0,
                ],
            ]
        );

        yield [
            [
                'options' => [
                    'suppliers' => [
                        // If default supplier is not set, no command is created
                        'default_supplier_id' => 0,
                        'supplier_ids' => [5],
                    ],
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'combination_id' => 0,
                            'product_supplier_id' => null,
                        ],
                    ],
                ],
            ],
            [$suppliersCommand, $updateSuppliersCommand],
        ];

        $suppliersCommand = new SetSuppliersCommand(
            $this->getProductId()->getValue(),
            [5]
        );
        $updateSuppliersCommand = new UpdateProductSuppliersCommand(
            $this->getProductId()->getValue(),
            [
                [
                    'supplier_id' => 5,
                    'currency_id' => 2,
                    'reference' => '',
                    'price_tax_excluded' => '0.5',
                    'combination_id' => 0,
                    'product_supplier_id' => 0,
                ],
            ]
        );

        yield [
            [
                'options' => [
                    'suppliers' => [
                        'supplier_ids' => [5],
                    ],
                    'product_suppliers' => [
                        [
                            'supplier_id' => 5,
                            'currency_id' => 2,
                            'reference' => '',
                            'price_tax_excluded' => '0.5',
                            'combination_id' => 0,
                            'product_supplier_id' => null,
                        ],
                    ],
                ],
            ],
            [$suppliersCommand, $updateSuppliersCommand],
        ];
    }
}
