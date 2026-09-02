<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Cart\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\CartProduct;

class CartProductTest extends TestCase
{
    public function testGetProductId(): void
    {
        $cartProduct = new CartProduct(42, 0, 0, 1);

        $this->assertSame(42, $cartProduct->getProductId());
    }

    public function testGetCombinationId(): void
    {
        $cartProduct = new CartProduct(1, 5, 0, 1);

        $this->assertSame(5, $cartProduct->getCombinationId());
    }

    public function testGetCustomizationId(): void
    {
        $cartProduct = new CartProduct(1, 0, 7, 1);

        $this->assertSame(7, $cartProduct->getCustomizationId());
    }

    public function testGetQuantity(): void
    {
        $cartProduct = new CartProduct(1, 0, 0, 3);

        $this->assertSame(3, $cartProduct->getQuantity());
    }

    public function testAllGetters(): void
    {
        $cartProduct = new CartProduct(10, 20, 30, 5);

        $this->assertSame(10, $cartProduct->getProductId());
        $this->assertSame(20, $cartProduct->getCombinationId());
        $this->assertSame(30, $cartProduct->getCustomizationId());
        $this->assertSame(5, $cartProduct->getQuantity());
    }
}
