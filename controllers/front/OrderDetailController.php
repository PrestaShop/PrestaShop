<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Adapter\Order\OrderPresenter;

class OrderDetailControllerCore extends FrontController
{
    public $php_self = 'order-detail';
    public $auth = true;
    public $authRedirection = 'history';
    public $ssl = true;

    protected $order_to_display;

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
                $this->errors[] = $this->trans('The order is no longer valid.', array(), 'Shop.Notifications.Error');
            } elseif (empty($msgText)) {
                $this->errors[] = $this->trans('The message cannot be blank.', array(), 'Shop.Notifications.Error');
            } elseif (!Validate::isMessage($msgText)) {
                $this->errors[] = $this->trans('This message is invalid (HTML is not allowed).', array(), 'Shop.Notifications.Error');
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
                            $this->trans(
                                'Message from a customer',
                                array(),
                                'Emails.Subject'
                            ),
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
                            strval(Configuration::get('PS_SHOP_EMAIL')),
                            $customer->firstname.' '.$customer->lastname,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            false,
                            null,
                            null,
                            $customer->email
                        );
                    }

                    Tools::redirect('index.php?controller=order-detail&id_order='.$idOrder.'&messagesent');
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
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $id_order = (int)Tools::getValue('id_order');
        $id_order = $id_order && Validate::isUnsignedId($id_order) ? $id_order : false;

        if (!$id_order) {
            $reference = Tools::getValue('reference');
            $reference = $reference && Validate::isReference($reference) ? $reference : false;
            $order = $reference ? Order::getByReference($reference)->getFirst() : false;
            $id_order = $order ? $order->id : false;
        }

        if (!$id_order) {
            $this->redirect_after = '404';
            $this->redirect();
        } else {
            if (Tools::getIsset('errorQuantity')) {
                $this->errors[] = $this->trans('You do not have enough products to request an additional merchandise return.', array(), 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorMsg')) {
                $this->errors[] = $this->trans('Please provide an explanation for your RMA.', array(), 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorDetail1')) {
                $this->errors[] = $this->trans('Please check at least one product you would like to return.', array(), 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorDetail2')) {
                $this->errors[] = $this->trans('For each product you wish to add, please specify the desired quantity.', array(), 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorNotReturnable')) {
                $this->errors[] = $this->trans('This order cannot be returned', array(), 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('messagesent')) {
                $this->success[] = $this->trans('Message successfully sent', array(), 'Shop.Notifications.Success');
            }

            $order = new Order($id_order);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $this->order_to_display = (new OrderPresenter())->present($order);

                $this->context->smarty->assign([
                    'order' => $this->order_to_display,
                    'HOOK_DISPLAYORDERDETAIL' => Hook::exec('displayOrderDetail', ['order' => $order]),
                ]);
            } else {
                $this->redirect_after = '404';
                $this->redirect();
            }
            unset($order);
        }

        parent::initContent();
        $this->setTemplate('customer/order-detail');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = array(
            'title' => $this->trans('Order history', array(), 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('history'),
        );

        return $breadcrumb;
    }
}
