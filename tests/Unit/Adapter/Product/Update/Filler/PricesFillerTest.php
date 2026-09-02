<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        // When product price is 0 and ecotax is disabled, it should force unit_price to 0 as well
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

        // When product price is 0, and ecotax is enabled, then unit_price should not be forced to 0
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
