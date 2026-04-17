<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ShippingFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use Product;

class ShippingFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     *
     * @param Product $product
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param Product $expectedProduct
     */
    public function testFillsUpdatableProperties(
        Product $product,
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        Product $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller(),
            $product,
            $command,
            $expectedUpdatableProperties,
            $expectedProduct
        );
    }

    /**
     * @return iterable
     */
    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $command = $this->getEmptyCommand()
            ->setWidth('10.5')
            ->setHeight('8.5')
            ->setDepth('4')
            ->setWeight('3.2')
        ;

        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->width = 10.5;
        $expectedProduct->height = 8.5;
        $expectedProduct->depth = 4.0;
        $expectedProduct->weight = 3.2;

        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'width',
                'height',
                'depth',
                'weight',
            ],
            $expectedProduct,
        ];

        $command = $this->getEmptyCommand()
            ->setAdditionalShippingCost('15.5')
            ->setDeliveryTimeNoteType(2)
            ->setLocalizedDeliveryTimeInStockNotes([
                1 => 'Available',
                2 => 'Yra sandelyje',
            ])
            ->setLocalizedDeliveryTimeOutOfStockNotes([
                1 => 'Currently out of stock',
                2 => 'Isparduota',
            ])
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->additional_shipping_cost = 15.5;
        $expectedProduct->additional_delivery_times = 2;
        $expectedProduct->delivery_in_stock = [
            1 => 'Available',
            2 => 'Yra sandelyje',
        ];
        $expectedProduct->delivery_out_stock = [
            1 => 'Currently out of stock',
            2 => 'Isparduota',
        ];

        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'additional_shipping_cost',
                'additional_delivery_times',
                'delivery_in_stock' => [1, 2],
                'delivery_out_stock' => [1, 2],
            ],
            $expectedProduct,
        ];
    }

    /**
     * @return ShippingFiller
     */
    private function getFiller(): ShippingFiller
    {
        return new ShippingFiller();
    }
}
