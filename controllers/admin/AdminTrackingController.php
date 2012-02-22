<?php
/*
* 2007-2012 PrestaShop
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

class AdminTrackingControllerCore extends AdminController
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

		$this->list_no_link = true;

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

	public function getCustomListCategoriesEmpty()
	{
		$this->clearListOptions();
		$this->table = 'category';
		$this->lang = true;
		$this->identifier = 'id_category';
		$this->_orderBy = 'id_category';
		$this->_orderWay = 'DESC';
		self::$currentIndex = 'index.php?controller=AdminCategories';
		$this->token = Tools::getAdminTokenLite('AdminCategories');

		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('view');
		$this->addRowActionSkipList('delete', array(Category::getTopCategory()->id));
		$this->addRowActionSkipList('edit', array(Category::getTopCategory()->id));

		$this->fieldsDisplay = (array(
			'id_category' => array('title' => $this->l('ID'), 'width' => 50),
			'name' => array('title' => $this->l('Name')),
			'description' => array('title' => $this->l('Description')),
			'active' => array('title' => $this->l('Status'), 'type' => 'bool', 'active' => 'status', 'width' => 50)
		));

		$this->_filter = ' AND a.`id_category` NOT IN (
			SELECT DISTINCT(cp.id_category)
			FROM `'._DB_PREFIX_.'category_product` cp
		)
		AND a.`id_category` != '.(int)Category::getTopCategory()->id;

		$this->tpl_list_vars = array('sub_title' => $this->l('List of empty categories:'));

		return parent::renderList();
	}

	public function getCustomListProductsAttributesNoStock()
	{
		$this->clearListOptions();
		$this->table = 'product';
		$this->lang = true;
		$this->identifier = 'id_product';
		$this->_orderBy = 'id_product';
		$this->_orderWay = 'DESC';
		self::$currentIndex = 'index.php?controller=AdminProducts';
		$this->token = Tools::getAdminTokenLite('AdminProducts');
		$this->show_toolbar = false;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->fieldsDisplay = array(
			'id_product' => array('title' => $this->l('ID'), 'width' => 50),
			'reference' => array('title' => $this->l('Reference'), 'width' => 150),
			'name' => array('title' => $this->l('Name')),
			'active' => array('title' => $this->l('Status'), 'type' => 'bool', 'active' => 'status', 'width' => 50)
		);

		$this->_filter = 'AND a.id_product IN (
			SELECT p.id_product
			FROM `'._DB_PREFIX_.'product` p
			'.Product::sqlStock('p').'
			WHERE p.id_product IN (
				SELECT DISTINCT(id_product)
				FROM `'._DB_PREFIX_.'product_attribute`
			)
			AND IFNULL(stock.quantity, 0) <= 0
		)';

		$this->tpl_list_vars = array('sub_title' => $this->l('List of products with attributes and without available quantities for sale:'));

		return parent::renderList();
	}

	public function getCustomListProductsNoStock()
	{
		$this->clearListOptions();
		$this->table = 'product';
		$this->lang = true;
		$this->identifier = 'id_product';
		$this->_orderBy = 'id_product';
		$this->_orderWay = 'DESC';
		$this->show_toolbar = false;
		self::$currentIndex = 'index.php?controller=AdminProducts';
		$this->token = Tools::getAdminTokenLite('AdminProducts');

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->fieldsDisplay = array(
			'id_product' => array('title' => $this->l('ID'), 'width' => 50),
			'reference' => array('title' => $this->l('Reference'), 'width' => 150),
			'name' => array('title' => $this->l('Name')),
			'active' => array('title' => $this->l('Status'), 'type' => 'bool', 'active' => 'status', 'width' => 50)
		);

		$this->_filter = 'AND a.id_product IN (
			SELECT p.id_product
			FROM `'._DB_PREFIX_.'product` p
			'.Product::sqlStock('p').'
			WHERE p.id_product NOT IN (
				SELECT DISTINCT(id_product)
				FROM `'._DB_PREFIX_.'product_attribute`
			)
			AND IFNULL(stock.quantity, 0) <= 0
		)';

		$this->tpl_list_vars = array('sub_title' => $this->l('List of products without attributes and without available quantities for sale:'));

		return parent::renderList();
	}

	public function getCustomListProductsDisabled()
	{
		$this->clearListOptions();
		$this->table = 'product';
		$this->lang = true;
		$this->identifier = 'id_product';
		$this->_orderBy = 'id_product';
		$this->_orderWay = 'DESC';
		$this->_filter = 'AND active = 0';
		$this->list_no_filter = true;
		$this->tpl_list_vars = array('sub_title' => $this->l('List of disabled products:'));
		$this->show_toolbar = false;
		self::$currentIndex = 'index.php?controller=AdminProducts';
		$this->token = Tools::getAdminTokenLite('AdminProducts');

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->fieldsDisplay = array(
			'id_product' => array('title' => $this->l('ID'), 'width' => 50),
			'reference' => array('title' => $this->l('Reference'), 'width' => 150),
			'name' => array('title' => $this->l('Name'))
		);

		return parent::renderList();
	}

	public function clearListOptions()
	{
		$this->table = '';
		$this->actions = array();
		$this->lang = false;
		$this->identifier = '';
		$this->_orderBy = '';
		$this->_orderWay = '';
		$this->_filter = '';
		$this->_group = '';
		$this->list_no_filter = true;
		$this->list_title = $this->l('Product disabled');
	}
}

