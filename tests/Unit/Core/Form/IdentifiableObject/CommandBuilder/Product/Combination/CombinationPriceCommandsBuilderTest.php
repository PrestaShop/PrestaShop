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

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationPricesCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\CombinationPriceCommandsBuilder;

class CombinationPriceCommandsBuilderTest extends AbstractCombinationCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new CombinationPriceCommandsBuilder();
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData);
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @return Generator
     */
    public function getExpectedCommands(): Generator
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        $command = new UpdateCombinationPricesCommand($this->getCombinationId()->getValue());
        yield [
            [
                'price_impact' => [
                    'not_handled' => 0,
                    'weight' => 12.0,
                ],
            ],
            [$command],
        ];

        $command = new UpdateCombinationPricesCommand($this->getCombinationId()->getValue());
        $command->setImpactOnUnitPrice('51.00');
        $command->setWholesalePrice('12.00');
        yield [
            [
                'price_impact' => [
                    'unit_price' => 51.00,
                    'wholesale_price' => '12.00',
                ],
            ],
            [$command],
        ];

        $command = new UpdateCombinationPricesCommand($this->getCombinationId()->getValue());
        $command->setEcoTax('42.00');
        $command->setImpactOnPrice('49.00');
        yield [
            [
                'price_impact' => [
                    'ecotax' => 42.00,
                    'price_tax_excluded' => '49.00',
                ],
            ],
            [$command],
        ];
    }
}
