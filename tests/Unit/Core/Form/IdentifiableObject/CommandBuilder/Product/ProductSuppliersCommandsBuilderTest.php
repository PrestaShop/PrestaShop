<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\RemoveAllAssociatedProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductSuppliersCommandsBuilder;

class ProductSuppliersCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
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
