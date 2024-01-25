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
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;

class OrderDetailControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'order-detail';
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $authRedirection = 'history';
    /** @var bool */
    public $ssl = true;

    protected $order_to_display;

    protected $reference;

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitMessage')) {
            $idOrder = (int) Tools::getValue('id_order');
            $msgText = Tools::getValue('msgText');

            if (!$idOrder || !Validate::isUnsignedId($idOrder)) {
                $this->errors[] = $this->trans('The order is no longer valid.', [], 'Shop.Notifications.Error');
            } elseif (empty(trim($msgText))) {
                $this->errors[] = $this->trans('The message cannot be blank.', [], 'Shop.Notifications.Error');
            }

            if (!count($this->errors)) {
                $order = new Order($idOrder);
                if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                    //check if a thread already exist
                    $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($this->context->customer->email, $order->id);
                    $id_product = (int) Tools::getValue('id_product');
                    $cm = new CustomerMessage();
                    if (!$id_customer_thread) {
                        $ct = new CustomerThread();
                        $ct->id_contact = 0;
                        $ct->id_customer = (int) $order->id_customer;
                        $ct->id_shop = (int) $this->context->shop->id;
                        if ($id_product && $order->orderContainProduct($id_product)) {
                            $ct->id_product = $id_product;
                        }
                        $ct->id_order = (int) $order->id;
                        $ct->id_lang = (int) $this->context->language->id;
                        $ct->email = $this->context->customer->email;
                        $ct->status = 'open';
                        $ct->token = Tools::passwdGen(12);
                        $ct->add();
                    } else {
                        $ct = new CustomerThread((int) $id_customer_thread);
                        $ct->status = 'open';
                        $ct->update();
                    }

                    $cm->id_customer_thread = $ct->id;
                    $cm->message = $msgText;
                    $client_ip_address = Tools::getRemoteAddr();
                    $cm->ip_address = (string) ip2long($client_ip_address);
                    $cm->add();

                    if (!Configuration::get('PS_MAIL_EMAIL_MESSAGE')) {
                        $to = (string) Configuration::get('PS_SHOP_EMAIL');
                    } else {
                        $to = new Contact((int) Configuration::get('PS_MAIL_EMAIL_MESSAGE'));
                        $to = (string) $to->email;
                    }
                    $toName = (string) Configuration::get('PS_SHOP_NAME');
                    $customer = $this->context->customer;

                    $product = new Product($id_product);
                    $product_name = '';
                    if (Validate::isLoadedObject($product) && isset($product->name[(int) $this->context->language->id])) {
                        $product_name = $product->name[(int) $this->context->language->id];
                    }

                    if (Validate::isLoadedObject($customer)) {
                        Mail::Send(
                            $this->context->language->id,
                            'order_customer_comment',
                            $this->trans(
                                'Message from a customer',
                                [],
                                'Emails.Subject'
                            ),
                            [
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{email}' => $customer->email,
                                '{id_order}' => (int) $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => Tools::nl2br(Tools::htmlentitiesUTF8($msgText)),
                                '{product_name}' => $product_name,
                            ],
                            $to,
                            $toName,
                            (string) Configuration::get('PS_SHOP_EMAIL'),
                            $customer->firstname . ' ' . $customer->lastname,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            false,
                            null,
                            null,
                            $customer->email
                        );
                    }

                    Tools::redirect($this->context->link->getPageLink(
                        'order-detail',
                        null,
                        null,
                        [
                            'id_order' => $idOrder,
                            'messagesent' => 1,
                        ]
                    ));
                } else {
                    $this->redirect_after = '404';
                    $this->redirect();
                }
            }
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        if (Configuration::isCatalogMode()) {
            Tools::redirect('index.php');
        }

        $id_order = (int) Tools::getValue('id_order');
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
                $this->errors[] = $this->trans('You do not have enough products to request an additional merchandise return.', [], 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorMsg')) {
                $this->errors[] = $this->trans('Please provide an explanation for your RMA.', [], 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorDetail1')) {
                $this->errors[] = $this->trans('Please check at least one product you would like to return.', [], 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorDetail2')) {
                $this->errors[] = $this->trans('For each product you wish to add, please specify the desired quantity.', [], 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('errorNotReturnable')) {
                $this->errors[] = $this->trans('This order cannot be returned', [], 'Shop.Notifications.Error');
            } elseif (Tools::getIsset('messagesent')) {
                $this->success[] = $this->trans('Message successfully sent', [], 'Shop.Notifications.Success');
            }

            $order = new Order($id_order);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                if ($order->id_shop != $this->context->shop->id && $this->context->customer->id_shop_group == $this->context->shop->id_shop_group) {
                    $shopGroup = new ShopGroup($this->context->customer->id_shop_group);
                    if (!$shopGroup->share_order) {
                        $this->redirect_after = '404';
                        $this->redirect();
                    }
                }
                $this->order_to_display = (new OrderPresenter())->present($order);

                $this->reference = $order->reference;

                $this->context->smarty->assign([
                    'order' => $this->order_to_display,
                    'orderIsVirtual' => $order->isVirtual(),
                    'HOOK_DISPLAYORDERDETAIL' => Hook::exec('displayOrderDetail', ['order' => $order]),
                ]);
            } else {
                $this->redirect_after = '404';
                $this->redirect();
            }
            unset($order);
        }

        $this->setTemplate('customer/order-detail');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => $this->trans('Order history', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('history'),
        ];

        if (!empty($this->reference)) {
            $breadcrumb['links'][] = [
                'title' => $this->reference,
                'url' => '#',
            ];
        }

        return $breadcrumb;
    }
}
