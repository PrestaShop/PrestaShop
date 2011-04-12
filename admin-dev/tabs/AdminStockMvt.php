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

class AdminStockMvt extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'stock_mvt';
	 	$this->className = 'StockMvt';
	 	$this->edit = false;
		$this->delete = false;
		$this->view = true;
				
		$this->fieldsDisplay = array(
		'id_stock_mvt' => array('title' => $this->l('ID'), 'width' => 40),
		'product_name' => array('title' => $this->l('Product Name'), 'width' => 250, 'havingFilter' => true),
		'quantity' => array('title' => $this->l('Quantity'), 'width' => 40),
		'reason' => array('title' => $this->l('Reason'), 'width' => 250),
		'id_order' => array('title' => $this->l('ID Order'), 'width' => 40),
		'employee' => array('title' => $this->l('Employee'), 'width' => 100, 'havingFilter' => true),
		);
		
		global $cookie;
		
		$this->_select = 'CONCAT(pl.name, \' \', GROUP_CONCAT(IFNULL(al.name, \'\'), \'\')) product_name, CONCAT(e.lastname, \' \', e.firstname) employee, mrl.name reason';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (a.id_product = pl.id_product AND pl.id_lang = '.(int)$cookie->id_lang.')
							LEFT JOIN `'._DB_PREFIX_.'stock_mvt_reason_lang` mrl ON (a.id_stock_mvt_reason = mrl.id_stock_mvt_reason AND mrl.id_lang = '.(int)$cookie->id_lang.')
							LEFT JOIN `'._DB_PREFIX_.'employee` e ON (e.id_employee = a.id_employee)
							LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.id_product_attribute = a.id_product_attribute)
							LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (al.id_attribute = pac.id_attribute AND al.id_lang = '.(int)$cookie->id_lang.')';
		$this->_group = 'GROUP BY a.id_stock_mvt';
		parent::__construct();
	}

	public function postProcess()
	{
		global $cookie;
		if (Tools::isSubmit('rebuildStock'))
			StockMvt::addMissingMvt((int)$cookie->id_employee, false);
		return parent::postProcess();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;
		$dl = 'name';
		echo '<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'&addstock_mvt_reason" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/search.gif" />'.$this->l('Stock Movement').'</legend>
				<label>'.$this->l('Name:').'</label>
				<div class="margin-form">';
		foreach ($this->_languages as $language)
			echo '<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="40" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, $dl, 'name');
		echo '</div>
				<div class="clear space">&nbsp;</div>
				<label>'.$this->l('Action:').'</label>
				<div class="margin-form">
					<select name="sign">
						<option value="1">'.$this->l('Increase stock').'</option>
						<option value="-1">'.$this->l('Decrease stock').'</option>
					</select>
				</div>
				<div class="clear space">&nbsp;</div>';
		echo 	'<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
			</fieldset>
		</form>';
	}
	
	public function viewstock_mvt()
	{
		global $cookie;
		
		$stockMvt = new StockMvt((int)Tools::getValue('id_stock_mvt'));
		$product = new Product((int)$stockMvt->id_product, true,  (int)$cookie->id_lang);
		$movements = $product->getStockMvts((int)$cookie->id_lang);

			echo '<h2>'.$this->l('Stock Movements for').' '.$product->name.'</h2>
			<table cellspacing="0" cellpadding="0" class="table widthfull">
				<tr>
					<th>'.$this->l('ID').'</th>
					<th>'.$this->l('Product Name').'</th>
					<th>'.$this->l('Quantity').'</th>
					<th>'.$this->l('Reason').'</th>
					<th>'.$this->l('Employee').'</th>
					<th>'.$this->l('Order').'</th>
					<th>'.$this->l('Date').'</th>
				</tr>';
			$irow = 0;
			foreach ($movements AS $k => $mvt)
			{
				echo '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td>'.$mvt['id_stock_mvt'].'</td>
					<td>'.$mvt['product_name'].'</td>
					<td>'.$mvt['quantity'].'</td>
					<td>'.$mvt['reason'].'</td>
					<td>'.$mvt['employee'].'</td>
					<td>#'.$mvt['id_order'].'</td>
					<td>'.Tools::displayDate($mvt['date_add'], (int)($cookie->id_lang)).'</td>
				</tr>';
			}
			echo '</table>';
	}
	
	public function display()
	{
		global $currentIndex, $cookie;
		
		$old_post = false;
		
		if (!isset($_GET['addstock_mvt_reason']) OR (Tools::isSubmit('submitAddstock_mvt_reason') AND Tools::getValue('id_stock_mvt_reason')))
		{
			if (isset($_POST))
			{
				$old_post = $_POST;
			}
			echo '<h2>'.$this->l('Stock movement history').'</h2>';
			parent::display();
			if (!isset($_GET['view'.$this->table]))
				echo '
				<fieldset>
					<form method="post" action="'.$currentIndex.'&token='.$this->token.'&rebuildMvt=1">
						<label for="stock_rebuild">'.$this->l('Calculate the movement of inventory missing').'</label>
						<div class="margin-form">
							<input class="button" type="submit" name="rebuildStock" value="'.$this->l('Submit').'" />
						</div>
					</form>
				</fieldset><br />';
		}
		if (isset($_GET['view'.$this->table]))
			return;
		if ($old_post)
			$_POST = $old_post;

	 	$this->table = 'stock_mvt_reason';
	 	$this->className = 'StockMvtReason';
	 	$this->identifier = 'id_stock_mvt_reason';
	 	$this->edit = true;
		$this->delete = true;
		$this->lang = true;
		$this->add = true;
		$this->view = false;
		$this->_listSkipDelete = array(1,2,3,4);
		

		$this->_defaultOrderBy = $this->identifier;
		$this->fieldsDisplay = array('id_stock_mvt_reason' => array('title' => $this->l('ID'), 'width' => 40),
												'sign' => array('title' => $this->l('Sign'), 'width' => 15, 'align' => 'center', 'type' => 'select',  'icon' => array(-1 => 'arrow_down.png', 1 => 'arrow_up.png'), 'orderby' => false),
												'name' => array('title' => $this->l('Name'), 'width' => 500));
		
		$reasons = StockMvtReason::getStockMvtReasons((int)$cookie->id_lang);
		$this->_fieldsOptions = array('PS_STOCK_MVT_REASON_DEFAULT' => array('title' => $this->l('Default Stock Movement reason:'), 
												'cast' => 'intval', 
												'type' => 'select', 
												'list' => $reasons, 
												'identifier' => 'id_stock_mvt_reason'));
		
		unset($this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter);
		
		echo '<h2>'.$this->l('Stock movement reason').'</h2>';
		$this->postProcess();
		return parent::display();
	}
}


