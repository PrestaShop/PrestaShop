<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLocalizationControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		parent::__construct();

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Configuration'),
				'fields' =>	array(
					'PS_LANG_DEFAULT' => array(
						'title' => $this->l('Default language'),
						'hint' => $this->l('The default language used in your shop.'),
						'cast' => 'intval',
						'type' => 'select',
						'identifier' => 'id_lang',
						'list' => Language::getLanguages(false)
					),
					'PS_COUNTRY_DEFAULT' => array(
						'title' => $this->l('Default country'),
						'hint' => $this->l('The default country used in your shop.'),
						'cast' => 'intval',
						'type' => 'select',
						'identifier' => 'id_country',
						'list' => Country::getCountries($this->context->language->id)
					),
					'PS_CURRENCY_DEFAULT' => array(
						'title' => $this->l('Default currency'),
						'hint' =>
							$this->l('The default currency used in your shop.').' - '.$this->l('If you change the default currency, you will have to manually edit every product price.'),
						'cast' => 'intval',
						'type' => 'select',
						'identifier' => 'id_currency',
						'list' => Currency::getCurrencies()
					),
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'localization' => array(
				'title' =>	$this->l('Localization'),
				'icon' =>	'icon-globe',
				'fields' =>	array(
					'PS_WEIGHT_UNIT' => array(
						'title' => $this->l('Weight unit'),
						'hint' => $this->l('The default weight unit for your shop (e.g. kg or lbs).'),
						'validation' => 'isWeightUnit',
						'required' => true,
						'type' => 'text'
					),
					'PS_DISTANCE_UNIT' => array(
						'title' => $this->l('Distance unit'),
						'hint' => $this->l('The default distance unit for your shop (e.g. km or mi).'),
						'validation' => 'isDistanceUnit',
						'required' => true,
						'type' => 'text'
					),
					'PS_VOLUME_UNIT' => array(
						'title' => $this->l('Volume unit'),
						'hint' => $this->l('The default volume unit for your shop.'),
						'validation' => 'isWeightUnit',
						'required' => true,
						'type' => 'text'
					),
					'PS_DIMENSION_UNIT' => array(
						'title' => $this->l('Dimension unit'),
						'hint' => $this->l('The default dimension unit for your shop (e.g. cm or in).'),
						'validation' => 'isDistanceUnit',
						'required' => true,
						'type' => 'text'
					)
				),
				'submit' => array('title' => $this->l('Save'))
			),
			'options' => array(
				'title' =>	$this->l('Advanced'),
				'fields' =>	array(
					'PS_LOCALE_LANGUAGE' => array(
						'title' => $this->l('Language locale'),
						'hint' => $this->l('Your server\'s language locale.'),
						'validation' => 'isLanguageIsoCode',
						'type' => 'text',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_LOCALE_COUNTRY' => array(
						'title' => $this->l('Country locale'),
						'hint' => $this->l('Your server\'s country locale.'),
						'validation' => 'isLanguageIsoCode',
						'type' => 'text',
						'visibility' => Shop::CONTEXT_ALL
					)
				),
				'submit' => array('title' => $this->l('Save'))
			)
		);

		if (function_exists('date_default_timezone_set'))
			$this->fields_options['general']['fields']['PS_TIMEZONE'] = array(
				'title' => $this->l('Time zone'),
				'validation' => 'isAnything',
				'type' => 'select',
				'list' => Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT name FROM '._DB_PREFIX_.'timezone'),
				'identifier' => 'name',
				'visibility' => Shop::CONTEXT_ALL
			);
	}



	public function postProcess()
	{
		if (Tools::isSubmit('submitLocalizationPack'))
		{
			$version = str_replace('.', '', _PS_VERSION_);
			$version = substr($version, 0, 2);

			if (Validate::isFileName(Tools::getValue('iso_localization_pack')))
			{
				if (Tools::getValue('download_updated_pack') == '1' || defined('_PS_HOST_MODE_'))
					$pack = @Tools::file_get_contents('http://api.prestashop.com/localization/'.$version.'/'.Tools::getValue('iso_localization_pack').'.xml');
				else
					$pack = false;
				
				if (defined('_PS_HOST_MODE_'))
					$path = _PS_CORE_DIR_.'/localization/'.Tools::getValue('iso_localization_pack').'.xml';
				else
					$path = _PS_ROOT_DIR_.'/localization/'.Tools::getValue('iso_localization_pack').'.xml';

				if (!$pack && !($pack = @Tools::file_get_contents($path)))
					$this->errors[] = Tools::displayError('Cannot load the localization pack.');

				if (!$selection = Tools::getValue('selection'))
					$this->errors[] = Tools::displayError('Please select at least one item to import.');
				else
				{
					foreach ($selection as $selected)
						if (!Validate::isLocalizationPackSelection($selected))
						{
							$this->errors[] = Tools::displayError('Invalid selection');
							return;
						}
					$localization_pack = new LocalizationPack();
					if (!$localization_pack->loadLocalisationPack($pack, $selection))
						$this->errors = array_merge($this->errors, $localization_pack->getErrors());
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=23&token='.$this->token);
				}
			}
		}

		// Remove the module list cache if the default country changed
		if (Tools::isSubmit('submitOptionsconfiguration') && file_exists(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST))
			@unlink(Module::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST);			
		
		parent::postProcess();
	}

	public function sortLocalizationsPack($a, $b)
	{
		return $a['name'] > $b['name'];
	}

	public function renderForm()
	{
		$localizations_pack = false;
		$this->tpl_option_vars['options_content'] = $this->renderOptions();

		$xml_localization = Tools::simplexml_load_file('http://api.prestashop.com/rss/localization.xml');
		if (!$xml_localization)
		{
			$localization_file = _PS_ROOT_DIR_.'/localization/localization.xml';
			if (file_exists($localization_file))
				$xml_localization = simplexml_load_file($localization_file);
		}

		$i = 0;
		if ($xml_localization)
			foreach ($xml_localization->pack as $key => $pack)
			{
				$localizations_pack[$i]['iso_localization_pack'] = (string)$pack->iso;
				$localizations_pack[$i]['name'] = (string)$pack->name;
				$i++;
			}

		if (!$localizations_pack)
			return $this->displayWarning($this->l('Cannot connect to prestashop.com'));

		usort($localizations_pack, array($this, 'sortLocalizationsPack'));

		$selection_import = array(
			array(
				'id' => 'states',
				'val' => 'states',
				'name' => $this->l('States')
			),
			array(
				'id' => 'taxes',
				'val' => 'taxes',
				'name' => $this->l('Taxes')
			),
			array(
				'id' => 'currencies',
				'val' => 'currencies',
				'name' => $this->l('Currencies')
			),
			array(
				'id' => 'languages',
				'val' => 'languages',
				'name' => $this->l('Languages')
			),
			array(
				'id' => 'units',
				'val' => 'units',
				'name' => $this->l('Units (e.g. weight, volume, distance)')
			),
			array(
				'id' => 'groups',
				'val' => 'groups',
				'name' => $this->l('Change the behavior of the taxes display for the groups')
			)
		);

		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Import a localization pack'),
				'icon' => 'icon-globe'
			),
			'input' => array(
				array(
					'type' => 'select',
					'class' => 'chosen',
					'label' => $this->l('Localization pack you want to import'),
					'name' => 'iso_localization_pack',
					'options' => array(
						'query' => $localizations_pack,
						'id' => 'iso_localization_pack',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Content to import'),
					'name' => 'selection[]',
					'lang' => true,
					'values' => array(
						'query' => $selection_import,
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type'	 => 'radio',
					'label'  => $this->l('Download pack data'),
					'desc' 	 => $this->l('If set to yes then the localization pack will be downloaded from prestashop.com. Otherwise the local xml file found in the localization folder of your PrestaShop installation will be used.'),
					'name' 	 => 'download_updated_pack',
					'class'  => 't',
					'is_bool'=> true,
					'values' => array(
						array(
							'id' 	=> 'download_updated_pack_yes',
							'value'	=> 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' 	=> 'download_updated_pack_no',
							'value'	=> 0,
							'label' => $this->l('No')
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('Import'),
				'icon' => 'process-icon-import',
				'name' => 'submitLocalizationPack'
			),
		);

		$this->fields_value = array(
			'selection[]_states' => true,
			'selection[]_taxes' => true,
			'selection[]_currencies' => true,
			'selection[]_languages' => true,
			'selection[]_units' => true,
			'download_updated_pack' => 1
		);

		$this->show_toolbar = true;
		return parent::renderForm();
	}

	public function initContent()
	{
		$this->initTabModuleList();
		if (!$this->loadObject(true))
			return;	

		$this->initToolbar();
		$this->initPageHeaderToolbar();
		$this->context->smarty->assign(array(
			'localization_form' => $this->renderForm(),
			'localization_options' => $this->renderOptions(),
			'url_post' => self::$currentIndex.'&token='.$this->token,
			'show_page_header_toolbar' => $this->show_page_header_toolbar,
			'page_header_toolbar_title' => $this->page_header_toolbar_title,
			'page_header_toolbar_btn' => $this->page_header_toolbar_btn
		));
	}
	
	public function display()
	{
		$this->initContent();
		parent::display();
	}

	public function beforeUpdateOptions()
	{
		$lang = new Language((int)Tools::getValue('PS_LANG_DEFAULT'));

		if (!$lang->active)
		{
			$lang->active = 1;
			$lang->save();
		}
	}

	public function updateOptionPsCurrencyDefault($value)
	{
		Configuration::updateValue('PS_CURRENCY_DEFAULT', $value);

		/* Set conversion rate of default currency to 1 */
		ObjectModel::updateMultishopTable('Currency', array('conversion_rate' => 1), 'a.id_currency');

		Currency::refreshCurrencies();
	}
}
