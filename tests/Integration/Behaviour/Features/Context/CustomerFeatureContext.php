<?php
/**
 * 2007-2019 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Context\Context as BehatContext;
use Context;
use Customer;

class CustomerFeatureContext implements BehatContext
{
    use CartAwareTrait;

    /**
     * @var Customer[]
     */
    protected $customers = [];

    /**
     * @Given /^There is a customer with name (.+) and email (.+)$/
     */
    public function setCustomer($customerName, $customerEmail)
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
     * @When /^Current customer is customer with name (.+)$/
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
        if (!isset($this->customers[$customerName])) {
            throw new \Exception('Customer with name "' . $customerName . '" was not added in fixtures');
        }
    }

    /**
     * @param $productName
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
    public function cleanData()
    {
        foreach ($this->customers as $customer) {
            $customer->delete();
        }
        $this->customers = [];
    }
}
