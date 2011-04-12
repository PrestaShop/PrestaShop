<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class TrustedShopsSoapApi
{
	const TS_SERVER   = 'www.trustedshops.de';
	const WS_USER     = 'presta-shopsoftware';
	const WS_PASSWORD = 'Yx1F5uXR';
	
	const ACTIVATE    = 1;
	const DESACTIVATE = 0;
	
	const RT_OK = 'OK';
	const RT_SOAP_ERROR      = -1;
	const RT_INVALID_TSID    = 'INVALID_TSID';
	const RT_NOT_REGISTERED  = 'NOT_REGISTERED_FOR_TRUSTEDRATING';
	const RT_WRONG_LOGIN     = 'WRONG_WSUSERNAME_WSPASSWORD';
	

	public static function validate($partener_package, $trusted_shops_id, $action = self::ACTIVATE)
	{
		$ini = ini_set('soap.wsdl_cache_enabled', 1);
		$result = self::RT_SOAP_ERROR;
		
		try
		{
			$wsdlUrl = 'https://'.self::TS_SERVER.'/ts/services/TsRating?wsdl';
			$client  = new SoapClient($wsdlUrl);
			
			$result = $client->updateRatingWidgetState($trusted_shops_id, $action, self::WS_USER, self::WS_PASSWORD, $partener_package);
		}
		catch(SoapFault $fault) 
		{
			$errorText = 'SOAP Fault: (faultcode:{$fault->faultcode}, faultstring:{$fault->faultstring})';
			
			/** Enable this line if you are experiencing issues with your Trusted Shops ID activation. 
			die($errorText);
			*/
		}
		
		if ($result == self::RT_WRONG_LOGIN)
			die('Wrong login/password');

		return $result;
	}
}

