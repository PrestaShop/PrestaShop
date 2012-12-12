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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

function fd($var)
{
	return (Tools::fd($var));
}

function p($var)
{
	return (Tools::p($var));
}

function d($var)
{
	Tools::d($var);
}

function ppp($var)
{
	return (Tools::p($var));
}

function ddd($var)
{
	Tools::d($var);
}

/**
 * Sanitize data which will be injected into SQL query
 *
 * @param string $string SQL data which will be injected into SQL query
 * @param boolean $htmlOK Does data contain HTML code ? (optional)
 * @return string Sanitized data
 */
function pSQL($string, $htmlOK = false)
{
	// Avoid thousands of "Db::getInstance()"...
	static $db = false;
	if (!$db)
		$db = Db::getInstance();

	return $db->escape($string, $htmlOK);
}

function bqSQL($string)
{
	return str_replace('`', '\`', pSQL($string));
}

/**
 * @deprecated
 */
function nl2br2($string)
{
	Tools::displayAsDeprecated();
	return Tools::nl2br($string);
}