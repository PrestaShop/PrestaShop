<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Customer;
use Group;
use RuntimeException;

class PriceDisplayMethodConfigurationFeatureContext extends AbstractConfigurationFeatureContext
{
    /**
     * @Given /^price display method for the group of the customer having email "(.+)" is "(tax included|tax excluded)"$/
     */
    public function setPriceDisplayMethodForCustomer(string $customerEmail, string $priceDisplayMethod): void
    {
        $data = Customer::getCustomersByEmail($customerEmail);
        $data = reset($data);
        if (!isset($data['id_customer'])) {
            throw new RuntimeException(sprintf('Customer with email %s was not found', $customerEmail));
        }
        $customer = new Customer($data['id_customer']);

        $group = new Group($customer->id_default_group);
        if ($priceDisplayMethod === 'tax included') {
            $group->price_display_method = Group::PRICE_DISPLAY_METHOD_TAX_INCL;
        } elseif ($priceDisplayMethod === 'tax excluded') {
            $group->price_display_method = Group::PRICE_DISPLAY_METHOD_TAX_EXCL;
        } else {
            throw new RuntimeException(sprintf('Price display method %s is not known', $priceDisplayMethod));
        }

        $group->update();
        Group::clearCachedValues();
    }
}
