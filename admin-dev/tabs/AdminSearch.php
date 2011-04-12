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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class AdminSearch extends AdminTab
{
	/**
	* Search a specific string in the products and categories
	*
	* @params string $query String to find in the catalog
	*/
	public function searchCatalog($query)
	{
		global $cookie;
		
		$this->_list['products'] = Product::searchByName((int)$cookie->id_lang, $query);
		if (!empty($this->_list['products']))
			for ($i = 0; $i < count($this->_list['products']); $i++)
				$this->_list['products'][$i]['nameh'] = str_ireplace($query, '<span class="highlight">'.Tools::htmlentitiesUTF8($query).'</span>', $this->_list['products'][$i]['name']);

		$this->_list['categories'] = Category::searchByName((int)$cookie->id_lang, $query);
	}

	/**
	* Search a specific name in the customers
	*
	* @params string $query String to find in the catalog
	*/
	public function	searchCustomer($query)
	{
		$this->_list['customers'] = Customer::searchByName($query);
	}

	function postProcess()
	{
		global $cookie;
		
		$query = trim(Tools::getValue('bo_query'));
		$searchType = (int)Tools::getValue('bo_search_type');
		
		/* Handle empty search field */
		if (empty($query))
			$this->_errors[] = Tools::displayError('Please fill in search form first.');
		else
		{
			echo '<h2>'.$this->l('Search results').'</h2>';
			
			if (!$searchType and strlen($query) > 1)
			{
				global $_LANGADM;
				$tabs = array();
				$result = Db::getInstance()->ExecuteS('SELECT class_name, name FROM '._DB_PREFIX_.'tab t INNER JOIN '._DB_PREFIX_.'tab_lang tl ON t.id_tab = tl.id_tab AND tl.id_lang = '.(int)$cookie->id_lang);
				foreach ($result as $row)
					$tabs[$row['class_name']] = $row['name'];
				foreach (AdminTab::$tabParenting as $key => $value)
					$tabs[$key] = $tabs[$value];
				$matchingResults = array();

				foreach ($_LANGADM as $key => $value)
					if (stripos($value, $query) !== false)
					{
						$key = substr($key, 0, -32);
						if (in_array($key, array('AdminTab', 'index')))
							continue;
						if (!isset($matchingResults[$tabs[$key]]))
							$matchingResults[$tabs[$key]] = array();
						$matchingResults[$tabs[$key]][] = array('tab' => $key, 'value' => $value);
					}
				
				if (count($matchingResults))
				{
					arsort($matchingResults);
					echo '<h3>'.$this->l('Features matching your query:').' '.count($matchingResults).'</h3>
					<table class="table" cellpadding="0" cellspacing="0">';
					foreach ($matchingResults as $key => $tab)
					{
						for ($i = 0; isset($tab[$i]); ++$i)
							echo '<tr>
							<th>'.($i == 0 ? htmlentities($key, ENT_COMPAT, 'utf-8') : '&nbsp;').'</th>
							<td>
								<a href="?tab='.$tab[$i]['tab'].'&token='.Tools::getAdminTokenLite($tab[$i]['tab']).'">
									'.htmlentities(stripslashes($tab[$i]['value']), ENT_COMPAT, 'utf-8').'
								</a>
							</td>
						</tr>';
					}
					echo '</table><div class="clear">&nbsp;</div>';
				}
			}
			
			
			/* Product research */
			if (!$searchType OR $searchType == 1)
			{
				$this->fieldsDisplay['catalog'] = (array(
					'ID' => array('title' => $this->l('ID')),
					'manufacturer' => array('title' => $this->l('Manufacturer')),
					'reference' => array('title' => $this->l('Reference')),
					'name' => array('title' => $this->l('Name')),
					'price' => array('title' => $this->l('Price')),
					'tax' => array('title' => $this->l('Tax')),
					'stock' => array('title' => $this->l('Stock')),
					'weight' => array('title' => $this->l('Weight')),
					'status' => array('title' => $this->l('Status')),
					'action' => array('title' => $this->l('Actions'))
				));

				/* Handle product ID */
				if ($searchType == 1 AND (int)$query AND Validate::isUnsignedInt((int)$query))
					if ($product = new Product((int)$query) AND Validate::isLoadedObject($product))
						Tools::redirectAdmin('index.php?tab=AdminCatalog&id_product='.(int)($product->id).'&addproduct'.'&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)));

				/* Normal catalog search */
				$this->searchCatalog($query);
			}

			/* Customer */
			if (!$searchType OR $searchType == 2)
			{
				$this->fieldsDisplay['customers'] = (array(
					'ID' => array('title' => $this->l('ID')),
					'sex' => array('title' => $this->l('Sex')),
					'name' => array('title' => $this->l('Name')),
					'e-mail' => array('title' => $this->l('e-mail')),
					'birthdate' => array('title' => $this->l('Birth date')),
					'register_date' => array('title' => $this->l('Register date')),
					'orders' => array('title' => $this->l('Orders')),
					'status' => array('title' => $this->l('Status')),
					'actions' => array('title' => $this->l('Actions'))
				));

				/* Handle customer ID */
				if ($searchType AND (int)$query AND Validate::isUnsignedInt((int)$query))
					if ($customer = new Customer((int)$query) AND Validate::isLoadedObject($customer))
						Tools::redirectAdmin('index.php?tab=AdminCustomers&id_customer='.(int)($customer->id).'&viewcustomer'.'&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)));

				/* Normal customer search */
				$this->searchCustomer($query);
			}

			/* Order */
			if ($searchType == 3)
			{
				if ((int)$query AND Validate::isUnsignedInt((int)$query) AND $order = new Order((int)$query) AND Validate::isLoadedObject($order))
					Tools::redirectAdmin('index.php?tab=AdminOrders&id_order='.(int)($order->id).'&vieworder'.'&token='.Tools::getAdminToken('AdminOrders'.(int)(Tab::getIdFromClassName('AdminOrders')).(int)($cookie->id_employee)));
				$this->_errors[] = Tools::displayError('No order found with this ID:').' '.Tools::htmlentitiesUTF8($query);
			}
			
			/* Invoices */
			if ($searchType == 4)
			{
				if ((int)$query AND Validate::isUnsignedInt((int)$query) AND $invoice = Order::getInvoice((int)$query))
					Tools::redirectAdmin('pdf.php?id_order='.(int)($invoice['id_order']).'&pdf');
				$this->_errors[] = Tools::displayError('No invoice found with this ID:').' '.Tools::htmlentitiesUTF8($query);
			}

			/* Cart */
			if ($searchType == 5)
			{
				if ((int)$query AND Validate::isUnsignedInt((int)$query) AND $cart = new Cart((int)$query) AND Validate::isLoadedObject($cart))
					Tools::redirectAdmin('index.php?tab=AdminCarts&id_cart='.(int)($cart->id).'&viewcart'.'&token='.Tools::getAdminToken('AdminCarts'.(int)(Tab::getIdFromClassName('AdminCarts')).(int)($cookie->id_employee)));
				$this->_errors[] = Tools::displayError('No cart found with this ID:').' '.Tools::htmlentitiesUTF8($query);
			}
		}
	}

	public function display()
	{
		global $cookie;
		$currentIndex = 'index.php';
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$query = trim(Tools::getValue('bo_query'));
		$nbCategories = $nbProducts = $nbCustomers = 0;
		
		/* Display categories if any has been matching */
		if (isset($this->_list['categories']) AND $nbCategories = sizeof($this->_list['categories']))
		{
			echo '<h3>'.$nbCategories.' '.($nbCategories > 1 ? $this->l('categories found with') : $this->l('category found with')).' <b>"'.Tools::htmlentitiesUTF8($query).'"</b></h3>';
			echo '<table cellspacing="0" cellpadding="0" class="table">';
			$irow = 0;
			foreach ($this->_list['categories'] AS $k => $category)
				echo '<tr class="'.($irow++ % 2 ? 'alt_row' : '').'"><td>'.rtrim(getPath($currentIndex.'?tab=AdminCatalog', $category['id_category'], '', $query), ' >').'</td></tr>';
			echo '</table>
			<div class="clear">&nbsp;</div>';
		}

		/* Display products if any has been matching */
		if (isset($this->_list['products']) AND !empty($this->_list['products']) AND $nbProducts = sizeof($this->_list['products']))
		{
			echo '<h3>'.$nbProducts.' '.($nbProducts > 1 ? $this->l('products found with') : $this->l('product found with')).' <b>"'.Tools::htmlentitiesUTF8($query).'"</b></h3>
			<table class="table" cellpadding="0" cellspacing="0">
				<tr>';
			foreach ($this->fieldsDisplay['catalog'] AS $field)
				echo '<th'.(isset($field['width']) ? 'style="width: '.$field['width'].'"' : '').'>'.$field['title'].'</th>';
			echo '</tr>';
			foreach ($this->_list['products'] AS $k => $product)
			{
				echo '
				<tr>
					<td>'.$product['id_product'].'</td>
					<td align="center">'.($product['manufacturer_name'] != NULL ? stripslashes($product['manufacturer_name']) : '--').'</td>
					<td>'.$product['reference'].'</td>
					<td><a href="'.$currentIndex.'?tab=AdminCatalog&id_product='.$product['id_product'].'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">'.stripslashes($product['nameh']).'</a></td>
					<td>'.Tools::displayPrice($product['price'], $currency).'</td>
					<td>'.stripslashes($product['tax_name']).'</td>
					<td align="center">'.$product['quantity'].'</td>
					<td align="center">'.$product['weight'].' '.Configuration::get('PS_WEIGHT_UNIT').'</td>
					<td align="center"><a href="'.$currentIndex.'?tab=AdminCatalog&id_product='.$product['id_product'].'&status&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">
					<img src="../img/admin/'.($product['active'] ? 'enabled.gif' : 'forbbiden.gif').'" alt="" /></a></td>
					<td>
						<a href="'.$currentIndex.'?tab=AdminCatalog&id_product='.$product['id_product'].'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/edit.gif" alt="'.$this->l('Modify this product').'" /></a>&nbsp;
						<a href="'.$currentIndex.'?tab=AdminCatalog&id_product='.$product['id_product'].'&deleteproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'"
							onclick="return confirm(\''.$this->l('Do you want to delete this product?', __CLASS__, true, false).' ('.addslashes($product['name']).')\');">
						<img src="../img/admin/delete.gif" alt="'.$this->l('Delete this product').'" /></a>
					</td>
				</tr>';
			}
			echo '</table>
			<div class="clear">&nbsp;</div>';
		}

		/* Display customers if any has been matching */
		if (isset($this->_list['customers']) AND !empty($this->_list['customers']) AND $nbCustomers = sizeof($this->_list['customers']))
		{
			echo '<h3>'.$nbCustomers.' '.($nbCustomers > 1 ? $this->l('customers') : $this->l('customer')).' '.$this->l('found with').' <b>"'.Tools::htmlentitiesUTF8($query).'"</b></h3>
			<table cellspacing="0" cellpadding="0" class="table widthfull">
				<tr>';
			foreach ($this->fieldsDisplay['customers'] AS $field)
				echo '<th'.(isset($field['width']) ? 'style="width: '.$field['width'].'"' : '').'>'.$field['title'].'</th>';
			echo '</tr>';
			$irow = 0;
			foreach ($this->_list['customers'] AS $k => $customer)
			{
				$imgGender = $customer['id_gender'] == 1 ? '<img src="../img/admin/male.gif" alt="'.$this->l('Male').'" />' : ($customer['id_gender'] == 2 ? '<img src="../img/admin/female.gif" alt="'.$this->l('Female').'" />' : '');
				echo '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td>'.$customer['id_customer'].'</td>
					<td class="center">'.$imgGender.'</td>
					<td>'.stripslashes($customer['lastname']).' '.stripslashes($customer['firstname']).'</td>
					<td>'.stripslashes($customer['email']).'<a href="mailto:'.stripslashes($customer['email']).'"> <img src="../img/admin/email_edit.gif" alt="'.$this->l('Write to this customer').'" /></a></td>
					<td>'.Tools::displayDate($customer['birthday'], (int)($cookie->id_lang)).'</td>
					<td>'.Tools::displayDate($customer['date_add'], (int)($cookie->id_lang)).'</td>
					<td>'.Order::getCustomerNbOrders($customer['id_customer']).'</td>
					<td class="center"><img src="../img/admin/'.($customer['active'] ? 'enabled.gif' : 'forbbiden.gif').'" alt="" /></td>
					<td class="center" width="60px">
						<a href="'.$currentIndex.'?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&viewcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/details.gif" alt="'.$this->l('View orders').'" /></a>
						<a href="'.$currentIndex.'?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&addcustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/edit.gif" alt="'.$this->l('Modify this customer').'" /></a>
						<a href="'.$currentIndex.'?tab=AdminCustomers&id_customer='.$customer['id_customer'].'&deletecustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'" onclick="return confirm(\''.$this->l('Are you sure?', __CLASS__, true, false).'\');">
						<img src="../img/admin/delete.gif" alt="'.$this->l('Delete this customer').'" /></a>
					</td>
				</tr>';
			}
			echo '</table>
			<div class="clear">&nbsp;</div>';
		}
			
		/* Display error if nothing has been matching */
		if (!$nbCategories AND !$nbProducts AND !$nbCustomers)
			echo '<h3>'.$this->l('Nothing found for').' "'.Tools::htmlentitiesUTF8($query).'"</h3>';
	}
}
