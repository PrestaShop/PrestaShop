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
*  @version  Release: $Revision: 7465 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminLocalizationControllerCore extends AdminController
{
	public function __construct()
	{
		$this->className = 'Configuration';
		$this->table = 'configuration';

		$this->options = array(
			'localization' => array(
				'title' =>	$this->l('Localization'),
				'width' =>	'width2',
				'icon' =>	'localization',
				'fields' =>	array(
					'PS_WEIGHT_UNIT' => array(
						'title' => $this->l('Weight unit:'),
						'desc' => $this->l('The weight unit of your shop (eg. kg or lbs)'),
						'validation' => 'isWeightUnit',
						'required' => true,
						'type' => 'text'
					),
					'PS_DISTANCE_UNIT' => array(
						'title' => $this->l('Distance unit:'),
						'desc' => $this->l('The distance unit of your shop (eg. km or mi)'),
						'validation' => 'isDistanceUnit',
						'required' => true,
						'type' => 'text'
					),
					'PS_VOLUME_UNIT' => array(
						'title' => $this->l('Volume unit:'),
						'desc' => $this->l('The volume unit of your shop'),
						'validation' => 'isWeightUnit',
						'required' => true,
						'type' => 'text'
					),
					'PS_DIMENSION_UNIT' => array(
						'title' => $this->l('Dimension unit:'),
						'desc' => $this->l('The dimension unit of your shop (eg. cm or in)'),
						'validation' => 'isDistanceUnit',
						'required' => true,
						'type' => 'text'
					)
				),
				'submit' => array('title' => $this->l('   Save   '), 'class' => 'button')
			),
			'options' => array(
				'title' =>	$this->l('Advanced'),
				'width' =>	'width2',
				'icon' =>	'localization',
				'fields' =>	array(
					'PS_LOCALE_LANGUAGE' => array(
						'title' => $this->l('Language locale:'),
						'desc' => $this->l('Your server\'s language locale.'),
						'validation' => 'isLanguageIsoCode',
						'type' => 'text',
						'visibility' => Shop::CONTEXT_ALL
					),
					'PS_LOCALE_COUNTRY' => array(
						'title' => $this->l('Country locale:'),
						'desc' => $this->l('Your server\'s country locale.'),
						'validation' => 'isLanguageIsoCode',
						'type' => 'text',
						'visibility' => Shop::CONTEXT_ALL
					)
				)
			)
		);

		parent::__construct();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitLocalizationPack'))
		{
			if (!$pack = @Tools::file_get_contents('http://www.prestashop.com/download/localization/'.Tools::getValue('iso_localization_pack').'.xml') &&
				!$pack = @Tools::file_get_contents(dirname(__FILE__).'/../../localization/'.Tools::getValue('iso_localization_pack').'.xml'))
				$this->_errors[] = Tools::displayError('Cannot load localization pack (from prestashop.com and from your local folder "localization")');
			else if (!$selection = Tools::getValue('selection'))
				$this->_errors[] = Tools::displayError('Please select at least one content item to import.');
			else
			{
				foreach ($selection as $selected)
					if (!Validate::isLocalizationPackSelection($selected))
					{
						$this->_errors[] = Tools::displayError('Invalid selection');
						return;
					}
				$localization_pack = new LocalizationPack();
				if (!$localization_pack->loadLocalisationPack($pack, $selection))
					$this->_errors = array_merge($this->_errors, $localization_pack->getErrors());
				else
					Tools::redirectAdmin(self::$currentIndex.'&conf=23&token='.$this->token);
			}
		}

		parent::postProcess();
	}

	public function initForm()
	{
		$localizations_pack = false;
		$this->tpl_option_vars['options_content'] = $this->initOptions();

		$xml_localization = Tools::simplexml_load_file('http://www.prestashop.com/rss/localization.xml');
		if (!$xml_localization)
		{
			$localization_file = dirname(__FILE__).'/../../localization/localization.xml';
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

		$selection_import = array(
			array(
				'id' => 'states',
				'name' => $this->l('States')
			),
			array(
				'id' => 'taxes',
				'name' => $this->l('Taxes')
			),
			array(
				'id' => 'currencies',
				'name' => $this->l('Currencies')
			),
			array(
				'id' => 'languages',
				'name' => $this->l('Languages')
			),
			array(
				'id' => 'units',
				'name' => $this->l('Units (e.g., weight, volume, distance)')
			)
		);

		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Localization pack import'),
				'image' => '../img/admin/localization.gif'
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Localization pack you want to import:'),
					'name' => 'iso_localization_pack',
					'options' => array(
						'query' => $localizations_pack,
						'id' => 'iso_localization_pack',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Content to import:'),
					'name' => 'selection[]',
					'lang' => true,
					'values' => array(
						'query' => $selection_import,
						'id' => 'id',
						'name' => 'name'
					)
				)
			),
			'submit' => array(
				'title' => $this->l('   Import   '),
				'class' => 'button',
				'name' => 'submitLocalizationPack'
			)
		);

		$this->fields_value = array(
			'selection[]_states' => true,
			'selection[]_taxes' => true,
			'selection[]_currencies' => true,
			'selection[]_languages' => true,
			'selection[]_units' => true
		);

		$this->show_toolbar = false;
		return parent::initForm();
	}

	public function initContent()
	{
		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		$this->content .= $this->initOptions();

		if (!$this->loadObject(true))
			return;

		$this->content .= $this->initForm();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}
}
