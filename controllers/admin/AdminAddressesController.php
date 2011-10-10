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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
//include_once(_PS_ADMIN_DIR_.'/../classes/AdminTab.php');
if (Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php'))
	include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');

class AdminAddressesControllerCore extends AdminController
{
	/** @var array countries list */
	private $countriesArray = array();

	public function __construct()
	{
	 	$this->table = 'address';
	 	$this->className = 'Address';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->requiredDatabase = true;
		$this->addressType = 'customer';
		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = true;
		$this->_select = 'cl.`name` as country';
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON
		(cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')';

		$countries = Country::getCountries($this->context->language->id);
		foreach ($countries AS $country)
			$this->countriesArray[$country['id_country']] = $country['name'];

		$this->fieldsDisplay = array(
		'id_address' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
		'firstname' => array('title' => $this->l('First name'), 'width' => 80, 'filter_key' => 'a!firstname'),
		'lastname' => array('title' => $this->l('Last name'), 'width' => 100, 'filter_key' => 'a!lastname'),
		'address1' => array('title' => $this->l('Address'), 'width' => 200),
		'postcode' => array('title' => $this->l('Postcode/ Zip Code'), 'align' => 'right', 'width' => 50),
		'city' => array('title' => $this->l('City'), 'width' => 150),
		'country' => array('title' => $this->l('Country'), 'width' => 100, 'type' => 'select', 'select' => $this->countriesArray, 'filter_key' => 'cl!id_country'));

		$this->template = 'adminAddresses.tpl';

		parent::__construct();
	}

	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]))
		{
			// Transform e-mail in id_customer for parent processing
			if ($this->addressType == 'customer')
			{
				if (Validate::isEmail(Tools::getValue('email')))
				{
					$customer = new Customer();
					$customer->getByEmail(Tools::getValue('email'));
					if (Validate::isLoadedObject($customer))
						$_POST['id_customer'] = $customer->id;
					else
						$this->_errors[] = Tools::displayError('This e-mail address is not registered.');
				}
				elseif ($id_customer = Tools::getValue('id_customer'))
				{
					$customer = new Customer((int)($id_customer));
					if (Validate::isLoadedObject($customer))
						$_POST['id_customer'] = $customer->id;
					else
						$this->_errors[] = Tools::displayError('Unknown customer');
				}
				else
					$this->_errors[] = Tools::displayError('Unknown customer');
				if (Country::isNeedDniByCountryId(Tools::getValue('id_country')) AND !Tools::getValue('dni'))
					$this->_errors[] = Tools::displayError('Identification number is incorrect or has already been used.');
			}

			// Check manufacturer selected
			if ($this->addressType == 'manufacturer')
			{
				$manufacturer = new Manufacturer((int)(Tools::getValue('id_manufacturer')));
				if (!Validate::isLoadedObject($manufacturer))
					$this->_errors[] = Tools::displayError('Manufacturer selected is not valid.');
			}

			/* If the selected country does not contain states */
			$id_state = (int)(Tools::getValue('id_state'));
			if ($id_country = Tools::getValue('id_country') AND $country = new Country((int)($id_country)) AND !(int)($country->contains_states) AND $id_state)
				$this->_errors[] = Tools::displayError('You have selected a state for a country that does not contain states.');

			/* If the selected country contains states, then a state have to be selected */
			if ((int)($country->contains_states) AND !$id_state)
				$this->_errors[] = Tools::displayError('An address located in a country containing states must have a state selected.');

			/* Check zip code */
			if ($country->need_zip_code)
			{
				$zip_code_format = $country->zip_code_format;
				if (($postcode = Tools::getValue('postcode')) AND $zip_code_format)
				{
					$zip_regexp = '/^'.$zip_code_format.'$/ui';
					$zip_regexp = str_replace(' ', '( |)', $zip_regexp);
					$zip_regexp = str_replace('-', '(-|)', $zip_regexp);
					$zip_regexp = str_replace('N', '[0-9]', $zip_regexp);
					$zip_regexp = str_replace('L', '[a-zA-Z]', $zip_regexp);
					$zip_regexp = str_replace('C', $country->iso_code, $zip_regexp);
					if (!preg_match($zip_regexp, $postcode))
						$this->_errors[] = Tools::displayError('Your zip/postal code is incorrect.').'<br />'.Tools::displayError('Must be typed as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $zip_code_format)));
				}
				elseif ($zip_code_format)
					$this->_errors[] = Tools::displayError('Postcode required.');
				elseif ($postcode AND !preg_match('/^[0-9a-zA-Z -]{4,9}$/ui', $postcode))
					$this->_errors[] = Tools::displayError('Your zip/postal code is incorrect.');
			}


			/* If this address come from order's edition and is the same as the other one (invoice or delivery one)
			** we delete its id_address to force the creation of a new one */
			if ((int)(Tools::getValue('id_order')))
			{
				$this->_redirect = false;
				if (isset($_POST['address_type']))
					$_POST['id_address'] = '';
			}
		}
		if (!sizeof($this->_errors))
			parent::postProcess();

		/* Reassignation of the order's new (invoice or delivery) address */
		$address_type = ((int)(Tools::getValue('address_type')) == 2 ? 'invoice' : ((int)(Tools::getValue('address_type')) == 1 ? 'delivery' : ''));
		if (isset($_POST['submitAdd'.$this->table]) AND ($id_order = (int)(Tools::getValue('id_order'))) AND !sizeof($this->_errors) AND !empty($address_type))
		{
			if(!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'orders SET `id_address_'.$address_type.'` = '.Db::getInstance()->Insert_ID().' WHERE `id_order` = '.$id_order))
				$this->_errors[] = Tools::displayError('An error occurred while linking this address to its order.');
			else
				Tools::redirectAdmin(Tools::getValue('back').'&conf=4');
		}
	}

	public function displayForm($isMainTab = true)
	{
		$content = parent::displayForm();
		if (!($obj = $this->loadObject(true)))
			return;

		$content .= '
		<form action="'.self::$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post">
		'.((int)($obj->id) ? '<input type="hidden" name="id_'.$this->table.'" value="'.(int)($obj->id).'" />' : '').'
		'.(($id_order = (int)(Tools::getValue('id_order'))) ? '<input type="hidden" name="id_order" value="'.(int)($id_order).'" />' : '').'
		'.(($address_type = (int)(Tools::getValue('address_type'))) ? '<input type="hidden" name="address_type" value="'.(int)($address_type).'" />' : '').'
		'.(Tools::getValue('realedit') ? '<input type="hidden" name="realedit" value="1" />' : '').'
			<fieldset>
				<legend><img src="../img/admin/contact.gif" alt="" />'.$this->l('Addresses').'</legend>';
		switch ($this->addressType)
		{
			case 'manufacturer':
				$content .= '<label>'.$this->l('Choose manufacturer').'</label>
				<div class="margin-form">';
				$manufacturers = Manufacturer::getManufacturers();
				$content .= '<select name="id_manufacturer">';
				if (!sizeof($manufacturers))
					$content .= '<option value="0">'.$this->l('No manufacturer available').'&nbsp</option>';
				foreach ($manufacturers as $manufacturer)
					$content .= '<option value="'.(int)($manufacturer['id_manufacturer']).'"'.($this->getFieldValue($obj, 'id_manufacturer') == $manufacturer['id_manufacturer'] ? ' selected="selected"' : '').'>'.$manufacturer['name'].'&nbsp</option>';
				$content .=	'</select>';
				$content .= '</div>';
				$content .= '<input type="hidden" name="alias" value="manufacturer">';
				break;
			case 'customer':
			default:
				if ($obj->id)
				{
					$customer = new Customer($obj->id_customer);
					$tokenCustomer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id);
					$content .= '
					<label>'.$this->l('Customer').'</label>
					<div class="margin-form"><a style="display: block; padding-top: 4px;" href="?tab=AdminCustomers&id_customer='.$customer->id.'&viewcustomer&token='.$tokenCustomer.'">'.$customer->lastname.' '.$customer->firstname.' ('.$customer->email.')</a></div>
					<input type="hidden" name="id_customer" value="'.$customer->id.'" />
					<input type="hidden" name="email" value="'.$customer->email.'" />';
				}
				else
				{
					$content .=
					'<label>'.$this->l('Customer e-mail').'</label>
					<div class="margin-form">
						<input type="text" size="33" name="email" value="'.htmlentities(Tools::getValue('email'), ENT_COMPAT, 'UTF-8').'" style="text-transform: lowercase;" /> <sup>*</sup>
					</div>';
				}
				$content .= '
					<label for="dni">'.$this->l('Identification Number').'</label>
					<div class="margin-form">
					<input type="text" name="dni" id="dni" value="'.htmlentities($this->getFieldValue($obj, 'dni'), ENT_COMPAT, 'UTF-8').'" />
					<p>'.$this->l('DNI / NIF / NIE').'</p>
					</div>';

				$content .= '<label>'.$this->l('Alias').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="alias" value="'.htmlentities($this->getFieldValue($obj, 'alias'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</div>';
				break;
		}

		$addresses_fields = $this->processAddressFormat();
		$addresses_fields = $addresses_fields["dlv_all_fields"];	// we use  delivery address

		foreach($addresses_fields as $addr_field_item)
		{
			if ($addr_field_item == 'company')
			{
				if ($this->addressType != 'manufacturer')
				{
					$content .= '<label>'.$this->l('Company').'</label>
						<div class="margin-form">
						<input type="text" size="33" name="company" value="'.htmlentities($this->getFieldValue($obj, 'company'), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
						</div>';

					if ((Configuration::get('VATNUMBER_MANAGEMENT') AND file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php')) && VatNumber::isApplicable(Configuration::get('PS_COUNTRY_DEFAULT')))
						$content .= '<div id="vat_area" style="display: visible">';
					else if(Configuration::get('VATNUMBER_MANAGEMENT'))
						$content .= '<div id="vat_area" style="display: hidden">';
					else
						$content .='<div style="display: none;">';

					$content .= '<label>'.$this->l('VAT number').'</label>
						<div class="margin-form">
						<input type="text" size="33" name="vat_number" value="'.htmlentities($this->getFieldValue($obj, 'vat_number'), ENT_COMPAT, 'UTF-8').'" />
						</div>
						</div>';
				}
			}
			elseif ($addr_field_item == 'lastname')
			{
				$content .= '
					<label>'.$this->l('Last name').'</label>
					<div class="margin-form">
					<input type="text" size="33" name="lastname" value="'.htmlentities($this->getFieldValue($obj, 'lastname'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
					</div>';
			}
			elseif ($addr_field_item == 'firstname')
			{

				$content .= '
					<label>'.$this->l('First name').'</label>
					<div class="margin-form">
					<input type="text" size="33" name="firstname" value="'.htmlentities($this->getFieldValue($obj, 'firstname'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					<span class="hint" name="help_box">'.$this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span></span>
					</div>';
			}
			elseif ($addr_field_item == 'address1')
			{

				$content .= '
					<label>'.$this->l('Address').'</label>
					<div class="margin-form">
					<input type="text" size="33" name="address1" value="'.htmlentities($this->getFieldValue($obj, 'address1'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					</div>';
			}
			elseif ($addr_field_item == 'address2')
			{

				$content .= '
					<label>'.$this->l('Address').' (2)</label>
									     <div class="margin-form">
												      <input type="text" size="33" name="address2" value="'.htmlentities($this->getFieldValue($obj, 'address2'), ENT_COMPAT, 'UTF-8').'" />
															      </div>';
			}
			elseif ($addr_field_item == 'postcode')
			{

				$content .= '
					<label>'.$this->l('Postcode/ Zip Code').'</label>
					<div class="margin-form">
					<input type="text" size="33" name="postcode" value="'.htmlentities($this->getFieldValue($obj, 'postcode'), ENT_COMPAT, 'UTF-8').'" />
					</div>';
			}
			elseif ($addr_field_item == 'city')
			{

				$content .= '
					<label>'.$this->l('City').'</label>
					<div class="margin-form">
					<input type="text" size="33" name="city" value="'.htmlentities($this->getFieldValue($obj, 'city'), ENT_COMPAT, 'UTF-8').'" style="text-transform: uppercase;" /> <sup>*</sup>
					</div>';
			}
			elseif ($addr_field_item == 'country' || $addr_field_item == 'Country:name')
			{

				$content .= '
					<label>'.$this->l('Country').'</label>
					<div class="margin-form">
					<select name="id_country" id="id_country" />';
				$selectedCountry = $this->getFieldValue($obj, 'id_country');
				foreach ($this->countriesArray AS $id_country => $name)
					$content .= '		<option value="'.$id_country.'"'.((!$selectedCountry AND Configuration::get('PS_COUNTRY_DEFAULT') == $id_country) ? ' selected="selected"' : ($selectedCountry == $id_country ? ' selected="selected"' : '')).'>'.$name.'</option>';
				$content .= '		</select> <sup>*</sup>
					</div>';


			$content .= '
				<div id="contains_states" '.(!Country::containsStates((int)$selectedCountry) ? 'style="display:none;"' : '').'>
				<label>'.$this->l('State').'</label>
				<div class="margin-form">
				<select name="id_state" id="id_state">
				</select>
				<sup>*</sup>
				</div>
				</div>';


				$id_country_ajax = (int)$this->getFieldValue($obj, 'id_country');

				$content .= '
					<script type="text/javascript">
					$(document).ready(function(){
							ajaxStates ();
							$(\'#id_country\').change(function() {
								ajaxStates ();
								});
							function ajaxStates ()
							{
								$.ajax({
									url: "ajax.php",
									cache: false,
									data: "ajaxStates=1&id_country="+$(\'#id_country\').val()+"&id_state="+$(\'#id_state\').val(),
									success: function(html)
										{
											if (html == \'false\')
											{
												$("#contains_states").fadeOut();
												$(\'#id_state option[value=0]\').attr("selected", "selected");
}
											else
											{
												$("#id_state").html(html);
												$("#contains_states").fadeIn();
												$(\'#id_state option[value='.(int)$obj->id_state.']\').attr("selected", "selected");
											}
										}
									}); ';
		if (file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'))
			$content .= '	$.ajax({
					type: "GET",
					url: "'._MODULE_DIR_.'vatnumber/ajax.php?id_country="+$(\'#id_country\').val(),
					success: function(isApplicable)
						{
							if(isApplicable == 1)
								$(\'#vat_area\').show();
							else
								$(\'#vat_area\').hide();
						}
					});';
		$content .= '	}; }); </script>';
		}

	} // End foreach
			$content .= '
				<label>'.$this->l('Home phone').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="phone" value="'.htmlentities($this->getFieldValue($obj, 'phone'), ENT_COMPAT, 'UTF-8').'" />
				</div>';

			$content .= '
				<label>'.$this->l('Mobile phone').'</label>
				<div class="margin-form">
					<input type="text" size="33" name="phone_mobile" value="'.htmlentities($this->getFieldValue($obj, 'phone_mobile'), ENT_COMPAT, 'UTF-8').'" />
				</div>';


			$content .= '
				<label>'.$this->l('Other').'</label>
				<div class="margin-form">
					<textarea name="other" cols="36" rows="4">'.htmlentities($this->getFieldValue($obj, 'other'), ENT_COMPAT, 'UTF-8').'</textarea>
					<span class="hint" name="help_box">'.$this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span></span>
				</div>';

			$content .= '
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>';
		$content .= '
		</form>';

		return $content;
	}

	protected function processAddressFormat()
	{
		$tmp_addr = new Address((int)Tools::getValue("id_address"));

		$selectedCountry = ($tmp_addr && $tmp_addr->id_country) ? $tmp_addr->id_country :
				(int)(Configuration::get('PS_COUNTRY_DEFAULT'));

		$inv_adr_fields = AddressFormat::getOrderedAddressFields($selectedCountry, false, true);
		$dlv_adr_fields = AddressFormat::getOrderedAddressFields($selectedCountry, false, true);

		$inv_all_fields = array();
		$dlv_all_fields = array();

		$out = array();

		foreach (array('inv','dlv') as $adr_type)
		{
			foreach (${$adr_type.'_adr_fields'} as $fields_line)
				foreach(explode(' ',$fields_line) as $field_item)
					${$adr_type.'_all_fields'}[] = trim($field_item);


			$out[$adr_type.'_adr_fields'] =  ${$adr_type.'_adr_fields'};
			$out[$adr_type.'_all_fields'] =  ${$adr_type.'_all_fields'};
		}

		return $out;
	}

	public function initContent()
	{
		if ($this->display != 'edit')
			$this->display = 'list';
		parent::initContent();
	}

}


