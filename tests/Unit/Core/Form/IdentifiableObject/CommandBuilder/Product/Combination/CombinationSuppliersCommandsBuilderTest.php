<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationSuppliersCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationSuppliersCommandsBuilder;

class CombinationSuppliersCommandsBuilderTest extends AbstractCombinationCommandBuilderTestCase
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
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
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
                'product_suppliers' => [],
            ],
            [],
        ];

        $suppliersCommand = new UpdateCombinationSuppliersCommand(
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

        yield [
            [
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
            [$suppliersCommand],
        ];

        $suppliersCommand = new UpdateCombinationSuppliersCommand(
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
            [$suppliersCommand],
        ];
    }
}
