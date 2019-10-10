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

namespace Tests\Integration\Behaviour\Features\Context;

use Context;
use Customer;

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
     * @When /^I am logged in as "(.+)"$/
     */
    public function setCurrentCustomer($customerName)
    {
        $this->checkCustomerWithNameExists($customerName);
        Context::getContext()->updateCustomer($this->customers[$customerName]);
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
