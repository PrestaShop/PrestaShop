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

class AdminStates extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'state';
	 	$this->className = 'State';
	 	$this->edit = true;
	 	$this->delete = true;

		$this->fieldsDisplay = array(
		'id_state' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Name'), 'width' => 140, 'filter_key' => 'a!name'),
		'iso_code' => array('title' => $this->l('ISO code'), 'align' => 'center', 'width' => 50),
		'zone' => array('title' => $this->l('Zone'), 'width' => 100, 'filter_key' => 'z!name'));
		$this->_select = 'z.`name` AS zone';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = a.`id_zone`)';

		parent::__construct();
	}

	public function postProcess()
	{
		if (!isset($this->table)) return false;

		/* Delete object */
		if (isset($_GET['delete'.$this->table]))
		{
			global $currentIndex;

			// set token
			$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;

			// Sub included tab postProcessing
			$this->includeSubTab('postProcess', array('submitAdd1', 'submitDel', 'delete', 'submitFilter', 'submitReset'));

			if ($this->tabAccess['delete'] === '1')
			{

					if (Validate::isLoadedObject($object = $this->loadObject()) AND isset($this->fieldImageSettings))
					{
							if (!$object->isUsed())
							{
								// check if request at least one object with noZeroObject
								if (isset($object->noZeroObject) AND sizeof($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
									$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
								else
								{
									$this->deleteImage($object->id);
									if ($this->deleted)
									{
										$object->deleted = 1;
										if ($object->update()) Tools::redirectAdmin($currentIndex.'&conf=1&token='.$token);
									}
									else if ($object->delete())
									{
										Tools::redirectAdmin($currentIndex.'&conf=1&token='.$token);
									}
									$this->_errors[] = Tools::displayError('An error occurred during deletion.');
								}
							} else {
								$this->_errors[] = Tools::displayError('This state is currently in use');
							}
					}
					else
						$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		} else {
			parent::postProcess();
		}
	}

	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();

		if (!($obj = $this->loadObject(true)))
			return;

		echo '
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/world.gif" />'.$this->l('States').'</legend>
				<label>'.$this->l('Name:').' </label>
				<div class="margin-form">
					<input type="text" size="30" maxlength="32" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<p class="clear">'.$this->l('State name to display in addresses and on invoices').'</p>
				</div>
				<label>'.$this->l('ISO code:').' </label>
				<div class="margin-form">
					<input type="text" size="5" maxlength="4" name="iso_code" value="'.htmlentities($this->getFieldValue($obj, 'iso_code'), ENT_COMPAT, 'UTF-8').'" style="text-transform: uppercase;" /> <sup>*</sup>
					<p>'.$this->l('1 to 4 letter ISO code (search on Wikipedia if you don\'t know)').'</p>
				</div>
				<label>'.$this->l('Country:').' </label>
				<div class="margin-form">
					<select name="id_country">';
				$countries = Country::getCountries((int)($cookie->id_lang), false, true);
				foreach ($countries AS $country)
					echo '<option value="'.(int)($country['id_country']).'"'.(($this->getFieldValue($obj, 'id_country') == $country['id_country']) ? ' selected="selected"' : '').'>'.$country['name'].'</option>';
				echo '
					</select>
					<p>'.$this->l('Country where state, region or city is located').'</p>
				</div>
				<label>'.$this->l('Zone:').' </label>
				<div class="margin-form">
					<select name="id_zone">';

		$zones = Zone::getZones();
		foreach ($zones AS $zone)
			echo '<option value="'.(int)($zone['id_zone']).'"'.(($this->getFieldValue($obj, 'id_zone') == $zone['id_zone']) ? ' selected="selected"' : '').'>'.$zone['name'].'</option>';

		echo '
					</select>
					<p>'.$this->l('Geographical zone where this state is located').'<br />'.$this->l('Used for shipping').'</p>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'active')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.((!$this->getFieldValue($obj, 'active') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enabled or disabled').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}

