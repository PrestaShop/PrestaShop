<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Context;
use Currency;
use Customer;
use PHPUnit\Framework\TestCase;
use Product;
use ProductAssembler;
use SpecificPrice;

/**
 * A quantity discount is matched against a quantity, and which quantity that is depends on who is
 * asking. A cart line asks about the quantity it holds. A product page asks about a quantity the
 * customer is about to ADD, so the quantity that decides the price is that plus what the cart already
 * holds - otherwise the page quotes a price the cart immediately contradicts.
 */
class ProductQuantityToPriceTest extends TestCase
{
    private const DISCOUNT_FROM_QUANTITY = 5;
    private const DISCOUNT_PERCENTAGE = 0.2;

    private int $productId;
    private int $specificPriceId;
    private float $priceWithoutDiscount;

    protected function setUp(): void
    {
        parent::setUp();

        // The CLI context carries neither a currency nor a visitor. Pricing needs the first to know its
        // rounding precision, and ProductSearchContext reads the id off the second.
        $context = Context::getContext();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        if (!$context->customer) {
            $context->customer = new Customer();
        }

        $product = new Product();
        $product->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Quantity discount fixture'];
        $product->link_rewrite = [(int) Configuration::get('PS_LANG_DEFAULT') => 'quantity-discount-fixture'];
        $product->price = 100.0;
        $product->id_category_default = (int) Configuration::get('PS_HOME_CATEGORY');
        $product->minimal_quantity = 1;
        $product->active = true;
        $product->add();
        $this->productId = (int) $product->id;

        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = $this->productId;
        $specificPrice->id_shop = 0;
        $specificPrice->id_shop_group = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = 0;
        $specificPrice->id_product_attribute = 0;
        $specificPrice->price = -1.0;
        $specificPrice->from_quantity = self::DISCOUNT_FROM_QUANTITY;
        $specificPrice->reduction = self::DISCOUNT_PERCENTAGE;
        $specificPrice->reduction_tax = 1;
        $specificPrice->reduction_type = 'percentage';
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->add();
        $this->specificPriceId = (int) $specificPrice->id;

        SpecificPrice::flushCache();
        $this->priceWithoutDiscount = $this->presentedPrice(['quantity_wanted' => 1]);
    }

    protected function tearDown(): void
    {
        if (!empty($this->specificPriceId)) {
            (new SpecificPrice($this->specificPriceId))->delete();
        }
        if (!empty($this->productId)) {
            (new Product($this->productId))->delete();
        }
        SpecificPrice::flushCache();

        parent::tearDown();
    }

    /**
     * The reported case: the cart already holds the threshold quantity and charges the discount, while
     * the product page is still asked about the single unit in its input.
     */
    public function testAProductPagePricesForTheQuantityTheCartLineWillHold(): void
    {
        $priceWhenAddingOneToACartOfFive = $this->presentedPrice([
            'quantity_wanted' => 1,
            'cart_quantity' => 5,
            'quantity_to_price' => 6,
        ]);

        $this->assertSame($this->discounted(), $priceWhenAddingOneToACartOfFive);
        $this->assertNotSame($this->priceWithoutDiscount, $priceWhenAddingOneToACartOfFive);
    }

    /**
     * The threshold is reached by the sum, not by either side on its own - three in the cart and two in
     * the input is five, and five is what the cart line will be priced as.
     */
    public function testTheQuantityInTheCartAndTheQuantityBeingAddedCountTogether(): void
    {
        $this->assertSame(
            $this->discounted(),
            $this->presentedPrice(['quantity_wanted' => 2, 'cart_quantity' => 3, 'quantity_to_price' => 5])
        );
        $this->assertSame(
            $this->priceWithoutDiscount,
            $this->presentedPrice(['quantity_wanted' => 1, 'cart_quantity' => 3, 'quantity_to_price' => 4])
        );
    }

    /**
     * The cart asks a different question and must keep getting the same answer: it prices the line it
     * holds, and it never sets quantity_to_price.
     */
    public function testACartLineStillPricesForTheQuantityItHolds(): void
    {
        $this->assertSame($this->discounted(), $this->presentedPrice(['cart_quantity' => 5]));
        $this->assertSame($this->discounted(), $this->presentedPrice(['quantity_wanted' => 5, 'cart_quantity' => 5]));
        $this->assertSame($this->priceWithoutDiscount, $this->presentedPrice(['quantity_wanted' => 1, 'cart_quantity' => 1]));
    }

    /**
     * A listing sets neither, and must stay independent of whatever the visitor happens to have in
     * their cart.
     */
    public function testAListingIsUnaffected(): void
    {
        $this->assertSame($this->priceWithoutDiscount, $this->presentedPrice([]));
    }

    private function discounted(): float
    {
        return round($this->priceWithoutDiscount * (1 - self::DISCOUNT_PERCENTAGE), 2);
    }

    /**
     * @param array<string, int> $quantities
     */
    private function presentedPrice(array $quantities): float
    {
        // getProductProperties keys its result cache on the product, not on the quantity it was priced
        // for, so a second call in the same process would return the first call's prices.
        Product::resetStaticCache();
        SpecificPrice::flushCache();

        $assembler = new ProductAssembler(Context::getContext());
        $presented = $assembler->assembleProduct(array_merge(['id_product' => $this->productId], $quantities));

        return round((float) $presented['price'], 2);
    }
}
