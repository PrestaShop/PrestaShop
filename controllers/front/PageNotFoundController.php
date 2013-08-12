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
			if ((bool)Configuration::get('PS_REWRITING_SETTINGS'))
				preg_match('¤([0-9]+)(\-[_a-zA-Z0-9-]*)?(-[0-9]+)?/(.+)\.(png|jpg|gif)$¤', $_SERVER['REQUEST_URI'], $matches);
			if ((!isset($matches[2]) || empty($matches[2])) && !(bool)Configuration::get('PS_REWRITING_SETTINGS'))
				preg_match('¤/([0-9]+)(\-[_a-zA-Z]*)\.(png|jpg|gif)$¤', $_SERVER['REQUEST_URI'], $matches);

			if (is_array($matches) && !empty($matches[2]) && Tools::strtolower(substr($matches[2], -8)) != '_default' && is_numeric($matches[1]))
			{			
				$matches[2] = substr($matches[2], 1, Tools::strlen($matches[2])).'_default';
				if (!isset($matches[4]))
					$matches[4] = '';
				header('Location: '.$this->context->link->getImageLink($matches[4], $matches[1], $matches[2]), true, 302);
				exit;
			}			

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

