<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Validate\ShopGroupValidator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\AddShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\AddShopGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use ShopGroup;

#[AsCommandHandler]
final class AddShopGroupHandler implements AddShopGroupHandlerInterface
{
    public function __construct(
        private readonly ShopGroupRepository $repository,
        private readonly ShopGroupValidator $validator,
    ) {
    }

    public function handle(AddShopGroupCommand $command): ShopGroupId
    {
        $shopGroup = new ShopGroup();
        $shopGroup->name = $command->getName();
        $shopGroup->color = $command->getColor();
        $shopGroup->share_customer = $command->isShareCustomer();
        $shopGroup->share_stock = $command->isShareStock();
        $shopGroup->share_order = $command->isShareOrder();
        $shopGroup->active = $command->isActive();

        $this->validator->validate($shopGroup);

        return $this->repository->add($shopGroup);
    }
}
