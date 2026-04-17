<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Entity\Repository\ImageTypeRepository;

class ImageTypeChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly ImageTypeRepository $imageTypeRepository
    ) {
    }

    public function getChoices(): array
    {
        $imageTypes = [];
        $dbImageTypes = $this->imageTypeRepository->findAll();

        foreach ($dbImageTypes as $dbImageType) {
            $imageTypes[$dbImageType->getName()] = $dbImageType->getId();
        }

        return $imageTypes;
    }

    public function buildChoicesByTypes(): array
    {
        $imageTypes = ['products' => [], 'categories' => [], 'manufacturers' => [], 'suppliers' => [], 'stores' => []];
        $dbImageTypes = $this->imageTypeRepository->findAll();

        foreach ($dbImageTypes as $dbImageType) {
            if ($dbImageType->isProducts()) {
                $imageTypes['products'][] = $dbImageType->getId();
            }
            if ($dbImageType->isCategories()) {
                $imageTypes['categories'][] = $dbImageType->getId();
            }
            if ($dbImageType->isManufacturers()) {
                $imageTypes['manufacturers'][] = $dbImageType->getId();
            }
            if ($dbImageType->isSuppliers()) {
                $imageTypes['suppliers'][] = $dbImageType->getId();
            }
            if ($dbImageType->isStores()) {
                $imageTypes['stores'][] = $dbImageType->getId();
            }
        }

        return $imageTypes;
    }
}
