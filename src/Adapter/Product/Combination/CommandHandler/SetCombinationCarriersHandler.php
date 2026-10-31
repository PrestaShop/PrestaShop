<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationCarriersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\SetCombinationCarriersHandlerInterface;

#[AsCommandHandler]
final class SetCombinationCarriersHandler implements SetCombinationCarriersHandlerInterface
{
    public function __construct(
        private readonly CombinationRepository $combinationRepository
    ) {
    }

    public function handle(SetCombinationCarriersCommand $command): void
    {
        $this->combinationRepository->setCarrierReferences(
            $command->getCombinationId(),
            $command->getCarrierReferenceIds(),
            $command->getShopConstraint()
        );
    }
}
