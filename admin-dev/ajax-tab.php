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

define('_PS_ADMIN_DIR_', getcwd());
define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility

include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
include(_PS_ADMIN_DIR_.'/functions.php');


include(_PS_ADMIN_DIR_.'/init.php');

if (empty($tab) and !sizeof($_POST))
{
	$tab = 'AdminHome';
	$_POST['tab'] = 'AdminHome';
	$_POST['token'] = Tools::getAdminTokenLite($tab);
}
	if ($id_tab = checkingTab($tab))
	{
    	$isoUser = Language::getIsoById(intval($cookie->id_lang));


		if (Validate::isLoadedObject($adminObj))
		{
			$adminObj->ajax = true;
			if ($adminObj->checkToken())
			{
				// the differences with index.php is here 

				$adminObj->ajaxPreProcess();
				$action = Tools::getValue('action');

				// no need to use displayConf() here

				if (!empty($action) AND method_exists($adminObj, 'ajaxProcess'.Tools::toCamelCase($action)) )
					$adminObj->{'ajaxProcess'.Tools::toCamelCase($action)}();
				else
					$adminObj->ajaxProcess();
				
				// @TODO We should use a displayAjaxError
				$adminObj->displayErrors();
				if (!empty($action) AND method_exists($adminObj, 'displayAjax'.Tools::toCamelCase($action)) )
					$adminObj->{'displayAjax'.$action}();
				else
					$adminObj->displayAjax();

			}
			else
			{
				// If this is an XSS attempt, then we should only display a simple, secure page
				ob_clean();

				// ${1} in the replacement string of the regexp is required, because the token may begin with a number and mix up with it (e.g. $17)
				$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$adminObj->token.'$2', $_SERVER['REQUEST_URI']);
				if (false === strpos($url, '?token=') AND false === strpos($url, '&token='))
					$url .= '&token='.$adminObj->token;

				// we can display the correct url
				// die(Tools::jsonEncode(array(translate('Invalid security token'),$url)));
				die(Tools::jsonEncode(translate('Invalid security token')));
			}
		}
	}



