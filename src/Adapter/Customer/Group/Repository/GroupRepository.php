<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\Repository;

use Group as CustomerGroup;
use PrestaShop\PrestaShop\Adapter\CoreException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotAddGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotUpdateGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\CustomerGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Provides methods to access Group data storage
 */
class GroupRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @throws CoreException
     * @throws GroupNotFoundException
     */
    public function get(CustomerGroupId $customerGroupId): CustomerGroup
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
     * @throws GroupNotFoundException
     */
    public function assertGroupExists(CustomerGroupId $groupId): void
    {
        $this->assertObjectModelExists(
            $groupId->getValue(),
            'group',
            GroupNotFoundException::class
        );
    }

    /**
     * @throws CoreException
     */
    public function create(CustomerGroup $customerGroup): CustomerGroupId
    {
        $groupId = $this->addObjectModelToShops(
            $customerGroup,
            array_map(fn (int $shopId) => new ShopId($shopId), $customerGroup->id_shop_list),
            CannotAddGroupException::class
        );

        return new CustomerGroupId($groupId);
    }

    /**
     * @return int[]
     */
    public function getAssociatedShopIds(int $customerGroupId): array
    {
        return $this->getObjectModelAssociatedShopIds($customerGroupId, CustomerGroup::class);
    }

    public function partialUpdate(CustomerGroup $customerGroup, array $propertiesToUpdate): void
    {
        $this->partiallyUpdateObjectModel($customerGroup, $propertiesToUpdate, CannotUpdateGroupException::class);
        if (in_array('id_shop_list', $propertiesToUpdate, true)) {
            $this->updateObjectModelShopAssociations((int) $customerGroup->id, CustomerGroup::class, $customerGroup->id_shop_list);
        }
    }

    public function delete(CustomerGroupId $customerGroupId): void
    {
        $customerGroup = $this->get($customerGroupId);
        $shopIds = $this->getAssociatedShopIds($customerGroupId->getValue());
        $this->deleteObjectModelFromShops($customerGroup, array_map(fn (int $shopId) => new ShopId($shopId), $shopIds), CannotDeleteGroupException::class);
    }
}
