<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\Validate;

use Group as CustomerGroup;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\DefaultCustomerGroupsRepository;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\CannotDeleteGroupException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class CustomerGroupValidator extends AbstractObjectModelValidator
{
    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly GroupRepository $groupRepository,
        private readonly DefaultCustomerGroupsRepository $defaultCustomerGroupsRepository,
    ) {
    }

    /**
     * @throws CannotDeleteGroupException
     */
    public function assertIsNotDefaultGroup(GroupId $groupId): void
    {
        if ($this->defaultCustomerGroupsRepository->isUsedAsDefaultGroup($groupId)) {
            throw new CannotDeleteGroupException(
                sprintf('Customer group #%d is used as a default group and cannot be deleted', $groupId->getValue()),
                CannotDeleteGroupException::GROUP_USED_AS_DEFAULT
            );
        }
    }

    /**
     * Like the legacy page, the group must be associated to at least one shop of the constraint.
     *
     * @throws GroupNotFoundException
     * @throws GroupConstraintException
     */
    public function assertCanBeDefaultGroup(GroupId $groupId, ShopConstraint $shopConstraint): void
    {
        $this->groupRepository->assertGroupExists($groupId);

        $shopIds = $this->shopRepository->getAssociatedShopIds($shopConstraint);
        $commonShopIds = array_intersect($shopIds, $this->groupRepository->getAssociatedShopIds($groupId->getValue()));
        if (empty($commonShopIds)) {
            throw new GroupConstraintException(
                sprintf(
                    'Customer group #%d is not associated to any of the shop(s) %s',
                    $groupId->getValue(),
                    implode(', ', $shopIds)
                ),
                GroupConstraintException::GROUP_NOT_ASSOCIATED_TO_SHOP
            );
        }
    }

    /**
     * @param CustomerGroup $customerGroup
     *
     * @return void
     *
     * @throws GroupConstraintException
     */
    public function validate(CustomerGroup $customerGroup): void
    {
        $this->validateThereIsAtLeastOneShop($customerGroup->id_shop_list);
        $this->validateShopsExists($customerGroup->id_shop_list);
        $this->validateGroupNames($customerGroup->name);
        $this->validatePriceDisplayMethod($customerGroup->price_display_method);
    }

    /**
     * @param array $shopIds
     *
     * @return void
     *
     * @throws GroupConstraintException
     */
    private function validateThereIsAtLeastOneShop(array $shopIds): void
    {
        if (empty($shopIds)) {
            throw new GroupConstraintException(
                'Customer group must be associated with at least one shop',
                GroupConstraintException::EMPTY_SHOP_LIST
            );
        }
    }

    /**
     * @param array $shopIds
     *
     * @return void
     */
    private function validateShopsExists(array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $this->shopRepository->assertShopExists(new ShopId($shopId));
        }
    }

    /**
     * @param int $priceDisplayMethod
     *
     * @return void
     *
     * @throws GroupConstraintException
     */
    private function validatePriceDisplayMethod(int $priceDisplayMethod): void
    {
        switch ($priceDisplayMethod) {
            case CustomerGroup::PRICE_DISPLAY_METHOD_TAX_INCL:
            case CustomerGroup::PRICE_DISPLAY_METHOD_TAX_EXCL:
                return;
            default:
                throw new GroupConstraintException(
                    sprintf('Invalid price display method "%s"', $priceDisplayMethod),
                    GroupConstraintException::INVALID_PRICE_DISPLAY_METHOD
                );
        }
    }

    /**
     * @param string[] $names
     *
     * @return void
     *
     * @throws GroupConstraintException
     */
    private function validateGroupNames(array $names): void
    {
        if (empty($names)) {
            throw new GroupConstraintException(
                'Customer group name cannot be empty',
                GroupConstraintException::EMPTY_NAME
            );
        }
        foreach ($names as $name) {
            $this->validateGroupName($name);
        }
    }

    /**
     * @param string $name
     *
     * @return void
     *
     * @throws GroupConstraintException
     */
    private function validateGroupName(string $name): void
    {
        if (strlen($name) > 32) {
            throw new GroupConstraintException(
                sprintf('Customer group name cannot be longer than 32 characters. Got "%s"', $name),
                GroupConstraintException::NAME_TOO_LONG
            );
        }
        if (false === preg_match('/^[^<>{}]*$/u', $name)) {
            throw new GroupConstraintException(
                'Customer group name cannot contain these characters: < > = { }',
                GroupConstraintException::INVALID_NAME
            );
        }
    }
}
