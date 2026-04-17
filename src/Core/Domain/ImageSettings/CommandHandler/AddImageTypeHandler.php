<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\AddImageTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ValueObject\ImageTypeId;
use PrestaShopBundle\Entity\ImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles @see AddImageTypeCommand
 */
#[AsCommandHandler]
final class AddImageTypeHandler implements AddImageTypeHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddImageTypeCommand $command): ImageTypeId
    {
        $imageType = new ImageType();
        $imageType->setName($command->getName());
        $imageType->setWidth($command->getWidth());
        $imageType->setHeight($command->getHeight());
        $imageType->setProducts($command->isProducts());
        $imageType->setCategories($command->isCategories());
        $imageType->setManufacturers($command->isManufacturers());
        $imageType->setSuppliers($command->isSuppliers());
        $imageType->setStores($command->isStores());

        $this->imageTypeRepository->save($imageType);

        return new ImageTypeId((int) $imageType->getId());
    }
}
