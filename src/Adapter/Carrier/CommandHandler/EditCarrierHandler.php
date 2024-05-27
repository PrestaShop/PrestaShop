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
use PrestaShop\PrestaShop\Adapter\Carrier\AbstractCarrierHandler;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\Validate\CarrierValidator;
use PrestaShop\PrestaShop\Adapter\File\Uploader\CarrierLogoFileUploader;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\EditCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\EditCarrierHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;

/**
 * Edit Carrier
 */
#[AsCommandHandler]
class EditCarrierHandler extends AbstractCarrierHandler implements EditCarrierHandlerInterface
{
    public function __construct(
        private readonly CarrierRepository $carrierRepository,
        private readonly CarrierLogoFileUploader $carrierLogoFileUploader,
        private readonly CarrierValidator $carrierValidator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditCarrierCommand $command): CarrierId
    {
        $carrier = $this->carrierRepository->get($command->getCarrierId());
        if ($command->getName()) {
            $carrier->name = $command->getName();
        }
        if ($command->getGrade()) {
            $carrier->grade = $command->getGrade();
        }
        if ($command->getTrackingUrl()) {
            $carrier->url = $command->getTrackingUrl();
        }
        if ($command->getPosition()) {
            $carrier->position = $command->getPosition();
        }
        if ($command->getActive()) {
            $carrier->active = $command->getActive();
        }
        if ($command->getLocalizedDelay()) {
            $carrier->delay = $command->getLocalizedDelay();
        }

        $this->carrierValidator->validate($carrier);

        $carrierId = $this->carrierRepository->updateInNewVersion($command->getCarrierId(), $carrier);

        if ($command->getLogoPathName() !== null) {
            $this->carrierLogoFileUploader->deleteOldFile($carrierId->getValue());

            if ($command->getLogoPathName() !== '') {
                $this->carrierValidator->validateLogoUpload($command->getLogoPathName());
                $this->carrierLogoFileUploader->upload($command->getLogoPathName(), $carrierId->getValue());
            }
        }

        return $carrierId;
    }
}
