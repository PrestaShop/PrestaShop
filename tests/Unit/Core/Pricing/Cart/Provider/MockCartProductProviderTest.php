<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Cart\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\CartProduct;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\MockCartProductProvider;

class MockCartProductProviderTest extends TestCase
{
    public function testReturnsEmptyArrayForUnknownCart(): void
    {
        $provider = new MockCartProductProvider([]);

        $this->assertSame([], $provider->getCartProducts(999));
    }

    public function testReturnsEmptyArrayWhenConstructedWithoutData(): void
    {
        $provider = new MockCartProductProvider();

        $this->assertSame([], $provider->getCartProducts(1));
    }

    public function testReturnsProductsForKnownCart(): void
    {
        $product1 = new CartProduct(1, 0, 0, 2);
        $product2 = new CartProduct(2, 5, 0, 1);

        $provider = new MockCartProductProvider([
            42 => [$product1, $product2],
        ]);

        $result = $provider->getCartProducts(42);

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]->getProductId());
        $this->assertSame(2, $result[1]->getProductId());
    }

    public function testReturnsEmptyArrayForOtherCartIds(): void
    {
        $provider = new MockCartProductProvider([
            1 => [new CartProduct(1, 0, 0, 1)],
        ]);

        $this->assertSame([], $provider->getCartProducts(2));
    }

    public function testMultipleCarts(): void
    {
        $provider = new MockCartProductProvider([
            1 => [new CartProduct(10, 0, 0, 1)],
            2 => [new CartProduct(20, 0, 0, 3), new CartProduct(30, 0, 0, 2)],
        ]);

        $this->assertCount(1, $provider->getCartProducts(1));
        $this->assertCount(2, $provider->getCartProducts(2));
        $this->assertSame(10, $provider->getCartProducts(1)[0]->getProductId());
        $this->assertSame(20, $provider->getCartProducts(2)[0]->getProductId());
    }
}
