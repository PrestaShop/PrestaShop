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

use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\OptionsFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use Product;

class OptionsFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     * @dataProvider getDataToTestShowPriceAndAvailableForOrderPropertiesFilling
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
     * This provider Provides the initial product and the expected one,
     * because there are some cases that depends on previous and new values
     *
     * @return iterable
     */
    public function getDataToTestShowPriceAndAvailableForOrderPropertiesFilling(): iterable
    {
        $product = $this->mockDefaultProduct();
        $product->show_price = false;

        // when available_for_order is set to true, then show_price must be forced to true
        $command = $this
            ->getEmptyCommand()
            ->setAvailableForOrder(true)
        ;

        $expectedProduct = $this->mockDefaultProduct();
        // default product properties already has these values,
        // but we still set them just to be more explicit about the expected change
        $expectedProduct->show_price = true;
        $expectedProduct->available_for_order = true;

        yield [
            $product,
            $command,
            [
                'available_for_order',
                'show_price',
            ],
            $expectedProduct,
        ];

        $product = $this->mockDefaultProduct();
        $product->available_for_order = true;
        $product->show_price = false;

        $command = $this
            ->getEmptyCommand()
            ->setAvailableForOrder(false)
            ->setShowPrice(true)
        ;

        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->available_for_order = false;
        $expectedProduct->show_price = true;

        yield [
            $product,
            $command,
            [
                'available_for_order',
                'show_price',
            ],
            $expectedProduct,
        ];

        $product = $this->mockDefaultProduct();
        $product->available_for_order = false;
        $product->show_price = false;

        $command = $this
            ->getEmptyCommand()
            ->setShowPrice(true)
        ;

        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->available_for_order = false;
        $expectedProduct->show_price = true;

        yield [
            $product,
            $command,
            [
                'show_price',
            ],
            $expectedProduct,
        ];
    }

    /**
     * @return iterable
     */
    public function getDataToTestUpdatablePropertiesFilling(): iterable
    {
        $command = $this->getEmptyCommand();
        yield [$this->mockDefaultProduct(), $command, [], $this->mockDefaultProduct()];

        $command = $this
            ->getEmptyCommand()
            ->setVisibility(ProductVisibility::VISIBLE_IN_CATALOG)
            ->setCondition(ProductCondition::USED)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->visibility = ProductVisibility::VISIBLE_IN_CATALOG;
        $expectedProduct->condition = ProductCondition::USED;

        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'visibility',
                'condition',
            ],
            $expectedProduct,
        ];

        $command = $this
            ->getEmptyCommand()
            ->setVisibility(ProductVisibility::INVISIBLE)
            ->setShowCondition(true)
            ->setManufacturerId(10)
            ->setOnlineOnly(false)
            ->setAvailableForOrder(false)
            ->setShowPrice(false)
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->visibility = ProductVisibility::INVISIBLE;
        $expectedProduct->show_condition = true;
        $expectedProduct->id_manufacturer = 10;
        $expectedProduct->available_for_order = false;
        $expectedProduct->show_price = false;

        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'visibility',
                'available_for_order',
                'show_price',
                'online_only',
                'show_condition',
                'id_manufacturer',
            ],
            $expectedProduct,
        ];
    }

    private function getFiller(): OptionsFiller
    {
        return new OptionsFiller();
    }
}
