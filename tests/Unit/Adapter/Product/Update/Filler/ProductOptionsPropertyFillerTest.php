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

use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ProductOptionsPropertyFiller;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ProductUpdatablePropertyFillerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use Product;

class ProductOptionsPropertyFillerTest extends PropertyFillerTestCase
{
    /**
     * @dataProvider getDataForTestShowPriceAndAvailableForOrderProperties
     *
     * @param Product $product
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     */
    public function testFillsShowPriceAndAvailableForOrderProperties(
        Product $product,
        UpdateProductCommand $command,
        array $expectedUpdatableProperties
    ): void {
        $this->assertSame(
            $expectedUpdatableProperties,
            $this->getFiller()->fillUpdatableProperties($product, $command)
        );
    }

    /**
     * @return iterable
     */
    public function getDataForTestShowPriceAndAvailableForOrderProperties(): iterable
    {
        $product = $this->mockProduct();
        $product->show_price = false;

        $command = $this
            ->getEmptyCommand()
            ->setAvailableForOrder(true)
        ;

        yield [
            $product,
            $command,
            [
                'available_for_order',
                'show_price',
            ],
        ];

        $product = $this->mockProduct();
        $command = $this
            ->getEmptyCommand()
            ->setAvailableForOrder(true)
        ;

        //@todo: now the command input doesn't change the value of previous product state, should we still update it?
        //       Or should we care to add checks if product value changed and only then update it?
        yield [
            $product,
            $command,
            [
                'available_for_order',
            ],
        ];

        $command = $this
            ->getEmptyCommand()
            ->setAvailableForOrder(false)
            ->setShowPrice(true)
        ;

        yield [
            $product,
            $command,
            [
                'available_for_order',
                'show_price',
            ],
        ];
    }

    public function getDataForTestFillsUpdatableProperties(): iterable
    {
        $command = $this->getEmptyCommand();
        yield [$command, []];

        $command = $this
            ->getEmptyCommand()
            ->setVisibility(ProductVisibility::VISIBLE_IN_CATALOG)
            ->setCondition(ProductCondition::USED)
        ;

        yield [
            $command,
            [
                'visibility',
                'condition',
            ],
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

        yield [
            $command,
            [
                'visibility',
                'available_for_order',
                'show_price',
                'online_only',
                'show_condition',
                'id_manufacturer',
            ],
        ];
    }

    public function getFiller(): ProductUpdatablePropertyFillerInterface
    {
        return new ProductOptionsPropertyFiller();
    }
}
