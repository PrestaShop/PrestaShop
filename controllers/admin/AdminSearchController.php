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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminSearchControllerCore extends AdminController
{
	public function postProcess()
	{
		$this->context = Context::getContext();
		$this->query = trim(Tools::getValue('bo_query'));
		$searchType = (int)Tools::getValue('bo_search_type');
		/* Handle empty search field */
		if (empty($this->query))
		{
			$this->_errors[] = Tools::displayError('Please fill in search form first.');
			return;
		}
		else
		{
			if (!$searchType and strlen($this->query) > 1)
				$this->searchFeatures();

			/* Product research */
			if (!$searchType OR $searchType == 1)
			{
				/* Handle product ID */
				if ($searchType == 1 AND (int)$this->query AND Validate::isUnsignedInt((int)$this->query))
					if ($product = new Product((int)$this->query) AND Validate::isLoadedObject($product))
						Tools::redirectAdmin('index.php?tab=AdminCatalog&id_product='.(int)($product->id).'&addproduct'.'&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)$this->context->employee->id));

				/* Normal catalog search */
				$this->searchCatalog();
			}

			/* Customer */
			if (!$searchType OR $searchType == 2 OR $searchType == 6)
			{
				if (!$searchType OR $searchType == 2)
				{
					/* Handle customer ID */
					if ($searchType AND (int)$this->query AND Validate::isUnsignedInt((int)$this->query))
						if ($customer = new Customer((int)$this->query) AND Validate::isLoadedObject($customer))
							Tools::redirectAdmin('index.php?tab=AdminCustomers&id_customer='.(int)($customer->id).'&viewcustomer'.'&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id));

					/* Normal customer search */
					$this->searchCustomer();
				}

				if ($searchType == 6)
					$this->searchIP();
			}

			/* Order */
			if ($searchType == 3)
			{
				if ((int)$this->query AND Validate::isUnsignedInt((int)$this->query) AND $order = new Order((int)$this->query) AND Validate::isLoadedObject($order))
					Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)($order->id).'&vieworder'.'&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)$this->context->employee->id));
				$this->_errors[] = Tools::displayError('No order found with this ID:').' '.Tools::htmlentitiesUTF8($this->query);
			}

			/* Invoices */
			if ($searchType == 4)
			{
				if ((int)$this->query AND Validate::isUnsignedInt((int)$this->query) AND $invoice = Order::getInvoice((int)$this->query))
					Tools::redirectAdmin('pdf.php?id_order='.(int)($invoice['id_order']).'&pdf');
				$this->_errors[] = Tools::displayError('No invoice found with this ID:').' '.Tools::htmlentitiesUTF8($this->query);
			}

			/* Cart */
			if ($searchType == 5)
			{
				if ((int)$this->query AND Validate::isUnsignedInt((int)$this->query) AND $cart = new Cart((int)$this->query) AND Validate::isLoadedObject($cart))
					Tools::redirectAdmin('index.php?tab=AdminCarts&id_cart='.(int)($cart->id).'&viewcart'.'&token='.Tools::getAdminToken('AdminCarts'.(int)(Tab::getIdFromClassName('AdminCarts')).(int)$this->context->employee->id));
				$this->_errors[] = Tools::displayError('No cart found with this ID:').' '.Tools::htmlentitiesUTF8($this->query);
			}
			/* IP */
			// 6 - but it is included in the customer block
		}
		$this->display = 'view';
	}


	public function searchIP()
	{
		if (!ip2long(trim($this->query)))
		{
			$this->_errors[] = Tools::displayError('It seems that this is not an IP address :').' '.Tools::htmlentitiesUTF8($this->query);
			return;
		}
		$this->_list['customers'] = Customer::searchByIp($this->query);
	}

	/**
	* Search a specific string in the products and categories
	*
	* @params string $query String to find in the catalog
	*/
	public function searchCatalog()
	{
		$this->context = Context::getContext();
		$this->_list['products'] = Product::searchByName($this->context->language->id, $this->query);
		$this->_list['categories'] = Category::searchByName($this->context->language->id, $this->query);
	}

	/**
	* Search a specific name in the customers
	*
	* @params string $query String to find in the catalog
	*/
	public function searchCustomer()
	{
		$this->_list['customers'] = Customer::searchByName($this->query);
	}

	/**
	* Search a feature in all store
	*
	* @params string $query String to find in the catalog
	*/
	public function searchFeatures()
	{
		global $_LANGADM;
		$tabs = array();
		$key_match = array();
		$result = Db::getInstance()->executeS('SELECT class_name, name FROM '._DB_PREFIX_.'tab t INNER JOIN '._DB_PREFIX_.'tab_lang tl ON t.id_tab = tl.id_tab AND tl.id_lang = '.(int)$this->context->language->id);
		foreach ($result as $row)
		{
			$tabs[strtolower($row['class_name'])] = $row['name'];
			$key_match[strtolower($row['class_name'])] = $row['class_name'];
		}
		foreach (AdminTab::$tabParenting as $key => $value)
		{
			$tabs[strtolower($key)] = $tabs[strtolower($value)];
			$key_match[strtolower($key)] = $key;
		}
		$this->_list['features'] = array();

		foreach ($_LANGADM as $key => $value)
		{
			if (stripos($value, $this->query) !== false)
			{
				$key = substr($key, 0, -32);
				if (in_array($key, array('AdminTab', 'index')))
					continue;
				// if class name doesn't exists, just ignore it
				if (!isset($tabs[$key]))
					continue;
				if (!isset($this->_list['features'][$tabs[$key]]))
					$this->_list['features'][$tabs[$key]] = array();
				$this->_list['features'][$tabs[$key]][] = array('link' => Context::getContext()->link->getAdminLink($key_match[$key]), 'value' => Tools::safeOutput($value));
			}
		}

		if (!count($this->_list['features']))
			$this->_list['features'] = false;
		else
			$this->_list['features'];
	}

	protected function initCustomerList()
	{
		$genders_icon = array('default' => 'unknown.gif');
		$genders = array(0 => $this->l('?'));
		foreach (Gender::getGenders() as $gender)
		{
			$genders_icon[$gender->id] = '../genders/'.(int)$gender->id.'.jpg';
			$genders[$gender->id] = $gender->name;
		}
		$this->fieldsDisplay['customers'] = (array(
			'id_customer' => array('title' => $this->l('ID'), 'align' => 'center'),
			'id_gender' => array('title' => $this->l('Gender'), 'align' => 'center', 'icon' => $genders_icon, 'list' => $genders),
			'firstname' => array('title' => $this->l('Name'), 'align' => 'center'),
			'lastname' => array('title' => $this->l('Name'), 'align' => 'center'),
			'email' => array('title' => $this->l('E-mail address'), 'align' => 'center'),
			'birthday' => array('title' => $this->l('Birth date'), 'align' => 'center', 'type' => 'date'),
			'date_add' => array('title' => $this->l('Register date'), 'align' => 'center', 'type' => 'date'),
			'orders' => array('title' => $this->l('Orders'), 'align' => 'center'),
			'active' => array('title' => $this->l('Enabled'),'align' => 'center','active' => 'status','type' => 'bool'),
		));
	}

	protected function initProductList()
	{
		$this->show_toolbar = false;
		$this->fieldsDisplay['products'] = (array(
			'id_product' => array('title' => $this->l('ID')),
			'manufacturer_name' => array('title' => $this->l('Manufacturer'), 'align' => 'center'),
			'reference' => array('title' => $this->l('Reference'), 'align' => 'center'),
			'name' => array('title' => $this->l('Name')),
			'price_tax_excl' => array('title' => $this->l('Price tax excl'), 'align' => 'right', 'type' => 'price'),
			'price_tax_incl' => array('title' => $this->l('Price tax incl'), 'align' => 'right', 'type' => 'price'),
			'status' => array('title' => $this->l('Status'), 'align' => 'center'),
		));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin('highlight');
	}

	// Override because we don't want any buttons
	public function initToolbar()
	{
	}

	public function initToolbarTitle()
	{
		$this->toolbar_title = $this->l('Search results');
	}

	public function renderView()
	{
		$this->tpl_view_vars['query'] = $this->query;
		$this->tpl_view_vars['show_toolbar'] = true;

		if (sizeof($this->_errors))
			return parent::renderView();
		else
		{
			$helper = new HelperList();
			$helper->currentIndex = self::$currentIndex;
			$helper->token = $this->token;
			$helper->shopLinkType = '';
			$helper->simple_header = true;
			if (isset($this->_list['features']))
				$this->tpl_view_vars['features'] = $this->_list['features'];
			if (isset($this->_list['categories']))
			{
				$categories = array();
				foreach($this->_list['categories'] as $category)
					$categories[] = getPath(self::$currentIndex.'?tab=AdminCatalog', (int)$category['id_category']);
				$this->tpl_view_vars['categories'] = $categories;
			}
			if (isset($this->_list['products']))
			{
				$view = '';
				$this->initProductList();
				$helper->identifier = 'id_product';
				$helper->actions = array('edit', 'view');
				$helper->show_toolbar = false;
				if ($this->_list['products'])
					$view = $helper->generateList($this->_list['products'], $this->fieldsDisplay['products']);

				$this->tpl_view_vars['products'] =  $view;
			}
			if (isset($this->_list['customers']))
			{
				$view = '';
				$this->initCustomerList();
				$helper->identifier = 'id_customer';
				$helper->actions = array('edit', 'view');
				if ($this->_list['customers'])
				{
					foreach($this->_list['customers'] as $key => $val)
						$this->_list['customers'][$key]['orders'] = Order::getCustomerNbOrders((int)$val['id_customer']);
					$view = $helper->generateList($this->_list['customers'], $this->fieldsDisplay['customers']);
				}
				$this->tpl_view_vars['customers'] =  $view;
			}
			return parent::renderView();
		}
	}
}
