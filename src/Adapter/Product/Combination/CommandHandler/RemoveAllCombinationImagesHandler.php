<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationImagesUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\RemoveAllCombinationImagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\RemoveAllCombinationImagesHandlerInterface;

/**
 * Handles @see RemoveAllCombinationImagesCommand using adapter udpater service
 */
#[AsCommandHandler]
final class RemoveAllCombinationImagesHandler implements RemoveAllCombinationImagesHandlerInterface
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
    public function handle(RemoveAllCombinationImagesCommand $command): void
    {
        $this->combinationImagesUpdater->deleteAllImageAssociations($command->getCombinationId());
    }
}
