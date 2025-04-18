<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Validate\CarrierValidator;
use PrestaShop\PrestaShop\Adapter\File\Uploader\CarrierLogoFileUploader;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\EditCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\EditCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotUpdateCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Edit Carrier
 */
#[AsCommandHandler]
class EditCarrierHandler implements EditCarrierHandlerInterface
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
    public function handle(EditCarrierCommand $command): CarrierId
    {
        $newCarrier = $this->carrierRepository->getEditableOrNewVersion($command->getCarrierId());
        $newCarrierId = new CarrierId($newCarrier->id);

        // General information
        if (null !== $command->getName()) {
            $newCarrier->name = $command->getName();
        }
        if (null !== $command->getGrade()) {
            $newCarrier->grade = $command->getGrade();
        }
        if (null !== $command->getTrackingUrl()) {
            $newCarrier->url = $command->getTrackingUrl();
        }
        if (null !== $command->getPosition()) {
            $newCarrier->position = $command->getPosition();
        }
        if (null !== $command->getActive()) {
            $newCarrier->active = $command->getActive();
        }
        if (null !== $command->getLocalizedDelay()) {
            $newCarrier->delay = $command->getLocalizedDelay();
        }
        if (null !== $command->getMaxWidth()) {
            $newCarrier->max_width = $command->getMaxWidth();
        }
        if (null !== $command->getMaxHeight()) {
            $newCarrier->max_height = $command->getMaxHeight();
        }
        if (null !== $command->getMaxDepth()) {
            $newCarrier->max_depth = $command->getMaxDepth();
        }
        if (null !== $command->getMaxWeight()) {
            $newCarrier->max_weight = $command->getMaxWeight();
        }

        // Shipping information
        if (null !== $command->hasAdditionalHandlingFee()) {
            $newCarrier->shipping_handling = $command->hasAdditionalHandlingFee();
        } else {
            // If carrier is free, we should not have shipping handling
            if ($command->isFree()) {
                $newCarrier->shipping_handling = false;
            }
        }

        if (null !== $command->isFree()) {
            $newCarrier->is_free = $command->isFree();
        } else {
            // If carrier has additional handling fee, we should not have free shipping enabled
            if ($command->hasAdditionalHandlingFee()) {
                $newCarrier->is_free = false;
            }
        }

        if ($command->getShippingMethod()) {
            $newCarrier->shipping_method = $command->getShippingMethod()->getValue();
        }

        if (null !== $command->getRangeBehavior()) {
            $newCarrier->range_behavior = (bool) $command->getRangeBehavior()->getValue();
        }

        $this->carrierValidator->validate($newCarrier);
        if ($command->getAssociatedGroupIds()) {
            $this->carrierValidator->validateGroupsExist($command->getAssociatedGroupIds());
        }
        if ($command->getLogoPathName() !== null && $command->getLogoPathName() !== '') {
            $this->carrierValidator->validateLogoUpload($command->getLogoPathName());
        }
        if (!empty($command->getAssociatedShopIds())) {
            foreach ($command->getAssociatedShopIds() as $shopId) {
                $this->shopRepository->assertShopExists($shopId);
            }
        }

        $this->carrierRepository->update(
            $newCarrier,
            CannotUpdateCarrierException::FAILED_UPDATE_CARRIER
        );

        if ($command->getAssociatedGroupIds()) {
            $newCarrier->setGroups($command->getAssociatedGroupIds());
        }

        $this->carrierRepository->update(
            $newCarrier,
            CannotUpdateCarrierException::FAILED_UPDATE_CARRIER
        );

        if (null !== $command->getAssociatedShopIds()) {
            $this->carrierRepository->updateAssociatedShops($newCarrierId, array_map(fn (ShopId $shopId) => $shopId->getValue(), $command->getAssociatedShopIds()));
        }

        if ($command->getLogoPathName() !== null) {
            $this->carrierLogoFileUploader->deleteOldFile($newCarrier->id);

            if ($command->getLogoPathName() !== '') {
                $this->carrierLogoFileUploader->upload($command->getLogoPathName(), $newCarrier->id);
            }
        }

        if (null !== $command->getZones()) {
            $this->carrierRepository->updateAssociatedZones($newCarrierId, $command->getZones());
        }

        return $newCarrierId;
    }
}
