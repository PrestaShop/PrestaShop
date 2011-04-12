<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
  
class StatsStock extends Module
{
    function __construct()
    {
        $this->name = 'statsstock';
        $this->tab = 'analytics_stats';
        $this->version = 1.0;
		$this->author = 'PrestaShop';

        parent::__construct();
		
        $this->displayName = $this->l('Stock stats');
        $this->description = '';
    }

	public function install()
	{
		return (parent::install() && $this->registerHook('AdminStatsModules'));
	}
	
    function hookAdminStatsModules()
    {
		global $cookie, $currentIndex;
		
		if (Tools::isSubmit('submitCategory'))
			$cookie->statsstock_id_category = Tools::getValue('statsstock_id_category');
		
		$ru = $currentIndex.'&module='.$this->name.'&token='.Tools::getValue('token');
		$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
		$filter = ((int)$cookie->statsstock_id_category ? 'WHERE p.id_product IN (SELECT cp.id_product FROM '._DB_PREFIX_.'category_product cp WHERE cp.id_category = '.(int)$cookie->statsstock_id_category.')' : '');
		$products = Db::getInstance()->ExecuteS('
		SELECT p.id_product, p.reference, pl.name,
			IFNULL((
				SELECT AVG(pa.wholesale_price)
				FROM '._DB_PREFIX_.'product_attribute pa WHERE p.id_product = pa.id_product
				AND wholesale_price != 0
			), p.wholesale_price) as wholesale_price,
			IFNULL((
				SELECT SUM(pa.quantity)
				FROM '._DB_PREFIX_.'product_attribute pa WHERE p.id_product = pa.id_product
			), p.quantity) as quantity,
			IFNULL((
				SELECT SUM(IF(pa.wholesale_price > 0, pa.wholesale_price, p.wholesale_price) * pa.quantity)
				FROM '._DB_PREFIX_.'product_attribute pa WHERE p.id_product = pa.id_product
			), p.wholesale_price * p.quantity) as stockvalue
		FROM '._DB_PREFIX_.'product p
		INNER JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = '.(int)$cookie->id_lang.')
		'.$filter);
		
		echo '
		<script type="text/javascript">$(\'#calendar\').slideToggle();</script>
		<h2 style="float:left">'.$this->l('Stock value').'</h2>
		<form action="'.$ru.'" method="post" style="float:right">
			<input type="hidden" name="submitCategory" value="1" />
			'.$this->l('Category').' : <select name="statsstock_id_category" onchange="this.form.submit();">
				<option value="0">-- '.$this->l('All').' --</option>';
		foreach (Category::getSimpleCategories($cookie->id_lang) as $category)
			echo '<option value="'.(int)$category['id_category'].'" '.($cookie->statsstock_id_category == $category['id_category'] ? 'selected="selected"' : '').'>'.$category['name'].'</option>';
		echo '	</select>
		</form>
		<div style="clear:both">&nbsp;</div>';
		
		if (!count($products))
			echo $this->l('Your catalog is empty.');
		else
		{
			$rollup = array('quantity' => 0, 'wholesale_price' => 0, 'stockvalue' => 0);
			echo '<table class="table" cellspacing="0" cellpadding="0">
			<tr>
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Ref.').'</th>
				<th style="width:350px">'.$this->l('Item').'</th>
				<th>'.$this->l('Stock').'</th>
				<th>'.$this->l('Price*').'</th>
				<th>'.$this->l('Value').'</th>
			</tr>';
			foreach ($products as $product)
			{
				$rollup['quantity'] += $product['quantity'];
				$rollup['wholesale_price'] += $product['wholesale_price'];
				$rollup['stockvalue'] += $product['stockvalue'];
				echo '<tr>
					<td>'.$product['id_product'].'</td>
					<td>'.$product['reference'].'</td>
					<td>'.$product['name'].'</td>
					<td>'.$product['quantity'].'</td>
					<td>'.Tools::displayPrice($product['wholesale_price'], $currency).'</td>
					<td>'.Tools::displayPrice($product['stockvalue'], $currency).'</td>
				</tr>';
			}
			echo '
				<tr>
					<th colspan="3"></th>
					<th>'.$this->l('Total stock').'</th>
					<th>'.$this->l('Avg price').'</th>
					<th>'.$this->l('Total value').'</th>
				</tr>
				<tr>
					<td colspan="3"></td>
					<td>'.$rollup['quantity'].'</td>
					<td>'.Tools::displayPrice($rollup['wholesale_price'] / count($products), $currency).'</td>
					<td>'.Tools::displayPrice($rollup['stockvalue'], $currency).'</td>
				</tr>
			</table>
			<p>* '.$this->l('Average price when the product has attributes.').'</p>';
		}
    }
}

