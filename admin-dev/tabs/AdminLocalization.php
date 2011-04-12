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

include_once(PS_ADMIN_DIR.'/tabs/AdminPreferences.php');

class AdminLocalization extends AdminPreferences
{
	public function __construct()
	{
		global $cookie;

		$lang = strtoupper(Language::getIsoById($cookie->id_lang));
		$this->className = 'Configuration';
		$this->table = 'configuration';

		$this->_fieldsLocalization = array(
			'PS_WEIGHT_UNIT' => array('title' => $this->l('Weight unit:'), 'desc' => $this->l('The weight unit of your shop (eg. kg or lbs)'), 'validation' => 'isWeightUnit', 'required' => true, 'type' => 'text'),
			'PS_DISTANCE_UNIT' => array('title' => $this->l('Distance unit:'), 'desc' => $this->l('The distance unit of your shop (eg. km or mi)'), 'validation' => 'isDistanceUnit', 'required' => true, 'type' => 'text'),
			'PS_VOLUME_UNIT' => array('title' => $this->l('Volume unit:'), 'desc' => $this->l('The volume unit of your shop'), 'validation' => 'isWeightUnit', 'required' => true, 'type' => 'text'),
			'PS_DIMENSION_UNIT' => array('title' => $this->l('Dimension unit:'), 'desc' => $this->l('The dimension unit of your shop (eg. cm or in)'), 'validation' => 'isDistanceUnit', 'required' => true, 'type' => 'text'));
		$this->_fieldsOptions = array(
			'PS_LOCALE_LANGUAGE' => array('title' => $this->l('Language locale:'), 'desc' => $this->l('Your server\'s language locale.'), 'validation' => 'isLanguageIsoCode', 'type' => 'text'),
			'PS_LOCALE_COUNTRY' => array('title' => $this->l('Country locale:'), 'desc' => $this->l('Your server\'s country locale.'), 'validation' => 'isLanguageIsoCode', 'type' => 'text')
		);

		parent::__construct();
	}

	public function postProcess()
	{
		global $currentIndex;

		if (isset($_POST['submitLocalization'.$this->table]))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->_postConfig($this->_fieldsLocalization);
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitLocalizationPack'))
		{
			if (!$pack = @Tools::file_get_contents('http://www.prestashop.com/download/localization/'.Tools::getValue('iso_localization_pack').'.xml') AND !$pack = @Tools::file_get_contents(dirname(__FILE__).'/../../localization/'.Tools::getValue('iso_localization_pack').'.xml'))
				$this->_errors[] = Tools::displayError('Cannot load localization pack (from prestashop.com and from your local folder "localization")');
			elseif (!$selection = Tools::getValue('selection'))
				$this->_errors[] = Tools::displayError('Please select at least one content item to import.');
			else
			{
				foreach ($selection AS $selected)
					if (!Validate::isLocalizationPackSelection($selected))
					{
						$this->_errors[] = Tools::displayError('Invalid selection');
						return ;
					}
				$localizationPack = new LocalizationPack();
				if (!$localizationPack->loadLocalisationPack($pack, $selection))
					$this->_errors = array_merge($this->_errors, $localizationPack->getErrors());
				else
					Tools::redirectAdmin($currentIndex.'&conf=23&token='.$this->token);
			}
			
			
		}
		parent::postProcess();
	}

	public function display()
	{
		global $currentIndex;

		$this->_displayForm('localization', $this->_fieldsLocalization, $this->l('Localization'), 'width2', 'localization');
		echo '<br />
		<form method="post" action="'.$currentIndex.'&token='.$this->token.'" class="width2" enctype="multipart/form-data">
		<fieldset>
			<legend><img src="../img/admin/localization.gif" />'.$this->l('Localization pack import').'</legend>
			<div style="clear: both; padding-top: 15px;">
			<label>'.$this->l('Localization pack you want to import:').'</label>
			<div class="margin-form">
			<select id="iso_localization_pack" name="iso_localization_pack">';
			$localization_packs = @simplexml_load_file('http://www.prestashop.com/rss/localization.xml');
			if (!$localization_packs)
				$localization_packs = simplexml_load_file(dirname(__FILE__).'/../../localization/localization.xml');
			if ($localization_packs)
				foreach($localization_packs->pack as $pack)
						echo '<option value="'.$pack->iso.'">'.$pack->name.'</option>';
			else
				echo '<option value="0">'.$this->l('Cannot connect to prestashop.com').'</option>';
			echo '</select></div>
			<br />
				<label>'.$this->l('Content to import:').'</label>
				<div class="margin-form" style="padding-top: 5px;">
					<input type="checkbox" name="selection[]" value="states" checked="checked" /> '.$this->l('States').'<br />
					<input type="checkbox" name="selection[]" value="taxes" checked="checked" /> '.$this->l('Taxes').'<br />
					<input type="checkbox" name="selection[]" value="currencies" checked="checked" /> '.$this->l('Currencies').'<br />
					<input type="checkbox" name="selection[]" value="languages" checked="checked" /> '.$this->l('Languages').'<br />
					<input type="checkbox" name="selection[]" value="units" checked="checked" /> '.$this->l('Units (e.g., weight, volume, distance)').'
				</div>
				<div align="center" style="margin-top: 20px;">
					<input type="submit" class="button" name="submitLocalizationPack" value="'.$this->l('   Import   ').'" />
				</div>
			</div>
		</fieldset>
		</form>
		<br />';
		$this->_displayForm('options', $this->_fieldsOptions, $this->l('Advanced'), 'width2', 'localization');
	}
}