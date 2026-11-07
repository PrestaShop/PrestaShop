<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlChangeHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\DeleteShopHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;

#[AsCommandHandler]
final class DeleteShopHandler implements DeleteShopHandlerInterface
{
    public function __construct(
        private readonly ShopRepository $repository,
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopUrlChangeHandler $shopUrlChangeHandler,
    ) {
    }

    public function handle(DeleteShopCommand $command): void
    {
        $shopId = $command->getShopId();
        $shop = $this->repository->get($shopId);

        if ($shopId->getValue() === (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops())) {
            throw new CannotDeleteShopException(
                sprintf('Shop %d is the default shop', $shopId->getValue()),
                CannotDeleteShopException::DEFAULT_SHOP
            );
        }
        if ($this->repository->hasCustomersOrOrders($shopId)) {
            throw new CannotDeleteShopException(
                sprintf('Shop %d has customers or orders', $shopId->getValue()),
                CannotDeleteShopException::HAS_DEPENDENCIES
            );
        }

        $this->repository->delete($shop);
        Shop::resetStaticCache();
        $this->shopUrlChangeHandler->handle();
    }
}
