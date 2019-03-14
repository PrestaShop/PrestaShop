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
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;

class AdminShopControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'shop';
        $this->className = 'Shop';
        $this->multishop_context = Shop::CONTEXT_ALL;

        parent::__construct();

        $this->id_shop_group = (int) Tools::getValue('id_shop_group');

        /* if $_GET['id_shop'] is transmitted, virtual url can be loaded in config.php, so we wether transmit shop_id in herfs */
        if ($this->id_shop = (int) Tools::getValue('shop_id')) {
            $_GET['id_shop'] = $this->id_shop;
        }

        $this->list_skip_actions['delete'] = array((int) Configuration::get('PS_SHOP_DEFAULT'));
        $this->fields_list = array(
            'id_shop' => array(
                'title' => $this->trans('Shop ID', array(), 'Admin.Shopparameters.Feature'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name' => array(
                'title' => $this->trans('Shop name', array(), 'Admin.Shopparameters.Feature'),
                'filter_key' => 'a!name',
                'width' => 200,
            ),
            'shop_group_name' => array(
                'title' => $this->trans('Shop group', array(), 'Admin.Shopparameters.Feature'),
                'width' => 150,
                'filter_key' => 'gs!name',
            ),
            'category_name' => array(
                'title' => $this->trans('Root category', array(), 'Admin.Shopparameters.Feature'),
                'width' => 150,
                'filter_key' => 'cl!name',
            ),
            'url' => array(
                'title' => $this->trans('Main URL for this shop', array(), 'Admin.Shopparameters.Feature'),
                'havingFilter' => 'url',
            ),
        );
    }

    public function getTabSlug()
    {
        return 'ROLE_MOD_TAB_ADMINSHOPGROUP_';
    }

    public function viewAccess($disable = false)
    {
        return Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        if (!$this->display && $this->id_shop_group) {
            if ($this->id_object) {
                $this->loadObject();
            }

            if (!$this->id_shop_group && $this->object && $this->object->id_shop_group) {
                $this->id_shop_group = $this->object->id_shop_group;
            }

            $this->page_header_toolbar_btn['edit'] = array(
                'desc' => $this->trans('Edit this shop group', array(), 'Admin.Shopparameters.Feature'),
                'href' => $this->context->link->getAdminLink('AdminShopGroup') . '&updateshop_group&id_shop_group='
                    . $this->id_shop_group,
            );

            $this->page_header_toolbar_btn['new'] = array(
                'desc' => $this->trans('Add new shop', array(), 'Admin.Shopparameters.Feature'),
                'href' => $this->context->link->getAdminLink('AdminShop') . '&add' . $this->table . '&id_shop_group='
                    . $this->id_shop_group,
            );
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        if ($this->display != 'edit' && $this->display != 'add') {
            if ($this->id_object) {
                $this->loadObject();
            }

            if (!$this->id_shop_group && $this->object && $this->object->id_shop_group) {
                $this->id_shop_group = $this->object->id_shop_group;
            }

            $this->toolbar_btn['new'] = array(
                'desc' => $this->trans('Add new shop', array(), 'Admin.Shopparameters.Feature'),
                'href' => $this->context->link->getAdminLink('AdminShop') . '&add' . $this->table . '&id_shop_group='
                    . $this->id_shop_group,
            );
        }
    }

    public function initContent()
    {
        parent::initContent();

        $this->addJqueryPlugin('cooki-plugin');
        $data = Shop::getTree();

        foreach ($data as &$group) {
            foreach ($group['shops'] as &$shop) {
                $current_shop = new Shop($shop['id_shop']);
                $urls = $current_shop->getUrls();

                foreach ($urls as &$url) {
                    $title = $url['domain'] . $url['physical_uri'] . $url['virtual_uri'];
                    if (strlen($title) > 23) {
                        $title = substr($title, 0, 23) . '...';
                    }

                    $url['name'] = $title;
                    $shop['urls'][$url['id_shop_url']] = $url;
                }
            }
        }

        $shops_tree = new HelperTreeShops('shops-tree', $this->trans('Multistore tree', array(), 'Admin.Shopparameters.Feature'));
        $shops_tree->setNodeFolderTemplate('shop_tree_node_folder.tpl')->setNodeItemTemplate('shop_tree_node_item.tpl')
            ->setHeaderTemplate('shop_tree_header.tpl')->setActions(array(
                new TreeToolbarLink(
                    'Collapse All',
                    '#',
                    '$(\'#' . $shops_tree->getId() . '\').tree(\'collapseAll\'); return false;',
                    'icon-collapse-alt'
                ),
                new TreeToolbarLink(
                    'Expand All',
                    '#',
                    '$(\'#' . $shops_tree->getId() . '\').tree(\'expandAll\'); return false;',
                    'icon-expand-alt'
                ),
            ))
            ->setAttribute('url_shop_group', $this->context->link->getAdminLink('AdminShopGroup'))
            ->setAttribute('url_shop', $this->context->link->getAdminLink('AdminShop'))
            ->setAttribute('url_shop_url', $this->context->link->getAdminLink('AdminShopUrl'))
            ->setData($data);
        $shops_tree = $shops_tree->render(null, false, false);

        if ($this->display == 'edit') {
            $this->toolbar_title[] = $this->object->name;
        } elseif (!$this->display && $this->id_shop_group) {
            $group = new ShopGroup($this->id_shop_group);
            $this->toolbar_title[] = $group->name;
        }

        $this->context->smarty->assign(array(
            'toolbar_scroll' => 1,
            'toolbar_btn' => $this->toolbar_btn,
            'title' => $this->toolbar_title,
            'shops_tree' => $shops_tree,
        ));
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'gs.name shop_group_name, cl.name category_name, CONCAT(\'http://\', su.domain, su.physical_uri, su.virtual_uri) AS url';
        $this->_join = '
			LEFT JOIN `' . _DB_PREFIX_ . 'shop_group` gs
				ON (a.id_shop_group = gs.id_shop_group)
			LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl
				ON (a.id_category = cl.id_category AND cl.id_lang=' . (int) $this->context->language->id . ')
			LEFT JOIN ' . _DB_PREFIX_ . 'shop_url su
				ON a.id_shop = su.id_shop AND su.main = 1
		';
        $this->_group = 'GROUP BY a.id_shop';

        if ($id_shop_group = (int) Tools::getValue('id_shop_group')) {
            $this->_where = 'AND a.id_shop_group = ' . $id_shop_group;
        }

        return parent::renderList();
    }

    public function displayAjaxGetCategoriesFromRootCategory()
    {
        if (Tools::isSubmit('id_category')) {
            $selected_cat = array((int) Tools::getValue('id_category'));
            $children = Category::getChildren((int) Tools::getValue('id_category'), $this->context->language->id);
            foreach ($children as $child) {
                $selected_cat[] = $child['id_category'];
            }

            $helper = new HelperTreeCategories('categories-tree', null, (int) Tools::getValue('id_category'), null, false);
            $this->content = $helper->setSelectedCategories($selected_cat)->setUseSearch(true)->setUseCheckBox(true)
                ->render();
        }
        parent::displayAjax();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('id_category_default')) {
            $_POST['id_category'] = Tools::getValue('id_category_default');
        }

        if (Tools::isSubmit('submitAddshopAndStay') || Tools::isSubmit('submitAddshop')) {
            $shop_group = new ShopGroup((int) Tools::getValue('id_shop_group'));
            if ($shop_group->shopNameExists(Tools::getValue('name'), (int) Tools::getValue('id_shop'))) {
                $this->errors[] = $this->trans('You cannot have two shops with the same name in the same group.', array(), 'Admin.Advparameters.Notification');
            }
        }

        if (count($this->errors)) {
            return false;
        }

        /** @var Shop|bool $result */
        $result = parent::postProcess();

        if ($result != false && (Tools::isSubmit('submitAddshopAndStay') || Tools::isSubmit('submitAddshop')) && (int) $result->id_category != (int) Configuration::get('PS_HOME_CATEGORY', null, null, (int) $result->id)) {
            Configuration::updateValue('PS_HOME_CATEGORY', (int) $result->id_category, false, null, (int) $result->id);
        }

        if ($this->redirect_after) {
            $this->redirect_after .= '&id_shop_group=' . $this->id_shop_group;
        }

        return $result;
    }

    public function processDelete()
    {
        if (!Validate::isLoadedObject($object = $this->loadObject())) {
            $this->errors[] = $this->trans('Unable to load this shop.', array(), 'Admin.Advparameters.Notification');
        } elseif (!Shop::hasDependency($object->id)) {
            $result = Category::deleteCategoriesFromShop($object->id) && parent::processDelete();
            Tools::generateHtaccess();

            return $result;
        } else {
            $this->errors[] = $this->trans('You cannot delete this shop (customer and/or order dependency).', array(), 'Admin.Shopparameters.Notification');
        }

        return false;
    }

    /**
     * @param Shop $new_shop
     *
     * @return bool
     */
    protected function afterAdd($new_shop)
    {
        $import_data = Tools::getValue('importData', array());

        // The root category should be at least imported
        $new_shop->copyShopData((int) Tools::getValue('importFromShop'), $import_data);

        // copy default data
        if (!Tools::getValue('useImportData') || (is_array($import_data) && !isset($import_data['group']))) {
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'group_shop` (`id_shop`, `id_group`)
					VALUES
					(' . (int) $new_shop->id . ', ' . (int) Configuration::get('PS_UNIDENTIFIED_GROUP') . '),
					(' . (int) $new_shop->id . ', ' . (int) Configuration::get('PS_GUEST_GROUP') . '),
					(' . (int) $new_shop->id . ', ' . (int) Configuration::get('PS_CUSTOMER_GROUP') . ')
				';
            Db::getInstance()->execute($sql);
        }

        return parent::afterAdd($new_shop);
    }

    /**
     * @param Shop $new_shop
     *
     * @return bool
     */
    protected function afterUpdate($new_shop)
    {
        $categories = Tools::getValue('categoryBox');

        if (!is_array($categories)) {
            $this->errors[] = $this->trans('Please create some sub-categories for this root category.', array(), 'Admin.Shopparameters.Notification');

            return false;
        }

        array_unshift($categories, Configuration::get('PS_ROOT_CATEGORY'));

        if (!Category::updateFromShop($categories, $new_shop->id)) {
            $this->errors[] = $this->trans('You need to select at least the root category.', array(), 'Admin.Shopparameters.Notification');
        }
        if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data)) {
            $new_shop->copyShopData((int) Tools::getValue('importFromShop'), $import_data);
        }

        if (Tools::isSubmit('submitAddshopAndStay') || Tools::isSubmit('submitAddshop')) {
            $this->redirect_after = self::$currentIndex . '&shop_id=' . (int) $new_shop->id . '&conf=4&token=' . $this->token;
        }

        return parent::afterUpdate($new_shop);
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP) {
            $this->_where .= ' AND a.id_shop_group = ' . (int) Shop::getContextShopGroupID();
        }

        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        $shop_delete_list = array();

        // don't allow to remove shop which have dependencies (customers / orders / ... )
        foreach ($this->_list as &$shop) {
            if (Shop::hasDependency($shop['id_shop'])) {
                $shop_delete_list[] = $shop['id_shop'];
            }
        }
        $this->context->smarty->assign('shops_having_dependencies', $shop_delete_list);
    }

    public function renderForm()
    {
        /** @var Shop $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Shop', array(), 'Admin.Global'),
                'icon' => 'icon-shopping-cart',
            ),
            'identifier' => 'shop_id',
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Shop name', array(), 'Admin.Shopparameters.Feature'),
                    'desc' => array(
                        $this->trans('This field does not refer to the shop name visible in the front office.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Follow [1]this link[/1] to edit the shop name used on the front office.', array(
                            '[1]' => '<a href="' . $this->context->link->getAdminLink('AdminStores') . '#store_fieldset_general">',
                            '[/1]' => '</a>',
                        ), 'Admin.Shopparameters.Help'), ),
                    'name' => 'name',
                    'required' => true,
                ),
            ),
        );

        $display_group_list = true;
        if ($this->display == 'edit') {
            $group = new ShopGroup($obj->id_shop_group);
            if ($group->share_customer || $group->share_order || $group->share_stock) {
                $display_group_list = false;
            }
        }

        if ($display_group_list) {
            $options = array();
            foreach (ShopGroup::getShopGroups() as $group) {
                /** @var ShopGroup $group */
                if ($this->display == 'edit' && ($group->share_customer || $group->share_order || $group->share_stock) && ShopGroup::hasDependency($group->id)) {
                    continue;
                }

                $options[] = array(
                    'id_shop_group' => $group->id,
                    'name' => $group->name,
                );
            }

            if ($this->display == 'add') {
                $group_desc = $this->trans('Warning: You won\'t be able to change the group of this shop if this shop belongs to a group with one of these options activated: Share Customers, Share Quantities or Share Orders.', array(), 'Admin.Shopparameters.Notification');
            } else {
                $group_desc = $this->trans('You can only move your shop to a shop group with all "share" options disabled -- or to a shop group with no customers/orders.', array(), 'Admin.Shopparameters.Notification');
            }

            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->trans('Shop group', array(), 'Admin.Shopparameters.Feature'),
                'desc' => $group_desc,
                'name' => 'id_shop_group',
                'options' => array(
                    'query' => $options,
                    'id' => 'id_shop_group',
                    'name' => 'name',
                ),
            );
        } else {
            $this->fields_form['input'][] = array(
                'type' => 'hidden',
                'name' => 'id_shop_group',
                'default' => $group->name,
            );
            $this->fields_form['input'][] = array(
                'type' => 'textShopGroup',
                'label' => $this->trans('Shop group', array(), 'Admin.Shopparameters.Feature'),
                'desc' => $this->trans('You can\'t edit the shop group because the current shop belongs to a group with the "share" option enabled.', array(), 'Admin.Shopparameters.Help'),
                'name' => 'id_shop_group',
                'value' => $group->name,
            );
        }

        $categories = Category::getRootCategories($this->context->language->id);
        $this->fields_form['input'][] = array(
            'type' => 'select',
            'label' => $this->trans('Category root', array(), 'Admin.Catalog.Feature'),
            'desc' => $this->trans('This is the root category of the store that you\'ve created. To define a new root category for your store, [1]please click here[/1].', array(
                '[1]' => '<a href="' . $this->context->link->getAdminLink('AdminCategories') . '&addcategoryroot" target="_blank">',
                '[/1]' => '</a>',
            ), 'Admin.Shopparameters.Help'),
            'name' => 'id_category',
            'options' => array(
                'query' => $categories,
                'id' => 'id_category',
                'name' => 'name',
            ),
        );

        if (Tools::isSubmit('id_shop')) {
            $shop = new Shop((int) Tools::getValue('id_shop'));
            $id_root = $shop->id_category;
        } else {
            $id_root = $categories[0]['id_category'];
        }

        $id_shop = (int) Tools::getValue('id_shop');
        self::$currentIndex = self::$currentIndex . '&id_shop_group=' . (int) (Tools::getValue('id_shop_group') ?
            Tools::getValue('id_shop_group') : (isset($obj->id_shop_group) ? $obj->id_shop_group : Shop::getContextShopGroupID()));
        $shop = new Shop($id_shop);
        $selected_cat = Shop::getCategories($id_shop);

        if (empty($selected_cat)) {
            // get first category root and preselect all these children
            $root_categories = Category::getRootCategories();
            $root_category = new Category($root_categories[0]['id_category']);
            $children = $root_category->getAllChildren($this->context->language->id);
            $selected_cat[] = $root_categories[0]['id_category'];

            foreach ($children as $child) {
                $selected_cat[] = $child->id;
            }
        }

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }

        $this->fields_form['input'][] = array(
            'type' => 'categories',
            'name' => 'categoryBox',
            'label' => $this->trans('Associated categories', array(), 'Admin.Catalog.Feature'),
            'tree' => array(
                'id' => 'categories-tree',
                'selected_categories' => $selected_cat,
                'root_category' => $root_category->id,
                'use_search' => true,
                'use_checkbox' => true,
            ),
            'desc' => $this->trans('By selecting associated categories, you are choosing to share the categories between shops. Once associated between shops, any alteration of this category will impact every shop.', array(), 'Admin.Shopparameters.Help'),
        );
        /*$this->fields_form['input'][] = array(
            'type' => 'switch',
            'label' => $this->trans('Enabled', array(), 'Admin.Global'),
            'name' => 'active',
            'required' => true,
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0
                )
            ),
            'desc' => $this->trans('Enable or disable your store?', array(), 'Admin.Shopparameters.Help')
        );*/

        $themes = (new ThemeManagerBuilder($this->context, Db::getInstance()))
                        ->buildRepository()
                        ->getList();

        $this->fields_form['input'][] = array(
            'type' => 'theme',
            'label' => $this->trans('Theme', array(), 'Admin.Design.Feature'),
            'name' => 'theme',
            'values' => $themes,
        );

        $this->fields_form['submit'] = array(
            'title' => $this->trans('Save', array(), 'Admin.Actions'),
        );

        if (Shop::getTotalShops() > 1 && $obj->id) {
            $disabled = array('active' => false);
        } else {
            $disabled = false;
        }

        $import_data = array(
            'carrier' => $this->trans('Carriers', array(), 'Admin.Shipping.Feature'),
            'cms' => $this->trans('Pages', array(), 'Admin.Design.Feature'),
            'contact' => $this->trans('Contact information', array(), 'Admin.Advparameters.Feature'),
            'country' => $this->trans('Countries', array(), 'Admin.Global'),
            'currency' => $this->trans('Currencies', array(), 'Admin.Global'),
            'discount' => $this->trans('Discount prices', array(), 'Admin.Advparameters.Feature'),
            'employee' => $this->trans('Employees', array(), 'Admin.Advparameters.Feature'),
            'image' => $this->trans('Images', array(), 'Admin.Global'),
            'lang' => $this->trans('Languages', array(), 'Admin.Global'),
            'manufacturer' => $this->trans('Brands', array(), 'Admin.Global'),
            'module' => $this->trans('Modules', array(), 'Admin.Global'),
            'hook_module' => $this->trans('Module hooks', array(), 'Admin.Advparameters.Feature'),
            'meta_lang' => $this->trans('Meta information', array(), 'Admin.Advparameters.Feature'),
            'product' => $this->trans('Products', array(), 'Admin.Global'),
            'product_attribute' => $this->trans('Product combinations', array(), 'Admin.Advparameters.Feature'),
            'stock_available' => $this->trans('Available quantities for sale', array(), 'Admin.Advparameters.Feature'),
            'store' => $this->trans('Stores', array(), 'Admin.Global'),
            'warehouse' => $this->trans('Warehouses', array(), 'Admin.Advparameters.Feature'),
            'webservice_account' => $this->trans('Webservice accounts', array(), 'Admin.Advparameters.Feature'),
            'attribute_group' => $this->trans('Attribute groups', array(), 'Admin.Advparameters.Feature'),
            'feature' => $this->trans('Features', array(), 'Admin.Global'),
            'group' => $this->trans('Customer groups', array(), 'Admin.Advparameters.Feature'),
            'tax_rules_group' => $this->trans('Tax rules groups', array(), 'Admin.Advparameters.Feature'),
            'supplier' => $this->trans('Suppliers', array(), 'Admin.Global'),
            'referrer' => $this->trans('Referrers/affiliates', array(), 'Admin.Advparameters.Feature'),
            'zone' => $this->trans('Zones', array(), 'Admin.International.Feature'),
            'cart_rule' => $this->trans('Cart rules', array(), 'Admin.Advparameters.Feature'),
        );

        // Hook for duplication of shop data
        $modules_list = Hook::getHookModuleExecList('actionShopDataDuplication');
        if (is_array($modules_list) && count($modules_list) > 0) {
            foreach ($modules_list as $m) {
                $import_data['Module' . ucfirst($m['module'])] = Module::getModuleName($m['module']);
            }
        }

        asort($import_data);

        if (!$this->object->id) {
            $this->fields_import_form = array(
                'radio' => array(
                    'type' => 'radio',
                    'label' => $this->trans('Import data', array(), 'Admin.Advparameters.Feature'),
                    'name' => 'useImportData',
                    'value' => 1,
                ),
                'select' => array(
                    'type' => 'select',
                    'name' => 'importFromShop',
                    'label' => $this->trans('Choose the source shop', array(), 'Admin.Advparameters.Feature'),
                    'options' => array(
                        'query' => Shop::getShops(false),
                        'name' => 'name',
                    ),
                ),
                'allcheckbox' => array(
                    'type' => 'checkbox',
                    'label' => $this->trans('Choose data to import', array(), 'Admin.Advparameters.Feature'),
                    'values' => $import_data,
                ),
                'desc' => $this->trans('Use this option to associate data (products, modules, etc.) the same way for each selected shop.', array(), 'Admin.Advparameters.Help'),
            );
        }

        if (!$obj->theme_name) {
            $themes = (new ThemeManagerBuilder($this->context, Db::getInstance()))
                            ->buildRepository()
                            ->getList();
            $theme = array_pop($themes);
            $theme_name = $theme->getName();
        } else {
            $theme_name = $obj->theme_name;
        }

        $this->fields_value = array(
            'id_shop_group' => (Tools::getValue('id_shop_group') ? Tools::getValue('id_shop_group') :
                (isset($obj->id_shop_group)) ? $obj->id_shop_group : Shop::getContextShopGroupID()),
            'id_category' => (Tools::getValue('id_category') ? Tools::getValue('id_category') :
                (isset($obj->id_category)) ? $obj->id_category : (int) Configuration::get('PS_HOME_CATEGORY')),
            'theme_name' => $theme_name,
        );

        $ids_category = array();
        $shops = Shop::getShops(false);
        foreach ($shops as $shop) {
            $ids_category[$shop['id_shop']] = $shop['id_category'];
        }

        $this->tpl_form_vars = array(
            'disabled' => $disabled,
            'checked' => (Tools::getValue('addshop') !== false) ? true : false,
            'defaultShop' => (int) Configuration::get('PS_SHOP_DEFAULT'),
            'ids_category' => $ids_category,
        );
        if (isset($this->fields_import_form)) {
            $this->tpl_form_vars = array_merge($this->tpl_form_vars, array('form_import' => $this->fields_import_form));
        }

        return parent::renderForm();
    }

    /**
     * Object creation.
     */
    public function processAdd()
    {
        if (!Tools::getValue('categoryBox') || !in_array(Tools::getValue('id_category'), Tools::getValue('categoryBox'))) {
            $this->errors[] = $this->trans('You need to select at least the root category.', array(), 'Admin.Advparameters.Notification');
        }

        if (Tools::isSubmit('id_category_default')) {
            $_POST['id_category'] = (int) Tools::getValue('id_category_default');
        }

        /* Checking fields validity */
        $this->validateRules();

        if (!count($this->errors)) {
            /** @var Shop $object */
            $object = new $this->className();
            $this->copyFromPost($object, $this->table);
            $this->beforeAdd($object);
            if (!$object->add()) {
                $this->errors[] = $this->trans('An error occurred while creating an object.', array(), 'Admin.Notifications.Error') .
                    ' <b>' . $this->table . ' (' . Db::getInstance()->getMsgError() . ')</b>';
            } elseif (($_POST[$this->identifier] = $object->id) && $this->postImage($object->id) && !count($this->errors) && $this->_redirect) {
                // voluntary do affectation here
                $parent_id = (int) Tools::getValue('id_parent', 1);
                $this->afterAdd($object);
                $this->updateAssoShop($object->id);
                // Save and stay on same form
                if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                    $this->redirect_after = self::$currentIndex . '&shop_id=' . (int) $object->id . '&conf=3&update' . $this->table . '&token=' . $this->token;
                }
                // Save and back to parent
                if (Tools::isSubmit('submitAdd' . $this->table . 'AndBackToParent')) {
                    $this->redirect_after = self::$currentIndex . '&shop_id=' . (int) $parent_id . '&conf=3&token=' . $this->token;
                }
                // Default behavior (save and back)
                if (empty($this->redirect_after)) {
                    $this->redirect_after = self::$currentIndex . ($parent_id ? '&shop_id=' . $object->id : '') . '&conf=3&token=' . $this->token;
                }
            }
        }

        $this->errors = array_unique($this->errors);
        if (count($this->errors) > 0) {
            $this->display = 'add';

            return;
        }

        $object->associateSuperAdmins();

        $categories = Tools::getValue('categoryBox');
        array_unshift($categories, Configuration::get('PS_ROOT_CATEGORY'));
        Category::updateFromShop($categories, $object->id);
        if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data) && isset($import_data['product'])) {
            ini_set('max_execution_time', 7200); // like searchcron.php
            Search::indexation(true);
        }

        return $object;
    }

    public function displayEditLink($token, $id, $name = null)
    {
        if ($this->access('edit')) {
            $tpl = $this->createTemplate('helpers/list/list_action_edit.tpl');
            if (!array_key_exists('Edit', self::$cache_lang)) {
                self::$cache_lang['Edit'] = $this->trans('Edit', array(), 'Admin.Actions');
            }

            $tpl->assign(array(
                'href' => $this->context->link->getAdminLink('AdminShop') . '&shop_id=' . (int) $id . '&update' . $this->table,
                'action' => self::$cache_lang['Edit'],
                'id' => $id,
            ));

            return $tpl->fetch();
        } else {
            return;
        }
    }

    public function initCategoriesAssociation($id_root = null)
    {
        if (is_null($id_root)) {
            $id_root = Configuration::get('PS_ROOT_CATEGORY');
        }
        $id_shop = (int) Tools::getValue('id_shop');
        $shop = new Shop($id_shop);
        $selected_cat = Shop::getCategories($id_shop);
        if (empty($selected_cat)) {
            // get first category root and preselect all these children
            $root_categories = Category::getRootCategories();
            $root_category = new Category($root_categories[0]['id_category']);
            $children = $root_category->getAllChildren($this->context->language->id);
            $selected_cat[] = $root_categories[0]['id_category'];

            foreach ($children as $child) {
                $selected_cat[] = $child->id;
            }
        }
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop')) {
            $root_category = new Category($shop->id_category);
        } else {
            $root_category = new Category($id_root);
        }
        $root_category = array('id_category' => $root_category->id, 'name' => $root_category->name[$this->context->language->id]);

        $helper = new Helper();

        return $helper->renderCategoryTree($root_category, $selected_cat, 'categoryBox', false, true);
    }

    public function ajaxProcessTree()
    {
        $tree = array();
        $sql = 'SELECT g.id_shop_group, g.name as group_name, s.id_shop, s.name as shop_name, u.id_shop_url, u.domain, u.physical_uri, u.virtual_uri
				FROM ' . _DB_PREFIX_ . 'shop_group g
				LEFT JOIN  ' . _DB_PREFIX_ . 'shop s ON g.id_shop_group = s.id_shop_group
				LEFT JOIN  ' . _DB_PREFIX_ . 'shop_url u ON u.id_shop = s.id_shop
				ORDER BY g.name, s.name, u.domain';
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($results as $row) {
            $id_shop_group = $row['id_shop_group'];
            $id_shop = $row['id_shop'];
            $id_shop_url = $row['id_shop_url'];

            // Group list
            if (!isset($tree[$id_shop_group])) {
                $tree[$id_shop_group] = array(
                    'data' => array(
                        'title' => '<b>' . $this->trans('Group', array(), 'Admin.Global') . '</b> ' . $row['group_name'],
                        'icon' => 'themes/' . $this->context->employee->bo_theme . '/img/tree-multishop-groups.png',
                        'attr' => array(
                            'href' => $this->context->link->getAdminLink('AdminShop') . '&id_shop_group=' . $id_shop_group,
                            'title' => $this->trans('Click here to display the shops in the %name% shop group', array('%name%' => $row['group_name']), 'Admin.Advparameters.Help'),
                        ),
                    ),
                    'attr' => array(
                        'id' => 'tree-group-' . $id_shop_group,
                    ),
                    'children' => array(),
                );
            }

            // Shop list
            if (!$id_shop) {
                continue;
            }

            if (!isset($tree[$id_shop_group]['children'][$id_shop])) {
                $tree[$id_shop_group]['children'][$id_shop] = array(
                    'data' => array(
                        'title' => $row['shop_name'],
                        'icon' => 'themes/' . $this->context->employee->bo_theme . '/img/tree-multishop-shop.png',
                        'attr' => array(
                            'href' => $this->context->link->getAdminLink('AdminShopUrl') . '&shop_id=' . (int) $id_shop,
                            'title' => $this->trans('Click here to display the URLs of the %name% shop', array('%name%' => $row['shop_name']), 'Admin.Advparameters.Help'),
                        ),
                    ),
                    'attr' => array(
                        'id' => 'tree-shop-' . $id_shop,
                    ),
                    'children' => array(),
                );
            }
            // Url list
            if (!$id_shop_url) {
                continue;
            }

            if (!isset($tree[$id_shop_group]['children'][$id_shop]['children'][$id_shop_url])) {
                $url = $row['domain'] . $row['physical_uri'] . $row['virtual_uri'];
                if (strlen($url) > 23) {
                    $url = substr($url, 0, 23) . '...';
                }

                $tree[$id_shop_group]['children'][$id_shop]['children'][$id_shop_url] = array(
                    'data' => array(
                        'title' => $url,
                        'icon' => 'themes/' . $this->context->employee->bo_theme . '/img/tree-multishop-url.png',
                        'attr' => array(
                            'href' => $this->context->link->getAdminLink('AdminShopUrl') . '&updateshop_url&id_shop_url=' . $id_shop_url,
                            'title' => $row['domain'] . $row['physical_uri'] . $row['virtual_uri'],
                        ),
                    ),
                    'attr' => array(
                        'id' => 'tree-url-' . $id_shop_url,
                    ),
                );
            }
        }

        // jstree need to have children as array and not object, so we use sort to get clean keys
        // DO NOT REMOVE this code, even if it seems really strange ;)
        sort($tree);
        foreach ($tree as &$groups) {
            sort($groups['children']);
            foreach ($groups['children'] as &$shops) {
                sort($shops['children']);
            }
        }

        $tree = array(array(
            'data' => array(
                'title' => '<b>' . $this->trans('Shop groups list', array(), 'Admin.Advparameters.Feature') . '</b>',
                'icon' => 'themes/' . $this->context->employee->bo_theme . '/img/tree-multishop-root.png',
                'attr' => array(
                    'href' => $this->context->link->getAdminLink('AdminShopGroup'),
                    'title' => $this->trans('Click here to display the list of shop groups', array(), 'Admin.Advparameters.Help'),
                ),
            ),
            'attr' => array(
                'id' => 'tree-root',
            ),
            'state' => 'open',
            'children' => $tree,
        ));

        die(json_encode($tree));
    }
}
