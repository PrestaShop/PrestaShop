<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cart\QueryHandler;

use Cart;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cart\QueryHandler\GetCartShopIdHandler;
use PrestaShop\PrestaShop\Adapter\Cart\Repository\CartRepository;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartShopId;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

class GetCartShopIdHandlerTest extends TestCase
{
    public function testItReturnsCartShopId(): void
    {
        $cartId = new CartId(42);
        $cart = $this->createMock(Cart::class);
        $cart->id_shop = 2;
        $cartRepository = $this->createMock(CartRepository::class);
        $cartRepository
            ->expects($this->once())
            ->method('get')
            ->with($cartId)
            ->willReturn($cart);

        $handler = new GetCartShopIdHandler($cartRepository);

        $shopId = $handler->handle(new GetCartShopId($cartId->getValue()));

        $this->assertSame(2, $shopId->getValue());
    }
}
