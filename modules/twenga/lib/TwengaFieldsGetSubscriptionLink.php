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
 *  @version  Release: $Revision: 16958 $
 *  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 **/

class TwengaFieldsGetSubscriptionLink extends TwengaFields
{
	public function __construct()
	{
		if (!is_array($this->fields) AND empty($this->fields))
		{
			$this->fields['PARTNER_AUTH_KEY'] = array(56, array('is_string', 'isCleanHtml'), true);
			$this->fields['site_url'] = array(200, array('is_string', 'isUrl'), true);
			$this->fields['feed_url'] = array(200, array('is_string', 'isUrl'), true);
			$this->fields['country'] = array(2, array('is_string', 'isCountryName'), true);
			$this->fields['site_id'] = array(32, array('is_string', 'isCleanHtml'));
			$this->fields['site_name'] = array(100, array('is_string', 'isCleanHtml'));
			$this->fields['firstname'] = array(100, array('is_string', 'isCleanHtml'));
			$this->fields['lastname'] = array(100, array('is_string', 'isCleanHtml'));
			$this->fields['civility'] = array(40, array('is_string', 'isCleanHtml'));
			$this->fields['position'] = array(100, array('is_string', 'isCleanHtml'));
			$this->fields['email'] = array(100, array('is_string', 'isEmail'));
			$this->fields['phone'] = array(30, array('is_string', 'isPhoneNumber'));
			$this->fields['mobile'] = array(30, array('is_string', 'isPhoneNumber'));
			$this->fields['organisation_name'] = array(100, array('is_string', 'isCleanHtml'));
			$this->fields['legaltype'] = array(30, array('is_string', 'isCleanHtml'));
			$this->fields['address'] = array(100, array('is_string', 'isCleanHtml'));
			$this->fields['postal_code'] = array(10, array('is_string', 'isCleanHtml'));
			$this->fields['city'] = array(30, array('is_string', 'isCityName'));
			$this->fields['module_version'] = array(10, array('is_string', 'isCleanHtml'));
			$this->fields['platform_version'] = array(12, array('is_string', 'isCleanHtml'));
		}
		parent::__construct();
	}   
}