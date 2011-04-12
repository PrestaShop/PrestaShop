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

class AdminGeolocation extends AdminTab
{
	public function display()
	{
		global $currentIndex, $cookie;
		
		echo '
		<h2>'.$this->l('Geolocation').'</h2>
		';
		
		if (!$this->_isGeoLiteCityAvailable())
			$this->displayWarning($this->l('In order to use Geolocation, please download').' <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">'.$this->l('this file').'</a> '.$this->l('and decompress it into tools/geoip/ directory'));
		
		echo '
		<form method="POST" action="'.$currentIndex.'&token='.Tools::getValue('token').'">
			<fieldset>
				<legend><img src="../img/admin/world.gif" alt="" /> '.$this->l('Geolocation by IP').'</legend>
				
				<label>'.$this->l('Geolocation by IP:').'</label>
				<div class="margin-form">
					<input type="radio" name="PS_GEOLOCATION_ENABLED" id="PS_GEOLOCATION_ENABLED_1" value="1" '.(Configuration::get('PS_GEOLOCATION_ENABLED') ? 'checked="checked"' : '').' /> <label class="t" for="PS_GEOLOCATION_ENABLED_1"><img src="../img/admin/enabled.gif" alt="" /> '.$this->l('Enabled').'</label>
					<input type="radio" name="PS_GEOLOCATION_ENABLED" id="PS_GEOLOCATION_ENABLED_0" value="0" '.(!Configuration::get('PS_GEOLOCATION_ENABLED') ? 'checked="checked"' : '').' /> <label class="t" for="PS_GEOLOCATION_ENABLED_0"><img src="../img/admin/disabled.gif" alt="" /> '.$this->l('Disabled').'</label>
					<p>'.$this->l('This option allows you, among other things, to restrict access to your shop for many countries. See below.').'</p>
				</div>
				
				<div class="margin-form">
					<input type="submit" class="button" name="submitGeolocationConfiguration" value="'.$this->l('Save').'" />
				</div>
			</fieldset>
		</form>
		';
		$allowedCountries = explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'));
		echo '
		<form method="POST" action="'.$currentIndex.'&token='.Tools::getValue('token').'">
			<fieldset style="margin-top:10px;">
				<legend><img src="../img/admin/world.gif" alt="" /> '.$this->l('Options').'</legend>
				
				<div class="hint" style="display:block;margin-bottom:20px;">
					'.$this->l('The following features are only available if you enable the Geolocation by IP feature.').'
				</div>
				
				<label>'.$this->l('Geolocation behavior for restricted countries:').'</label>
				<div class="margin-form">
					<select name="PS_GEOLOCATION_BEHAVIOR">
						<option value="'._PS_GEOLOCATION_NO_CATALOG_.'" '.(Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ ? 'selected' : '').'>'.$this->l('Visitors can\'t see your catalog').'</option>
						<option value="'._PS_GEOLOCATION_NO_ORDER_.'" '.(Configuration::get('PS_GEOLOCATION_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ ? 'selected' : '').'>'.$this->l('Visitors can see your catalog but can\'t make an order').'</option>
					</select>	
				</div>
				
				<div class="clear" style="margin-top:10px;"></div>
				
				<label>'.$this->l('Geolocation behavior for undefined countries:').'</label>
				<div class="margin-form">
					<select name="PS_GEOLOCATION_NA_BEHAVIOR">
						<option value="-1" '.(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == -1 ? 'selected' : '').'>'.$this->l('All features are available').'</option>
						<option value="'._PS_GEOLOCATION_NO_CATALOG_.'" '.(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_CATALOG_ ? 'selected' : '').'>'.$this->l('Visitors can\'t see your catalog').'</option>
						<option value="'._PS_GEOLOCATION_NO_ORDER_.'" '.(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR') == _PS_GEOLOCATION_NO_ORDER_ ? 'selected' : '').'>'.$this->l('Visitors can see your catalog but can\'t make an order').'</option>
					</select>	
				</div>
				
				<div class="clear" style="margin-top:10px;"></div>
				
				<label>'.$this->l('Select countries that can access your store:').'</label>
				<div class="margin-form">
					<table class="table" cellspacing="0">
						<thead>
							<tr>
								<th><input type="checkbox" name="checkAll" onclick="checkDelBoxes(this.form, \'countries[]\', this.checked)" /></th>
								<th>'.$this->l('Name').'</th>
							<tr>
						</thead>
						<tbody>
		';
		foreach (Country::getCountries(intval($cookie->id_lang)) AS $country)
			echo '
				<tr>
					<td><input type="checkbox" name="countries[]" value="'.strtoupper(Tools::htmlentitiesUTF8($country['iso_code'])).'" '.(in_array(strtoupper($country['iso_code']), $allowedCountries) ? 'checked="checked"' : '').' /></td>
					<td>'.Tools::htmlentitiesUTF8($country['name']).'</td>
				</tr>
			';
		echo '
						</tbody>
					</table>
				</div>
				
				<div class="margin-form">
					<input type="submit" class="button" name="submitGeolocationCountries" value="'.$this->l('Save').'" />
				</div>
			</fieldset>
		</form>
		
		<form method="POST" action="'.$currentIndex.'&token='.Tools::getValue('token').'">
			<fieldset style="margin-top:10px;">
				<legend><img src="../img/admin/world.gif" alt="" /> '.$this->l('Whitelist of IP addresses').'</legend>
				
				<div class="hint" style="display:block;margin-bottom:20px;">
					'.$this->l('You can add many IP addresses, these addresses will always be allowed to access your shop (e.g. Google bots IP).').'
				</div>
				
				<label for="PS_GEOLOCATION_WHITELIST">'.$this->l('Allowed IP addresses:').'</label>
				<div class="margin-form">
					<textarea name="PS_GEOLOCATION_WHITELIST" id="PS_GEOLOCATION_WHITELIST" cols="80" rows="30">'.Tools::htmlentitiesUTF8(str_replace(';', "\n", Configuration::get('PS_GEOLOCATION_WHITELIST'))).'</textarea>
				</div>
				
				<div class="margin-form">
					<input type="submit" class="button" name="submitGeolocationWhitelist" value="'.$this->l('Save').'" />
				</div>
			</fieldset>
		</form>
		';
	}
	
	public function postProcess()
	{
		global $currentIndex;
		
		if (Tools::isSubmit('submitGeolocationConfiguration'))
		{
			if ($this->_isGeoLiteCityAvailable())
			{
				Configuration::updateValue('PS_GEOLOCATION_ENABLED', intval(Tools::getValue('PS_GEOLOCATION_ENABLED')));
				Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->_errors[] = Tools::displayError('Geolocation database is unavailable.');
		}
		
		if (Tools::isSubmit('submitGeolocationCountries'))
		{
			if (!is_array(Tools::getValue('countries')) OR !sizeof(Tools::getValue('countries')))
				$this->_errors[] = Tools::displayError('Country selection is invalid');
			else
			{
				Configuration::updateValue('PS_GEOLOCATION_BEHAVIOR', (!(int)(Tools::getValue('PS_GEOLOCATION_BEHAVIOR')) ? _PS_GEOLOCATION_NO_CATALOG_ : _PS_GEOLOCATION_NO_ORDER_));
				Configuration::updateValue('PS_GEOLOCATION_NA_BEHAVIOR', (int)Tools::getValue('PS_GEOLOCATION_NA_BEHAVIOR'));
				Configuration::updateValue('PS_ALLOWED_COUNTRIES', implode(';', Tools::getValue('countries')));
				Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
		}
		
		if (Tools::isSubmit('submitGeolocationWhitelist'))
		{
			if (!Validate::isCleanHtml(Tools::getValue('PS_GEOLOCATION_WHITELIST')))
				$this->_errors[] = Tools::displayError('Invalid whitelist');
			else
			{	
				Configuration::updateValue('PS_GEOLOCATION_WHITELIST', str_replace("\n", ';', str_replace("\r", '', Tools::getValue('PS_GEOLOCATION_WHITELIST'))));
				Tools::redirectAdmin($currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
		}
		
		return parent::postProcess();
	}
	
	private function _isGeoLiteCityAvailable()
	{
		if (file_exists(_PS_GEOIP_DIR_.'GeoLiteCity.dat'))
			return true;
		return false;
	}
}

