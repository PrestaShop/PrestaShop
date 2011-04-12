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

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminCurrencies extends AdminTab
{
	public function __construct()
	{
	 	$this->table = 'currency';
	 	$this->className = 'Currency';
	 	$this->lang = false;
	 	$this->edit = true;
		$this->delete = true;

		$this->fieldsDisplay = array(
		'id_currency' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Currency'), 'width' => 100),
		'iso_code' => array('title' => $this->l('ISO code'), 'align' => 'center', 'width' => 35),
		'iso_code_num' => array('title' => $this->l('ISO code num'), 'align' => 'center', 'width' => 35),
		'sign' => array('title' => $this->l('Symbol'), 'width' => 20, 'align' => 'center', 'orderby' => false, 'search' => false),
		'conversion_rate' => array('title' => $this->l('Conversion rate'), 'float' => true, 'align' => 'center', 'width' => 50, 'search' => false),
		'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false));

		$this->optionTitle = $this->l('Currencies options');
		$this->_fieldsOptions = array(
			'PS_CURRENCY_DEFAULT' => array(
				'title' => $this->l('Default currency:'),
				'desc' => $this->l('The default currency used in shop')
					.'<div class=warn"><img src="../img/admin/warn2.png" />'.$this->l('If you change default currency, you will have to manually edit every product price.').'</div>',
					'cast' => 'intval',
					'type' => 'select',
					'identifier' => 'id_currency',
					'list' => Currency::getCurrencies()
				),
		);
		$this->_where = 'AND a.`deleted` = 0';

		parent::__construct();
	}

	public function postProcess()
	{
		global $currentIndex;

		if (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
		 	{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->id == Configuration::get('PS_CURRENCY_DEFAULT'))
						$this->_errors[] = $this->l('You can\'t delete the default currency');
					elseif ($object->delete())
						Tools::redirectAdmin($currentIndex.'&conf=1'.'&token='.$this->token);
					else
						$this->_errors[] = Tools::displayError('An error occurred during deletion.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		elseif ((isset($_GET['status'.$this->table]) OR isset($_GET['status'])) AND Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->active AND $object->id == Configuration::get('PS_CURRENCY_DEFAULT'))
						$this->_errors[] = $this->l('You can\'t disable the default currency');
					elseif ($object->toggleStatus())
						Tools::redirectAdmin($currentIndex.'&conf=5'.((($id_category = (int)(Tools::getValue('id_category'))) AND Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$this->token);
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::getValue('submitOptions'.$this->table))
		{
			foreach ($this->_fieldsOptions as $key => $field)
			{
				Configuration::updateValue($key, $field['cast'](Tools::getValue($key)));
				if ($key == 'PS_CURRENCY_DEFAULT')
					Currency::refreshCurrencies();
			}
			Tools::redirectAdmin($currentIndex.'&conf=6'.'&token='.$this->token);
		}
		elseif (Tools::isSubmit('submitExchangesRates'))
		{
			if (!$this->_errors[] = Currency::refreshCurrencies())
				Tools::redirectAdmin($currentIndex.'&conf=6'.'&token='.$this->token);
		}
		else
			parent::postProcess();
	}

	public function displayOptionsList()
	{
		global	$currentIndex;

		parent::displayOptionsList();
		echo '<br /><br />
		<form action="'.$currentIndex.'&token='.$this->token.'" method="post">
			<fieldset>
			<legend><img src="../img/admin/exchangesrate.gif" />'.$this->l('Currency rates').'</legend>
			<label>'.$this->l('Update currency rates').'</label>
				<div class="margin-form">
					<p>'.$this->l('Update your currencies exchanges rates with a real-time tool').'</p>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('Update currency rates').'" name="submitExchangesRates" class="button" />
				</div>
			</fieldset>
		</form>';
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
			<fieldset><legend><img src="../img/admin/money.gif" />'.$this->l('Currencies').'</legend>
				<label>'.$this->l('Currency:').' </label>
				<div class="margin-form">
					<input type="text" size="30" maxlength="32" name="name" value="'.htmlentities($this->getFieldValue($obj, 'name'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Only letters and the minus character are allowed').'<span class="hint-pointer">&nbsp;</span></span>
					<p class="clear">'.$this->l('Will appear on Front Office, e.g., euro, dollar').'...</p>
				</div>
				<label>'.$this->l('ISO code:').' </label>
				<div class="margin-form">
					<input type="text" size="30" maxlength="32" name="iso_code" value="'.htmlentities($this->getFieldValue($obj, 'iso_code'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint-pointer">&nbsp;</span>
					<p class="clear">'.$this->l('ISO code, e.g., USD for dollar, EUR for euro').'...</p>
				</div>
				<label>'.$this->l('Numeric ISO code:').' </label>
				<div class="margin-form">
					<input type="text" size="30" maxlength="32" name="iso_code_num" value="'.htmlentities($this->getFieldValue($obj, 'iso_code_num'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint-pointer">&nbsp;</span>
					<p class="clear">'.$this->l('Numeric ISO code, e.g., 840 for dollar, 978 for euro').'...</p>
				</div>
				<label>'.$this->l('Symbol:').' </label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="8" name="sign" value="'.htmlentities($this->getFieldValue($obj, 'sign'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Will appear on Front Office, e.g., &euro;, $').'...</p>
				</div>
				<label>'.$this->l('Conversion rate:').' </label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="11" name="conversion_rate" value="'.htmlentities($this->getFieldValue($obj, 'conversion_rate')).'" /> <sup>*</sup>
					<p class="clear">'.$this->l('Conversion rate from one unit of your shop\'s default currency (for example, 1€) to this currency. For example, if the default currency is euros and this currency is dollars, type \'1.20\'').' 1&euro; = $1.20</p>
				</div>
				<label>'.$this->l('Formatting:').' </label>
				<div class="margin-form">
					<select name="format">';
				$currency_formats = array(
					1 => 'X0,000.00 ('.$this->l('as with dollars').')',
					2 => '0 000,00X ('.$this->l('as with euros').')',
					3 => 'X0.000,00',
					4 => '0,000.00X',
				);
				foreach ($currency_formats AS $nb => $desc)
					echo '<option value="'.$nb.'"'.($this->getFieldValue($obj, 'format') == $nb ? 'selected="selected"' : '').'>'.$desc.'</option>';
				echo '
					</select>
					<p class="clear">'.$this->l('Applies to all prices, e.g.,').' $1,240.15</p>
				</div>
				<label>'.$this->l('Decimals:').' </label>
				<div class="margin-form">
					<input type="radio" name="decimals" id="decimals_on" value="1" '.($this->getFieldValue($obj, 'decimals') ? 'checked="checked" ' : '').'/>
					<label class="t" for="decimals_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" /></label>
					<input type="radio" name="decimals" id="decimals_off" value="0" '.(!$this->getFieldValue($obj, 'decimals') ? 'checked="checked" ' : '').'/>
					<label class="t" for="decimals_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" /></label>
					<p>'.$this->l('Display decimals on prices').'</p>
				</div>
				<label>'.$this->l('Blank:').' </label>
				<div class="margin-form">
					<input type="radio" name="blank" id="blank_on" value="1" '.($this->getFieldValue($obj, 'blank') ? 'checked="checked" ' : '').'/>
					<label class="t" for="blank_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="blank" id="blank_off" value="0" '.(!$this->getFieldValue($obj, 'blank') ? 'checked="checked" ' : '').'/>
					<label class="t" for="blank_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Include a blank between sign and price, e.g.,').'<br />$1,240.15 -> $ 1,240.15</p>
				</div>
				<label>'.$this->l('Enable:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.($this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.(!$this->getFieldValue($obj, 'active') ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
				</div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}

