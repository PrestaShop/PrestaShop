<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\AbstractBulkCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\BulkDeleteImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\BulkImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles command that bulk delete image types
 */
#[AsCommandHandler]
class BulkDeleteImageTypeHandler extends AbstractBulkCommandHandler implements BulkDeleteImageTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteImageTypeCommand $command): void
    {
        $this->handleBulkAction($command->getImageTypeIds(), ImageTypeException::class);
    }

    protected function buildBulkException(array $caughtExceptions): BulkImageTypeException
    {
        return new BulkImageTypeException(
            $caughtExceptions,
            'Errors occurred during image type bulk delete action',
        );
    }

    /**
     * @param ImageTypeId $id
     * @param mixed $command
     *
     * @return void
     *
     * @throws ImageTypeNotFoundException
     */
    protected function handleSingleAction(mixed $id, mixed $command): void
    {
        $imageType = $this->imageTypeRepository->find($id->getValue());

        if (null === $imageType) {
            throw new ImageTypeNotFoundException(sprintf('Unable to find image type with id "%d" for deletion', $id->getValue()));
        }

        $this->imageTypeRepository->delete($imageType);
    }

    protected function supports($id): bool
    {
        return $id instanceof ImageTypeId;
    }
}
