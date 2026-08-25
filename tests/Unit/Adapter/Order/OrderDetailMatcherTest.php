<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Order;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailMatcher;

class OrderDetailMatcherTest extends TestCase
{
    /**
     * A customizable product ordered both plain and customized produces two order details sharing the same
     * product and combination: only the customization tells them apart.
     */
    private const ORDER_DETAILS = [
        ['id_order_detail' => 1, 'product_id' => 14, 'product_attribute_id' => 0, 'id_customization' => 0],
        ['id_order_detail' => 2, 'product_id' => 14, 'product_attribute_id' => 0, 'id_customization' => 5],
        ['id_order_detail' => 3, 'product_id' => 14, 'product_attribute_id' => 0, 'id_customization' => 6],
        ['id_order_detail' => 4, 'product_id' => 14, 'product_attribute_id' => 3, 'id_customization' => 0],
        ['id_order_detail' => 5, 'product_id' => 14, 'product_attribute_id' => 3, 'id_customization' => 7],
    ];

    /**
     * @dataProvider provideProductIdentities
     */
    public function testItMatchesOnProductCombinationAndCustomization(
        int $productId,
        int $combinationId,
        int $customizationId,
        ?int $expectedOrderDetailId
    ): void {
        $matched = (new OrderDetailMatcher())->match(self::ORDER_DETAILS, $productId, $combinationId, $customizationId);

        if ($expectedOrderDetailId === null) {
            $this->assertNull($matched);

            return;
        }

        $this->assertNotNull($matched);
        $this->assertSame($expectedOrderDetailId, $matched['id_order_detail']);
    }

    public function provideProductIdentities(): iterable
    {
        yield 'plain line' => [14, 0, 0, 1];
        yield 'first customization' => [14, 0, 5, 2];
        yield 'second customization' => [14, 0, 6, 3];
        yield 'combination, plain' => [14, 3, 0, 4];
        yield 'combination, customized' => [14, 3, 7, 5];
        yield 'unknown customization' => [14, 0, 99, null];
        yield 'unknown combination' => [14, 99, 0, null];
        yield 'unknown product' => [99, 0, 0, null];
    }

    /**
     * The legacy layer hands over raw database rows whose values are typed by the database driver: pdo_mysql
     * returns integers where mysqli returns strings, so the comparison must not depend on it.
     */
    public function testItMatchesRowsWhateverTheDatabaseDriverTypes(): void
    {
        $stringRows = [
            ['id_order_detail' => '1', 'product_id' => '14', 'product_attribute_id' => '3', 'id_customization' => '0'],
            ['id_order_detail' => '2', 'product_id' => '14', 'product_attribute_id' => '3', 'id_customization' => '7'],
        ];

        $matched = (new OrderDetailMatcher())->match($stringRows, 14, 3, 7);

        $this->assertNotNull($matched);
        $this->assertSame('2', $matched['id_order_detail']);
    }

    /**
     * Cart product lines carry a null combination when the product has none, and Product::getProductProperties()
     * casts the combination to an int while leaving the other identifiers untouched.
     */
    public function testItMatchesACartProductLine(): void
    {
        $matcher = new OrderDetailMatcher();

        $plainLine = ['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 0];
        $customizedLine = ['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 6];

        $this->assertSame(1, $matcher->matchCartProduct(self::ORDER_DETAILS, $plainLine)['id_order_detail']);
        $this->assertSame(3, $matcher->matchCartProduct(self::ORDER_DETAILS, $customizedLine)['id_order_detail']);
    }

    public function testItMatchesNothingInAnEmptyOrder(): void
    {
        $this->assertNull((new OrderDetailMatcher())->match([], 14));
    }
}
