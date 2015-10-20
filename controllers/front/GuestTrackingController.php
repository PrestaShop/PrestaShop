<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class GuestTrackingControllerCore extends OrderDetailController
{
    public $ssl = true;
    public $auth = false;
    public $php_self = 'guest-tracking';
    private $order_collection = [];

    /**
     * Initialize guest tracking controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
        if ($this->context->customer->isLogged()) {
            Tools::redirect('history.php');
        }
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitGuestTracking') || Tools::isSubmit('submitTransformGuestToCustomer')) {
            // Get order reference, ignore package reference (after the #, on the order reference)
            $order_reference = current(explode('#', Tools::getValue('order_reference')));

            if (!empty($order_reference)) {
                $this->order_collection = Order::getByReference($order_reference);
            }

            $email = Tools::getValue('email');

            if (empty($order_reference)) {
                $this->errors[] = $this->l('Please provide your order\'s reference number.');
            } elseif (empty($email) || !Validate::isEmail($email)) {
                $this->errors[] = $this->l('Please provide a valid email address.');
            } elseif (!Customer::customerExists($email, false, false)) {
                $this->errors[] = $this->l('There is no account associated with this email address.');
            } elseif (Customer::customerExists($email, false, true)) {
                $this->errors[] = $this->l('This page is for guest accounts only. Since your guest account has already been transformed into a customer account, you can no longer view your order here. Please log in to your customer account to view this order');
                $this->context->smarty->assign('show_login_link', true);
            } elseif (!count($this->order_collection) || $this->order_collection->count() != 1 || !$this->order_collection->getFirst()->isAssociatedAtGuest($email)) {
                $this->errors[] = $this->l('Invalid order reference');
            } else {
                $this->assignOrderTracking($this->order_collection);
                if (Tools::isSubmit('submitTransformGuestToCustomer')) {
                    $customer = new Customer((int)$this->order_collection->getFirst()->id_customer);
                    if (!Validate::isLoadedObject($customer)) {
                        $this->errors[] = $this->l('Invalid customer');
                    } elseif (!Tools::getValue('password')) {
                        $this->errors[] = $this->l('Invalid password.');
                    } elseif (!$customer->transformToCustomer($this->context->language->id, Tools::getValue('password'))) {
                        $this->errors[] = $this->l('An error occurred while transforming a guest into a registered customer.');
                    } else {
                        $this->success[] = $this->l('Your guest account has been successfully transformed into a customer account. You can now log in as a registered shopper.');
                    }
                }
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        FrontController::initContent();

        /* Handle brute force attacks */
        if (count($this->errors)) {
            sleep(1);
        }

        if (count($this->order_collection) > 0) {
            $this->setTemplate('customer/guest-tracking.tpl');
        } else {
            $this->setTemplate('customer/guest-login.tpl');
        }
    }

    /**
     * Assigns template vars related to order tracking information
     *
     * @param PrestaShopCollection $this->order_collection
     *
     * @throws PrestaShopException
     */
    protected function assignOrderTracking($order_collection)
    {
        $order = $order_collection->getFirst();

        if ((int)$order->isReturnable()) {
            $this->info[] = $this->l('You cannot return merchandise with a guest account');
        }

        $this->order_to_display['data'] = $this->getTemplateVarOrder($order);
        $this->order_to_display['products'] = $this->getTemplateVarProducts($order);
        $this->order_to_display['history'] = $this->getTemplateVarOrderHistory($order);
        $this->order_to_display['addresses'] = $this->getTemplateVarAddresses($order);
        $this->order_to_display['shipping'] = $this->getTemplateVarShipping($order);
        $this->order_to_display['messages'] = $this->getTemplateVarMessages($order);
        $this->order_to_display['carrier'] = $this->getTemplateVarCarrier($order);

        $this->order_to_display['data']['followup'] = '';
        if ($this->order_to_display['carrier']['url'] && $order->shipping_number) {
            $this->order_to_display['data']['followup'] = str_replace('@', $order->shipping_number, $this->order_to_display['carrier']['url']);
        }

        $this->order_to_display['customer'] = $this->objectSerializer->toArray(new Customer($order->id_customer));

        $this->context->smarty->assign([
            'order' => $this->order_to_display,
            'hook_orderdetaildisplayed' => Hook::exec('displayOrderDetail', ['order' => $order]),
            'use_tax' => Configuration::get('PS_TAX'),
        ]);
    }
}
