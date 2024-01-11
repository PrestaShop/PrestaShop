<?php
/*
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

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\Repository;

use Group as CustomerGroup;
use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotAddGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotUpdateGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Provides methods to access Group data storage
 */
class GroupRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @param GroupId $customerGroupId
     *
     * @throws CoreException
     * @throws GroupNotFoundException
     *
     * @return CustomerGroup
     */
    public function get(GroupId $customerGroupId): CustomerGroup
    {
        /** @var CustomerGroup $customerGroup */
        $customerGroup = $this->getObjectModel(
            $customerGroupId->getValue(),
            CustomerGroup::class,
            GroupNotFoundException::class
        );

        return $customerGroup;
    }

    /**
     * @param GroupId $groupId
     *
     * @throws GroupNotFoundException
     */
    public function assertGroupExists(GroupId $groupId): void
    {
        $this->assertObjectModelExists(
            $groupId->getValue(),
            'group',
            GroupNotFoundException::class
        );
    }

    /**
     * @param CustomerGroup $customerGroup
     *
     * @throws CoreException
     *
     * @return GroupId
     */
    public function create(CustomerGroup $customerGroup): GroupId
    {
        $groupId = $this->addObjectModelToShops(
            $customerGroup,
            array_map(fn (int $shopId) => new ShopId($shopId), $customerGroup->id_shop_list),
            CannotAddGroupException::class
        );

        return new GroupId($groupId);
    }

    /**
     * @param int $customerGroupId
     *
     * @return int[]
     */
    public function getAssociatedShopIds(int $customerGroupId): array
    {
        return $this->getObjectModelAssociatedShopIds($customerGroupId, CustomerGroup::class);
    }

    /**
     * @param CustomerGroup $customerGroup
     */
    public function partialUpdate(CustomerGroup $customerGroup, array $propertiesToUpdate): void
    {
        $this->partiallyUpdateObjectModel($customerGroup, $propertiesToUpdate, CannotUpdateGroupException::class);
        $this->updateObjectModelShopAssociations((int) $customerGroup->id, CustomerGroup::class, $customerGroup->id_shop_list);
    }

    public function delete(GroupId $customerGroupId): void
    {
        $customerGroup = $this->get($customerGroupId);
        $shopIds = $this->getAssociatedShopIds($customerGroupId->getValue());
        $this->deleteObjectModelFromShops($customerGroup, array_map(fn (int $shopId) => new ShopId($shopId), $shopIds), CannotDeleteGroupException::class);
    }
}
