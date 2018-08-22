<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property ShopGroup $object
 */
class AdminShopGroupControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'shop_group';
        $this->className = 'ShopGroup';
        $this->lang = false;
        $this->multishop_context = Shop::CONTEXT_ALL;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        if (!Tools::getValue('realedit')) {
            $this->deleted = false;
        }

        $this->show_toolbar = false;

        $this->fields_list = array(
            'id_shop_group' => array(
                'title' => $this->trans('ID', array(), 'Admin.Global'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Shop group', array(), 'Admin.Advparameters.Feature'),
                'width' => 'auto',
                'filter_key' => 'a!name',
            ),
        );

        $this->fields_options = array(
            'general' => array(
                'title' => $this->trans('Multistore options', array(), 'Admin.Advparameters.Feature'),
                'fields' => array(
                    'PS_SHOP_DEFAULT' => array(
                        'title' => $this->trans('Default shop', array(), 'Admin.Advparameters.Feature'),
                        'cast' => 'intval',
                        'type' => 'select',
                        'identifier' => 'id_shop',
                        'list' => Shop::getShops(),
                        'visibility' => Shop::CONTEXT_ALL,
                    ),
                ),
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions')),
            ),
        );
    }

    public function viewAccess($disable = false)
    {
        return Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
    }

    public function initContent()
    {
        parent::initContent();

        $this->addJqueryPlugin('cooki-plugin');
        $data = Shop::getTree();

        foreach ($data as $key_group => &$group) {
            foreach ($group['shops'] as $key_shop => &$shop) {
                $current_shop = new Shop($shop['id_shop']);
                $urls = $current_shop->getUrls();

                foreach ($urls as $key_url => &$url) {
                    $title = $url['domain'] . $url['physical_uri'] . $url['virtual_uri'];
                    if (strlen($title) > 23) {
                        $title = substr($title, 0, 23) . '...';
                    }

                    $url['name'] = $title;
                    $shop['urls'][$url['id_shop_url']] = $url;
                }
            }
        }

        $shops_tree = new HelperTreeShops('shops-tree', $this->trans('Multistore tree', array(), 'Admin.Advparameters.Feature'));
        $shops_tree->setNodeFolderTemplate('shop_tree_node_folder.tpl')->setNodeItemTemplate('shop_tree_node_item.tpl')
            ->setHeaderTemplate('shop_tree_header.tpl')->setActions(array(
                new TreeToolbarLink(
                    'Collapse All',
                    '#',
                    '$(\'#' . $shops_tree->getId() . '\').tree(\'collapseAll\'); return false;',
                    'icon-collapse-alt'),
                new TreeToolbarLink(
                    'Expand All',
                    '#',
                    '$(\'#' . $shops_tree->getId() . '\').tree(\'expandAll\'); return false;',
                    'icon-expand-alt'),
            ))
            ->setAttribute('url_shop_group', $this->context->link->getAdminLink('AdminShopGroup'))
            ->setAttribute('url_shop', $this->context->link->getAdminLink('AdminShop'))
            ->setAttribute('url_shop_url', $this->context->link->getAdminLink('AdminShopUrl'))
            ->setData($data);
        $shops_tree = $shops_tree->render(null, false, false);

        if ($this->display == 'edit') {
            $this->toolbar_title[] = $this->object->name;
        }

        $this->context->smarty->assign(array(
            'toolbar_scroll' => 1,
            'toolbar_btn' => $this->toolbar_btn,
            'title' => $this->toolbar_title,
            'shops_tree' => $shops_tree,
        ));
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if ($this->display != 'add' && $this->display != 'edit') {
            $this->page_header_toolbar_btn['new'] = array(
                'desc' => $this->trans('Add a new shop group', array(), 'Admin.Advparameters.Feature'),
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            );
            $this->page_header_toolbar_btn['new_2'] = array(
                'desc' => $this->trans('Add a new shop', array(), 'Admin.Advparameters.Feature'),
                'href' => $this->context->link->getAdminLink('AdminShop') . '&addshop',
                'imgclass' => 'new_2',
                'icon' => 'process-icon-new',
            );
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display != 'add' && $this->display != 'edit') {
            $this->toolbar_btn['new'] = array(
                'desc' => $this->trans('Add a new shop group', array(), 'Admin.Advparameters.Feature'),
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            );
        }
    }

    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Shop group', array(), 'Admin.Advparameters.Feature'),
                'icon' => 'icon-shopping-cart',
            ),
            'description' => $this->trans('Warning: Enabling the "share customers" and "share orders" options is not recommended. Once activated and orders are created, you will not be able to disable these options. If you need these options, we recommend using several categories rather than several shops.', array(), 'Admin.Advparameters.Help'),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Shop group name', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'name',
                    'required' => true,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Share customers', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'share_customer',
                    'required' => true,
                    'class' => 't',
                    'is_bool' => true,
                    'disabled' => ($this->id_object && $this->display == 'edit' && ShopGroup::hasDependency($this->id_object, 'customer')) ? true : false,
                    'values' => array(
                        array(
                            'id' => 'share_customer_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'share_customer_off',
                            'value' => 0,
                        ),
                    ),
                    'desc' => $this->trans('Once this option is enabled, the shops in this group will share customers. If a customer registers in any one of these shops, the account will automatically be available in the others shops of this group.', array(), 'Admin.Advparameters.Help') . '<br/>' . $this->trans('Warning: you will not be able to disable this option once you have registered customers.', array(), 'Admin.Advparameters.Help'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Share available quantities to sell', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'share_stock',
                    'required' => true,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'share_stock_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'share_stock_off',
                            'value' => 0,
                        ),
                    ),
                    'desc' => $this->trans('Share available quantities between shops of this group. When changing this option, all available products quantities will be reset to 0.', array(), 'Admin.Advparameters.Feature'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Share orders', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'share_order',
                    'required' => true,
                    'class' => 't',
                    'is_bool' => true,
                    'disabled' => ($this->id_object && $this->display == 'edit' && ShopGroup::hasDependency($this->id_object, 'order')) ? true : false,
                    'values' => array(
                        array(
                            'id' => 'share_order_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'share_order_off',
                            'value' => 0,
                        ),
                    ),
                    'desc' => $this->trans('Once this option is enabled (which is only possible if customers and available quantities are shared among shops), the customer\'s cart will be shared by all shops in this group. This way, any purchase started in one shop will be able to be completed in another shop from the same group.', array(), 'Admin.Advparameters.Help') . '<br/>' . $this->trans('Warning: You will not be able to disable this option once you\'ve started to accept orders.', array(), 'Admin.Advparameters.Help'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Status', array(), 'Admin.Global'),
                    'name' => 'active',
                    'required' => true,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                        ),
                    ),
                    'desc' => $this->trans('Enable or disable this shop group?', array(), 'Admin.Advparameters.Help'),
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions'),
            ),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if (Shop::getTotalShops() > 1 && $obj->id) {
            $disabled = array(
                'share_customer' => true,
                'share_stock' => true,
                'share_order' => true,
                'active' => false,
            );
        } else {
            $disabled = false;
        }

        $default_shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        $this->tpl_form_vars = array(
            'disabled' => $disabled,
            'checked' => (Tools::getValue('addshop_group') !== false) ? true : false,
            'defaultGroup' => $default_shop->id_shop_group,
        );

        $this->fields_value = array(
            'active' => true,
        );

        return parent::renderForm();
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        $shop_group_delete_list = array();

        // test store authorized to remove
        foreach ($this->_list as $shop_group) {
            $shops = Shop::getShops(true, $shop_group['id_shop_group']);
            if (!empty($shops)) {
                $shop_group_delete_list[] = $shop_group['id_shop_group'];
            }
        }
        $this->addRowActionSkipList('delete', $shop_group_delete_list);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('delete' . $this->table) || Tools::isSubmit('status') || Tools::isSubmit('status' . $this->table)) {
            /** @var ShopGroup $object */
            $object = $this->loadObject();

            if (ShopGroup::getTotalShopGroup() == 1) {
                $this->errors[] = $this->trans('You cannot delete or disable the last shop group.', array(), 'Admin.Notifications.Error');
            } elseif ($object->haveShops()) {
                $this->errors[] = $this->trans('You cannot delete or disable a shop group in use.', array(), 'Admin.Notifications.Error');
            }

            if (count($this->errors)) {
                return false;
            }
        }

        return parent::postProcess();
    }

    protected function afterAdd($new_shop_group)
    {
        //Reset available quantitites
        StockAvailable::resetProductFromStockAvailableByShopGroup($new_shop_group);
    }

    protected function afterUpdate($new_shop_group)
    {
        //Reset available quantitites
        StockAvailable::resetProductFromStockAvailableByShopGroup($new_shop_group);
    }

    public function renderOptions()
    {
        if ($this->fields_options && is_array($this->fields_options)) {
            $this->display = 'options';
            $this->show_toolbar = false;
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->id = $this->id;
            $helper->tpl_vars = $this->tpl_option_vars;
            $options = $helper->generateOptions($this->fields_options);

            return $options;
        }
    }
}
