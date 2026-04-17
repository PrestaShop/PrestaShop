<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup\QueryHandler;

use AttributeGroup as AttributeGroupObjectModel;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\Attribute;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\AttributeGroup;
use ProductAttribute as AttributeObjectModel;

abstract class AbstractAttributeGroupQueryHandler
{
    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * @var AttributeGroupRepository
     */
    protected $attributeGroupRepository;

    public function __construct(
        AttributeRepository $attributeRepository,
        AttributeGroupRepository $attributeGroupRepository
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * @param array<int, AttributeGroupObjectModel> $attributeGroups
     * @param array<int, array<int, AttributeObjectModel>> $attributes
     *
     * @return AttributeGroup[]
     */
    protected function formatAttributeGroupsList(
        array $attributeGroups,
        array $attributes
    ): array {
        $attributeGroupsResult = [];

        foreach ($attributeGroups as $attributeGroupId => $attributeGroup) {
            if (!isset($attributes[$attributeGroupId])) {
                $attributesResult = [];
            } else {
                $attributesResult = [];
                foreach ($attributes[$attributeGroupId] as $attributeId => $attribute) {
                    $attributesResult[] = new Attribute(
                        $attributeId,
                        $attribute->position,
                        $attribute->color,
                        $attribute->name,
                        file_exists(_PS_COL_IMG_DIR_ . $attributeId . '.jpg') ? _THEME_COL_DIR_ . $attributeId . '.jpg' : null
                    );
                }
            }

            $attributeGroupsResult[] = new AttributeGroup(
                $attributeGroupId,
                $attributeGroup->name,
                $attributeGroup->public_name,
                $attributeGroup->group_type,
                $attributeGroup->is_color_group,
                $attributeGroup->position,
                $attributesResult
            );
        }

        return $attributeGroupsResult;
    }
}
