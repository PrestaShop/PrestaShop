<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class FrontController extends FrontControllerCore
{
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		if (!is_array($css_uri))
			$css_uri = array($css_uri);

		$new_uri = array();
		foreach ($css_uri as $uri)
			if ($uri && !preg_match('/^http(s?):\/\//', $uri) && preg_match('#.css$#', $uri))
				$new_uri[] = 'http://'.Tools::getMediaServer($uri).$uri;
			else
				$new_uri[] = $uri;

		return parent::addCSS($new_uri, $css_media_type);
	}

	public function addJS($js_uri)
	{
		if (!is_array($js_uri))
			$js_uri = array($js_uri);

		foreach ($js_uri as &$uri)
			if ($uri && !preg_match('/^http(s?):\/\//', $uri))
				$uri = 'http://'.Tools::getMediaServer($uri).$uri;
		return parent::addJS($js_uri);
	}
}