<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class LoyaltyStateModule extends ObjectModel
{
	public $name;
	public $id_order_state;

	protected $fieldsValidate = array('id_order_state' => 'isInt');
	protected $fieldsRequiredLang = array('name');
	protected $fieldsSizeLang = array('name' => 128);
	protected $fieldsValidateLang = array('name' => 'isGenericName');

	protected $table = 'loyalty_state';
	protected $identifier = 'id_loyalty_state';

	public function getFields()
	{
		parent::validateFields();
		$fields['id_order_state'] = (int)($this->id_order_state);
		return $fields;
	}

	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name'));
	}

	static public function getDefaultId() { return 1; }
	static public function getValidationId() { return 2; }
	static public function getCancelId() { return 3; }
	static public function getConvertId() { return 4; }
	static public function getNoneAwardId() { return 5; }

	static public function insertDefaultData()
	{
		$loyaltyModule = new Loyalty();
		$languages = Language::getLanguages();
		
		$defaultTranslations = array('default' => array('id_loyalty_state' => (int)LoyaltyStateModule::getDefaultId(), 'default' => $loyaltyModule->getL('Awaiting validation'), 'en' => 'Awaiting validation', 'fr' => 'En attente de validation'));
		$defaultTranslations['validated'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getValidationId(), 'id_order_state' => _PS_OS_DELIVERED_, 'default' => $loyaltyModule->getL('Available'), 'en' => 'Available', 'fr' => 'Disponible');
		$defaultTranslations['cancelled'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getCancelId(), 'id_order_state' => _PS_OS_CANCELED_, 'default' => $loyaltyModule->getL('Cancelled'), 'en' => 'Cancelled', 'fr' => 'Annulés');
		$defaultTranslations['converted'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getConvertId(), 'default' => $loyaltyModule->getL('Already converted'), 'en' => 'Already converted', 'fr' => 'Déjà convertis');
		$defaultTranslations['none_award'] = array('id_loyalty_state' => (int)LoyaltyStateModule::getNoneAwardId(), 'default' => $loyaltyModule->getL('Unavailable on discounts'), 'en' => 'Unavailable on discounts', 'fr' => 'Non disponbile sur produits remisés');
		
		foreach ($defaultTranslations AS $loyaltyState)
		{
			$state = new LoyaltyStateModule((int)$loyaltyState['id_loyalty_state']);
			if (isset($loyaltyState['id_order_state']))
				$state->id_order_state = (int)$loyaltyState['id_order_state'];
			$state->name[(int)Configuration::get('PS_LANG_DEFAULT')] = $loyaltyState['default'];
			foreach ($languages AS $language)
				if (isset($loyaltyState[$language['iso_code']]))
					$state->name[(int)$language['id_lang']] = $loyaltyState[$language['iso_code']];
			$state->save();
		}

		return true;
	}
}