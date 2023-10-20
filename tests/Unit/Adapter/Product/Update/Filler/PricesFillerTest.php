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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\PricesFiller;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;

class PricesFillerTest extends ProductFillerTestCase
{
    /**
     * @dataProvider getDataToTestUpdatablePropertiesFilling
     * @dataProvider getDataToTestUnitPriceAndPricePropertiesFilling
     *
     * @param Product $product
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param bool $ecoTaxEnabled
     * @param Product $expectedProduct
     */
    public function testFillsUpdatableProperties(
        Product $product,
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        bool $ecoTaxEnabled,
        Product $expectedProduct
    ): void {
        $this->fillUpdatableProperties(
            $this->getFiller($ecoTaxEnabled, $command->getShopConstraint()),
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
            ->setWholesalePrice('4.99')
            ->setPrice('45.99')
            ->setEcotax('0.3')
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->wholesale_price = 4.99;
        $expectedProduct->price = 45.99;
        $expectedProduct->ecotax = 0.3;

        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'wholesale_price',
                'price',
                'ecotax',
            ],
            false,
            $expectedProduct,
        ];

        // Check that unit_price_ratio is changed, but it is not in updatable properties.
        // More info in the PricesFiller comments
        $command = $this->getEmptyCommand()
            ->setPrice('50')
            ->setUnitPrice('10')
        ;
        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->price = 50.0;
        $expectedProduct->unit_price = 10.0;
        $expectedProduct->unit_price_ratio = 5.0;
        yield [
            $this->mockDefaultProduct(),
            $command,
            [
                'price',
                'unit_price',
            ],
            false,
            $expectedProduct,
        ];
    }

    /**
     * @return iterable
     */
    public function getDataToTestUnitPriceAndPricePropertiesFilling(): iterable
    {
        //When product price is 0 and ecotax is disabled, it should force unit_price to 0 as well
        $command = $this->getEmptyCommand()
            ->setPrice('0')
        ;
        $product = $this->mockDefaultProduct();
        $product->price = 20.5;
        $product->unit_price = 3.0;

        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->price = 0.0;
        $expectedProduct->unit_price = 0.0;

        yield [
            $product,
            $command,
            [
                'price',
                'unit_price',
            ],
            false,
            $expectedProduct,
        ];

        //When product price is 0, and ecotax is enabled, then unit_price should not be forced to 0
        $command = $this->getEmptyCommand()
            ->setPrice('0')
        ;
        $product = $this->mockDefaultProduct();
        $product->price = 20.5;
        $product->unit_price = 3.0;
        $product->ecotax = 3.5;

        $expectedProduct = $this->mockDefaultProduct();
        $expectedProduct->price = 0.0;
        $expectedProduct->unit_price = 3.0;
        $expectedProduct->ecotax = 3.5;

        yield [
            $product,
            $command,
            [
                'price',
            ],
            true,
            $expectedProduct,
        ];
    }

    /**
     * @param bool $ecoTaxEnabled
     * @param ShopConstraint $shopConstraint
     *
     * @return PricesFiller
     */
    private function getFiller(bool $ecoTaxEnabled, ShopConstraint $shopConstraint): PricesFiller
    {
        $numberExtractor = $this->getMockBuilder(NumberExtractor::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['extract'])
            ->getMock()
        ;
        $numberExtractor
            ->method('extract')
            ->willReturnCallback(function (Product $product, string $propertyPath) {
                return new DecimalNumber((string) $product->{$propertyPath});
            })
        ;

        $configuration = $this->getMockBuilder(Configuration::class)
            ->onlyMethods(['get'])
            ->getMock()
        ;
        $configuration
            ->method('get')
            ->willReturnMap([
                ['PS_USE_ECOTAX', null, $shopConstraint, $ecoTaxEnabled],
            ])
        ;

        return new PricesFiller($numberExtractor, $configuration);
    }
}
