<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopUrlRepository;
use PrestaShop\PrestaShop\Adapter\Shop\ShopUrlChangeHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopUrlCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\DeleteShopUrlHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopUrlException;

#[AsCommandHandler]
final class DeleteShopUrlHandler implements DeleteShopUrlHandlerInterface
{
    public function __construct(
        private readonly ShopUrlRepository $repository,
        private readonly ShopUrlChangeHandler $shopUrlChangeHandler,
    ) {
    }

    public function handle(DeleteShopUrlCommand $command): void
    {
        $shopUrl = $this->repository->get($command->getShopUrlId());

        if ($shopUrl->main) {
            throw new CannotDeleteShopUrlException(
                sprintf('Shop url %d cannot be deleted because it is a main url', $shopUrl->id),
                CannotDeleteShopUrlException::MAIN_URL
            );
        }

        $this->repository->delete($shopUrl);
        $this->shopUrlChangeHandler->handle();
    }
}
