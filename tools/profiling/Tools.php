<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Tools extends ToolsCore
{
	public static function redirect($url, $base_uri = __PS_BASE_URI__, Link $link = null, $headers = null)
	{
		if (!$link)
			$link = Context::getContext()->link;

		if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $link)
		{
			if (strpos($url, $base_uri) === 0)
				$url = substr($url, strlen($base_uri));
			if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0)
			{
				$url = substr($url, strlen('index.php?controller='));
				if (Configuration::get('PS_REWRITING_SETTINGS'))
					$url = Tools::strReplaceFirst('&', '?', $url);
			}

			$explode = explode('?', $url);
			// don't use ssl if url is home page
			// used when logout for example
			$use_ssl = !empty($url);
			$url = $link->getPageLink($explode[0], $use_ssl);
			if (isset($explode[1]))
				$url .= '?'.$explode[1];
		}

		// Send additional headers
		if ($headers)
		{
			if (!is_array($headers))
				$headers = array($headers);

			foreach ($headers as $header)
				header($header);
		}

		Context::getContext()->controller->setRedirectAfter($url);
	}

	public static function redirectLink($url)
	{
		if (!preg_match('@^https?://@i', $url))
		{
			if (strpos($url, __PS_BASE_URI__) !== false && strpos($url, __PS_BASE_URI__) == 0)
				$url = substr($url, strlen(__PS_BASE_URI__));
			$explode = explode('?', $url);
			$url = Context::getContext()->link->getPageLink($explode[0]);
			if (isset($explode[1]))
				$url .= '?'.$explode[1];
		}

		Context::getContext()->controller->setRedirectAfter($url);
	}

	public static function redirectAdmin($url)
	{
		if (!is_object(Context::getContext()->controller))
			Context::getContext()->controller = new StdClass();
		Context::getContext()->controller->setRedirectAfter($url);
	}
}
