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

namespace PrestaShop\PrestaShop\Adapter\AttributeGroup;

use AttributeGroup;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * This class will provide data from DB / ORM about Attribute groups.
 */
class AttributeGroupDataProvider
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
     * @param ShopId $shopId
     *
     * @return array
     *
     * @throws InvalidShopConstraintException
     * @throws ShopException
     */
    public function getAttributeGroupChoices(ShopId $shopId, LanguageId $languageId): array
    {
        $shopConstraint = ShopConstraint::shop($shopId->getValue());

        $groups = $this->attributeGroupRepository->getAttributeGroups($shopConstraint);
        $return = [];

        /** @var AttributeGroup $group */
        foreach ($groups as $group) {
            $return[sprintf('%s (%d)', $group->name[$languageId->getValue()], $group->id)] = $group->id;
        }

        return $return;
    }
}
