<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationImagesUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\SetCombinationImagesHandlerInterface;

/**
 * Handles @see SetCombinationImagesCommand using adapter udpater service
 */
#[AsCommandHandler]
final class SetCombinationImagesHandler implements SetCombinationImagesHandlerInterface
{
    /**
     * @var CombinationImagesUpdater
     */
    private $combinationImagesUpdater;

    /**
     * @param CombinationImagesUpdater $combinationImagesUpdater
     */
    public function __construct(CombinationImagesUpdater $combinationImagesUpdater)
    {
        $this->combinationImagesUpdater = $combinationImagesUpdater;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SetCombinationImagesCommand $command): void
    {
        $this->combinationImagesUpdater->associateImages($command->getCombinationId(), $command->getImageIds());
    }
}
