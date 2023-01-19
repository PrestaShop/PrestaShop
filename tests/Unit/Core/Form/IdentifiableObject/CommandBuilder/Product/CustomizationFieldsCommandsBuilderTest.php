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

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\RemoveAllCustomizationFieldsFromProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Command\SetProductCustomizationFieldsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\CustomizationFieldsCommandsBuilder;

class CustomizationFieldsCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @var CustomizationFieldsCommandsBuilder
     */
    private $customizationFieldsCommandBuilder;

    public function setUp(): void
    {
        $this->customizationFieldsCommandBuilder = new CustomizationFieldsCommandsBuilder();
    }

    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builtCommands = $this->customizationFieldsCommandBuilder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedCommands(): Generator
    {
        yield [
            [
                'how did I get here?' => ['useless val'],
            ],
            [],
        ];

        yield [
            [
                'customizations' => null,
            ],
            [],
        ];

        yield [
            [
                'details' => [
                    'customizations' => null,
                ],
            ],
            [],
        ];

        yield [
            [
                'details' => [
                    'customizations' => [],
                ],
            ],
            [new RemoveAllCustomizationFieldsFromProductCommand($this->getProductId()->getValue())],
        ];

        $localizedNames = [
            '1' => 'test lang 1',
            '3' => 'test lang 3',
        ];

        $command = new SetProductCustomizationFieldsCommand(
            $this->getProductId()->getValue(),
            [
                [
                    'type' => CustomizationFieldType::TYPE_TEXT,
                    'localized_names' => $localizedNames,
                    'is_required' => false,
                    'id' => 3,
                    'added_by_module' => false,
                ],
                [
                    'type' => CustomizationFieldType::TYPE_TEXT,
                    'localized_names' => $localizedNames,
                    'is_required' => false,
                    'id' => null,
                    'added_by_module' => false,
                ],
                [
                    'type' => CustomizationFieldType::TYPE_FILE,
                    'localized_names' => $localizedNames,
                    'is_required' => true,
                    'id' => null,
                    'added_by_module' => false,
                ],
            ],
            $this->getSingleShopConstraint()
        );

        yield [
            [
                'details' => [
                    'customizations' => [
                        'customization_fields' => [
                            [
                                'type' => 1,
                                'name' => $localizedNames,
                                'required' => 0,
                                'id' => '3',
                            ],
                            [
                                'type' => 1,
                                'name' => $localizedNames,
                                'required' => false,
                                'id' => '0',
                            ],
                            [
                                'type' => '0',
                                'name' => $localizedNames,
                                'required' => true,
                                'id' => 0,
                            ],
                        ],
                    ],
                ],
            ],
            [$command],
        ];
    }
}
