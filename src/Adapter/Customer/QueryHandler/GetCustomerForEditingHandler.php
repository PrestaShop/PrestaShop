<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Handles command that gets customer for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetCustomerForEditingHandler implements GetCustomerForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetCustomerForEditing $query)
    {
        $customerId = $query->getCustomerId();
        $customer = new Customer($customerId->getValue());

        if ($customer->id !== $customerId->getValue()) {
            throw new CustomerNotFoundException(sprintf('Customer with id "%d" was not found', $customerId->getValue()));
        }

        $birthday = null === $customer->birthday ?
            Birthday::createEmpty() :
            new Birthday($customer->birthday)
        ;

        return new EditableCustomer(
            $customerId,
            (int) $customer->id_gender,
            new FirstName($customer->firstname),
            new LastName($customer->lastname),
            new Email($customer->email),
            $birthday,
            (bool) $customer->active,
            (bool) $customer->optin,
            (bool) $customer->newsletter,
            $customer->getGroups(),
            (int) $customer->id_default_group,
            (string) $customer->company,
            (string) $customer->siret,
            (string) $customer->ape,
            (string) $customer->website,
            (float) $customer->outstanding_allow_amount,
            (int) $customer->max_payment_days,
            (int) $customer->id_risk,
            (bool) $customer->isGuest()
        );
    }
}
