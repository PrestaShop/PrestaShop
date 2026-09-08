<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Adapter\Discount\Update\DiscountConditionsUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\DeleteDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\DeleteDiscountHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;

#[AsCommandHandler]
class DeleteDiscountHandler implements DeleteDiscountHandlerInterface
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
        private readonly DiscountConditionsUpdater $discountConditionsUpdater,
        private readonly ShopContext $shopContext,
    ) {
    }

    public function handle(DeleteDiscountCommand $command): void
    {
        $this->deleteDiscount($command->getDiscountId());
    }

    private function deleteDiscount(DiscountId $discountId): void
    {
        if ($this->shopContext->isAllShopContext()) {
            $this->discountRepository->delete($discountId);

            return;
        }

        $existingShopIds = $this->discountRepository->getShopsIds($discountId);
        $remainingShopIds = array_diff($existingShopIds, $this->shopContext->getAssociatedShopIds());

        if (empty($existingShopIds) || empty($remainingShopIds)) {
            $this->discountRepository->delete($discountId);

            return;
        }

        // Other shops still use this discount: dissociate from current shop(s) only.
        $this->discountConditionsUpdater->update($discountId, shopIds: array_values($remainingShopIds));
    }
}
