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


class AdminGroupShop extends AdminTab
{

	public function __construct()
	{
	 	$this->table = 'group_shop';
	 	$this->className = 'GroupShop';
	 	$this->edit = true;
		$this->delete = false;
		$this->deleted = false;
		
		$this->fieldsDisplay = array(
		'id_group_shop' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('County'), 'width' => 130, 'filter_key' => 'b!name'),
		'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'active'));

		parent::__construct();
	}
	
	public function postProcess()
	{
		if (Tools::isSubmit('delete'.$this->table) OR Tools::isSubmit('status') OR Tools::isSubmit('status'.$this->table))
		{
			$object = $this->loadObject();
			if(GroupShop::getTotalGroupShops() == 1)
				$this->_errors[] = Tools::displayError('You cannot delete or disable the last groupshop.');
			elseif($object->haveShops())
				$this->_errors[] = Tools::displayError('You cannot delete or disable a groupshop which have this shops using it.');
			
			if (sizeof($this->_errors))
				return false;
		}
		return parent::postProcess();
	}
		
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm($isMainTab);
		
		if (!($obj = $this->loadObject(true)))
			return;
		if (Shop::getTotalShops() > 1 AND $obj->id)
			$disabled = 'disabled="disabled"';
		else
			$disabled = '';
		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend>'.$this->l('GroupShop').'</legend><span class="hint" name="help_box" style="display:block;">'.$this->l('You can\'t edit GroupShop when you have more than one Shop').'</span><br />
				<label for="name">'.$this->l('GroupShop name').'</label>
				<div class="margin-form">
					<input type="text" name="name" id="name" value="'.$this->getFieldValue($obj, 'name').'" />
				</div>
				<label for="share_datas">'.$this->l('Share datas').'</label>
				<div class="margin-form">
					<input type="radio" name="share_datas" '.$disabled.' id="share_datas_on" value="1" '.($this->getFieldValue($obj, 'share_datas') ? 'checked="checked" ' : '').'/>
					<label class="t" for="share_datas_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="share_datas" '.$disabled.' id="share_datas_off" value="0" '.(!$this->getFieldValue($obj, 'share_datas') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Share customers, orders and carts between shops of this group').'</p>
				</div>
				<label for="share_stock">'.$this->l('Share stock').'</label>
				<div class="margin-form">
					<input type="radio" name="share_stock" '.$disabled.' id="share_stock_on" value="1" '.($this->getFieldValue($obj, 'share_stock') ? 'checked="checked" ' : '').'/>
					<label class="t" for="share_stock_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="share_stock" '.$disabled.' id="share_datas_off" value="0" '.(!$this->getFieldValue($obj, 'share_stock') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Sare stock between shops of this group').'</p>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enable or disable shop').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}
