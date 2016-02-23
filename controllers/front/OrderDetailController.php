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

use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

class OrderDetailControllerCore extends ProductPresentingFrontControllerCore
{
    public $php_self = 'order-detail';
    public $auth = true;
    public $authRedirection = 'history';
    public $ssl = true;

    protected $order_to_display;

    /**
     * Initialize order detail controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            $idOrder = (int)Tools::getValue('id_order');
            $msgText = Tools::getValue('msgText');

            if (!$idOrder || !Validate::isUnsignedId($idOrder)) {
                $this->errors[] = $this->l('The order is no longer valid.');
            } elseif (empty($msgText)) {
                $this->errors[] = $this->l('The message cannot be blank.');
            } elseif (!Validate::isMessage($msgText)) {
                $this->errors[] = $this->l('This message is invalid (HTML is not allowed).');
            }
            if (!count($this->errors)) {
                $order = new Order($idOrder);
                if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($this->context->customer->email, $order->id);
                    $id_product = (int)Tools::getValue('id_product');
                    $cm = new CustomerMessage();
                    if (!$id_customer_thread) {
                        $ct = new CustomerThread();
                        $ct->id_contact = 0;
                        $ct->id_customer = (int)$order->id_customer;
                        $ct->id_shop = (int)$this->context->shop->id;
                        if ($id_product && $order->orderContainProduct($id_product)) {
                            $ct->id_product = $id_product;
                        }
                        $ct->id_order = (int)$order->id;
                        $ct->id_lang = (int)$this->context->language->id;
                        $ct->email = $this->context->customer->email;
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    } else {
                        $ct = new CustomerThread((int)$id_customer_thread);
                        $ct->status = 'open';
                        $ct->update();
                    }

                    $cm->id_customer_thread = $ct->id;
                    $cm->message = $msgText;
                    $cm->ip_address = (int)ip2long($_SERVER['REMOTE_ADDR']);
                    $cm->add();

                    if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE')) {
                        $to = strval(Configuration::get('PS_SHOP_EMAIL'));
                    } else {
                        $to = new Contact((int)Configuration::get('PS_MAIL_EMAIL_MESSAGE'));
                        $to = strval($to->email);
                    }
                    $toName = strval(Configuration::get('PS_SHOP_NAME'));
                    $customer = $this->context->customer;

                    $product = new Product($id_product);
                    $product_name = '';
                    if (Validate::isLoadedObject($product) && isset($product->name[(int)$this->context->language->id])) {
                        $product_name = $product->name[(int)$this->context->language->id];
                    }

                    if (Validate::isLoadedObject($customer)) {
                        Mail::Send(
                            $this->context->language->id,
                            'order_customer_comment',
                            Mail::l('Message from a customer'),
                            array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{email}' => $customer->email,
                                '{id_order}' => (int)$order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => Tools::nl2br($msgText),
                                '{product_name}' => $product_name
                            ),
                            $to,
                            $toName,
                            $customer->email,
                            $customer->firstname.' '.$customer->lastname
                        );
                    }

                    $this->success[] = $this->l('Message successfully sent');
                } else {
                    $this->redirect_after = '404';
                    $this->redirect();
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
        parent::initContent();

        if (!($id_order = (int)Tools::getValue('id_order')) || !Validate::isUnsignedId($id_order)) {
            $this->redirect_after = '404';
            $this->redirect();
        } else {
            $order = new Order($id_order);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $this->order_to_display = (new OrderPresenter())->present($order);

                $this->context->smarty->assign([
                    'order' => $this->order_to_display,
                    'hook_orderdetaildisplayed' => Hook::exec('displayOrderDetail', ['order' => $order]),
                    'use_tax' => Configuration::get('PS_TAX'),
                ]);
            } else {
                $this->redirect_after = '404';
                $this->redirect();
            }
            unset($order);
        }

        $this->setTemplate('customer/order-detail.tpl');
    }
}
