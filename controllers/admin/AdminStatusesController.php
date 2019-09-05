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

/**
 * @property OrderState $object
 */
class AdminStatusesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order_state';
        $this->className = 'OrderState';
        $this->lang = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os',
        );

        parent::__construct();

        $this->bulk_actions = array('delete' => array('text' => $this->trans('Delete selected', array(), 'Admin.Actions'), 'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')));
    }

    public function init()
    {
        if (Tools::isSubmit('addorder_return_state')) {
            $this->display = 'add';
        }
        if (Tools::isSubmit('updateorder_return_state')) {
            $this->display = 'edit';
        }

        return parent::init();
    }

    /**
     * init all variables to render the order status list.
     */
    protected function initOrderStatutsList()
    {
        $this->fields_list = array(
            'id_order_state' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'width' => 'auto',
                'color' => 'color',
            ),
            'logo' => array(
                'title' => $this->trans('Icon', array(), 'Admin.Shopparameters.Feature'),
                'align' => 'text-center',
                'image' => 'os',
                'orderby' => false,
                'search' => false,
                'class' => 'fixed-width-xs',
            ),
            'send_email' => array(
                'title' => $this->trans('Send email to customer', array(), 'Admin.Shopparameters.Feature'),
                'align' => 'text-center',
                'active' => 'sendEmail',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
            'delivery' => array(
                'title' => $this->trans('Delivery', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'delivery',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
            'invoice' => array(
                'title' => $this->trans('Invoice', array(), 'Admin.Global'),
                'align' => 'text-center',
                'active' => 'invoice',
                'type' => 'bool',
                'ajax' => true,
                'orderby' => false,
                'class' => 'fixed-width-sm',
            ),
            'template' => array(
                'title' => $this->trans('Email template', array(), 'Admin.Shopparameters.Feature'),
            ),
        );
    }

    /**
     * init all variables to render the order return list.
     */
    protected function initOrdersReturnsList()
    {
        $this->table = 'order_return_state';
        $this->className = 'OrderReturnState';
        $this->_defaultOrderBy = $this->identifier = 'id_order_return_state';
        $this->list_id = 'order_return_state';
        $this->deleted = false;
        $this->_orderBy = null;

        $this->fields_list = array(
            'id_order_return_state' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Name', array(), 'Admin.Global'),
                'align' => 'left',
                'width' => 'auto',
                'color' => 'color',
            ),
        );
    }

    protected function initOrderReturnsForm()
    {
        $id_order_return_state = (int) Tools::getValue('id_order_return_state');

        // Create Object OrderReturnState
        $order_return_state = new OrderReturnState($id_order_return_state);

        //init field form variable for order return form
        $this->fields_form = array();

        //$this->initToolbar();
        $this->getlanguages();
        $helper = new HelperForm();
        $helper->currentIndex = self::$currentIndex;
        $helper->token = $this->token;
        $helper->table = 'order_return_state';
        $helper->identifier = 'id_order_return_state';
        $helper->id = $order_return_state->id;
        $helper->toolbar_scroll = false;
        $helper->languages = $this->_languages;
        $helper->default_form_language = $this->default_form_language;
        $helper->allow_employee_form_lang = $this->allow_employee_form_lang;

        if ($order_return_state->id) {
            $helper->fields_value = array(
                'name' => $this->getFieldValue($order_return_state, 'name'),
                'color' => $this->getFieldValue($order_return_state, 'color'),
            );
        } else {
            $helper->fields_value = array(
                'name' => $this->getFieldValue($order_return_state, 'name'),
                'color' => '#ffffff',
            );
        }

        $helper->toolbar_btn = $this->toolbar_btn;
        $helper->title = $this->trans('Edit return status', array(), 'Admin.Shopparameters.Feature');

        return $helper;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_order_state'] = array(
                'href' => self::$currentIndex . '&addorder_state&token=' . $this->token,
                'desc' => $this->trans('Add new order status', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            );
            $this->page_header_toolbar_btn['new_order_return_state'] = array(
                'href' => self::$currentIndex . '&addorder_return_state&token=' . $this->token,
                'desc' => $this->trans('Add new order return status', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new',
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Function used to render the list to display for this controller.
     */
    public function renderList()
    {
        //init and render the first list
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', $this->getUnremovableStatuses());
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash',
            ),
        );
        $this->initOrderStatutsList();
        $lists = parent::renderList();

        //init and render the second list
        $this->list_skip_actions = array();
        $this->_filter = false;
        $this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5));
        $this->initOrdersReturnsList();
        $this->checkFilterForOrdersReturnsList();

        // call postProcess() to take care of actions and filters
        $this->postProcess();
        $this->toolbar_title = $this->trans('Return statuses', array(), 'Admin.Shopparameters.Feature');

        parent::initToolbar();
        $lists .= parent::renderList();

        return $lists;
    }

    protected function getUnremovableStatuses()
    {
        return array_map(function ($row) {
            return (int) $row['id_order_state'];
        }, Db::getInstance()->executeS('SELECT id_order_state FROM ' . _DB_PREFIX_ . 'order_state WHERE unremovable = 1'));
    }

    protected function checkFilterForOrdersReturnsList()
    {
        // test if a filter is applied for this list
        if (Tools::isSubmit('submitFilter' . $this->table) || $this->context->cookie->{'submitFilter' . $this->table} !== false) {
            $this->filter = true;
        }

        // test if a filter reset request is required for this list
        if (isset($_POST['submitReset' . $this->table])) {
            $this->action = 'reset_filters';
        } else {
            $this->action = '';
        }
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->trans('Order status', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-time',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Status name', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->trans('Order status (e.g. \'Pending\').', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Invalid characters: numbers and', array(), 'Admin.Shopparameters.Help') . ' !<>,;?=+()@#"{}_$%:',
                    ),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Icon', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'icon',
                    'hint' => $this->trans('Upload an icon from your computer (File type: .gif, suggested size: 16x16).', array(), 'Admin.Shopparameters.Help'),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->trans('Color', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'color',
                    'hint' => $this->trans('Status will be highlighted in this color. HTML colors only.', array(), 'Admin.Shopparameters.Help') . ' "lightblue", "#CC6600")',
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'logable',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Consider the associated order as validated.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'invoice',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Allow a customer to download and view PDF versions of his/her invoices.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'hidden',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Hide this status in all customer orders.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'send_email',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Send an email to the customer when his/her order status has changed.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'pdf_invoice',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->trans('Attach invoice PDF to email.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'pdf_delivery',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->trans('Attach delivery slip PDF to email.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'shipped',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on',  'name' => $this->trans('Set the order as shipped.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'paid',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Set the order as paid.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'checkbox',
                    'name' => 'delivery',
                    'values' => array(
                        'query' => array(
                            array('id' => 'on', 'name' => $this->trans('Show delivery PDF.', array(), 'Admin.Shopparameters.Feature'), 'val' => '1'),
                        ),
                        'id' => 'id',
                        'name' => 'name',
                    ),
                ),
                array(
                    'type' => 'select_template',
                    'label' => $this->trans('Template', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'template',
                    'lang' => true,
                    'options' => array(
                        'query' => $this->getTemplates(),
                        'id' => 'id',
                        'name' => 'name',
                        'folder' => 'folder',
                    ),
                    'hint' => array(
                        $this->trans('Only letters, numbers and underscores ("_") are allowed.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Email template for both .html and .txt.', array(), 'Admin.Shopparameters.Help'),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
        );

        if (Tools::isSubmit('updateorder_state') || Tools::isSubmit('addorder_state')) {
            return $this->renderOrderStatusForm();
        } elseif (Tools::isSubmit('updateorder_return_state') || Tools::isSubmit('addorder_return_state')) {
            return $this->renderOrderReturnsForm();
        } else {
            return parent::renderForm();
        }
    }

    protected function renderOrderStatusForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = array(
            'logable_on' => $this->getFieldValue($obj, 'logable'),
            'invoice_on' => $this->getFieldValue($obj, 'invoice'),
            'hidden_on' => $this->getFieldValue($obj, 'hidden'),
            'send_email_on' => $this->getFieldValue($obj, 'send_email'),
            'shipped_on' => $this->getFieldValue($obj, 'shipped'),
            'paid_on' => $this->getFieldValue($obj, 'paid'),
            'delivery_on' => $this->getFieldValue($obj, 'delivery'),
            'pdf_delivery_on' => $this->getFieldValue($obj, 'pdf_delivery'),
            'pdf_invoice_on' => $this->getFieldValue($obj, 'pdf_invoice'),
        );

        if ($this->getFieldValue($obj, 'color') !== false) {
            $this->fields_value['color'] = $this->getFieldValue($obj, 'color');
        } else {
            $this->fields_value['color'] = '#ffffff';
        }

        return parent::renderForm();
    }

    protected function renderOrderReturnsForm()
    {
        $helper = $this->initOrderReturnsForm();
        $helper->show_cancel_button = true;

        $back = Tools::safeOutput(Tools::getValue('back', ''));
        if (empty($back)) {
            $back = self::$currentIndex . '&token=' . $this->token;
        }
        if (!Validate::isCleanHtml($back)) {
            die(Tools::displayError());
        }

        $helper->back_url = $back;

        $this->fields_form[0]['form'] = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->trans('Return status', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-time',
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Status name', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'name',
                    'lang' => true,
                    'required' => true,
                    'hint' => array(
                        $this->trans('Order\'s return status name.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Invalid characters: numbers and', array(), 'Admin.Shopparameters.Help') . ' !<>,;?=+()@#"�{}_$%:',
                    ),
                ),
                array(
                    'type' => 'color',
                    'label' => $this->trans('Color', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'color',
                    'hint' => $this->trans('Status will be highlighted in this color. HTML colors only.', array(), 'Admin.Shopparameters.Help') . ' "lightblue", "#CC6600")',
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
        );

        return $helper->generateForm($this->fields_form);
    }

    protected function getTemplates()
    {
        $default_path = '../mails/';
        // Mail templates can also be found in the theme folder
        $theme_path = '../themes/' . $this->context->shop->theme->getName() . '/mails/';

        $array = array();
        foreach (Language::getLanguages(false) as $language) {
            $iso_code = $language['iso_code'];

            // If there is no folder for the given iso_code in /mails or in /themes/[theme_name]/mails, we bypass this language
            if (!@filemtime(_PS_ADMIN_DIR_ . '/' . $default_path . $iso_code) && !@filemtime(_PS_ADMIN_DIR_ . '/' . $theme_path . $iso_code)) {
                continue;
            }

            $theme_templates_dir = _PS_ADMIN_DIR_ . '/' . $theme_path . $iso_code;
            $theme_templates = is_dir($theme_templates_dir) ? scandir($theme_templates_dir, SCANDIR_SORT_NONE) : array();
            // We merge all available emails in one array
            $templates = array_unique(array_merge(scandir(_PS_ADMIN_DIR_ . '/' . $default_path . $iso_code, SCANDIR_SORT_NONE), $theme_templates));
            foreach ($templates as $key => $template) {
                if (!strncmp(strrev($template), 'lmth.', 5)) {
                    $search_result = array_search($template, $theme_templates);
                    $array[$iso_code][] = array(
                        'id' => substr($template, 0, -5),
                        'name' => substr($template, 0, -5),
                        'folder' => ((!empty($search_result) ? $theme_path : $default_path)),
                    );
                }
            }
        }

        return $array;
    }

    public function postProcess()
    {
        if (Tools::isSubmit($this->table . 'Orderby') || Tools::isSubmit($this->table . 'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('submitAddorder_return_state')) {
            $id_order_return_state = Tools::getValue('id_order_return_state');

            // Create Object OrderReturnState
            $order_return_state = new OrderReturnState((int) $id_order_return_state);

            $order_return_state->color = Tools::getValue('color');
            $order_return_state->name = array();
            foreach (Language::getIDs(false) as $id_lang) {
                $order_return_state->name[$id_lang] = Tools::getValue('name_' . $id_lang);
            }

            // Update object
            if (!$order_return_state->save()) {
                $this->errors[] = $this->trans('An error has occurred: Can\'t save the current order\'s return status.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }

        if (Tools::isSubmit('submitBulkdeleteorder_return_state')) {
            $this->className = 'OrderReturnState';
            $this->table = 'order_return_state';
            $this->boxes = Tools::getValue('order_return_stateBox');
            parent::processBulkDelete();
        }

        if (Tools::isSubmit('deleteorder_return_state')) {
            $id_order_return_state = Tools::getValue('id_order_return_state');

            // Create Object OrderReturnState
            $order_return_state = new OrderReturnState((int) $id_order_return_state);

            if (!$order_return_state->delete()) {
                $this->errors[] = $this->trans('An error has occurred: Can\'t delete the current order\'s return status.', array(), 'Admin.Orderscustomers.Notification');
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&conf=1&token=' . $this->token);
            }
        }

        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $this->deleted = false; // Disabling saving historisation
            $_POST['invoice'] = (int) Tools::getValue('invoice_on');
            $_POST['logable'] = (int) Tools::getValue('logable_on');
            $_POST['send_email'] = (int) Tools::getValue('send_email_on');
            $_POST['hidden'] = (int) Tools::getValue('hidden_on');
            $_POST['shipped'] = (int) Tools::getValue('shipped_on');
            $_POST['paid'] = (int) Tools::getValue('paid_on');
            $_POST['delivery'] = (int) Tools::getValue('delivery_on');
            $_POST['pdf_delivery'] = (int) Tools::getValue('pdf_delivery_on');
            $_POST['pdf_invoice'] = (int) Tools::getValue('pdf_invoice_on');
            if (!$_POST['send_email']) {
                foreach (Language::getIDs(false) as $id_lang) {
                    $_POST['template_' . $id_lang] = '';
                }
            }

            return parent::postProcess();
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            $order_state = new OrderState(Tools::getValue('id_order_state'), $this->context->language->id);
            if (!$order_state->isRemovable()) {
                $this->errors[] = $this->trans('For security reasons, you cannot delete default order statuses.', array(), 'Admin.Shopparameters.Notification');
            } else {
                return parent::postProcess();
            }
        } elseif (Tools::isSubmit('submitBulkdelete' . $this->table)) {
            foreach (Tools::getValue($this->table . 'Box') as $selection) {
                $order_state = new OrderState((int) $selection, $this->context->language->id);
                if (!$order_state->isRemovable()) {
                    $this->errors[] = $this->trans('For security reasons, you cannot delete default order statuses.', array(), 'Admin.Shopparameters.Notification');

                    break;
                }
            }

            if (!count($this->errors)) {
                return parent::postProcess();
            }
        } else {
            return parent::postProcess();
        }
    }

    protected function filterToField($key, $filter)
    {
        if ($this->table == 'order_state') {
            $this->initOrderStatutsList();
        } elseif ($this->table == 'order_return_state') {
            $this->initOrdersReturnsList();
        }

        return parent::filterToField($key, $filter);
    }

    protected function afterImageUpload()
    {
        parent::afterImageUpload();

        if (($id_order_state = (int) Tools::getValue('id_order_state')) &&
             isset($_FILES) && count($_FILES) && file_exists(_PS_ORDER_STATE_IMG_DIR_ . $id_order_state . '.gif')) {
            $current_file = _PS_TMP_IMG_DIR_ . 'order_state_mini_' . $id_order_state . '_' . $this->context->shop->id . '.gif';

            if (file_exists($current_file)) {
                unlink($current_file);
            }
        }

        return true;
    }

    public function ajaxProcessSendEmailOrderState()
    {
        $id_order_state = (int) Tools::getValue('id_order_state');

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'order_state SET `send_email`= NOT `send_email` WHERE id_order_state=' . $id_order_state;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            echo json_encode(array('success' => 1, 'text' => $this->trans('The status has been updated successfully.', array(), 'Admin.Notifications.Success')));
        } else {
            echo json_encode(array('success' => 0, 'text' => $this->trans('An error occurred while updating the status.', array(), 'Admin.Notifications.Error')));
        }
    }

    public function ajaxProcessDeliveryOrderState()
    {
        $id_order_state = (int) Tools::getValue('id_order_state');

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'order_state SET `delivery`= NOT `delivery` WHERE id_order_state=' . $id_order_state;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            echo json_encode(array('success' => 1, 'text' => $this->trans('The status has been updated successfully.', array(), 'Admin.Notifications.Success')));
        } else {
            echo json_encode(array('success' => 0, 'text' => $this->trans('An error occurred while updating the status.', array(), 'Admin.Notifications.Error')));
        }
    }

    public function ajaxProcessInvoiceOrderState()
    {
        $id_order_state = (int) Tools::getValue('id_order_state');

        $sql = 'UPDATE ' . _DB_PREFIX_ . 'order_state SET `invoice`= NOT `invoice` WHERE id_order_state=' . $id_order_state;
        $result = Db::getInstance()->execute($sql);

        if ($result) {
            echo json_encode(array('success' => 1, 'text' => $this->trans('The status has been updated successfully.', array(), 'Admin.Notifications.Success')));
        } else {
            echo json_encode(array('success' => 0, 'text' => $this->trans('An error occurred while updating the status.', array(), 'Admin.Notifications.Error')));
        }
    }
}
