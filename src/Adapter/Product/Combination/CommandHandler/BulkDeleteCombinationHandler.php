<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationDeleter;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\BulkDeleteCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\BulkDeleteCombinationHandlerInterface;

/**
 * Deletes multiple combinations using legacy object model
 */
#[AsCommandHandler]
class BulkDeleteCombinationHandler implements BulkDeleteCombinationHandlerInterface
{
    /**
     * @var CombinationDeleter
     */
    private $combinationDeleter;

    /**
     * @param CombinationDeleter $combinationDeleter
     */
    public function __construct(
        CombinationDeleter $combinationDeleter
    ) {
        $this->combinationDeleter = $combinationDeleter;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(BulkDeleteCombinationCommand $command): void
    {
        $this->combinationDeleter->bulkDeleteProductCombinations(
            $command->getProductId(),
            $command->getCombinationIds(),
            $command->getShopConstraint()
        );
    }
}
