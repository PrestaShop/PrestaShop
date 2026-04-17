<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationStockAvailableHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

/**
 * Updates combination stock available using legacy object model
 */
#[AsCommandHandler]
class UpdateCombinationStockAvailableHandler implements UpdateCombinationStockAvailableHandlerInterface
{
    /**
     * @var CombinationStockUpdater
     */
    private $combinationStockUpdater;

    /**
     * @param CombinationStockUpdater $combinationStockUpdater
     */
    public function __construct(
        CombinationStockUpdater $combinationStockUpdater
    ) {
        $this->combinationStockUpdater = $combinationStockUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationStockAvailableCommand $command): void
    {
        $stockModification = null;
        if ($command->getDeltaQuantity()) {
            $stockModification = StockModification::buildDeltaQuantity($command->getDeltaQuantity());
        } elseif (null !== $command->getFixedQuantity()) {
            $stockModification = StockModification::buildFixedQuantity($command->getFixedQuantity());
        }

        $properties = new CombinationStockProperties(
            $stockModification,
            $command->getLocation()
        );

        $this->combinationStockUpdater->update($command->getCombinationId(), $properties, $command->getShopConstraint());
    }
}
