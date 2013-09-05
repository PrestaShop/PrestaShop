<?php
/**
 * 2007-2013 PrestaShop
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
 *  @copyright 2007-2013 PrestaShop SA : 6 rue lacepede, 75005 PARIS
 *  @version  Release: $Revision: 14011 $
 *  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 **/

class TwengaFieldsGetTrackingScript extends TwengaFields
{
	public function __construct()
	{
		if (!is_array($this->fields) AND empty($this->fields))
		{
			// required
			$this->fields['PARTNER_AUTH_KEY'] = array(56, array('is_string', 'isCleanHtml'), true);
			$this->fields['key'] = array(32, array('is_string','isCleanHtml'), true);
			$this->fields['total_ht'] = array(0, array('isFloat'), true);
			$this->fields['order_id'] = array(40, array('isInt'));
			$this->fields['user_id'] = array(40, array('isInt'));
			$this->fields['cli_email'] = array(100, array('isEmail'));
			$this->fields['basket_id'] = array(40, array('isInt'));
			
			// optionnals
			$this->fields['currency'] = array(40, array('is_string','isCleanHtml'));
			$this->fields['total_ttc'] = array(0, array('isFloat'));
			$this->fields['shipping'] = array(0, array('isFloat'));
			$this->fields['tax'] = array(0, array('isFloat'));
			$this->fields['tva'] = array(0, array('isFloat'));
			$this->fields['cli_firstname'] = array(0, array('is_string','isCleanHtml'));
			$this->fields['cli_lastname'] = array(0, array('is_string','isCleanHtml'));
			$this->fields['cli_city'] = array(0, array('is_string','isCleanHtml'));
			$this->fields['cli_state'] = array(0, array('is_string','isCleanHtml'));
			$this->fields['cli_country'] = array(0, array('is_string','isCleanHtml'));
			$this->fields['cli_segment'] = array(0, array('is_string','isCleanHtml'));
			$this->fields['payement_method'] = array(0, array('is_string','isCleanHtml'));
			
			
			// Array of items
			$this->fields['items'] = array(0, array('is_array', 'isItemsOrder'));
		}
		parent::__construct();
	}
	public static function isItemsOrder ($value)
	{
		$str_error = Tools::displayError('For the items');
		foreach ($value as $key=>$item)
		{
			$str_error .= ' '.$key.' ';
			$str_error .= isset($item['name']) ? $item['name'].' ' : '';
			$str_error .= ' :';
			if (isset($item['total_ht']) && !Validate::isFloat($item['total_ht']))
				throw new TwengaFieldsException($str_error.Tools::displayError('The total HT must be a float value.'));
			if (isset($item['quantity']) && !Validate::isInt($item['quantity']))
				throw new TwengaFieldsException($str_error.Tools::displayError('The quantity must be a integer value.'));
			if (isset($item['sku']) && !is_string($item['sku']) && strlen($item['sku']) > 40)
				throw new TwengaFieldsException($str_error.Tools::displayError('The sku must be a string with length less than 40 chars.'));
			if (isset($item['name']) && !is_string($item['name']))
				throw new TwengaFieldsException($str_error.Tools::displayError('The name must be a string with length less than 100 chars.'));
			if (isset($item['category_name']) && !is_string($item['category_name']))
				throw new TwengaFieldsException($str_error.Tools::displayError('The category name must be a string with length less than 100 chars.'));
		}
		return true;
	}
}