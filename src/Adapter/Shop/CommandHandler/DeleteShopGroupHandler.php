<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\DeleteShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\DeleteShopGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopGroupException;

#[AsCommandHandler]
final class DeleteShopGroupHandler implements DeleteShopGroupHandlerInterface
{
    public function __construct(private readonly ShopGroupRepository $repository)
    {
    }

    public function handle(DeleteShopGroupCommand $command): void
    {
        $shopGroupId = $command->getShopGroupId();

        if ([] !== $this->repository->getShopsFromGroup($shopGroupId)) {
            throw new CannotDeleteShopGroupException(
                sprintf('Shop group %d cannot be deleted because it contains shops', $shopGroupId->getValue()),
                CannotDeleteShopGroupException::GROUP_HAS_SHOPS
            );
        }

        $this->repository->delete($this->repository->get($shopGroupId));
    }
}
