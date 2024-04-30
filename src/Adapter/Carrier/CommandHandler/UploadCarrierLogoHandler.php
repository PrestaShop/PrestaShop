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
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Command\UploadCarrierLogoCommand;
use PrestaShop\PrestaShop\Core\Domain\Carrier\CommandHandler\UploadCarrierLogoHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedLogoImageExtensionException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageSizeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Upload carrier logo
 */
#[AsCommandHandler]
class UploadCarrierLogoHandler extends AbstractCarrierHandler implements UploadCarrierLogoHandlerInterface
{
    protected const AVAILABLE_IMAGE_EXTENSION = ['jpg', 'jpeg'];

    protected const MAX_IMAGE_SIZE_IN_BYTES = 8 * 1000000;

    public function __construct(
        private readonly CarrierRepository $carrierRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UploadCarrierLogoCommand $command): CarrierId
    {
        $this->assertIsValidLogoImageExtension($command->getUploadedFile());
        $this->assertIsValidLogoImageSize($command->getUploadedFile());

        $carrier = $this->getCarrier($command->getCarrierId());

        $newCarrierId = $this->carrierRepository->addNewVersion($command->getCarrierId(), $carrier);
        $this->carrierRepository->uploadLogo($newCarrierId, $command->getUploadedFile());

        return $command->getCarrierId();
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws NotSupportedLogoImageExtensionException
     */
    private function assertIsValidLogoImageExtension(UploadedFile $uploadedFile): void
    {
        $extension = $uploadedFile->getClientOriginalExtension();
        if (!in_array($extension, self::AVAILABLE_IMAGE_EXTENSION, true)) {
            throw new NotSupportedLogoImageExtensionException(sprintf(
                'Not supported "%s" image logo extension. Supported extensions are "%s"',
                $extension,
                implode(',', self::AVAILABLE_IMAGE_EXTENSION
                )));
        }
    }

    private function assertIsValidLogoImageSize(UploadedFile $uploadedFile): void
    {
        $size = $uploadedFile->getSize();
        if ($size > self::MAX_IMAGE_SIZE_IN_BYTES) {
            throw UploadedImageSizeException::build(self::MAX_IMAGE_SIZE_IN_BYTES);
        }
    }
}
