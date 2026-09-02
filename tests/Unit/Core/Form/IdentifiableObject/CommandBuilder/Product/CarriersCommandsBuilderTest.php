<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\CarriersCommandsBuilder;

class CarriersCommandsBuilderTest extends AbstractProductCommandBuilderTestCase
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
