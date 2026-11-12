<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Cart\Repository\CartRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartShopId;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryHandler\GetCartShopIdHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

#[AsQueryHandler]
final class GetCartShopIdHandler implements GetCartShopIdHandlerInterface
{
    public function __construct(
        private readonly CartRepository $cartRepository,
    ) {
    }

    public function handle(GetCartShopId $query): ShopId
    {
        $cart = $this->cartRepository->get($query->getCartId());

        return new ShopId((int) $cart->id_shop);
    }
}
