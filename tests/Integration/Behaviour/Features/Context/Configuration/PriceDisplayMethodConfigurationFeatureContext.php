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
