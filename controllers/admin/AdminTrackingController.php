<?php
/*
* 2007-2011 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminTrackingController extends AdminController
{
	public function postprocess()
	{
		if (Tools::getValue('id_product') && Tools::isSubmit('statusproduct'))
		{
			$this->table = 'product';
			$this->identifier = 'id_product';
			$this->action = 'status';
			$this->className = 'Product';
		}
		else if (Tools::getValue('id_category') && Tools::isSubmit('statuscategory'))
		{
			$this->table = 'category';
			$this->identifier = 'id_category';
			$this->action = 'status';
			$this->className = 'Category';
		}

		parent::postprocess();
	}

	public function initContent()
	{
		$methods = get_class_methods($this);
		$tpl_vars['arrayList'] = array();
		foreach ($methods as $method_name)
			if (preg_match('#getCustomList(.+)#', $method_name, $matches))
				$this->content .= call_user_func(array($this,$matches[0]));

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function getObjects($type)
	{
		switch ($type)
		{
			case 'categories_empty':
				$sql = '
					SELECT id_category
					FROM `'._DB_PREFIX_.'category`
					WHERE id_category NOT IN (
					  SELECT DISTINCT(id_category)
					  FROM `'._DB_PREFIX_.'category_product`
					)
				';
				break;
			case 'products_disabled':
				$sql = '
					SELECT *
					FROM `'._DB_PREFIX_.'product`
					WHERE active = 0
				';
				$this->_list['message'] = $this->l('List of disabled products:');
				break;

			case 'products_nostock':
				$sql = '
					SELECT DISTINCT(id_product)
					FROM `'._DB_PREFIX_.'product`
					WHERE id_product IN (
					  SELECT id_product
					  FROM `'._DB_PREFIX_.'product`
					  WHERE id_product NOT IN (
						SELECT DISTINCT(id_product)
						FROM `'._DB_PREFIX_.'product_attribute`
					  )
					  AND quantity <= 0
					)
				';
				$this->_list['message'] = $this->l('List of out of stock products without attributes:');
				break;

			case 'attributes_nostock':
				$sql = 'SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`,
							m.`name` AS manufacturer_name, pl.`name` AS name, p.`weight` AS product_weight, p.`active` AS active
						FROM `'._DB_PREFIX_.'product_attribute` pa
						LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
							ON pac.`id_product_attribute` = pa.`id_product_attribute`
						LEFT JOIN `'._DB_PREFIX_.'attribute` a
							ON a.`id_attribute` = pac.`id_attribute`
						LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag
							ON ag.`id_attribute_group` = a.`id_attribute_group`
						LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al
							ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$this->context->language->id.')
						LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
							ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$this->context->language->id.')
						LEFT JOIN `'._DB_PREFIX_.'product` p
							ON (p.`id_product` = pa.`id_product`)
						LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
							ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int)$this->context->language->id.$this->context->shop->addSqlRestrictionOnLang('pl').')
						'.Product::sqlStock('p', 'pa').'
						LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
							ON (p.`id_manufacturer` = m.`id_manufacturer`)
						WHERE stock.quantity <= 0
						ORDER BY pa.`id_product_attribute`';
				$this->_list['message'] = $this->l('List of out of stock products with attributes:');
				break;
		}

		return Db::getInstance()->executeS($sql);
	}

	public function getCustomListCategoriesEmpty()
	{
		$this->clearListOptions();
		$this->table = 'category';
		$this->lang = true;
		$this->identifier = 'id_category';
		$this->_defaultOrderBy = 'id_category';
		$this->_defaultOrderWay = 'DESC';
		self::$currentIndex = 'index.php?controller=AdminCategories';
		$this->token = Tools::getAdminTokenLite('AdminCategories');

		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('view');

		$this->fieldsDisplay = (array(
			'id_category' => array('title' => $this->l('ID')),
			'name' => array('title' => $this->l('Name')),
			'description' => array('title' => $this->l('Description')),
			'active' => array('title' => $this->l('Status'), 'type' => 'bool', 'active' => 'status')
		));

		$this->_filter = ' AND a.id_category NOT IN (
			SELECT DISTINCT(cp.id_category)
			FROM `'._DB_PREFIX_.'category_product` cp
		)';

		$this->getObjects('categories_empty');
		$this->tpl_list_vars = array('sub_title' => $this->l('List of empty categories:'));

		return parent::initList();
	}

	public function getCustomListProductsAttributesNoStock()
	{
		$this->clearListOptions();
		$this->table = 'product';
		$this->lang = true;
		$this->identifier = 'id_product';
		$this->_defaultOrderBy = 'id_product';
		$this->_defaultOrderWay = 'DESC';
		self::$currentIndex = 'index.php?controller=AdminProducts';
		$this->token = Tools::getAdminTokenLite('AdminProducts');
		$this->show_toolbar = false;
		$this->fieldsDisplay = array(
			'ID' => array('title' => $this->l('ID')),
			'manufacturer' => array('title' => $this->l('Manufacturer')),
			'reference' => array('title' => $this->l('Reference')),
			'name' => array('title' => $this->l('Name')),
			'price' => array('title' => $this->l('Price')),
			'tax' => array('title' => $this->l('Tax')),
			'stock' => array('title' => $this->l('Stock')),
			'weight' => array('title' => $this->l('Weight')),
			'active' => array('title' => $this->l('Status'), 'type' => 'bool', 'active' => 'status')
		);

		$this->_filter = 'AND a.id_product IN (
			SELECT id_product
			FROM `'._DB_PREFIX_.'product`
			WHERE id_product IN (
				SELECT DISTINCT(id_product)
				FROM `'._DB_PREFIX_.'product_attribute`
			)
			AND quantity <= 0
		)';

		$this->tpl_list_vars = array('sub_title' => $this->l('List of out of stock products without attributes:'));

		return parent::initList();
	}

	public function getCustomListProductsNoStock()
	{
		$this->clearListOptions();
		$this->table = 'product';
		$this->lang = true;
		$this->identifier = 'id_product';
		$this->_defaultOrderBy = 'id_product';
		$this->_defaultOrderWay = 'DESC';
		$this->show_toolbar = false;
		self::$currentIndex = 'index.php?controller=AdminProducts';
		$this->token = Tools::getAdminTokenLite('AdminProducts');

		$this->fieldsDisplay = array(
			'ID' => array('title' => $this->l('ID')),
			'manufacturer' => array('title' => $this->l('Manufacturer')),
			'reference' => array('title' => $this->l('Reference')),
			'name' => array('title' => $this->l('Name')),
			'price' => array('title' => $this->l('Price')),
			'tax' => array('title' => $this->l('Tax')),
			'stock' => array('title' => $this->l('Stock')),
			'weight' => array('title' => $this->l('Weight')),
			'active' => array('title' => $this->l('Status'), 'type' => 'bool', 'active' => 'status')
		);

		$this->_filter = 'AND a.id_product IN (
			SELECT id_product
			FROM `'._DB_PREFIX_.'product`
			WHERE id_product NOT IN (
				SELECT DISTINCT(id_product)
				FROM `'._DB_PREFIX_.'product_attribute`
			)
			AND quantity <= 0
		)';

		$this->tpl_list_vars = array('sub_title' => $this->l('List of out of stock products with attributes:'));

		return parent::initList();
	}

	public function getCustomListProductsDisabled()
	{
		$this->clearListOptions();
		$this->table = 'product';
		$this->lang = true;
		$this->identifier = 'id_product';
		$this->_defaultOrderBy = 'id_product';
		$this->_defaultOrderWay = 'DESC';
		$this->_filter = 'AND active = 0';
		$this->list_no_filter = true;
		$this->tpl_list_vars = array('sub_title' => $this->l('List of disabled products:'));
		$this->show_toolbar = false;
		self::$currentIndex = 'index.php?controller=AdminProducts';
		$this->token = Tools::getAdminTokenLite('AdminProducts');

		return parent::initList();
	}

	public function clearListOptions()
	{
		$this->table = '';
		$this->lang = false;
		$this->identifier = '';
		$this->_defaultOrderBy = '';
		$this->_defaultOrderWay = '';
		$this->_filter = '';
		$this->list_no_filter = true;
		$this->list_title = $this->l('Product disabled');
	}
}

