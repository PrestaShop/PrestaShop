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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductBasicInformationCommand;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\BasicInformationCommandsBuilder;

class BasicInformationCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands)
    {
        $builder = new BasicInformationCommandsBuilder();
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

        $command = new UpdateProductBasicInformationCommand($this->getProductId()->getValue());
        yield [
            [
                'description' => [
                    'not_handled' => 0,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductBasicInformationCommand($this->getProductId()->getValue());
        $localizedNames = [
            1 => 'Nom français',
            2 => 'French name',
        ];
        $command->setLocalizedNames($localizedNames);
        yield [
            [
                'header' => [
                    'name' => $localizedNames,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductBasicInformationCommand($this->getProductId()->getValue());
        $localizedDescriptions = [
            1 => 'Description française',
            2 => 'English description',
        ];
        $command->setLocalizedDescriptions($localizedDescriptions);
        yield [
            [
                'description' => [
                    'description' => $localizedDescriptions,
                ],
            ],
            [$command],
        ];

        $command = new UpdateProductBasicInformationCommand($this->getProductId()->getValue());
        $localizedShortDescriptions = [
            1 => 'Résumé français',
            2 => 'English summary',
        ];
        $command->setLocalizedShortDescriptions($localizedShortDescriptions);
        yield [
            [
                'description' => [
                    'description_short' => $localizedShortDescriptions,
                ],
            ],
            [$command],
        ];
    }
}
