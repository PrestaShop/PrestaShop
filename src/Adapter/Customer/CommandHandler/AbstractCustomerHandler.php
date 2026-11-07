<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use PrestaShop\PrestaShop\Adapter\Customer\Group\Repository\GroupRepository;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerGroupShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\MissingCustomerRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use Shop;

/**
 * Provides reusable methods for customer command handlers.
 *
 * @internal
 */
abstract class AbstractCustomerHandler
{
    /**
     * @param CustomerId $customerId
     * @param Customer $customer
     *
     * @throws CustomerNotFoundException
     */
    protected function assertCustomerWasFound(CustomerId $customerId, Customer $customer)
    {
        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found.', $customerId->getValue()));
        }
    }

    /**
     * @param Customer $customer
     *
     * @throws MissingCustomerRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(Customer $customer)
    {
        $errors = $customer->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingCustomerRequiredFieldsException($missingFields, sprintf('One or more required fields for customer are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }

    /**
     * @param int[] $groupIds
     *
     * @throws CustomerGroupShopAssociationException
     */
    protected function assertGroupsAreAssociatedToCustomerShop(GroupRepository $groupRepository, array $groupIds, int $customerShopId): void
    {
        if (empty($groupIds) || !Shop::isFeatureActive()) {
            return;
        }

        // A customer shared by its shop group belongs to every shop of that group
        $customerShopIds = array_map('intval', Shop::getSharedShops($customerShopId, Shop::SHARE_CUSTOMER));

        foreach ($groupIds as $groupId) {
            if (empty(array_intersect($groupRepository->getAssociatedShopIds((int) $groupId), $customerShopIds))) {
                throw new CustomerGroupShopAssociationException((int) $groupId, sprintf('Customer group with id "%d" is not associated with the customer\'s shop.', (int) $groupId));
            }
        }
    }
}
