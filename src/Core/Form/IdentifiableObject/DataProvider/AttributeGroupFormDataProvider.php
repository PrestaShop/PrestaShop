<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;

class AttributeGroupFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private CommandBusInterface $queryBus,
        private ShopContext $shopContext,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        /** @var EditableAttributeGroup $editableAttributeGroup */
        $editableAttributeGroup = $this->queryBus->handle(new GetAttributeGroupForEditing((int) $id));

        return [
            'name' => $editableAttributeGroup->getName(),
            'public_name' => $editableAttributeGroup->getPublicName(),
            'group_type' => $editableAttributeGroup->getType(),
            'shop_association' => $editableAttributeGroup->getAssociatedShopIds(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'group_type' => AttributeGroupType::ATTRIBUTE_GROUP_TYPE_SELECT,
            'shop_association' => $this->shopContext->getAssociatedShopIds(),
        ];
    }
}
