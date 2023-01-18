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

use DateTime;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\StockInformationFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use Product;

class StockInformationFillerTest extends ProductFillerTestCase
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
            new StockInformationFiller(),
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
        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setMinimalQuantity(11)
            ->setPackStockType(PackStockType::STOCK_TYPE_PRODUCTS_ONLY)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->minimal_quantity = 11;
        $expectedProduct->pack_stock_type = PackStockType::STOCK_TYPE_PRODUCTS_ONLY;

        yield [
            $product,
            $command,
            [
                'minimal_quantity',
                'pack_stock_type',
            ],
            $expectedProduct,
        ];

        $localizedAvailableNow = [
            1 => 'français available now',
            2 => 'english available now',
        ];
        $localizedAvailableLater = [
            1 => 'français available later',
            2 => 'english available later',
        ];

        $product = $this->mockDefaultProduct();
        $command = $this->getEmptyCommand()
            ->setLocalizedAvailableNowLabels($localizedAvailableNow)
            ->setLocalizedAvailableLaterLabels($localizedAvailableLater)
            ->setLowStockThreshold(42)
            ->setMinimalQuantity(10)
            ->setPackStockType(PackStockType::STOCK_TYPE_BOTH)
            ->setAvailableDate(new DateTime('2022-10-10'))
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->available_now = $localizedAvailableNow;
        $expectedProduct->available_later = $localizedAvailableLater;
        $expectedProduct->low_stock_alert = true;
        $expectedProduct->low_stock_threshold = 42;
        $expectedProduct->minimal_quantity = 10;
        $expectedProduct->pack_stock_type = PackStockType::STOCK_TYPE_BOTH;
        $expectedProduct->available_date = '2022-10-10';

        yield [
            $product,
            $command,
            [
                'available_later' => [1, 2],
                'available_now' => [1, 2],
                'low_stock_threshold',
                'low_stock_alert',
                'minimal_quantity',
                'pack_stock_type',
                'available_date',
            ],
            $expectedProduct,
        ];
    }
}
