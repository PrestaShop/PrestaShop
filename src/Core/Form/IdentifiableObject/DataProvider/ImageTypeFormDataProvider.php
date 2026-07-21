<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Query\GetImageTypeForEditing;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\QueryResult\EditableImageType;

/**
 * Provides data for image type add/edit form.
 */
final class ImageTypeFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id): array
    {
        /** @var EditableImageType $result */
        $result = $this->queryBus->handle(new GetImageTypeForEditing($id));

        return [
            'id' => $id,
            'name' => $result->getName(),
            'width' => $result->getWidth(),
            'height' => $result->getHeight(),
            'products' => $result->isProducts(),
            'categories' => $result->isCategories(),
            'manufacturers' => $result->isManufacturers(),
            'suppliers' => $result->isSuppliers(),
            'stores' => $result->isStores(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [
            'name' => '',
            'width' => null,
            'height' => null,
            'products' => false,
            'categories' => false,
            'manufacturers' => false,
            'suppliers' => false,
            'stores' => false,
        ];
    }
}
