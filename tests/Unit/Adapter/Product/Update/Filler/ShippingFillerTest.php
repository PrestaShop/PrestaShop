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
