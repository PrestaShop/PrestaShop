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

class AdminRangeWeight extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'range_weight';
	 	$this->className = 'RangeWeight';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
				
		$this->fieldsDisplay = array(
		'id_range_weight' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'carrier_name' => array('title' => $this->l('Carrier'), 'align' => 'center', 'width' => 25, 'filter_key' => 'ca!name'),
		'delimiter1' => array('title' => $this->l('From'), 'width' => 86, 'float' => true, 'suffix' => Configuration::get('PS_WEIGHT_UNIT'), 'align' => 'right'),
		'delimiter2' => array('title' => $this->l('To'), 'width' => 86, 'float' => true,'suffix' => Configuration::get('PS_WEIGHT_UNIT'), 'align' => 'right'));
		
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.`id_carrier` = a.`id_carrier`)';
		$this->_select = 'ca.`name` AS carrier_name';
		$this->_where = 'AND ca.`deleted` = 0';
		
		parent::__construct();
	}
	
	public function displayListContent($token = NULL)
	{
		foreach ($this->_list as $key => $list)
			if ($list['carrier_name'] == '0')
				$this->_list[$key]['carrier_name'] = Configuration::get('PS_SHOP_NAME');
		parent::displayListContent($token);
	}
	
	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]) AND Tools::getValue('delimiter1') >= Tools::getValue('delimiter2'))
			$this->_errors[] = Tools::displayError('Invalid range');
		else
			parent::postProcess();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/t/AdminRangeWeight.gif" />'.$this->l('Weight ranges').'</legend>
				<label>'.$this->l('Carrier').'</label>
				<div class="margin-form">
					<select name="id_carrier">';
			$carriers = Carrier::getCarriers((int)(Configuration::get('PS_LANG_DEFAULT')), true , false,false, NULL, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
			$id_carrier = Tools::getValue('id_carrier', $obj->id_carrier);
			foreach ($carriers AS $carrier)
				echo '<option value="'.(int)($carrier['id_carrier']).'"'.(($carrier['id_carrier'] == $id_carrier) ? ' selected="selected"' : '').'>'.$carrier['name'].'</option><sup>*</sup>';
			echo '
					</select>
					<p class="clear">'.$this->l('Carrier to which this range will be applied').'</p>
				</div>
				<label>'.$this->l('From:').' </label>
				<div class="margin-form">
					<input type="text" size="4" name="delimiter1" value="'.htmlentities($this->getFieldValue($obj, 'delimiter1'), ENT_COMPAT, 'UTF-8').'" /> '.Configuration::get('PS_WEIGHT_UNIT').' <sup>*</sup>
					<p class="clear">'.$this->l('Range start (included)').'</p>
				</div>
				<label>'.$this->l('To:').' </label>
				<div class="margin-form">
					<input type="text" size="4" name="delimiter2" value="'.htmlentities($this->getFieldValue($obj, 'delimiter2'), ENT_COMPAT, 'UTF-8').'" /> '.Configuration::get('PS_WEIGHT_UNIT').' <sup>*</sup>
					<p class="clear">'.$this->l('Range end (excluded)').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}


