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

function validate_isName(s)
{
	var reg = /^[^0-9!<>,;?=+()@#"°{}_$%:]+$/;
	return reg.test(s);
}

function validate_isGenericName(s)
{
	var reg = /^[^<>={}]+$/;
	return reg.test(s);
}

function validate_isAddress(s)
{
	var reg = /^[^!<>?=+@{}_$%]+$/;
	return reg.test(s);
}

function validate_isPostCode(s)
{
	var reg = /^[a-z 0-9-]+$/i;
	return reg.test(s);
}

function validate_isCityName(s)
{
	var reg = /^[^!<>;?=+@#"°{}_$%]+$/;
	return reg.test(s);
}

function validate_isMessage(s)
{
	var reg = /^[^<>{}]+$/;
	return reg.test(s);
}

function validate_isPhoneNumber(s)
{
	var reg = /^[+0-9. ()-]+$/;
	return reg.test(s);
}

function validate_isDniLite(s)
{
	var reg = /^[0-9a-z-.]{1,16}$/i;
	return reg.test(s);
}
