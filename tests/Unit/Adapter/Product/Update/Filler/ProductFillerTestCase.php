<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Update\Filler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ProductFillerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductCondition;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use Product;

abstract class ProductFillerTestCase extends TestCase
{
    protected const DEFAULT_LANG_ID = 1;
    protected const DEFAULT_SHOP_ID = 2;
    protected const PRODUCT_ID = 3;

    /**
     * @param ProductFillerInterface $filler
     * @param Product $product
     * @param UpdateProductCommand $command
     * @param array $expectedUpdatableProperties
     * @param Product $expectedProduct
     */
    protected function fillUpdatableProperties(
        ProductFillerInterface $filler,
        Product $product,
        UpdateProductCommand $command,
        array $expectedUpdatableProperties,
        Product $expectedProduct
    ) {
        $this->assertSame(
            $expectedUpdatableProperties,
            $filler->fillUpdatableProperties($product, $command)
        );

        // make sure the product properties were filled as expected.
        $this->assertEquals($expectedProduct, $product);
    }

    /**
     * This method mocks product into its default state.
     * Feel free to override it if needed for specific test cases.
     *
     * @return Product
     */
    protected function mockDefaultProduct(): Product
    {
        $product = $this->createMock(Product::class);
        $product->id = self::PRODUCT_ID;
        $product->name = [];
        $product->description = [];
        $product->description_short = [];
        $product->link_rewrite = [];
        $product->show_price = true;
        $product->price = 0.0;
        $product->unit_price_ratio = 0.0;
        $product->available_for_order = true;
        $product->visibility = ProductVisibility::VISIBLE_EVERYWHERE;
        $product->condition = ProductCondition::NEW;
        $product->show_condition = false;
        $product->online_only = false;
        $product->width = 0;
        $product->height = 0;
        $product->depth = 0;
        $product->weight = 0;
        $product->additional_shipping_cost = 0;
        $product->additional_delivery_times = 1;
        $product->minimal_quantity = 1;
        $product->low_stock_alert = false;
        $product->pack_stock_type = PackStockType::STOCK_TYPE_DEFAULT;
        $product->available_date = DateTimeUtil::NULL_DATE;

        return $product;
    }

    /**
     * @return UpdateProductCommand
     */
    protected function getEmptyCommand(): UpdateProductCommand
    {
        return new UpdateProductCommand(self::PRODUCT_ID, ShopConstraint::shop(self::DEFAULT_SHOP_ID));
    }
}
