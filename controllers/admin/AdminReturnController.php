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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * @property OrderReturn $object
 */
class AdminReturnControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_return';
        $this->className = 'OrderReturn';
        $this->colorOnBackground = true;

        parent::__construct();

        $this->_select = 'ors.color, orsl.`name`, o.`id_shop`';
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'order_return_state ors ON (ors.`id_order_return_state` = a.`state`)';
        $this->_join .= 'LEFT JOIN ' . _DB_PREFIX_ . 'order_return_state_lang orsl ON (orsl.`id_order_return_state` = a.`state` AND orsl.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_join .= ' LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON (o.`id_order` = a.`id_order`)';
        $this->_orderBy = 'id_order_return';
        $this->_orderWay = 'DESC';

        $this->fields_list = [
            'id_order_return' => ['title' => $this->trans('ID', [], 'Admin.Global'), 'align' => 'center', 'width' => 25],
            'id_order' => ['title' => $this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'), 'width' => 100, 'align' => 'center', 'filter_key' => 'a!id_order', 'havingFilter' => true],
            'name' => ['title' => $this->trans('Status', [], 'Admin.Global'), 'color' => 'color', 'width' => 'auto', 'align' => 'left'],
            'date_add' => ['title' => $this->trans('Date issued', [], 'Admin.Orderscustomers.Feature'), 'width' => 150, 'type' => 'date', 'align' => 'right', 'filter_key' => 'a!date_add'],
        ];

        $this->fields_options = [
            'general' => [
                'title' => $this->trans('Merchandise return (RMA) options', [], 'Admin.Orderscustomers.Feature'),
                'fields' => [
                    'PS_ORDER_RETURN' => [
                        'title' => $this->trans('Enable returns', [], 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Would you like to allow merchandise returns in your shop?', [], 'Admin.Orderscustomers.Help'),
                        'cast' => 'intval', 'type' => 'bool', ],
                    'PS_ORDER_RETURN_NB_DAYS' => [
                        'title' => $this->trans('Time limit of validity', [], 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('How many days after the delivery date does the customer have to return a product?', [], 'Admin.Orderscustomers.Help'),
                        'cast' => 'intval',
                        'type' => 'text',
                        'size' => '2', ],
                    'PS_RETURN_PREFIX' => [
                        'title' => $this->trans('Returns prefix', [], 'Admin.Orderscustomers.Feature'),
                        'desc' => $this->trans('Prefix used for return name (e.g. RE00001).', [], 'Admin.Orderscustomers.Help'),
                        'size' => 6,
                        'type' => 'textLang',
                    ],
                ],
                'submit' => ['title' => $this->trans('Save', [], 'Admin.Actions')],
            ],
        ];

        $this->_where = Shop::addSqlRestriction(false, 'o');
        $this->_use_found_rows = false;
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Return Merchandise Authorization (RMA)', [], 'Admin.Orderscustomers.Feature'),
                'icon' => 'icon-clipboard',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => 'id_order',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_customer',
                ],
                [
                    'type' => 'text_customer',
                    'label' => $this->trans('Customer', [], 'Admin.Global'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                ],
                [
                    'type' => 'text_order',
                    'label' => $this->trans('Order', [], 'Admin.Global'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                ],
                [
                    'type' => 'free',
                    'label' => $this->trans('Customer explanation', [], 'Admin.Orderscustomers.Feature'),
                    'name' => 'question',
                    'size' => '',
                    'required' => false,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Status', [], 'Admin.Global'),
                    'name' => 'state',
                    'required' => false,
                    'options' => [
                        'query' => OrderReturnState::getOrderReturnStates($this->context->language->id),
                        'id' => 'id_order_return_state',
                        'name' => 'name',
                    ],
                    'desc' => $this->trans('Merchandise return (RMA) status.', [], 'Admin.Orderscustomers.Help'),
                ],
                [
                    'type' => 'list_products',
                    'label' => $this->trans('Products', [], 'Admin.Global'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                    'desc' => $this->trans('List of products in return package.', [], 'Admin.Orderscustomers.Help'),
                ],
                [
                    'type' => 'pdf_order_return',
                    'label' => $this->trans('Returns form', [], 'Admin.Orderscustomers.Feature'),
                    'name' => '',
                    'size' => '',
                    'required' => false,
                    'desc' => $this->trans('The link is only available after validation and before the parcel gets delivered.', [], 'Admin.Orderscustomers.Help'),
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Admin.Actions'),
            ],
            'buttons' => [
                'save-and-stay' => [
                    'title' => $this->trans('Save and stay', [], 'Admin.Actions'),
                    'name' => 'submitAdd' . $this->table . 'AndStay',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save',
                ],
            ],
        ];

        $order = new Order($this->object->id_order);
        $quantity_displayed = [];
        // Customized products */
        if ($returned_customizations = OrderReturn::getReturnedCustomizedProducts((int) ($this->object->id_order))) {
            foreach ($returned_customizations as $returned_customization) {
                $quantity_displayed[(int) $returned_customization['id_order_detail']] = isset($quantity_displayed[(int) $returned_customization['id_order_detail']]) ? $quantity_displayed[(int) $returned_customization['id_order_detail']] + (int) $returned_customization['product_quantity'] : (int) $returned_customization['product_quantity'];
            }
        }

        // Classic products
        $products = OrderReturn::getOrdersReturnProducts($this->object->id, $order);

        // Prepare customer explanation for display
        $this->object->question = '<span class="normal-text">' . nl2br($this->object->question) . '</span>';

        $parameters = ['vieworder' => 1, 'id_order' => (int) $order->id];
        $orderUrl = $this->context->link->getAdminLink('AdminOrders', true, [], $parameters);

        $this->tpl_form_vars = [
            'customer' => new Customer($this->object->id_customer),
            'url_customer' => $this->context->link->getAdminLink('AdminCustomers', true, [], [
                'id_customer' => $this->object->id_customer,
                'viewcustomer' => 1,
            ]),
            'text_order' => $this->trans(
                'Order #%id% from %date%',
                [
                    '%id%' => $order->id,
                    '%date%' => Tools::displayDate($order->date_upd),
                ],
                'Admin.Orderscustomers.Feature'
            ),
            'url_order' => $orderUrl,
            'picture_folder' => _THEME_PROD_PIC_DIR_,
            'returnedCustomizations' => $returned_customizations,
            'customizedDatas' => Product::getAllCustomizedDatas((int) ($order->id_cart)),
            'products' => $products,
            'quantityDisplayed' => $quantity_displayed,
            'id_order_return' => $this->object->id,
            'state_order_return' => $this->object->state,
        ];

        return parent::renderForm();
    }

    public function initToolbar()
    {
        // If display list, we don't want the "add" button
        if (!$this->display || $this->display == 'list') {
            return;
        } elseif ($this->display != 'options') {
            $this->toolbar_btn['save-and-stay'] = [
                'short' => 'SaveAndStay',
                'href' => '#',
                'desc' => $this->trans('Save and stay', [], 'Admin.Actions'),
                'force_desc' => true,
            ];
        }

        parent::initToolbar();
    }

    public function postProcess()
    {
        $this->context = Context::getContext();
        if (Tools::isSubmit('deleteorder_return_detail')) {
            if ($this->access('delete')) {
                if (($id_order_detail = (int) (Tools::getValue('id_order_detail'))) && Validate::isUnsignedId($id_order_detail)) {
                    if (($id_order_return = (int) (Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                        $orderReturn = new OrderReturn($id_order_return);
                        if (!Validate::isLoadedObject($orderReturn)) {
                            die(Tools::displayError());
                        }
                        if ((int) ($orderReturn->countProduct()) > 1) {
                            if (OrderReturn::deleteOrderReturnDetail($id_order_return, $id_order_detail, (int) (Tools::getValue('id_customization', 0)))) {
                                Tools::redirectAdmin(self::$currentIndex . '&conf=4token=' . $this->token);
                            } else {
                                $this->errors[] = $this->trans('An error occurred while deleting the details of your order return.', [], 'Admin.Orderscustomers.Notification');
                            }
                        } else {
                            $this->errors[] = $this->trans('You need at least one product.', [], 'Admin.Orderscustomers.Notification');
                        }
                    } else {
                        $this->errors[] = $this->trans('The order return is invalid.', [], 'Admin.Orderscustomers.Notification');
                    }
                } else {
                    $this->errors[] = $this->trans('The order return content is invalid.', [], 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', [], 'Admin.Notifications.Error');
            }
        } elseif (Tools::isSubmit('submitAddorder_return') || Tools::isSubmit('submitAddorder_returnAndStay')) {
            if ($this->access('edit')) {
                if (($id_order_return = (int) (Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                    $orderReturn = new OrderReturn($id_order_return);
                    $order = new Order($orderReturn->id_order);
                    $customer = new Customer($orderReturn->id_customer);
                    $orderLanguage = new Language((int) $order->id_lang);
                    $orderReturn->state = (int) (Tools::getValue('state'));
                    if ($orderReturn->save()) {
                        $orderReturnState = new OrderReturnState($orderReturn->state);
                        $vars = [
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                            '{id_order_return}' => $id_order_return,
                            '{state_order_return}' => (isset($orderReturnState->name[(int) $order->id_lang]) ? $orderReturnState->name[(int) $order->id_lang] : $orderReturnState->name[(int) Configuration::get('PS_LANG_DEFAULT')]),
                        ];
                        Mail::Send(
                            (int) $order->id_lang,
                            'order_return_state',
                            $this->trans(
                                'Your order return status has changed',
                                [],
                                'Emails.Subject',
                                $orderLanguage->locale
                            ),
                            $vars,
                            $customer->email,
                            $customer->firstname . ' ' . $customer->lastname,
                            null,
                            null,
                            null,
                            null,
                            _PS_MAIL_DIR_,
                            true,
                            (int) $order->id_shop
                        );

                        if (Tools::isSubmit('submitAddorder_returnAndStay')) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token . '&updateorder_return&id_order_return=' . (int) $id_order_return);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                        }
                    }
                } else {
                    $this->errors[] = $this->trans('No order return ID has been specified.', [], 'Admin.Orderscustomers.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        }
        parent::postProcess();
    }
}
