<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Discount\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Discount\Update\DiscountDuplicator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\DuplicateDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\CommandHandler\DuplicateDiscountHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;

#[AsCommandHandler]
class DuplicateDiscountHandler implements DuplicateDiscountHandlerInterface
{
    public function __construct(
        private readonly DiscountDuplicator $discountDuplicator,
    ) {
    }

    public function handle(DuplicateDiscountCommand $command): DiscountId
    {
        return $this->discountDuplicator->duplicate($command->getDiscountId());
    }
}
