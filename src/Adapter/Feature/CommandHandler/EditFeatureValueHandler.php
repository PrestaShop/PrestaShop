<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\EditFeatureValueHandlerInterface;

/**
 * Handles edition of feature value using legacy model.
 */
#[AsCommandHandler]
class EditFeatureValueHandler implements EditFeatureValueHandlerInterface
{
    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    /**
     * @param FeatureValueRepository $featureValueRepository
     */
    public function __construct(FeatureValueRepository $featureValueRepository)
    {
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(EditFeatureValueCommand $command): void
    {
        $featureValue = $this->featureValueRepository->get($command->getFeatureValueId());

        if (null !== $command->getLocalizedValues()) {
            $featureValue->value = $command->getLocalizedValues();
        }
        if (null !== $command->getFeatureId()) {
            $featureValue->id_feature = $command->getFeatureId()->getValue();
        }

        $this->featureValueRepository->update($featureValue);
    }
}
