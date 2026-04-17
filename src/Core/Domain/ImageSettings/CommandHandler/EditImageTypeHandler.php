<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShopBundle\Entity\ImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

#[AsCommandHandler]
final class EditImageTypeHandler extends AbstractObjectModelHandler implements EditImageTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws ImageTypeException
     */
    public function handle(EditImageTypeCommand $command): void
    {
        /** @var ImageType $imageType */
        $imageType = $this->imageTypeRepository->find($command->getImageTypeId()->getValue());

        if (null == $imageType->getId()) {
            throw new ImageTypeNotFoundException(sprintf('Image type with id "%d" was not found', $command->getImageTypeId()->getValue()));
        }

        if (null !== $command->getName()) {
            $imageType->setName($command->getName());
        }

        if (null !== $command->getWidth()) {
            $imageType->setWidth($command->getWidth());
        }

        if (null !== $command->getHeight()) {
            $imageType->setHeight($command->getHeight());
        }

        if (null !== $command->isProducts()) {
            $imageType->setProducts($command->isProducts());
        }

        if (null !== $command->isCategories()) {
            $imageType->setCategories($command->isCategories());
        }

        if (null !== $command->isManufacturers()) {
            $imageType->setManufacturers($command->isManufacturers());
        }

        if (null !== $command->isSuppliers()) {
            $imageType->setSuppliers($command->isSuppliers());
        }

        if (null !== $command->isStores()) {
            $imageType->setStores($command->isStores());
        }

        $this->imageTypeRepository->save($imageType);
    }
}
