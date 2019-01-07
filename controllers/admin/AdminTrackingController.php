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
 * @property Product|Category $object
 */
class AdminTrackingControllerCore extends AdminController
{
    public $bootstrap = true;

    /** @var HelperList */
    protected $_helper_list;

    public function postprocess()
    {
        if (Tools::getValue('id_product') && Tools::isSubmit('statusproduct')) {
            $this->table = 'product';
            $this->identifier = 'id_product';
            $this->action = 'status';
            $this->className = 'Product';
        } elseif (Tools::getValue('id_category') && Tools::isSubmit('statuscategory')) {
            $this->table = 'category';
            $this->identifier = 'id_category';
            $this->action = 'status';
            $this->className = 'Category';
        }

        $this->list_no_link = true;

        parent::postprocess();
    }

    public function initContent()
    {
        if ($id_category = Tools::getValue('id_category') && Tools::getIsset('viewcategory')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminProducts') . '&id_category=' . (int) $id_category . '&viewcategory');
        }

        $this->_helper_list = new HelperList();

        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            $this->warnings[] = $this->trans('List of products without available quantities for sale are not displayed because stock management is disabled.', array(), 'Admin.Catalog.Notification');
        }

        $methods = get_class_methods($this);
        $tpl_vars['arrayList'] = array();
        foreach ($methods as $method_name) {
            if (preg_match('#getCustomList(.+)#', $method_name, $matches)) {
                $this->clearListOptions();
                $this->content .= call_user_func(array($this, $matches[0]));
            }
        }
        $this->context->smarty->assign(array(
            'content' => $this->content,
        ));
    }

    public function getCustomListCategoriesEmpty()
    {
        $this->table = 'category';
        $this->list_id = 'empty_categories';
        $this->lang = true;
        $this->className = 'Category';
        $this->identifier = 'id_category';
        $this->_orderBy = 'id_category';
        $this->_orderWay = 'DESC';
        $this->_list_index = 'index.php?controller=AdminCategories';
        $this->_list_token = Tools::getAdminTokenLite('AdminCategories');

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->addRowActionSkipList('delete', array((int) Configuration::get('PS_ROOT_CATEGORY')));
        $this->addRowActionSkipList('edit', array((int) Configuration::get('PS_ROOT_CATEGORY')));

        $this->fields_list = (array(
            'id_category' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'b!name'),
            'description' => array('title' => $this->trans('Description', array(), 'Admin.Global'), 'callback' => 'getDescriptionClean'),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'type' => 'bool', 'active' => 'status', 'align' => 'center', 'class' => 'fixed-width-xs'),
        ));
        $this->clearFilters();

        $this->_join = Shop::addSqlAssociation('category', 'a');
        $this->_filter = ' AND NOT EXISTS (
			SELECT 1
			FROM `' . _DB_PREFIX_ . 'category_product` cp
			WHERE a.`id_category` = cp.id_category
		)
		AND a.`id_category` != ' . (int) Configuration::get('PS_ROOT_CATEGORY');
        $this->toolbar_title = $this->trans('List of empty categories:', array(), 'Admin.Catalog.Feature');

        return $this->renderList();
    }

    public function getCustomListProductsAttributesNoStock()
    {
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            return;
        }

        $this->table = 'product';
        $this->list_id = 'no_stock_products_attributes';
        $this->lang = true;
        $this->identifier = 'id_product';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';
        $this->className = 'Product';
        $this->_list_index = 'index.php?controller=AdminProducts';
        $this->_list_token = Tools::getAdminTokenLite('AdminProducts');
        $this->show_toolbar = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_product' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'reference' => array('title' => $this->trans('Reference', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'b!name'),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'type' => 'bool', 'active' => 'status', 'align' => 'center', 'class' => 'fixed-width-xs', 'filter_key' => 'a!active'),
        );

        $this->clearFilters();

        $this->_join = Shop::addSqlAssociation('product', 'a');
        $this->_filter = 'AND EXISTS (
			SELECT 1
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Product::sqlStock('p') . '
			WHERE a.id_product = p.id_product AND EXISTS (
				SELECT 1
				FROM `' . _DB_PREFIX_ . 'product_attribute` WHERE `' . _DB_PREFIX_ . 'product_attribute`.id_product = p.id_product
			)
			AND IFNULL(stock.quantity, 0) <= 0
		)';
        $this->toolbar_title = $this->trans('List of products with combinations but without available quantities for sale:', array(), 'Admin.Catalog.Feature');

        return $this->renderList();
    }

    public function getCustomListProductsNoStock()
    {
        if (!Configuration::get('PS_STOCK_MANAGEMENT')) {
            return;
        }

        $this->table = 'product';
        $this->list_id = 'no_stock_products';
        $this->className = 'Product';
        $this->lang = true;
        $this->identifier = 'id_product';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';
        $this->show_toolbar = false;
        $this->_list_index = 'index.php?controller=AdminProducts';
        $this->_list_token = Tools::getAdminTokenLite('AdminProducts');

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_product' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'reference' => array('title' => $this->trans('Reference', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global')),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'type' => 'bool', 'active' => 'status', 'align' => 'center', 'class' => 'fixed-width-xs', 'filter_key' => 'a!active'),
        );
        $this->clearFilters();

        $this->_join = Shop::addSqlAssociation('product', 'a');
        $this->_filter = 'AND EXISTS (
			SELECT 1
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Product::sqlStock('p') . '
			WHERE a.id_product = p.id_product AND NOT EXISTS (
				SELECT 1
				FROM `' . _DB_PREFIX_ . 'product_attribute` pa WHERE pa.id_product = p.id_product
			)
			AND IFNULL(stock.quantity, 0) <= 0
		)';

        $this->toolbar_title = $this->trans('List of products without combinations and without available quantities for sale:', array(), 'Admin.Catalog.Feature');

        return $this->renderList();
    }

    public function getCustomListProductsDisabled()
    {
        $this->table = 'product';
        $this->list_id = 'disabled_products';
        $this->className = 'Product';
        $this->lang = true;
        $this->identifier = 'id_product';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';
        $this->_filter = 'AND product_shop.`active` = 0';
        $this->show_toolbar = false;
        $this->_list_index = 'index.php?controller=AdminProducts';
        $this->_list_token = Tools::getAdminTokenLite('AdminProducts');

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->fields_list = array(
            'id_product' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'reference' => array('title' => $this->trans('Reference', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'b!name'),
        );

        $this->clearFilters();

        $this->_join = Shop::addSqlAssociation('product', 'a');
        $this->toolbar_title = $this->trans('List of disabled products', array(), 'Admin.Catalog.Feature');

        return $this->renderList();
    }

    public function getCustomListProductsWithoutPhoto()
    {
        $this->table = 'product';
        $this->list_id = 'products_without_photo';
        $this->lang = true;
        $this->identifier = 'id_product';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';
        $this->className = 'Product';
        $this->_list_index = 'index.php?controller=AdminProducts';
        $this->_list_token = Tools::getAdminTokenLite('AdminProducts');
        $this->show_toolbar = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->fields_list = array(
            'id_product' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'reference' => array('title' => $this->trans('Reference', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'b!name'),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'type' => 'bool', 'active' => 'status', 'align' => 'center', 'class' => 'fixed-width-xs'),
        );
        $this->clearFilters();
        $this->_join = Shop::addSqlAssociation('product', 'a');
        $this->_filter = 'AND NOT EXISTS (
			SELECT 1
			FROM `' . _DB_PREFIX_ . 'image` img
			WHERE a.id_product = img.id_product
		)';
        $this->toolbar_title = $this->trans('List of products without images', array(), 'Admin.Catalog.Feature');

        return $this->renderList(true);
    }

    public function getCustomListProductsWithoutDescription()
    {
        $this->table = 'product';
        $this->list_id = 'products_without_description';
        $this->lang = true;
        $this->identifier = 'id_product';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';
        $this->className = 'Product';
        $this->_list_index = 'index.php?controller=AdminProducts';
        $this->_list_token = Tools::getAdminTokenLite('AdminProducts');
        $this->show_toolbar = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->fields_list = array(
            'id_product' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'reference' => array('title' => $this->trans('Reference', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'b!name'),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'type' => 'bool', 'active' => 'status', 'align' => 'center', 'class' => 'fixed-width-xs'),
        );
        $this->clearFilters();
        $defaultLanguage = new Language(Configuration::get('PS_LANG_DEFAULT'));
        $this->_join = Shop::addSqlAssociation('product', 'a');
        $this->_filter = 'AND EXISTS (
			SELECT 1
			FROM `' . _DB_PREFIX_ . 'product_lang` pl
			WHERE
			a.id_product = pl.id_product AND
			pl.id_lang = ' . (int) $defaultLanguage->id . ' AND
			pl.id_shop = ' . (int) $this->context->shop->id . ' AND
			description = "" AND description_short = ""
		)';
        $this->toolbar_title = $this->trans('List of products without description', array(), 'Admin.Catalog.Feature');

        return $this->renderList(true);
    }

    public function getCustomListProductsWithoutPrice()
    {
        $this->table = 'product';
        $this->list_id = 'products_without_price';
        $this->lang = true;
        $this->identifier = 'id_product';
        $this->_orderBy = 'id_product';
        $this->_orderWay = 'DESC';
        $this->className = 'Product';
        $this->_list_index = 'index.php?controller=AdminProducts';
        $this->_list_token = Tools::getAdminTokenLite('AdminProducts');
        $this->show_toolbar = false;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->fields_list = array(
            'id_product' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'class' => 'fixed-width-xs', 'align' => 'center'),
            'reference' => array('title' => $this->trans('Reference', array(), 'Admin.Global')),
            'name' => array('title' => $this->trans('Name', array(), 'Admin.Global'), 'filter_key' => 'b!name'),
            'active' => array('title' => $this->trans('Status', array(), 'Admin.Global'), 'type' => 'bool', 'active' => 'status', 'align' => 'center', 'class' => 'fixed-width-xs'),
        );
        $this->clearFilters();
        $this->_join = Shop::addSqlAssociation('product', 'a');
        $this->_filter = ' AND a.price = "0.000000" AND a.wholesale_price = "0.000000" AND NOT EXISTS (
			SELECT 1
			FROM `' . _DB_PREFIX_ . 'specific_price` sp
			WHERE a.id_product = sp.id_product
		)';
        $this->toolbar_title = $this->trans('List of products without price', array(), 'Admin.Catalog.Feature');

        return $this->renderList();
    }

    public function renderList($withPagination = false)
    {
        $paginationLimit = 20;
        $this->processFilter();

        if (!($this->fields_list && is_array($this->fields_list))) {
            return false;
        }
        $this->getList($this->context->language->id, null, null, 0, $withPagination ? $paginationLimit : null);

        $helper = new HelperList();

        // Empty list is ok
        if (!is_array($this->_list)) {
            $this->displayWarning($this->trans('Bad SQL query', array(), 'Admin.Notifications.Error') . '<br />' . htmlspecialchars($this->_list_error));

            return false;
        }

        $this->setHelperDisplay($helper);
        if ($withPagination) {
            $helper->_default_pagination = $paginationLimit;
            $helper->_pagination = $this->_pagination;
        }
        $helper->tpl_vars = $this->tpl_list_vars;
        $helper->tpl_delete_link_vars = $this->tpl_delete_link_vars;

        // For compatibility reasons, we have to check standard actions in class attributes
        foreach ($this->actions_available as $action) {
            if (!in_array($action, $this->actions) && isset($this->$action) && $this->$action) {
                $this->actions[] = $action;
            }
        }
        $helper->is_cms = $this->is_cms;
        $list = $helper->generateList($this->_list, $this->fields_list);

        return $list;
    }

    public function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null)
    {
        $this->_helper_list->currentIndex = $this->_list_index;
        $this->_helper_list->identifier = $this->identifier;
        $this->_helper_list->table = $this->table;

        return $this->_helper_list->displayEnableLink($this->_list_token, $id, $value, $active, $id_category, $id_product);
    }

    public function displayDeleteLink($token, $id, $name = null)
    {
        $this->_helper_list->currentIndex = $this->_list_index;
        $this->_helper_list->identifier = $this->identifier;
        $this->_helper_list->table = $this->table;

        return $this->_helper_list->displayDeleteLink($this->_list_token, $id, $name);
    }

    public function displayEditLink($token, $id, $name = null)
    {
        $this->_helper_list->currentIndex = $this->_list_index;
        $this->_helper_list->identifier = $this->identifier;
        $this->_helper_list->table = $this->table;

        return $this->_helper_list->displayEditLink($this->_list_token, $id, $name);
    }

    protected function clearFilters()
    {
        if (Tools::isSubmit('submitResetempty_categories')) {
            $this->processResetFilters('empty_categories');
        }

        if (Tools::isSubmit('submitResetno_stock_products_attributes')) {
            $this->processResetFilters('no_stock_products_attributes');
        }

        if (Tools::isSubmit('submitResetno_stock_products')) {
            $this->processResetFilters('no_stock_products');
        }

        if (Tools::isSubmit('submitResetdisabled_products')) {
            $this->processResetFilters('disabled_products');
        }

        if (Tools::isSubmit('submitResetproducts_without_photo')) {
            $this->processResetFilters('products_without_photo');
        }
        if (Tools::isSubmit('submitResetproducts_without_description')) {
            $this->processResetFilters('products_without_description');
        }
        if (Tools::isSubmit('submitResetproducts_without_price')) {
            $this->processResetFilters('products_without_price');
        }
    }

    public function clearListOptions()
    {
        $this->table = '';
        $this->actions = array();
        $this->list_skip_actions = array();
        $this->lang = false;
        $this->identifier = '';
        $this->_orderBy = '';
        $this->_orderWay = '';
        $this->_filter = '';
        $this->_group = '';
        $this->_where = '';
        $this->list_title = $this->trans('Product disabled', array(), 'Admin.Catalog.Feature');
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, Context::getContext()->shop->id);
    }

    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }
}
