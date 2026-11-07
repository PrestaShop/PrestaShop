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
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\EditShopGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\EditShopGroupHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopGroupException;
use ShopGroup;
use StockAvailable;

#[AsCommandHandler]
final class EditShopGroupHandler implements EditShopGroupHandlerInterface
{
    public function __construct(
        private readonly ShopGroupRepository $repository,
        private readonly ShopGroupValidator $validator,
    ) {
    }

    public function handle(EditShopGroupCommand $command): void
    {
        $shopGroupId = $command->getShopGroupId();
        $shopGroup = $this->repository->get($shopGroupId);
        $previousSharingOptions = $this->getSharingOptions($shopGroup);
        $propertiesToUpdate = [];

        if (null !== $command->getName()) {
            $shopGroup->name = $command->getName();
            $propertiesToUpdate[] = 'name';
        }
        if (null !== $command->getColor()) {
            $shopGroup->color = $command->getColor();
            $propertiesToUpdate[] = 'color';
        }
        if (null !== $command->isShareCustomer()) {
            $shopGroup->share_customer = $command->isShareCustomer();
            $propertiesToUpdate[] = 'share_customer';
        }
        if (null !== $command->isShareStock()) {
            $shopGroup->share_stock = $command->isShareStock();
            $propertiesToUpdate[] = 'share_stock';
        }
        if (null !== $command->isShareOrder()) {
            $shopGroup->share_order = $command->isShareOrder();
            $propertiesToUpdate[] = 'share_order';
        }
        if (null !== $command->isActive()) {
            if (!$command->isActive() && $shopGroup->active && [] !== $this->repository->getShopsFromGroup($shopGroupId)) {
                throw new CannotUpdateShopGroupException(
                    sprintf('Shop group %d cannot be disabled because it contains shops', $shopGroupId->getValue()),
                    CannotUpdateShopGroupException::CANNOT_DISABLE_GROUP_WITH_SHOPS
                );
            }
            $shopGroup->active = $command->isActive();
            $propertiesToUpdate[] = 'active';
        }

        $sharingOptions = $this->getSharingOptions($shopGroup);
        $this->validator->assertSharingOptionsCanChange($previousSharingOptions, $sharingOptions);
        $this->validator->validate($shopGroup);
        $this->repository->partialUpdate($shopGroup, $propertiesToUpdate);

        if ($sharingOptions['share_stock'] !== $previousSharingOptions['share_stock']) {
            StockAvailable::resetProductFromStockAvailableByShopGroup($shopGroup);
        }
    }

    /**
     * @return array{share_customer: bool, share_stock: bool, share_order: bool}
     */
    private function getSharingOptions(ShopGroup $shopGroup): array
    {
        return [
            'share_customer' => (bool) $shopGroup->share_customer,
            'share_stock' => (bool) $shopGroup->share_stock,
            'share_order' => (bool) $shopGroup->share_order,
        ];
    }
}
