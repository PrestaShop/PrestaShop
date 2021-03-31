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

use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetAttributeGroupList;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryHandler\GetAttributeGroupListHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryResult\AttributeGroup;
use PrestaShopBundle\Entity\AttributeGroup as AttributeGroupEntity;
use PrestaShopBundle\Entity\AttributeGroupLang;
use PrestaShopBundle\Entity\Repository\AttributeGroupRepository;

/**
 * Handles the query GetAttributeGroupList using Doctrine repository
 */
class GetAttributeGroupListHandler implements GetAttributeGroupListHandlerInterface
{
    /**
     * @var AttributeGroupRepository
     */
    private $attributeGroupRepository;

    /**
     * @param AttributeGroupRepository $attributeGroupRepository
     */
    public function __construct(AttributeGroupRepository $attributeGroupRepository)
    {
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(GetAttributeGroupList $query): array
    {
        $attributeGroups = [];
        $attributeGroupEntities = $this->attributeGroupRepository->listOrderedAttributeGroups($query->withAttributes());

        /** @var AttributeGroupEntity $attributeGroupEntity */
        foreach ($attributeGroupEntities as $attributeGroupEntity) {
            $localizedNames = [];
            $localizedPublicNames = [];
            /** @var AttributeGroupLang $attributeGroupLang */
            foreach ($attributeGroupEntity->getAttributeGroupLangs() as $attributeGroupLang) {
                $localizedNames[$attributeGroupLang->getLang()->getId()] = $attributeGroupLang->getName();
                $localizedPublicNames[$attributeGroupLang->getLang()->getId()] = $attributeGroupLang->getPublicName();
            }

            $attributeGroups[] = new AttributeGroup(
                $attributeGroupEntity->getId(),
                $localizedNames,
                $localizedPublicNames,
                $attributeGroupEntity->getGroupType(),
                $attributeGroupEntity->getIsColorGroup(),
                $attributeGroupEntity->getPosition()
            );
        }

        return $attributeGroups;
    }
}
