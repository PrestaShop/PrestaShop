<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PageNotFoundControllerCore extends FrontController
{
	public $php_self = '404';
	public $page_name = 'pagenotfound';

	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Found');

		if (in_array(Tools::strtolower(substr($_SERVER['REQUEST_URI'], -3)), array('png', 'jpg', 'gif')))
		{
			header('Content-Type: image/gif');
			readfile(_PS_IMG_DIR_.'404.gif');
			exit;
		}
		elseif (in_array(Tools::strtolower(substr($_SERVER['REQUEST_URI'], -3)), array('.js', 'css')))
			exit;

		parent::initContent();

		$this->setTemplate(_PS_THEME_DIR_.'404.tpl');
	}
	
	public function canonicalRedirection($canonical_url = '')
	{
		// 404 - no need to redirect to the canonical url
	}
}

