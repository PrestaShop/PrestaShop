<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopUrlRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\ToggleShopUrlMainCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\ToggleShopUrlMainHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;

#[AsCommandHandler]
final class ToggleShopUrlMainHandler implements ToggleShopUrlMainHandlerInterface
{
    public function __construct(
        private readonly ShopUrlRepository $repository,
    ) {
    }

    public function handle(ToggleShopUrlMainCommand $command): void
    {
        $shopUrl = $this->repository->get($command->getShopUrlId());

        if ($shopUrl->main) {
            throw new ShopUrlConstraintException(
                sprintf('Shop url %d is a main url, set another url as main instead', $shopUrl->id),
                ShopUrlConstraintException::MAIN_URL_CANNOT_BE_UNSET
            );
        }

        if (!$shopUrl->active) {
            $shopUrl->active = true;
            $this->repository->partialUpdate($shopUrl, ['active']);
        }
        $this->repository->setMain($shopUrl);
    }
}
