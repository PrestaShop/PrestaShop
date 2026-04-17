<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\DeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShopBundle\Entity\ImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles command that delete image type
 */
#[AsCommandHandler]
final class DeleteImageTypeHandler implements DeleteImageTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteImageTypeCommand $command): void
    {
        /** @var ?ImageType $imageType */
        $imageType = $this->imageTypeRepository->find($command->getImageTypeId()->getValue());

        if (!$imageType) {
            throw new ImageTypeNotFoundException(sprintf('Unable to find image type with id "%d" for deletion', $command->getImageTypeId()->getValue()));
        }

        $this->imageTypeRepository->delete($imageType);
    }
}
