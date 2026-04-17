<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Create\CombinationCreator;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductSupplierUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\GenerateProductCombinationsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\GenerateProductCombinationsHandlerInterface;

/**
 * Handles @see GenerateProductCombinationsCommand using legacy object model
 */
#[AsCommandHandler]
final class GenerateProductCombinationsHandler implements GenerateProductCombinationsHandlerInterface
{
    /**
     * @var CombinationCreator
     */
    private $combinationCreator;

    /**
     * @var ProductSupplierUpdater
     */
    private $productSupplierUpdater;

    /**
     * @param CombinationCreator $combinationCreator
     * @param ProductSupplierUpdater $productSupplierUpdater
     */
    public function __construct(
        CombinationCreator $combinationCreator,
        ProductSupplierUpdater $productSupplierUpdater
    ) {
        $this->combinationCreator = $combinationCreator;
        $this->productSupplierUpdater = $productSupplierUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GenerateProductCombinationsCommand $command): array
    {
        $combinationIds = $this->combinationCreator->createCombinations(
            $command->getProductId(),
            $command->getGroupedAttributeIdsList(),
            $command->getShopConstraint()
        );

        $this->productSupplierUpdater->updateMissingProductSuppliers($command->getProductId());

        return $combinationIds;
    }
}
