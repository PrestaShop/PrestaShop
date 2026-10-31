<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\CommandHandler;

use Customer;
use Db;
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
     * Ensures every assigned group is associated with at least one of the shops the customer
     * belongs to. In multistore, the group list is not scoped to the customer's shop (e.g. in
     * "all shops" context every group is offered), so a customer could otherwise be saved with a
     * group that does not exist in its shop.
     *
     * @param int[] $groupIds
     * @param int $customerShopId
     *
     * @throws CustomerGroupShopAssociationException
     */
    protected function assertGroupsAreAssociatedToCustomerShop(array $groupIds, int $customerShopId)
    {
        // Single-shop installs have no per-shop group scoping to enforce.
        if (empty($groupIds) || !Shop::isFeatureActive()) {
            return;
        }

        // A shared customer (Shop::SHARE_CUSTOMER) belongs to every shop of its group, so any of
        // those shops' groups are valid; otherwise only the customer's own shop counts.
        $customerShopIds = array_map('intval', Shop::getSharedShops($customerShopId, Shop::SHARE_CUSTOMER));
        if (empty($customerShopIds)) {
            return;
        }

        // WHY: `group` is not a registered multistore ObjectModel, so getAssociatedShops()/the
        // group repository cannot resolve the group_shop association — read it directly here.
        $rows = Db::getInstance()->executeS('
            SELECT DISTINCT `id_group`
            FROM `' . _DB_PREFIX_ . 'group_shop`
            WHERE `id_shop` IN (' . implode(',', $customerShopIds) . ')
        ');
        $allowedGroupIds = array_map('intval', array_column($rows ?: [], 'id_group'));

        foreach ($groupIds as $groupId) {
            if (!in_array((int) $groupId, $allowedGroupIds, true)) {
                throw new CustomerGroupShopAssociationException(sprintf('Customer group with id "%d" is not associated with the customer\'s shop.', (int) $groupId));
            }
        }
    }
}
