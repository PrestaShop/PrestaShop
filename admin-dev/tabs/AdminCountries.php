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
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');

class AdminCountries extends AdminTab
{
	public function __construct()
	{
		global $cookie;

	 	$this->table = 'country';
	 	$this->className = 'Country';
	 	$this->lang = true;
	 	$this->edit = true;
		$this->deleted = false;
	 	$this->_select = 'z.`name` AS zone';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = a.`id_zone`)';

		$this->fieldsDisplay = array(
		'id_country' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'name' => array('title' => $this->l('Country'), 'width' => 130, 'filter_key' => 'b!name'),
		'iso_code' => array('title' => $this->l('ISO code'), 'width' => 70, 'align' => 'center'),
		'call_prefix' => array('title' => $this->l('Call prefix'), 'width' => 40, 'align' => 'center', 'callback' => 'displayCallPrefix'),
		'zone' => array('title' => $this->l('Zone'), 'width' => 100, 'filter_key' => 'z!name'),
		'a!active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'a!active'));

		$this->optionTitle = $this->l('Countries options');
		$this->_fieldsOptions = array('PS_COUNTRY_DEFAULT' => array('title' => $this->l('Default country:'), 'desc' => $this->l('The default country used in shop'), 'cast' => 'intval', 'type' => 'select', 'identifier' => 'id_country', 'list' => Country::getCountries((int)($cookie->id_lang))));
		parent::__construct();
	}

	public function postProcess()
	{
		if (isset($_GET['delete'.$this->table]) OR Tools::getValue('submitDel'.$this->table))
			$this->_errors[] = Tools::displayError('You cannot delete a country. If you do not want it available for customers, please disable it.');
		else
		{
			if (Tools::getValue('submitAdd'.$this->table))
			{
				$id_country = Tools::getValue('id_country');
				$tmp_addr_format = new AddressFormat($id_country);

				$save_status = false;

				$is_new = is_null($tmp_addr_format->id_country);
				if ($is_new)
				{
					$tmp_addr_format = new AddressFormat();
					$tmp_addr_format->id_country = $id_country;
				}
				
				$tmp_addr_format->format = Tools::getValue('address_layout');
				
				if (strlen($tmp_addr_format->format) > 0)
				{
					if ($tmp_addr_format->checkFormatFields())
						$save_status = ($is_new) ? $tmp_addr_format->save(): $tmp_addr_format->update();
					else
					{
						$errorList = $tmp_addr_format->getErrorList();
						foreach($errorList as $numError => $error)
							$this->_errors[] = $error;
					}
						

					if (!$save_status)
						$this->_errors[] = Tools::displayError('Invalid address layout'.Db::getInstance()->getMsgError());
				}
				unset($tmp_addr_format);
			}

			return parent::postProcess();
		}
	}

	private function _displayValidFields()
	{
		$html = '<ul>';
		$appendContainer = '';
		
		$objectList = AddressFormat::getLiableClass('Address');
		$objectList['Address'] = NULL;
		
		// Get the available properties for each class
		foreach($objectList as $className => &$object)
		{
			$fields = array();

			$html .= '<li>
				<a href="javascript:void(0);" onClick="displayAvailableFields(\''.$className.'\')">'.$className.'</a>';
			foreach(AddressFormat::getValidateFields($className) as $name)
				$fields[] = '<a style="color:#4B8;" href="javascript:void(0);" class="addPattern" id="'.$className.':'.$name.'">
					'.$name.'</a>';
			$html .= '
				<div class="availableFieldsList" id="availableListFieldsFor_'.$className.'" style="width:300px;">
				'.implode(', ', $fields).'</div></li>';
			unset($object);
		}
		return $html .= '</ul>';
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		$defaultLayout = '';
		
		$defaultLayoutTab = array(
			array('firstname', 'lastname'),
			array('company'),
			array('vat_number'),
			array('address1'),
			array('address2'),
			array('postcode', 'city'),
			array('Country:name'),
			array('phone'));
			
		if (!($obj = $this->loadObject(true)))
			return;
			
		foreach ($defaultLayoutTab as $line)
			$defaultLayout .= implode(' ', $line)."\r\n";

		echo '
		<script type="text/javascript" language="javascript" src="'._PS_JS_DIR_.'jquery/jquery-fieldselection.js"></script>
		<script type="text/javascript" language="javascript">
			
			lastLayoutModified = "";
			
			$(document).ready(function()
			{
				$(".availableFieldsList").css("display", "none");
				$(".addPattern").click(function()
				{
					addFieldsToCursorPosition($(this).attr("id"))
					lastLayoutModified = $("#ordered_fields").val();
				});
				$("#ordered_fields").keyup(function()
				{
					lastLayoutModified = $(this).val();
				});
				$("#useLastDefaultLayout").mouseover(function()
				{
					switchExplanationText("'.$this->l('Will display back your last registered layout').'");
				});
				$("#useDefaultLayoutSystem").mouseover(function()
				{
					switchExplanationText("'.$this->l('Will display a default layout for this country').'");
				});
				$("#useCurrentLastModifiedLayout").mouseover(function()
				{
					switchExplanationText("'.$this->l('Will display back you\'re current editing layout').'");
				});
				$("#eraseCurrentLayout").mouseover(function()
				{
					switchExplanationText("'.$this->l('Will delete the current layout').'");
				});
				
			});
			
			function  switchExplanationText(text)
			{
				$("#explanationText").fadeOut("fast", function()
				{
					$(this).html(text);
					$(this).fadeIn("fast");
				});
			}
			
			function addFieldsToCursorPosition(pattern)
			{
				$("#ordered_fields").replaceSelection(pattern + " ");
			}
			
			function displayAvailableFields(containerName)
			{
				$(".availableFieldsList").each( function (){
					if ($(this).attr(\'id\') != \'availableListFieldsFor_\'+containerName)
						$(this).slideUp();
				});
				$("#availableListFieldsFor_" + containerName).slideToggle();
			}
			
			function resetLayout(defaultLayout, type)
			{
				if (confirm("'.$this->l('Are you sure to apply this selection ?').'"))
				{
				$("#ordered_fields").val(unescape(defaultLayout.replace(/\+/g, " ")));
			}
			}
			
		</script>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset><legend><img src="../img/admin/world.gif" />'.$this->l('Countries').'</legend>
				<label>'.$this->l('Country:').' </label>
				<div class="margin-form">';

				foreach ($this->_languages as $language)
					echo '
					<div id="name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $this->_defaultFormLanguage ? 'block' : 'none').'; float: left;">
						<input size="30" type="text" name="name_'.$language['id_lang'].'" value="'.htmlentities($this->getFieldValue($obj, 'name', (int)($language['id_lang'])), ENT_COMPAT, 'UTF-8').'" /><sup> *</sup>
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
					</div>';
		$this->displayFlags($this->_languages, $this->_defaultFormLanguage, 'name', 'name');
		echo '		<p style="clear: both">'.$this->l('Name of country').'</p>
				</div>
				<label>'.$this->l('ISO code:').' </label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="3" name="iso_code" value="'.htmlentities($this->getFieldValue($obj, 'iso_code'), ENT_COMPAT, 'UTF-8').'" style="text-transform: uppercase;" /> <sup>*</sup>
					<p>'.$this->l('2- or 3-letter ISO code, e.g., FR for France').'. <a href="http://www.iso.org/iso/en/prods-services/iso3166ma/02iso-3166-code-lists/list-en1.html" target="_blank">'.$this->l('Official list here').'</a>.</p>
				</div>
				<label>'.$this->l('Call prefix:').' </label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="3" name="call_prefix" value="'.(int)($this->getFieldValue($obj, 'call_prefix')).'" style="text-transform: uppercase;" /> <sup>*</sup>
					<p>'.$this->l('International call prefix, e.g., 33 for France.').'.</p>
				</div>
				<label>'.$this->l('Default currency:').' </label>
				<div class="margin-form">
					<select name="id_currency">
						<option value="0" '.(Tools::getValue('id_currency', $obj->id_currency) == 0 ? 'selected' : '').'>'.$this->l('Default store currency').'</option>
		';
		$currencies = Currency::getCurrencies();
		foreach ($currencies AS $currency)
			echo '<option value="'.intval($currency['id_currency']).'" '.(Tools::getValue('id_currency', $obj->id_currency) == $currency['id_currency'] ? 'selected' : '').'>'.Tools::htmlentitiesUTF8($currency['name']).'</option>';
		echo '
					</select>
				</div>
				<label>'.$this->l('Zone:').' </label>
				<div class="margin-form">
					<select name="id_zone">';
		$zones = Zone::getZones();
		foreach ($zones AS $zone)
			echo '		<option value="'.(int)($zone['id_zone']).'"'.(($this->getFieldValue($obj, 'id_zone') == $zone['id_zone']) ? ' selected="selected"' : '').'>'.$zone['name'].'</option>';
		$address_layout = AddressFormat::getAddressCountryFormat($obj->id);
		if ($value = Tools::getValue('address_layout'))
			$address_layout = $value;
			
		echo '		</select>
					<p>'.$this->l('Geographical zone where country is located').'</p>
				</div>
				<label>'.$this->l('Need zip code:').' </label>
				<div class="margin-form">
					<input type="radio" name="need_zip_code" id="need_zip_code_on" value="1" onchange="disableZipFormat();" '.((!$obj->id OR $this->getFieldValue($obj, 'need_zip_code')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="need_zip_code_on"> <img src="../img/admin/enabled.gif" alt="" title="'.$this->l('Yes').'" /></label>
					<input type="radio" name="need_zip_code" id="need_zip_code_off" value="0" onchange="disableZipFormat();" '.((!$this->getFieldValue($obj, 'need_zip_code') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="need_zip_code_off"> <img src="../img/admin/disabled.gif" alt="" title="'.$this->l('No').'" /></label>
				</div>
				<label class="zip_code_format">'.$this->l('Zip code format:').' </label>
				<div class="margin-form zip_code_format">
					<input type="text" name="zip_code_format" id="zip_code_format" value="'.$this->getFieldValue($obj, 'zip_code_format').'" onkeyup="$(\'#zip_code_format\').val($(\'#zip_code_format\').val().toUpperCase());" /> <sup>*</sup>
					<p>'.$this->l('National zip code (L for a letter, N for a number and C for the Iso code), e.g., NNNNN for France. No verification if undefined').'.</p>
				</div>
				<label class="address_layout">'.$this->l('Address layout:').' </label>
				<div class="margin-form" style="vertical-align: top;">
					<div style="float:left">
						<textarea id="ordered_fields" name="address_layout" style="width: 300px;height: 140px;">'.$address_layout.'</textarea>
					</div>
					<div style="float:left; margin-left:20px; width:340px;">
						'.$this->l('Liable fields for the address information (click to have more details)').': '.$this->_displayValidFields().'
					</div>
					<div class="clear"></div>
					<div style="margin:10px 0 10px 0;">
						<a id="useLastDefaultLayout" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout(\''.urlencode($address_layout).'\', \'lastDefault\');" class="button">'.
							$this->l('Use the last registered layout').'</a>
						<a id="useDefaultLayoutSystem" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout(\''.urlencode($defaultLayout).'\', \'defaultSystem\');" class="button">'.
							$this->l('Use a default layout').'</a>
						<a id="useCurrentLastModifiedLayout" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout(lastLayoutModified, \'currentModified\')" class="button">'.
							$this->l('Use my current modified layout').'</a>
						<a id="eraseCurrentLayout" style="margin-left:5px;" href="javascript:void(0)" onClick="resetLayout(\'\', \'erase\');" class="button">'.
							$this->l('Clean layout').'</a>
						<div style="margin-top:10px; padding-top:5px; height:10px;" id="explanationText"></div>
					</div>
				</div>
				<label>'.$this->l('Status:').' </label>
				<div class="margin-form">
					<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'active')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="" title="'.$this->l('Enabled').'" /></label>
					<input type="radio" name="active" id="active_off" value="0" '.((!$this->getFieldValue($obj, 'active') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="" title="'.$this->l('Disabled').'" /></label>
					<p>'.$this->l('Enabled or disabled').'</p>
				</div>
				<label>'.$this->l('Contains states:').' </label>
				<div class="margin-form">
					<input type="radio" name="contains_states" id="contains_states_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'contains_states')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="contains_states_on"> <img src="../img/admin/enabled.gif" alt="" title="" />'.$this->l('Yes').'</label>
					<input type="radio" name="contains_states" id="contains_states_off" value="0" '.((!$this->getFieldValue($obj, 'contains_states') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="contains_states_off"> <img src="../img/admin/disabled.gif" alt="" title="" />'.$this->l('No').'</label>
				</div>
				<label>'.$this->l('Need tax identification number?').' </label>
				<div class="margin-form">
					<input type="radio" name="need_identification_number" id="need_identification_number_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'need_identification_number')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="need_identification_number_on"> <img src="../img/admin/enabled.gif" alt="" title="" />'.$this->l('Yes').'</label>
					<input type="radio" name="need_identification_number" id="need_identification_number_off" value="0" '.((!$this->getFieldValue($obj, 'need_identification_number') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="need_identification_number_off"> <img src="../img/admin/disabled.gif" alt="" title="" />'.$this->l('No').'</label>
				</div>
				<div class="clear"></div>
				<label>'.$this->l('Display tax label:').' </label>
				<div class="margin-form">
					<input type="radio" name="display_tax_label" id="display_tax_label_on" value="1" '.((!$obj->id OR $this->getFieldValue($obj, 'display_tax_label')) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_tax_label_on"> <img src="../img/admin/enabled.gif" alt="" title="" />'.$this->l('Yes').'</label>
					<input type="radio" name="display_tax_label" id="display_tax_label_off" value="0" '.((!$this->getFieldValue($obj, 'display_tax_label') AND $obj->id) ? 'checked="checked" ' : '').'/>
					<label class="t" for="display_tax_label_off"> <img src="../img/admin/disabled.gif" alt="" title="" />'.$this->l('No').'</label>
				</div>';
		if (Tools::isMultiShopActivated())
		{
			echo '<label>'.$this->l('Shop association:').'</label><div class="margin-form">';
			$this->displayAssoShop();
			echo '</div>';
		}
		echo '
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>
		<script type="text/javascript">disableZipFormat();</script>';
	}
}

