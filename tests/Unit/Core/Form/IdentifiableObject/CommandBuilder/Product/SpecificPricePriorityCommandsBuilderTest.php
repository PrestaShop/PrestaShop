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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\RemoveSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\SpecificPricePriorityCommandsBuilder;

class SpecificPricePriorityCommandsBuilderTest extends AbstractProductCommandBuilderTest
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
