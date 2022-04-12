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

use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductStatusCommandsBuilder;

class ProductStatusCommandsBuilderTest extends AbstractProductCommandBuilderTest
{
    /**
     * @dataProvider getExpectedSingleShopCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildSingleShopCommands(array $formData, array $expectedCommands)
    {
        $this->assertBuildCommands($formData, $expectedCommands);
    }

    /**
     * @dataProvider getExpectedMultiShopCommands
     *
     * @param array $formData
     * @param array $expectedCommands
     */
    public function testBuildMultiShopCommands(array $formData, array $expectedCommands): void
    {
        $this->assertBuildCommands($formData, $expectedCommands);
    }

    public function getExpectedSingleShopCommands(): iterable
    {
        yield [
            [
                'no data' => ['useless value'],
            ],
            [],
        ];

        $command = $this->getSingleShopCommand(true);
        yield [
            [
                'footer' => [
                    'active' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getSingleShopCommand(false);
        yield [
            [
                'footer' => [
                    'active' => false,
                ],
            ],
            [$command],
        ];
    }

    /**
     * @return iterable
     */
    public function getExpectedMultiShopCommands(): iterable
    {
        $command = $this->getAllShopsCommand(false);
        yield [
            [
                'footer' => [
                    'active' => false,
                    self::MODIFY_ALL_NAME_PREFIX . 'active' => true,
                ],
            ],
            [$command],
        ];

        $command = $this->getAllShopsCommand(true);
        yield [
            [
                'footer' => [
                    'active' => true,
                    self::MODIFY_ALL_NAME_PREFIX . 'active' => true,
                ],
            ],
            [$command],
        ];
    }

    /**
     * @param array $formData
     * @param array $expectedCommands
     */
    private function assertBuildCommands(array $formData, array $expectedCommands): void
    {
        $builder = new ProductStatusCommandsBuilder(self::MODIFY_ALL_NAME_PREFIX);
        $builtCommands = $builder->buildCommands(
            $this->getProductId(),
            $formData,
            $this->getSingleShopConstraint()
        );
        $this->assertEquals($expectedCommands, $builtCommands);
    }

    /**
     * @param bool $status
     *
     * @return UpdateProductStatusCommand
     */
    private function getSingleShopCommand(bool $status): UpdateProductStatusCommand
    {
        return new UpdateProductStatusCommand(
            $this->getProductId()->getValue(),
            $status,
            $this->getSingleShopConstraint()
        );
    }

    /**
     * @param bool $status
     *
     * @return UpdateProductStatusCommand
     */
    private function getAllShopsCommand(bool $status): UpdateProductStatusCommand
    {
        return new UpdateProductStatusCommand(
            $this->getProductId()->getValue(),
            $status,
            ShopConstraint::allShops()
        );
    }
}
