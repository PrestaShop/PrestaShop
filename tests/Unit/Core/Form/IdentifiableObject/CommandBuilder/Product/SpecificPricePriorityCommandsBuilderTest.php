<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\RemoveSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\SpecificPricePriorityCommandsBuilder;

class SpecificPricePriorityCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new SpecificPricePriorityCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands()
    {
        yield [
            [
                'no_type_data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [],
            [],
        ];

        $expectedCommand = new RemoveSpecificPricePriorityForProductCommand($this->getProductId()->getValue());
        yield [
            [
                'pricing' => [
                    'priority_management' => [
                        'use_custom_priority' => false,
                    ],
                ],
            ],
            [$expectedCommand],
        ];

        // priorities are provided, but "use_custom_priority" is false, so we still expect removal command.
        yield [
            [
                'pricing' => [
                    'priority_management' => [
                        'use_custom_priority' => false,
                        'priorities' => ['id_shop', 'id_group', 'id_currency', 'id_country'],
                    ],
                ],
            ],
            [$expectedCommand],
        ];

        $expectedCommand = new SetSpecificPricePriorityForProductCommand(
            $this->getProductId()->getValue(),
            ['id_group', 'id_shop', 'id_currency', 'id_country']
        );
        yield [
            [
                'pricing' => [
                    'priority_management' => [
                        'use_custom_priority' => true,
                        'priorities' => ['id_group', 'id_shop', 'id_currency', 'id_country'],
                    ],
                ],
            ],
            [$expectedCommand],
        ];
    }
}
