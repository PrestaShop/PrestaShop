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

class AdminTracking extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'none';
	 	$this->className = 'none';

		parent::__construct();
	}

	public function display()
	{
		global $currentIndex;

		echo '<h2 class="space">'.$this->l('Catalog tracking').'</h2>';
		$this->getObjects('categories_empty');
		$this->displayCategories();
		$this->getObjects('products_disabled');
		$this->displayProducts();
		$this->getObjects('products_nostock');
		$this->displayProducts();
		$this->getObjects('attributes_nostock');
		$this->displayAttributes();
	}

	public function getObjects($type)
	{
		global $cookie;

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
				$this->_list['message'] = $this->l('List of empty categories:');
				break ;
			case 'products_disabled':
				$sql = '
					SELECT *
					FROM `'._DB_PREFIX_.'product`
					WHERE active = 0
				';
				$this->_list['message'] = $this->l('List of disabled products:');
				break ;
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
				break ;
			case 'attributes_nostock':
				$sql = '
				SELECT pa.*, ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, al.`name` AS attribute_name, a.`id_attribute`,
				m.`name` AS manufacturer_name, pl.`name` AS name, p.`weight` AS product_weight, p.`active` AS active
				FROM `'._DB_PREFIX_.'product_attribute` pa
				LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
				LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
				LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)($cookie->id_lang).')
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)($cookie->id_lang).')
				LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = pa.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang`  = '.(int)($cookie->id_lang).')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.`id_manufacturer` = m.`id_manufacturer`)
				WHERE pa.quantity <= 0
				ORDER BY pa.`id_product_attribute`
				';
				$this->_list['message'] = $this->l('List of out of stock products with attributes:');
				break ;
		}
		$this->_list['obj'] = Db::getInstance()->ExecuteS($sql);
	}

	public function displayCategories()
	{
		global $currentIndex;

		if (isset($this->_list['obj']))
		{
			$nbCategories = sizeof($this->_list['obj']);
			echo '<h3>'.$this->_list['message'].' '.$nbCategories.' '.$this->l('found').'</h3>';
			if (!$nbCategories)
				return ;
			echo '
			<table cellspacing="0" cellpadding="0" class="table">';
			$irow = 0;
			foreach ($this->_list['obj'] AS $k => $category)
				echo '<tr class="'.($irow++ % 2 ? 'alt_row' : '').'"><td>'.rtrim(getPath('index.php?tab=AdminCatalog', $category['id_category']), ' >').'</td></tr>';
			echo '</table><br /><br />';
		}
	}


	public function displayProducts()
	{
		global $currentIndex, $cookie;

		if (isset($this->_list['obj']))
		{
			$nbProducts = sizeof($this->_list['obj']);
			echo '<h3>'.$this->_list['message'].' '.$nbProducts.' '.$this->l('found').'</h3>';
			if (!$nbProducts)
				return ;
			$this->fieldsDisplay = (array(
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
			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
			echo '
			<table class="table" cellpadding="0" cellspacing="0">
				<tr>';
			foreach ($this->fieldsDisplay AS $field)
				echo '<th'.(isset($field['width']) ? 'style="width: '.$field['width'].'"' : '').'>'.$field['title'].'</th>';
			echo '
				</tr>';
			foreach ($this->_list['obj'] AS $k => $prod)
			{
				$product = new Product((int)($prod['id_product']));
				$product->name = $product->name[(int)($cookie->id_lang)];
				$taxrate = Tax::getProductTaxRate($product->id);

				echo '
				<tr>
					<td>'.$product->id.'</td>
					<td align="center">'.($product->manufacturer_name != NULL ? stripslashes($product->manufacturer_name) : '--').'</td>
					<td>'.$product->reference.'</td>
					<td><a href="index.php?tab=AdminCatalog&id_product='.$product->id.'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">'.stripslashes($product->name).'</a></td>
					<td>'.Tools::displayPrice($product->getPrice(), $currency).'</td>
					<td>'.(float)$taxrate.'% </td>
					<td align="center">'.$product->quantity.'</td>
					<td align="center">'.$product->weight.' '.Configuration::get('PS_WEIGHT_UNIT').'</td>
					<td align="center"><a href="index.php?tab=AdminCatalog&id_product='.$product->id.'&status&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'"><img src="../img/admin/'.($product->active ? 'enabled.gif' : 'disabled.gif').'" alt="" /></a></td>
					<td>
						<a href="index.php?tab=AdminCatalog&id_product='.$product->id.'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/edit.gif" alt="'.$this->l('Modify this product').'" /></a>&nbsp;
						<a href="index.php?tab=AdminCatalog&id_product='.$product->id.'&deleteproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'" onclick="return confirm(\''.addslashes($this->l('Do you want to delete').' '.$product->name).' ?\');">
						<img src="../img/admin/delete.gif" alt="'.$this->l('Delete this product').'" /></a>
					</td>
				</tr>';
			}
			echo '</table><br /><br />';
		}
	}

	public function displayAttributes()
	{
		global $currentIndex, $cookie;

		if (isset($this->_list['obj']))
		{
			$nbAttributes = sizeof($this->_list['obj']);
			echo '<h3>'.$this->_list['message'].' '.$nbAttributes.' '.$this->l('found').'</h3>';
			if (!$nbAttributes)
				return ;
			$this->fieldsDisplay = (array(
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

			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
			echo '
			<table class="table" cellpadding="0" cellspacing="0">
				<tr>';
			foreach ($this->fieldsDisplay AS $field)
				echo '<th'.(isset($field['width']) ? 'style="width: '.$field['width'].'"' : '').'>'.$field['title'].'</th>';
			echo '
				</tr>';

			$attributes = array();
			$prevAttributeId = '';
			foreach ($this->_list['obj'] AS $prod)
			{
				if ($prevAttributeId == $prod['id_product_attribute'])
					$prod['combination_name'] = $attributes[$prod['id_product_attribute']]['combination_name'].', '.$prod['group_name'].' : '.$prod['attribute_name'];
				else
					$prod['combination_name'] = $prod['group_name'].' : '.$prod['attribute_name'];

				$attributes[$prod['id_product_attribute']] = $prod;
				$prevAttributeId = $prod['id_product_attribute'];
			}

			foreach ($attributes AS $prod)
			{
				$taxrate = Tax::getProductTaxRate($prod['id_product']);

				echo '
				<tr>
					<td>'.$prod['id_product'].'</td>
					<td align="center">'.($prod['manufacturer_name'] != NULL ? stripslashes($prod['manufacturer_name']) : '--').'</td>
					<td>'.$prod['reference'].'</td>
					<td><a href="index.php?tab=AdminCatalog&id_product='.$prod['id_product'].'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">'.stripslashes($prod['name']).' ('.$prod['combination_name'].')'.'</a></td>
					<td>'.Tools::displayPrice(Product::getPriceStatic((int)($prod['id_product']), true, $prod['id_product_attribute']), $currency).'</td>
					<td>'.(float)$taxrate.'% </td>
					<td align="center">'.$prod['quantity'].'</td>
					<td align="center">'.($prod['weight'] + $prod['product_weight']).' '.Configuration::get('PS_WEIGHT_UNIT').'</td>
					<td align="center"><a href="index.php?tab=AdminCatalog&id_product='.$prod['id_product'].'&status&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'"><img src="../img/admin/'.($prod['active'] ? 'enabled.gif' : 'disabled.gif').'" alt="" /></a></td>
					<td>
						<a href="index.php?tab=AdminCatalog&id_product='.$prod['id_product'].'&addproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'">
						<img src="../img/admin/edit.gif" alt="'.$this->l('Modify this product').'" /></a>&nbsp;
						<a href="index.php?tab=AdminCatalog&id_product='.$prod['id_product'].'&deleteproduct&token='.Tools::getAdminToken('AdminCatalog'.(int)(Tab::getIdFromClassName('AdminCatalog')).(int)($cookie->id_employee)).'" onclick="return confirm(\''.addslashes($this->l('Do you want to delete').' '.$prod['name']).' ?\');">
						<img src="../img/admin/delete.gif" alt="'.$this->l('Delete this product').'" /></a>
					</td>
				</tr>';
			}
			echo '</table><br /><br />';
		}
	}
}

