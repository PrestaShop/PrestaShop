<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\EditFeatureHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles feature editing.
 */
#[AsCommandHandler]
class EditFeatureHandler extends AbstractObjectModelHandler implements EditFeatureHandlerInterface
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
    public function handle(EditFeatureCommand $command): void
    {
        $feature = $this->featureRepository->get($command->getFeatureId());

        if (null !== $command->getLocalizedNames()) {
            $feature->name = $command->getLocalizedNames();
        }

        $this->featureRepository->update($feature);

        // ObjectModel::update doesn't seem to remove unassociated shops, so we must always update them manually afterwards
        if (null !== $command->getAssociatedShopIds()) {
            $this->associateWithShops($feature, array_map(static function (ShopId $shopId) {
                return $shopId->getValue();
            }, $command->getAssociatedShopIds()));
        }
    }
}
