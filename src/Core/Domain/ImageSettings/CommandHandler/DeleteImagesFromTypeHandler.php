<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
