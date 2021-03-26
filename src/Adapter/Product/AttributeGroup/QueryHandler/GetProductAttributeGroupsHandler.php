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

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Attribute\QueryResult\Attribute;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryHandler\GetProductAttributeGroupsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryResult\AttributeGroup;

/**
 * Handles the query GetProductAttributeGroups using adapter repository
 */
class GetProductAttributeGroupsHandler implements GetProductAttributeGroupsHandlerInterface
{
    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    public function __construct(
        AttributeRepository $attributeRepository
    ) {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetProductAttributeGroups $query): array
    {
        $attributeInfos = $this->attributeRepository->getAttributesInfoByProductId($query->getProductId(), $query->getLanguageId());

        return $this->formatGroups($attributeInfos);
    }

    /**
     * @param array $attributeInfos
     *
     * @return AttributeGroup[]
     */
    private function formatGroups(array $attributeInfos): array
    {
        $groupsData = [];
        foreach ($attributeInfos as $attributeInfo) {
            $groupId = (int) $attributeInfo['id_attribute_group'];
            if (!isset($groupsData[$groupId])) {
                $groupsData[$groupId] = [
                    'name' => $attributeInfo['attribute_group_name'],
                    'public_name' => $attributeInfo['attribute_group_public_name'],
                    'attributes' => [],
                ];
            }

            $groupsData[$groupId]['attributes'][] = new Attribute(
                (int) $attributeInfo['id_attribute'],
                (int) $attributeInfo['position'],
                $attributeInfo['color'],
                $attributeInfo['attribute_name']
            );
        }

        return array_map(function (array $groupData, int $groupId) {
            return new AttributeGroup(
                $groupId,
                $groupData['name'],
                $groupData['public_name'],
                $groupData['attributes']
            );
        }, $groupsData, array_keys($groupsData));
    }
}
