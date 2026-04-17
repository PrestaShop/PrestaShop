<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\ImageTypeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

/**
 * Handles command that gets image type for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetImageTypeForEditingHandler implements GetImageTypeForEditingHandlerInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetImageTypeForEditing $query): EditableImageType
    {
        $imageType = $this->imageTypeRepository->find($query->getImageTypeId()->getValue());

        if (null === $imageType) {
            throw new ImageTypeNotFoundException(sprintf('Image type with id "%d" not found', $query->getImageTypeId()->getValue()));
        }

        return new EditableImageType(
            $query->getImageTypeId(),
            (string) $imageType->getName(),
            (int) $imageType->getWidth(),
            (int) $imageType->getHeight(),
            (bool) $imageType->isProducts(),
            (bool) $imageType->isCategories(),
            (bool) $imageType->isManufacturers(),
            (bool) $imageType->isSuppliers(),
            (bool) $imageType->isStores()
        );
    }
}
