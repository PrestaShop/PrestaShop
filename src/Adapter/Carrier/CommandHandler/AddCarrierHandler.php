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
