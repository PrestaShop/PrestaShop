<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use FeatureValue;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\AddFeatureValueHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Handles adding of feature value using legacy model.
 */
#[AsCommandHandler]
class AddFeatureValueHandler implements AddFeatureValueHandlerInterface
{
    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    public function __construct(
        FeatureRepository $featureRepository,
        FeatureValueRepository $featureValueRepository
    ) {
        $this->featureRepository = $featureRepository;
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(AddFeatureValueCommand $command): FeatureValueId
    {
        $this->featureRepository->assertExists($command->getFeatureId());

        return $this->featureValueRepository->add($this->fillObjectWithCommand($command));
    }

    /**
     * @param AddFeatureValueCommand $command
     *
     * @return FeatureValue
     */
    private function fillObjectWithCommand(AddFeatureValueCommand $command): FeatureValue
    {
        $featureValue = new FeatureValue();
        $featureValue->id_feature = (int) $command->getFeatureId()->getValue();
        $featureValue->value = $command->getLocalizedValues();

        return $featureValue;
    }
}
