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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetDefaultCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination\DefaultCombinationCommandsBuilder;

class DefaultCombinationCommandsBuilderTest extends AbstractCombinationCommandsBuilderTest
{
    /**
     * @dataProvider getExpectedCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommand(array $formData, array $expectedCommands): void
    {
        $builder = new DefaultCombinationCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @dataProvider getExpectedCommandsMultiShop
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildCommandMultiShop(array $formData, array $expectedCommands): void
    {
        $builder = new DefaultCombinationCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getCombinationId(), $formData, $this->getSingleShopConstraint());
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

        yield [
            [
                'header' => [
                    'is_default' => false,
                ],
            ],
            [],
        ];

        yield [
            [
                'header' => [
                    'is_default' => '0',
                ],
            ],
            [],
        ];

        yield [
            [
                'header' => [
                    'is_default' => 0,
                ],
            ],
            [],
        ];

        yield [
            [
                'header' => [
                    'is_default' => '',
                ],
            ],
            [],
        ];

        yield [
            [
                'header' => [
                    'is_default' => null,
                ],
            ],
            [],
        ];

        $command = new SetDefaultCombinationCommand($this->getCombinationId()->getValue(), $this->getSingleShopConstraint());
        yield [
            [
                'header' => [
                    'is_default' => true,
                ],
            ],
            [$command],
        ];

        yield [
            [
                'header' => [
                    'is_default' => 1,
                ],
            ],
            [$command],
        ];

        yield [
            [
                'header' => [
                    'is_default' => '1',
                ],
            ],
            [$command],
        ];
    }

    /**
     * @return iterable
     */
    public function getExpectedCommandsMultiShop(): iterable
    {
        yield [
            [
                'header' => [
                    'is_default' => false,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => true,
                ],
            ],
            [],
        ];

        yield [
            [
                'random stuff' => 'just to make sure it has no impact',
                'header' => [
                    'name' => [2 => 'shouldnt matter'],
                    'is_default' => true,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => false,
                ],
            ],
            [new SetDefaultCombinationCommand($this->getCombinationId()->getValue(), $this->getSingleShopConstraint())],
        ];

        yield [
            [
                'header' => [
                    'name' => [2 => 'shouldnt matter'],
                    'is_default' => 1,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => null,
                ],
            ],
            [new SetDefaultCombinationCommand($this->getCombinationId()->getValue(), $this->getSingleShopConstraint())],
        ];

        yield [
            [
                'random stuff' => 'just to make sure it has no impact',
                'header' => [
                    'name' => [2 => 'shouldnt matter'],
                    'is_default' => true,
                    self::MODIFY_ALL_SHOPS_PREFIX . 'is_default' => true,
                ],
            ],
            [new SetDefaultCombinationCommand($this->getCombinationId()->getValue(), ShopConstraint::allShops())],
        ];
    }
}
