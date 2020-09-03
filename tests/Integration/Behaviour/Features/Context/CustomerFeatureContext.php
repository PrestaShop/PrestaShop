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

namespace Tests\Integration\Behaviour\Features\Context;

use CartRule;
use Configuration;
use Context;
use Country;
use Customer;
use Exception;
use PrestaShop\PrestaShop\Adapter\Validate;
use RuntimeException;

class CustomerFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Customer[]
     */
    protected $customers = [];

    /**
     * @Given /^there is a customer named "(.+)" whose email is "(.+)"$/
     */
    public function createCustomer($customerName, $customerEmail)
    {
        $customer = new Customer();
        $customer->firstname = 'fake';
        $customer->lastname = 'fake';
        $customer->passwd = 'fakefake';
        $customer->email = $customerEmail;
        $customer->add();
        $this->customers[$customerName] = $customer;
    }

    /**
     * @Given there is customer :reference with email :customerEmail
     */
    public function customerExists($reference, $customerEmail)
    {
        $data = Customer::getCustomersByEmail($customerEmail);
        $customer = new Customer($data[0]['id_customer']);

        if (!Validate::isLoadedObject($customer)) {
            throw new Exception(sprintf('Customer with email "%s" does not exist.', $customerEmail));
        }

        SharedStorage::getStorage()->set($reference, $customer);
    }

    /**
     * @Given customer :reference has address in :isoCode country
     */
    public function customerHasAddressInCountry($reference, $isoCode)
    {
        $customer = SharedStorage::getStorage()->get($reference);
        $customerAddresses = $customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT'));

        foreach ($customerAddresses as $address) {
            $country = new Country($address['id_country']);

            if ($country->iso_code === $isoCode) {
                return;
            }
        }

        throw new RuntimeException(sprintf('Customer does not have address in "%s" country', $isoCode));
    }

    /**
     * @When /^I am logged in as "(.+)"$/
     */
    public function setCurrentCustomer($customerName)
    {
        $this->checkCustomerWithNameExists($customerName);
        Context::getContext()->updateCustomer($this->customers[$customerName]);
    }

    /**
     * @Given private note is not set about customer :reference
     */
    public function assertPrivateNoteIsNotSetAboutCustomer($reference)
    {
        /** @var Customer $customer */
        $customer = SharedStorage::getStorage()->get($reference);

        if ($customer->note) {
            throw new RuntimeException(sprintf('It was expected that customer "%s" should not have private note.', $reference));
        }
    }

    /**
     * @Then customer :reference private note should be :privateNote
     */
    public function assertPrivateNoteAboutCustomer($reference, $privateNote)
    {
        /** @var Customer $customer */
        $customer = SharedStorage::getStorage()->get($reference);

        if ($customer->note !== $privateNote) {
            throw new RuntimeException(sprintf('It was expected that customer "%s" private note should be "%s", but actually is "%s".', $reference, $privateNote, $customer->note));
        }
    }

    /**
     * @Then customer :reference last voucher is :voucherAmount
     */
    public function checkCustomerHasVoucher(string $reference, float $voucherAmount)
    {
        /** @var Customer $customer */
        $customer = SharedStorage::getStorage()->get($reference);
        $cartRules = CartRule::getCustomerCartRules((int) Configuration::get('PS_LANG_DEFAULT'), $customer->id, true, false);
        if (empty($cartRules)) {
            throw new RuntimeException('Cannot find any cart rules for customer');
        }

        $voucher = $cartRules[count($cartRules) - 1];
        if ($voucherAmount !== (float) $voucher['reduction_amount']) {
            throw new RuntimeException(sprintf('Invalid voucher amount, expected %s but got %s instead', $voucherAmount, $voucher['reduction_amount']));
        }
    }

    /**
     * @param $customerName
     */
    public function checkCustomerWithNameExists($customerName)
    {
        $this->checkFixtureExists($this->customers, 'Customer', $customerName);
    }

    /**
     * @param $customerName
     *
     * @return Customer
     */
    public function getCustomerWithName($customerName)
    {
        return $this->customers[$customerName];
    }

    /**
     * @AfterScenario
     */
    public function cleanCustomerFixtures()
    {
        foreach ($this->customers as $customer) {
            $customer->delete();
        }
        $this->customers = [];
    }
}
