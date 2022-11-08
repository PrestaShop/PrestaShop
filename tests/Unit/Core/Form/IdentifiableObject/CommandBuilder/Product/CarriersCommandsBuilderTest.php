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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\CarriersCommandsBuilder;

class CarriersCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedSingleShopCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildSingleShopCommands(array $formData, array $expectedCommands): void
    {
        $builder = new CarriersCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @dataProvider getExpectedMultiShopCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildMultiShopCommands(array $formData, array $expectedCommands): void
    {
        $builder = new CarriersCommandsBuilder(self::MODIFY_ALL_SHOPS_PREFIX);
        $builtCommands = $builder->buildCommands($this->getProductId(), $formData, $this->getSingleShopConstraint());
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    public function getExpectedMultiShopCommands(): iterable
    {
        $command = $this->getAllShopsCommand([1, 2, 3]);
        yield [
            [
                'shipping' => [
                    'carriers' => ['1', '2', '3'],
                    self::MODIFY_ALL_SHOPS_PREFIX . 'carriers' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand([1, 2, 3]);
        yield [
            [
                'shipping' => [
                    'carriers' => ['1', '2', '3'],
                ],
            ],
            [$command],
        ];
    }

    public function getExpectedSingleShopCommands(): iterable
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        yield [
            [
                'shipping' => [
                    'not_handled' => 0,
                ],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand([1, 2, 3]);
        yield [
            [
                'shipping' => [
                    'carriers' => ['1', '2', '3'],
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand([]);
        yield [
            [
                'shipping' => [
                    'carriers' => [],
                ],
            ],
            [$command],
        ];
    }

    private function getSingleShopCommand(array $carrierReferenceIds): SetCarriersCommand
    {
        return new SetCarriersCommand(
            $this->getProductId()->getValue(),
            $carrierReferenceIds,
            $this->getSingleShopConstraint()
        );
    }

    private function getAllShopsCommand(array $carrierReferenceIds): SetCarriersCommand
    {
        return new SetCarriersCommand(
            $this->getProductId()->getValue(),
            $carrierReferenceIds,
            ShopConstraint::allShops()
        );
    }
}
