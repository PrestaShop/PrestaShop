<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\AddFeatureHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

/**
 * Handles adding of features using legacy logic.
 */
#[AsCommandHandler]
class AddFeatureHandler extends AbstractObjectModelHandler implements AddFeatureHandlerInterface
{
    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    public function __construct(
        FeatureRepository $featureRepository
    ) {
        $this->featureRepository = $featureRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddFeatureCommand $command): FeatureId
    {
        $feature = $this->featureRepository->create(
            $command->getLocalizedNames(),
            $command->getAssociatedShopIds()
        );

        return new FeatureId((int) $feature->id);
    }
}
