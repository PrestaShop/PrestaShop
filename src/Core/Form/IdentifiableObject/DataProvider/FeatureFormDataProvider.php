<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Query\GetFeatureForEditing;
use PrestaShop\PrestaShop\Core\Domain\Feature\QueryResult\EditableFeature;

/**
 * Provides data for feature forms.
 */
class FeatureFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @var array
     */
    private $defaultShopAssociation;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $isMultistoreFeatureActive
     * @param array $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $isMultistoreFeatureActive,
        array $defaultShopAssociation
    ) {
        $this->queryBus = $queryBus;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var EditableFeature $editableFeature */
        $editableFeature = $this->queryBus->handle(new GetFeatureForEditing((int) $id));

        return [
            'name' => $editableFeature->getName(),
            'shop_association' => $editableFeature->getShopAssociationIds(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
