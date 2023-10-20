<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
                        $attribute->name
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
