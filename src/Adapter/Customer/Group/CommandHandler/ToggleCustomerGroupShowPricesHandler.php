<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\ToggleCustomerGroupShowPricesCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\CommandHandler\ToggleCustomerGroupShowPricesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotToggleCustomerGroupShowPricesException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotUpdateGroupException;

#[AsCommandHandler]
final class ToggleCustomerGroupShowPricesHandler implements ToggleCustomerGroupShowPricesHandlerInterface
{
    public function __construct(
        private readonly GroupRepository $customerGroupRepository,
    ) {
    }

    public function handle(ToggleCustomerGroupShowPricesCommand $command): void
    {
        $group = $this->customerGroupRepository->get($command->getCustomerGroupId());
        $group->show_prices = !$group->show_prices;

        try {
            $this->customerGroupRepository->partialUpdate($group, ['show_prices']);
        } catch (CannotUpdateGroupException $e) {
            throw new CannotToggleCustomerGroupShowPricesException(
                sprintf('Failed to toggle show_prices for customer group with id "%d"', $command->getCustomerGroupId()->getValue()),
                0,
                $e
            );
        }
    }
}
