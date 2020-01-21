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
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerByEmailNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForAddressCreation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler\GetCustomerForAddressCreationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\AddressCreationCustomerInformation;
use PrestaShopDatabaseException;

/**
 * Handles finding customer by email
 */
final class GetCustomerForAddressCreationHandler implements GetCustomerForAddressCreationHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @return AddressCreationCustomerInformation
     *
     * @throws CustomerByEmailNotFoundException
     * @throws CustomerException
     */
    public function handle(GetCustomerForAddressCreation $query): AddressCreationCustomerInformation
    {
        $email = $query->getCustomerEmail();

        try {
            $result = Customer::searchByName($email);
        } catch (PrestaShopDatabaseException $e) {
            throw new CustomerException(sprintf('Failed to fetch results for customers with email %s', $email));
        }

        if (empty($result)) {
            throw new CustomerByEmailNotFoundException(sprintf('Failed to find customer with email %s', $email));
        }

        $customer = reset($result);

        $customerInformation = new AddressCreationCustomerInformation(
            (int) $customer['id_customer'],
            $customer['firstname'],
            $customer['lastname']
        );

        if (null !== $customer['company']) {
            $customerInformation->setCompany($customer['company']);
        }

        return $customerInformation;
    }
}
