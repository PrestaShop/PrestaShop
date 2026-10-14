<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopUrlRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\ToggleShopUrlStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\ToggleShopUrlStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlConstraintException;

#[AsCommandHandler]
final class ToggleShopUrlStatusHandler implements ToggleShopUrlStatusHandlerInterface
{
    public function __construct(
        private readonly ShopUrlRepository $repository,
    ) {
    }

    public function handle(ToggleShopUrlStatusCommand $command): void
    {
        $shopUrl = $this->repository->get($command->getShopUrlId());

        if ($shopUrl->main && $shopUrl->active) {
            throw new ShopUrlConstraintException('The main URL of a shop cannot be disabled', ShopUrlConstraintException::MAIN_URL_MUST_BE_ACTIVE);
        }

        $shopUrl->active = !$shopUrl->active;
        $this->repository->partialUpdate($shopUrl, ['active']);
    }
}
