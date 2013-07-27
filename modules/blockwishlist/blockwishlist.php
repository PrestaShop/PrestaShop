<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockWishList extends Module
{
	const INSTALL_SQL_FILE = 'install.sql';

	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'blockwishlist';
		$this->tab = 'front_office_features';
		$this->version = 0.2;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = $this->l('Wishlist block');
		$this->description = $this->l('Adds a block containing the customer\'s wishlists.');
		$this->default_wishlist_name = $this->l('My wishlist');
	}
	
	public function install()
	{
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql AS $query)
			if($query)
				if(!Db::getInstance()->execute(trim($query)))
					return false;
		if (!parent::install() OR
						!$this->registerHook('rightColumn') OR
						!$this->registerHook('productActions') OR
						!$this->registerHook('cart') OR
						!$this->registerHook('customerAccount') OR
						!$this->registerHook('header') OR
						!$this->registerHook('adminCustomers')
					)
			return false;
		/* This hook is optional */
		$this->registerHook('displayMyAccountBlock');
		return true;
	}
	
	public function uninstall()
	{
		return (
			Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'wishlist') AND
			Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'wishlist_email') AND
			Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'wishlist_product') AND
			Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'wishlist_product_cart') AND 
			parent::uninstall()
		);
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitSettings'))
		{
			$activated = Tools::getValue('activated');
			if ($activated != 0 AND $activated != 1)
				$this->_html .= '<div class="alert error">'.$this->l('Activate module : Invalid choice.').'</div>';
			$this->_html .= '<div class="conf confirm">'.$this->l('Settings updated').'</div>';
		}
		$this->_displayForm();
		return ($this->_html);
	}

	private function _displayForm()
	{
		$this->_displayFormView();
	}
	
	private function _displayFormView()
	{
		$customers = Customer::getCustomers();
		if (!sizeof($customers))
			return;
		$id_customer = (int)(Tools::getValue('id_customer'));
		if (!$id_customer)
			$id_customer = $customers[0]['id_customer'];
		$this->_html .= '<br />
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="listing">
			<fieldset>
				<legend><img src="'.$this->_path.'img/icon/package_go.png" alt="" title="" />'.$this->l('Listing').'</legend>

				<label>'.$this->l('Customers').'</label>
				<div class="margin-form">
					<select name="id_customer" onchange="$(\'#listing\').submit();">';
		foreach ($customers as $customer)
		{
			$this->_html .= '<option value="'.(int)($customer['id_customer']).'"';
			if ($customer['id_customer'] == $id_customer)
				$this->_html .= ' selected="selected"';
			$this->_html .= '>'.htmlentities($customer['firstname'], ENT_COMPAT, 'UTF-8').' '.htmlentities($customer['lastname'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$this->_html .= '
					</select>
				</div>';
		require_once(dirname(__FILE__).'/WishList.php');
		$wishlists = WishList::getByIdCustomer($id_customer);
		if (!sizeof($wishlists))
			return ($this->_html .= '</fieldset></form>');
		$id_wishlist = false;
		foreach ($wishlists AS $row)
			if ($row['id_wishlist'] == Tools::getValue('id_wishlist'))
			{
				$id_wishlist = (int)(Tools::getValue('id_wishlist'));
				break;
			}
		if (!$id_wishlist)
			$id_wishlist = $wishlists[0]['id_wishlist'];
		$this->_html .= '
				<label>'.$this->l('Wishlists').'</label>
				<div class="margin-form">
					<select name="id_wishlist" onchange="$(\'#listing\').submit();">';
		foreach ($wishlists as $wishlist)
		{
			$this->_html .= '<option value="'.(int)($wishlist['id_wishlist']).'"';
			if ($wishlist['id_wishlist'] == $id_wishlist)
			{
				$this->_html .= ' selected="selected"';
				$counter = $wishlist['counter'];
			}
			$this->_html .= '>'.htmlentities($wishlist['name'], ENT_COMPAT, 'UTF-8').'</option>';
		}
		$this->_html .= '
					</select>
				</div>';
		$this->_displayProducts((int)($id_wishlist));
		$this->_html .= 	'</fieldset>
		</form>';
	}
	
	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'blockwishlist.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/ajax-wishlist.js');
		
		$this->smarty->assign(array('wishlist_link' => $this->context->link->getModuleLink('blockwishlist', 'mywishlist')));
	}

	public function hookRightColumn($params)
	{
		global $errors;

		require_once(dirname(__FILE__).'/WishList.php');
		if ($this->context->customer->isLogged())
		{
			$wishlists = Wishlist::getByIdCustomer($this->context->customer->id);
			if (empty($this->context->cookie->id_wishlist) === true ||
				WishList::exists($this->context->cookie->id_wishlist, $this->context->customer->id) === false)
			{
				if (!sizeof($wishlists))
					$id_wishlist = false;
				else
				{
					$id_wishlist = (int)($wishlists[0]['id_wishlist']);
					$this->context->cookie->id_wishlist = (int)($id_wishlist);
				}
			}
			else
				$id_wishlist = $this->context->cookie->id_wishlist;
			$this->smarty->assign(array(
				'id_wishlist' => $id_wishlist,
				'isLogged' => true,
				'wishlist_products' => ($id_wishlist == false ? false : WishList::getProductByIdCustomer($id_wishlist, $this->context->customer->id, $this->context->language->id, null, true)),
				'wishlists' => $wishlists,
				'ptoken' => Tools::getToken(false)));
		}
		else
			$this->smarty->assign(array('wishlist_products' => false, 'wishlists' => false));
		
		return ($this->display(__FILE__, 'blockwishlist.tpl'));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookProductActions($params)
	{
		$this->smarty->assign('id_product', (int)(Tools::getValue('id_product')));
		return ($this->display(__FILE__, 'blockwishlist-extra.tpl'));
	}
	
	public function hookCustomerAccount($params)
	{
		return $this->display(__FILE__, 'my-account.tpl');
	}

	public function hookDisplayMyAccountBlock($params)
	{
		return $this->hookCustomerAccount($params);
	}
	
	private function _displayProducts($id_wishlist)
	{
		include_once(dirname(__FILE__).'/WishList.php');
		
		$wishlist = new WishList($id_wishlist);
		$products = WishList::getProductByIdCustomer($id_wishlist, $wishlist->id_customer, $this->context->language->id);
		for ($i = 0; $i < sizeof($products); ++$i)
		{
			$obj = new Product((int)($products[$i]['id_product']), false, $this->context->language->id);
			if (!Validate::isLoadedObject($obj))
				continue;
			else
			{
				$images = $obj->getImages($this->context->language->id);
				foreach ($images AS $k => $image)
				{
					if ($image['cover'])
					{
						$products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
						break;
					}
				}
				if (!isset($products[$i]['cover']))
					$products[$i]['cover'] = $this->context->language->iso_code.'-default';
			}
		}
		$this->_html .= '
		<table class="table">
			<thead>
				<tr>
					<th class="first_item" style="width:600px;">'.$this->l('Product').'</th>
					<th class="item" style="text-align:center;width:150px;">'.$this->l('Quantity').'</th>
					<th class="item" style="text-align:center;width:150px;">'.$this->l('Priority').'</th>
				</tr>
			</thead>
			<tbody>';
			$priority = array($this->l('High'), $this->l('Medium'), $this->l('Low'));
			foreach ($products as $product)
			{
				$this->_html .= '
				<tr>
					<td class="first_item">
						<img src="'.$this->context->link->getImageLink($product['link_rewrite'], $product['cover'], 'small').'" alt="'.htmlentities($product['name'], ENT_COMPAT, 'UTF-8').'" style="float:left;" />
						'.$product['name'];
				if (isset($product['attributes_small']))
					$this->_html .= '<br /><i>'.htmlentities($product['attributes_small'], ENT_COMPAT, 'UTF-8').'</i>';
				$this->_html .= '
					</td>
					<td class="item" style="text-align:center;">'.(int)($product['quantity']).'</td>
					<td class="item" style="text-align:center;">'.$priority[(int)($product['priority']) % 3].'</td>
				</tr>';
			}
		$this->_html .= '</tbody></table>';
	}
	
	public function hookAdminCustomers($params)
	{
		require_once(dirname(__FILE__).'/WishList.php');
		
		$customer = new Customer((int)($params['id_customer']));
		if (!Validate::isLoadedObject($customer))
			die (Tools::displayError());

		$this->_html = '<h2>'.$this->l('Wishlists').'</h2>';
		
		$wishlists = WishList::getByIdCustomer((int)($customer->id));
		if (!sizeof($wishlists))
			$this->_html .= $customer->lastname.' '.$customer->firstname.' '.$this->l('No wishlist.');
		else
		{
			$this->_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" id="listing">';
	
			$id_wishlist = (int)(Tools::getValue('id_wishlist'));
			if (!$id_wishlist)
					$id_wishlist = $wishlists[0]['id_wishlist'];
			
			$this->_html .= '<span>'.$this->l('Wishlist').': </span> <select name="id_wishlist" onchange="$(\'#listing\').submit();">';
			foreach ($wishlists as $wishlist)
			{
				$this->_html .= '<option value="'.(int)($wishlist['id_wishlist']).'"';
				if ($wishlist['id_wishlist'] == $id_wishlist)
				{
					$this->_html .= ' selected="selected"';
					$counter = $wishlist['counter'];
				}
				$this->_html .= '>'.htmlentities($wishlist['name'], ENT_COMPAT, 'UTF-8').'</option>';
			}		
			$this->_html .= '</select>';
			
			$this->_displayProducts((int)($id_wishlist));
						
			$this->_html .= '</form><br />';
			
			return $this->_html;
		}
	}
	/*
	* Display Error from controler
	*/
	public function errorLogged()
	{
		return $this->l('You must be logged in to manage your wishlists.');
	}
}

