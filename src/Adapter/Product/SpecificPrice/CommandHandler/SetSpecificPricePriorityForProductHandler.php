<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update\SpecificPricePriorityUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command\SetSpecificPricePriorityForProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\CommandHandler\SetSpecificPricePriorityForProductHandlerInterface;

/**
 * Handles @see SetSpecificPricePriorityForProductCommand using legacy obj model
 */
#[AsCommandHandler]
class SetSpecificPricePriorityForProductHandler implements SetSpecificPricePriorityForProductHandlerInterface
{
    /**
     * @var SpecificPricePriorityUpdater
     */
    private $specificPricePriorityUpdater;

    /**
     * @param SpecificPricePriorityUpdater $specificPricePriorityUpdater
     */
    public function __construct(
        SpecificPricePriorityUpdater $specificPricePriorityUpdater
    ) {
        $this->specificPricePriorityUpdater = $specificPricePriorityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SetSpecificPricePriorityForProductCommand $command): void
    {
        $this->specificPricePriorityUpdater->setPrioritiesForProduct(
            $command->getProductId(),
            $command->getPriorityList()
        );
    }
}
