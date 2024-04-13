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

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Adapter\ImageThumbnailsRegenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImagesFromTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShopBundle\Entity\ImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles command that delete images from defined image type
 */
#[AsCommandHandler]
final class DeleteImagesFromTypeHandler implements DeleteImagesFromTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository,
        private readonly ImageThumbnailsRegenerator $imageThumbnailsRegenerator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteImagesFromTypeCommand $command): void
    {
        // Get image type by id
        /** @var ?ImageType $imageType */
        $imageType = $this->imageTypeRepository->find($command->getImageTypeId()->getValue());

        if (!$imageType) {
            throw new ImageTypeNotFoundException(sprintf('Unable to find image type with id "%d" for deletion', $command->getImageTypeId()->getValue()));
        }

        // Delete all images linked to image type
        $this->imageThumbnailsRegenerator->deleteImagesFromType($imageType->getName(), _PS_IMG_DIR_ . '{c,m,su,p,st}/');
    }
}
