<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\FeatureValue\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\FeatureValue\Update\CombinationFeatureValueUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\SetCombinationFeatureValuesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\CommandHandler\SetCombinationFeatureValuesHandlerInterface;

/**
 * Handles @see SetCombinationFeatureValuesCommand using legacy object model
 */
#[AsCommandHandler]
class SetCombinationFeatureValuesHandler implements SetCombinationFeatureValuesHandlerInterface
{
    /**
     * @var CombinationFeatureValueUpdater
     */
    private $combinationFeatureValueUpdater;

    public function __construct(CombinationFeatureValueUpdater $combinationFeatureValueUpdater)
    {
        $this->combinationFeatureValueUpdater = $combinationFeatureValueUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetCombinationFeatureValuesCommand $command): array
    {
        return $this->combinationFeatureValueUpdater->setFeatureValues($command->getCombinationId(), $command->getFeatureValues());
    }
}
