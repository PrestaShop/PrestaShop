<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

//
// IMPORTANT : don't forget to delete the underscore _ in the file name if you want to use it !
//

// if FB class is already loaded, just enable it. else, enable it only if fb.php exists and is loaded
if (!defined('PS_USE_FIREPHP') AND class_exists('FB'))
	define('PS_USE_FIREPHP',true);
elseif (file_exists(_PS_TOOL_DIR_.'FirePHP/fb.php'))
{
	if (!defined('PS_USE_FIREPHP'))
	{
		require_once(_PS_TOOL_DIR_.'FirePHP/fb.php');
		define('PS_USE_FIREPHP',true);
	}
	else
		define('PS_USE_FIREPHP',false);
}
else
	define('PS_USE_FIREPHP',class_exists('FB'));

class Tools extends ToolsCore
{
	/**
	* Redirect user to another page after 5 sec
	*
	* @param string $url Desired URL
	* @param string $baseUri Base URI (optional)
	*/
	public static function redirect($url, $baseUri = __PS_BASE_URI__, Link $link = null)
	{
		if (!$link)
			$link = Context::getContext()->link;
		if (strpos($url, 'http://') === FALSE && strpos($url, 'https://') === FALSE)
		{
			if (strpos($url, $baseUri) !== FALSE && strpos($url, $baseUri) == 0)
				$url = substr($url, strlen($baseUri));
			$explode = explode('?', $url);
			$url = $link->getPageLink($explode[0], true);
			if (isset($explode[1]))
				$url .= '?'.$explode[1];
			$baseUri = '';
		}

		if (isset($_SERVER['HTTP_REFERER']) AND ($url == $_SERVER['HTTP_REFERER']))
    	header('Refresh: 5; url='.$_SERVER['HTTP_REFERER']);
		else
			header('Refresh: 5; url='.$baseUri.$url);
		echo '<h1>Redirection automatique dans 5 secondes</h1><a href='.$url.'>'.$url.'</a>';
		exit;
	}


	/**
	* Redirect url wich allready PS_BASE_URI after 5 sec
	*
	* @param string $url Desired URL
	*/
	public static function redirectLink($url)
	{
		if (!preg_match('@^https?://@i', $url))
		{
			if (strpos($url, __PS_BASE_URI__) !== FALSE && strpos($url, __PS_BASE_URI__) == 0)
				$url = substr($url, strlen(__PS_BASE_URI__));
			$explode = explode('?', $url);
			$url = Context::getContext()->link->getPageLink($explode[0]);
			if (isset($explode[1]))
				$url .= '?'.$explode[1];
		}

		header('Refresh: 5; url='.$url);
		echo '<h1>Redirection automatique dans 5 secondes</h1><a href='.$url.'>'.$url.'</a>';
		exit;
	}
	/**
	* Redirect user to another admin page after 5 sec
	*
	* @param string $url Desired URL
	*/
	public static function redirectAdmin($url)
	{
		header('Refresh: 5; url='.$url);
		echo '<h1>Redirection automatique dans 5 secondes</h1><a href='.$url.'>'.$url.'</a>';
		exit;
	}


	/**
	* Display an error with detailed object
	* (display in firefox console if Firephp is enabled)
	*
	* @param mixed $object
	* @param boolean $kill
	* @return $object if $kill = false;
	*/
	public static function dieObject($object, $kill = true)
	{
		if(PS_USE_FIREPHP)
			FB::error($object);
		else
			return parent::dieObject($object,$kill);

		if ($kill)
			die('END');
		return $object;
	}

	/**
	* ALIAS OF dieObject() - Display an error with detailed object
	* (display in firefox console if Firephp is enabled)
	*
	* @param object $object Object to display
	*/
	public static function d($obj, $kill = true)
	{
		if(PS_USE_FIREPHP)
			FB::error($obj);
		else
			parent::d($obj,$kill);

		if ($kill)
			die('END');
		return $object;
	}

	/**
	* ALIAS OF dieObject() - Display an error with detailed object but don't stop the execution
	* (display in firefox console if Firephp is enabled)
	*
	* @param object $object Object to display
	*/
	public static function p($object)
	{
		if(PS_USE_FIREPHP)
			FB::info($object);
		else
			return parent::p($object);
		return $object;
	}

	/**
	* Display a warning message indicating that the method is deprecated
	* (display in firefox console if Firephp is enabled)
	*/
	public static function displayAsDeprecated($message = null)
	{
		if (_PS_DISPLAY_COMPATIBILITY_WARNING_)
		{
			$backtrace = debug_backtrace();
			$callee = next($backtrace);
			if (PS_USE_FIREPHP)
				FB::warn('Function <strong>'.$callee['function'].'()</strong> is deprecated in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['line'].'</strong><br />', 'Deprecated method');
			else
				trigger_error('Function <strong>'.$callee['function'].'()</strong> is deprecated in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['line'].'</strong><br />', E_USER_WARNING);

			$message = sprintf(
				Tools::displayError('The function %1$s (Line %2$s) is deprecated and will be removed in the next major version.'),
				$callee['function'],
				$callee['line']
			);
			Logger::addLog($message, 3, $callee['class']);
		}
	}

	/**
	 * Display a warning message indicating that the parameter is deprecated
	* (display in firefox console if Firephp is enabled)
	 */
	public static function displayParameterAsDeprecated($parameter)
	{
		if (_PS_DISPLAY_COMPATIBILITY_WARNING_)
		{
		$backtrace = debug_backtrace();
		$callee = next($backtrace);
			trigger_error('Parameter <strong>'.$parameter.'</strong> in function <strong>'.$callee['function'].'()</strong> is deprecated in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['Line'].'</strong><br />', E_USER_WARNING);

			if(PS_USE_FIREPHP)
				FB::trace('Parameter <strong>'.$parameter.'</strong> in function <strong>'.$callee['function'].'()</strong> is deprecated in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['Line'].'</strong><br />', 'deprecated parameter');
			else
				$message = sprintf(
					Tools::displayError('The parameter %1$s in function %2$s (Line %3$s) is deprecated and will be removed in the next major version.'),
					$parameter,
					$callee['function'],
					$callee['Line']
				);
			Logger::addLog($message, 3, $callee['class']);
		}
	}

	/**
	 * use of FirePHP::error() if allowed
	 *
	 * @param mixed $obj
	 * @param string $label
	 * @return void
	 */
	public static function error($obj, $label = '')
	{
		if(PS_USE_FIREPHP)
			FB::error($obj, $label);
	}

	/**
	 * use of FirePHP::warn() if allowed
	 *
	 * @param mixed $obj
	 * @param string $label
	 * @return void
	 */
	public static function warn($obj, $label = '')
	{
		if(PS_USE_FIREPHP)
			FB::warn($obj, $label);
	}

	/**
	 * use of FirePHP::info() if allowed
	 *
	 * @param mixed $obj
	 * @param string $label
	 * @return void
	 */
	public static function info($obj, $label = '')
	{
		if(PS_USE_FIREPHP)
			FB::info($obj, $label);
	}

	/**
	 * use of FirePHP::log() if allowed
	 *
	 * @param mixed $obj
	 * @param string $label
	 * @return void
	 */
	public static function log($obj, $label = '')
	{
		if(PS_USE_FIREPHP)
			FB::log($obj,$label);
	}
	/**
	* display debug_backtrace()
	* (display in firefox console if Firephp is enabled)
	*
	* @param mixed $obj
	* @return void
	*/
	public static function trace($obj = NULL, $label = '')
	{
		if(PS_USE_FIREPHP)
			FB::trace($obj, $label);
		else{
			Tools::p($obj);
			echo'<pre><h1>'.$label.'</h1><br/>';
			debug_print_backtrace();
			echo '</pre>';
		}
	}
}
// Add some convenient shortcut

if (!function_exists('error'))
{
	function error($obj, $label = ''){
		return Tools::error($obj, $label);
	}
}

if (!function_exists('warn'))
{
	function warn($obj, $label = ''){
		return Tools::warn($obj,$label);
	}
}

if (!function_exists('info'))
{
	function info($obj, $label = ''){
		return Tools::info($obj, $label);
	}
}

if (!function_exists('log'))
{
	function log($obj, $label = ''){
		return Tools::log($obj, $label);
	}
}

if (!function_exists('trace'))
{
	function trace($obj, $label = ''){
		return Tools::trace($obj, $label);
	}
}

