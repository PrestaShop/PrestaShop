<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\DefaultCombinationUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\CombinationFillerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;

/**
 * Handles the @see UpdateCombinationCommand using legacy object model
 */
#[AsCommandHandler]
class UpdateCombinationHandler implements UpdateCombinationHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationFillerInterface
     */
    private $combinationFiller;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationFillerInterface $combinationFiller,
        DefaultCombinationUpdater $defaultCombinationUpdater
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationFiller = $combinationFiller;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationCommand $command): void
    {
        $combination = $this->combinationRepository->getByShopConstraint($command->getCombinationId(), $command->getShopConstraint());
        $updatableProperties = $this->combinationFiller->fillUpdatableProperties($combination, $command);

        $this->combinationRepository->partialUpdate(
            $combination,
            $updatableProperties,
            $command->getShopConstraint(),
            CannotUpdateCombinationException::FAILED_UPDATE_COMBINATION
        );

        // Only update default if the property is set AND is true
        if (true === $command->isDefault()) {
            $this->defaultCombinationUpdater->setDefaultCombination(
                $command->getCombinationId(),
                $command->getShopConstraint()
            );
        }
    }
}
