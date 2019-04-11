<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Customer\QueryHandler;

use Customer;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\EditableCustomer;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\Birthday;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\LastName;

/**
 * Handles command that gets customer for editing
 *
 * @internal
 */
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
            throw new CustomerNotFoundException(
                $customerId,
                sprintf('Customer with id "%s" was not found', $customerId->getValue())
            );
        }

        $birthday = null === $customer->birthday ?
            Birthday::createEmpty() :
            new Birthday($customer->birthday)
        ;

        return new EditableCustomer(
            $customerId,
            $customer->id_gender,
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
            (int) $customer->id_risk
        );
    }
}
