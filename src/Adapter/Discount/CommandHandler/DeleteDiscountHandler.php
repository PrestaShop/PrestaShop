<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\DeleteDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\DeleteDiscountHandlerInterface;

#[AsCommandHandler]
class DeleteDiscountHandler implements DeleteDiscountHandlerInterface
{
    public function __construct(
        private readonly DiscountRepository $discountRepository,
    ) {
    }

    public function handle(DeleteDiscountCommand $command): void
    {
        $this->discountRepository->delete($command->getDiscountId());
    }
}
