<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationDeleter;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\DeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\DeleteCombinationHandlerInterface;

/**
 * Handles @see DeleteCombinationCommand using adapter udpater service
 */
#[AsCommandHandler]
class DeleteCombinationHandler implements DeleteCombinationHandlerInterface
{
    /**
     * @var CombinationDeleter
     */
    private $combinationDeleter;

    /**
     * @param CombinationDeleter $combinationDeleter
     */
    public function __construct(CombinationDeleter $combinationDeleter)
    {
        $this->combinationDeleter = $combinationDeleter;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(DeleteCombinationCommand $command): void
    {
        $this->combinationDeleter->deleteCombination($command->getCombinationId(), $command->getShopConstraint());
    }
}
