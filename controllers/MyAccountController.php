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

class MyAccountControllerCore extends FrontController
{
	public function __construct()
	{
		$this->auth = true;
		$this->php_self = 'my-account.php';
		$this->authRedirection = 'my-account.php';
		$this->ssl = true;
	
		parent::__construct();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		Tools::addCSS(_THEME_CSS_DIR_.'my-account.css');
	}
	
	public function process()
	{
		parent::process();
		
		self::$smarty->assign(array(
			'voucherAllowed' => (int)(Configuration::get('PS_VOUCHERS')),
			'returnAllowed' => (int)(Configuration::get('PS_ORDER_RETURN'))
		));
		self::$smarty->assign('HOOK_CUSTOMER_ACCOUNT', Module::hookExec('customerAccount'));
	}
	
	public function displayContent()
	{
		parent::displayContent();
		self::$smarty->display(_PS_THEME_DIR_.'my-account.tpl');
	}
}

