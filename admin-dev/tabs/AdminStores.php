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

include_once(dirname(__FILE__).'/../../classes/AdminTab.php');

class AdminStores extends AdminTab
{
	/** @var array countries list */
	private $countriesArray = array();
	
	public function __construct()
	{
		global $cookie;
		
	 	$this->table = 'store';
	 	$this->className = 'Store';
	 	$this->lang = false;
	 	$this->edit = true;
	 	$this->delete = true;
		
		$this->fieldImageSettings = array('name' => 'image', 'dir' => 'st');
		
		$this->_select = 'cl.`name` country, st.`name` state';
		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)($cookie->id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'state` st ON (st.`id_state` = a.`id_state`)';
		
		$countries = Country::getCountries((int)($cookie->id_lang));
		foreach ($countries AS $country)
			$this->countriesArray[$country['id_country']] = $country['name'];
				
		$this->fieldsDisplay = array(
			'id_store' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'country' => array('title' => $this->l('Country'), 'width' => 100),
			'state' => array('title' => $this->l('State'), 'width' => 100),
			'city' => array('title' => $this->l('City'), 'width' => 100),
			'postcode' => array('title' => $this->l('Zip code'), 'width' => 50),
			'name' => array('title' => $this->l('Name'), 'width' => 120),
			'phone' => array('title' => $this->l('Phone'), 'width' => 70),
			'fax' => array('title' => $this->l('Fax'), 'width' => 70),
			'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false)
		);
		
		$this->optionTitle = $this->l('Parameters');
		$this->_fieldsOptions = array(
			'PS_STORES_DISPLAY_FOOTER' => array('title' => $this->l('Display in the footer:'), 'desc' => $this->l('Display a link to the store locator in the footer'), 'cast' => 'intval', 'type' => 'bool'),
			'PS_STORES_DISPLAY_SITEMAP' => array('title' => $this->l('Display in the sitemap page:'), 'desc' => $this->l('Display a link to the store locator in the sitemap page'), 'cast' => 'intval', 'type' => 'bool'),
			'PS_STORES_SIMPLIFIED' => array('title' => $this->l('Show a simplified store locator:'), 'desc' => $this->l('No map, no search, only a store directory'), 'cast' => 'intval', 'type' => 'bool'),
			'PS_STORES_CENTER_LAT' => array('title' => $this->l('Latitude by default:'), 'desc' => $this->l('Used for the position by default of the map'), 'cast' => 'floatval', 'type' => 'float', 'size' => '10'),
			'PS_STORES_CENTER_LONG' => array('title' => $this->l('Longitude by default:'), 'desc' => $this->l('Used for the position by default of the map'), 'cast' => 'floatval', 'type' => 'float', 'size' => '10'));
		
		parent::__construct();
	}
	
	protected function postImage($id)
	{
		$ret = parent::postImage($id);
		if (($id_store = (int)(Tools::getValue('id_store'))) AND isset($_FILES) AND sizeof($_FILES) AND file_exists(_PS_STORE_IMG_DIR_.$id_store.'.jpg'))
		{
			$imagesTypes = ImageType::getImagesTypes('categories');
			foreach ($imagesTypes AS $k => $imageType)
				imageResize(_PS_STORE_IMG_DIR_.$id_store.'.jpg', _PS_STORE_IMG_DIR_.$id_store.'-'.stripslashes($imageType['name']).'.jpg', (int)($imageType['width']), (int)($imageType['height']));
		}
		return $ret;
	}
	
	public function displayOptionsList()
	{
		parent::displayOptionsList();
		
		echo '<br /><p><img src="../img/admin/asterisk.gif" class="middle" /> '.$this->l('You can also replace the icon representing your store in Google Maps. Go to the Preferences tab, and then the Appearance subtab.').'</p>';
	}
	
	public function postProcess()
	{
		if (isset($_POST['submitAdd'.$this->table]))
		{
			/* Cleaning fields */
			foreach ($_POST as $kp => $vp)
				$_POST[$kp] = trim($vp);

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

			/* Store hours */
			$_POST['hours'] = array();
			for ($i = 1; $i < 8; $i++)
				$_POST['hours'][] .= Tools::getValue('hours_'.(int)($i));
			$_POST['hours'] = serialize($_POST['hours']);
		}

		if (!sizeof($this->_errors))
			parent::postProcess();
	}
	
	public function displayForm($isMainTab = true)
	{
		global $currentIndex, $cookie;
		parent::displayForm();
		
		if (!($obj = $this->loadObject(true)))
			return;
		echo '
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
						  		$("#contains_states").fadeOut();
						  	else
						  	{
						  		$("#id_state").html(html);
						  		$("#contains_states").fadeIn();
						  	}
						  }
						});
					};
				});
				</script>
		<form action="'.$currentIndex.'&submitAdd'.$this->table.'=1&token='.$this->token.'" method="post" enctype="multipart/form-data">
		'.($obj->id ? '<input type="hidden" name="id_'.$this->table.'" value="'.$obj->id.'" />' : '').'
			<fieldset>
				<legend><img src="../img/admin/home.gif" />'.$this->l('Stores').'</legend>
				<div style="padding-right: 40px; border-right: 1px solid #E0D0B1; float: left;">
					<label>'.$this->l('Name:').'</label>
					<div class="margin-form">
						<input type="text" size="33" name="name" value="'.htmlentities(Tools::getValue('name', $obj->name), ENT_COMPAT, 'UTF-8').'" />
						<span class="hint" name="help_box">'.$this->l('Allowed characters: letters, spaces and').' (-)<span class="hint-pointer">&nbsp;</span></span>
						<p class="clear">'.$this->l('Store name, e.g. Citycentre Mall Store').'</p>
					</div>
					<label>'.$this->l('Address:').'</label>
					<div class="margin-form">
						<input type="text" size="33" name="address1" value="'.htmlentities($this->getFieldValue($obj, 'address1'), ENT_COMPAT, 'UTF-8').'" /> <sup>*</sup>
					</div>
					<label>'.$this->l('Address').' (2):</label>
					<div class="margin-form">
						<input type="text" size="33" name="address2" value="'.htmlentities($this->getFieldValue($obj, 'address2'), ENT_COMPAT, 'UTF-8').'" />
					</div>
					<label>'.$this->l('Postcode/ Zip Code:').'</label>
					<div class="margin-form">
						<input type="text" size="6" name="postcode" value="'.htmlentities($this->getFieldValue($obj, 'postcode'), ENT_COMPAT, 'UTF-8').'" />
					</div>
					<label>'.$this->l('City:').'</label>
					<div class="margin-form">
						<input type="text" size="33" name="city" value="'.htmlentities($this->getFieldValue($obj, 'city'), ENT_COMPAT, 'UTF-8').'" style="text-transform: uppercase;" /> <sup>*</sup>
					</div>
					<label>'.$this->l('Country:').'</label>
					<div class="margin-form">
						<select name="id_country" id="id_country"/>';
			$selectedCountry = $this->getFieldValue($obj, 'id_country');
			foreach ($this->countriesArray AS $id_country => $name)
				echo '		<option value="'.$id_country.'"'.((!$selectedCountry AND Configuration::get('PS_COUNTRY_DEFAULT') == $id_country) ? ' selected="selected"' : ($selectedCountry == $id_country ? ' selected="selected"' : '')).'>'.$name.'</option>';
			echo '		</select> <sup>*</sup>
					</div>
					<div id="contains_states">
						<label>'.$this->l('State:').'</label>
						<div class="margin-form">
							<select name="id_state" id="id_state">
							</select> <sup>*</sup>
						</div>
					</div>
					<label>'.$this->l('Latitude / Longitude:').'</label>
					<div class="margin-form">
						<input type="text" size="8" maxlength="10" name="latitude" value="'.htmlentities($this->getFieldValue($obj, 'latitude'), ENT_COMPAT, 'UTF-8').'" onKeyUp="javascript:this.value = this.value.replace(/,/g, \'.\');" /> / <input type="text" size="8" maxlength="10" name="longitude" value="'.htmlentities($this->getFieldValue($obj, 'longitude'), ENT_COMPAT, 'UTF-8').'" onKeyUp="javascript:this.value = this.value.replace(/,/g, \'.\');" />
						<p class="clear">'.$this->l('Store coords, eg. 45.265469 / -47.226478').'</p>
					</div>
					<label>'.$this->l('Phone:').'</label>
					<div class="margin-form">
						<input type="text" size="33" name="phone" value="'.htmlentities($this->getFieldValue($obj, 'phone'), ENT_COMPAT, 'UTF-8').'" />
					</div>
					<label>'.$this->l('Fax:').'</label>
					<div class="margin-form">
						<input type="text" size="33" name="fax" value="'.htmlentities($this->getFieldValue($obj, 'fax'), ENT_COMPAT, 'UTF-8').'" />
					</div>
					<label>'.$this->l('E-mail address:').' </label>
					<div class="margin-form">
						<input type="text" size="33" name="email" value="'.htmlentities($this->getFieldValue($obj, 'email'), ENT_COMPAT, 'UTF-8').'" />
					</div>
					<label>'.$this->l('Note:').' </label>
					<div class="margin-form">
						<textarea name="note" style="width: 250px; height: 75px;">'.htmlentities($this->getFieldValue($obj, 'note'), ENT_COMPAT, 'UTF-8').'</textarea>
					</div>
					<label>'.$this->l('Status:').' </label>
					<div class="margin-form">
						<input type="radio" name="active" id="active_on" value="1" '.((!$obj->id OR Tools::getValue('active', $obj->active)) ? 'checked="checked" ' : '').'/>
						<label class="t" for="active_on"> <img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" /></label>
						<input type="radio" name="active" id="active_off" value="0" '.((!Tools::getValue('active', $obj->active) AND $obj->id) ? 'checked="checked" ' : '').'/>
						<label class="t" for="active_off"> <img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" /></label>
						<p>'.$this->l('Display or not this store').'</p>
					</div>
				</div>
				<div style="padding-left: 40px; float: left;">
					<label style="text-align: left; width: inherit;">'.$this->l('Picture:').' </label>
					<div class="margin-form" style="padding: 0; display: inline;">
						<input type="file" name="image" />
						<p class="clear">'.$this->l('Store window picture').'</p>';

				echo $this->displayImage($obj->id, _PS_STORE_IMG_DIR_.'/'.$obj->id.'.jpg', 350, NULL, Tools::getAdminToken('AdminStores'.(int)(Tab::getIdFromClassName('AdminStores')).(int)($cookie->id_employee)));
				
				echo '</div>
					<table cellpadding="2" cellspacing="2" style="padding: 10px; margin-top: 15px; border: 1px solid #BBB;">
						<tr>
							<th colspan="2">'.$this->l('Hours:').'</th>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td style="font-size: 0.85em;">'.$this->l('Sample: 10:00AM - 9:30PM').'</td>
						</tr>';
						
						$days = array();
						$days[1] = $this->l('Monday');
						$days[2] = $this->l('Tuesday');
						$days[3] = $this->l('Wednesday');
						$days[4] = $this->l('Thursday');
						$days[5] = $this->l('Friday');
						$days[6] = $this->l('Saturday');
						$days[7] = $this->l('Sunday');
						
						$hours = $this->getFieldValue($obj, 'hours');
						if (!empty($hours))
							$hoursUnserialized = unserialize($hours);
						
						for ($i = 1; $i < 8; $i++)
							echo '
							<tr style="color: #7F7F7F; font-size: 0.85em;">
								<td>'.$days[(int)($i)].'</td>
								<td><input type="text" size="25" name="hours_'.(int)($i).'" value="'.(isset($hoursUnserialized) ? htmlentities($hoursUnserialized[$i - 1], ENT_COMPAT, 'UTF-8') : '').'" /><br /></td>
							</tr>';
			echo '
					</table>
				</div>
				<div class="clear"></div>
				<div class="margin-form">
					<input type="submit" value="'.$this->l('   Save   ').'" name="submitAdd'.$this->table.'" class="button" />
				</div>
				<div class="small"><sup>*</sup> '.$this->l('Required field').'</div>
			</fieldset>
		</form>';
	}
}


