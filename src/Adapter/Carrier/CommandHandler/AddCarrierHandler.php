<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use Carrier;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Validate\CarrierValidator;
use PrestaShop\PrestaShop\Adapter\File\Uploader\CarrierLogoFileUploader;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\AddCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\AddCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

/**
 * Add new Carrier
 */
#[AsCommandHandler]
class AddCarrierHandler implements AddCarrierHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly CarrierLogoFileUploader $carrierLogoFileUploader,
        private readonly CarrierValidator $carrierValidator,
        private readonly ShopRepository $shopRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddCarrierCommand $command): CarrierId
    {
        $carrier = new Carrier();
        // General information
        $carrier->name = $command->getName();
        $carrier->grade = $command->getGrade();
        $carrier->url = $command->getTrackingUrl();
        $carrier->active = $command->getActive();
        $carrier->delay = $command->getLocalizedDelay();
        $carrier->max_width = $command->getMaxWidth();
        $carrier->max_height = $command->getMaxHeight();
        $carrier->max_weight = $command->getMaxWeight();
        $carrier->max_depth = $command->getMaxDepth();

        if (null !== $command->getPosition()) {
            $carrier->position = $command->getPosition();
        } else {
            $lastPosition = $this->carrierRepository->getLastPosition();
            $carrier->position = $lastPosition !== null ? $lastPosition + 1 : 0;
        }

        // Shipping information
        $carrier->shipping_handling = $command->hasAdditionalHandlingFee();
        $carrier->is_free = $command->isFree();
        $carrier->shipping_method = $command->getShippingMethod()->getValue();
        $carrier->range_behavior = (bool) $command
            ->getRangeBehavior()
            ->getValue();

        $this->carrierValidator->validate($carrier);
        $this->carrierValidator->validateGroupsExist(
            $command->getAssociatedGroupIds()
        );

        foreach ($command->getAssociatedShopIds() as $shopId) {
            $this->shopRepository->assertShopExists($shopId);
        }

        $carrierId = $this->carrierRepository->add($carrier, $command->getAssociatedShopIds());
        $carrier->setGroups($command->getAssociatedGroupIds());

        if ($command->getLogoPathName() !== null) {
            $this->carrierValidator->validateLogoUpload(
                $command->getLogoPathName()
            );
            $this->carrierLogoFileUploader->upload(
                $command->getLogoPathName(),
                $carrierId->getValue()
            );
        }

        $this->carrierRepository->updateAssociatedZones($carrierId, $command->getZones());

        return $carrierId;
    }
}
