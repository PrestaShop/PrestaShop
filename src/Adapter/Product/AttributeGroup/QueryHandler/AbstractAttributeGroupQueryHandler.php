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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\AttributeGroup\QueryHandler;

use AttributeGroup as AttributeGroupObjectModel;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\QueryResult\Attribute;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryResult\AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use ProductAttribute as AttributeObjectModel;

abstract class AbstractAttributeGroupQueryHandler
{
    /**
     * @var AttributeRepository
     */
    protected $attributeRepository;

    public function __construct(
        AttributeRepository $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
     * @return AttributeGroupId[]
     */
    protected function getAttributeGroupIds(ShopConstraint $shopConstraint): array
    {
        $attributeGroups = $this->attributeRepository->getAttributeGroups($shopConstraint);

        return array_map(static function (int $id): AttributeGroupId {
            return new AttributeGroupId($id);
        }, array_keys($attributeGroups));
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
