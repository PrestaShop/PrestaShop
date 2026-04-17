<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\QueryHandler;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryHandler\GetAttributeGroupForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;

/**
 * Handles query which gets attribute group for editing
 */
#[AsQueryHandler]
final class GetAttributeGroupForEditingHandler implements GetAttributeGroupForEditingHandlerInterface
{
    private AttributeGroupRepository $attributeGroupRepository;

    public function __construct(AttributeGroupRepository $attributeGroupRepository)
    {
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetAttributeGroupForEditing $query): EditableAttributeGroup
    {
        $attributeGroup = $this->attributeGroupRepository->get(
            $query->getAttributeGroupId()
        );

        return new EditableAttributeGroup(
            $query->getAttributeGroupId()->getValue(),
            $attributeGroup->name,
            $attributeGroup->public_name,
            $attributeGroup->group_type,
            $attributeGroup->getAssociatedShops()
        );
    }
}
