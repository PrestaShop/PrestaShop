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

class PrepaidServicesAPI
{
	const DISPOSITION_CREATED = 'R';
	const DISPOSITION_DISPOSED = 'S';
	const DISPOSITION_DEBITED = 'E';
	const DISPOSITION_CONSUMED = 'O';
	const DISPOSITION_CANCELLED = 'L';
	const DISPOSITION_INVALID = 'I';
	const DISPOSITION_EXPIRED = 'X';

	// Test & Production environment (shared between CT & PSC)
	private static $base_url = array('T' => array('create_disposition_url'     => 'https://shops.test.at.paysafecard.com/pscmerchant/CreateDispositionServlet',
											'get_disposition_state_url' => 'https://shops.test.at.paysafecard.com/pscmerchant/GetDispositionStateServlet',
											'execute_debit_url'         => 'https://shops.test.at.paysafecard.com/pscmerchant/DebitServlet',
											'get_serial_number_url'     => 'https://shops.test.at.paysafecard.com/pscmerchant/GetSerialNumbersServlet'
										   ),
							  'P' => array('create_disposition_url'     => 'https://shops.cc.at.paysafecard.com/pscmerchant/CreateDispositionServlet',
											'get_disposition_state_url' => 'https://shops.cc.at.paysafecard.com/pscmerchant/GetDispositionStateServlet',
											'execute_debit_url'         => 'https://shops.cc.at.paysafecard.com/pscmerchant/DebitServlet',
											'get_serial_number_url'     => 'https://shops.cc.at.paysafecard.com/pscmerchant/GetSerialNumbersServlet'
										   ));
	
	public static function getBaseUrl($key, $env)
	{
		$base_url = self::$base_url[$env];
		
		if (array_key_exists($key, $base_url)) 
			return $base_url[$key];
			
		return '';
	}
	
	public static function createDisposition($configuration, $mid, $mtid, $amount, $currency, $okurl, $nokurl, $businesstype, $reportingcriteria)
	{
		$language = 'en';
		$params = 'currency='.$currency.'&mid='.$mid.'&mtid='.$mtid.'&amount='.$amount.'&businesstype='.$businesstype.
			      '&reportingcriteria='.$reportingcriteria.'&okurl='.$okurl.'&nokurl='.$nokurl.'&language='.$language;

		
		list ($rc, $msg, $data) = self::_doHttpRequest(self::getBaseUrl('create_disposition_url', $configuration['env']), $params, $configuration['keyring_file'], $configuration['keyring_pw'], $configuration['keyring_prepaid']);
		
		if ($rc == 0) {
			$data_array = explode("\n", $data,7);
			$resultcode = trim($data_array[0]);
			$errorcode = trim($data_array[1]);
			$errormessage = trim($data_array[2]);
      
			return array($resultcode, $errorcode, $errormessage);
		} else {
			$resultcode = '9001';
			$errorcode = $rc;
			$errormessage = 'libcurl error: '.$msg;

			return array($resultcode,$errorcode,$errormessage);
		}
	}
	
	public static function getDispositionState($configuration, $mid, $mtid, $currency_iso_code)
	{
		$language = 'en';
		$params = 'mid='.$mid.'&mtid='.$mtid.'&language='.$language;

		list ($rc, $msg, $data) = self::_doHttpRequest(self::getBaseUrl('get_disposition_state_url', $configuration['env']), $params, $configuration['keyring_file'], $configuration['keyring_pw'], $configuration['keyring_prepaid']);
		
		if ($rc == 0) {
			$dataarray = explode("\n", $data,7);
			$resultcode = trim($dataarray[0]);
			$errorcode = trim($dataarray[1]);
			$errormessage = trim($dataarray[2]);
			$amount = trim($dataarray[3]);
			$currency = trim($dataarray[4]);
			$state = trim($dataarray[5]);
			
			return array($resultcode, $errorcode, $errormessage, $amount, $currency, $state);
	   } else {
			$resultcode = '9001';
			$errorcode = $rc;
			$errormessage = 'libcurl error: '.$msg;
		  
		  return array($resultcode, $errorcode, $errormessage);
	   }
	}
	
	public static function getSerialNumbers($configuration, $mid, $mtid, $currency_iso_code)
	{
		$language = 'en';
		$params = 'mid='.$mid.'&mtid='.$mtid.'&language='.$language;

	   list ($rc, $msg, $data) = self::_doHttpRequest(self::getBaseUrl('get_serial_number_url', $configuration['env']), $params, $configuration['keyring_file'], $configuration['keyring_pw'], $configuration['keyring_prepaid']);
	   if ($rc == 0) {
		/* read and return data from paysafecard server */
			$dataarray = explode("\n", $data,7);
			$resultcode = trim($dataarray[0]);
			$errorcode = trim($dataarray[1]);
			$errormessage = trim($dataarray[2]);
			$amount = trim($dataarray[3]);
			$currency = trim($dataarray[4]);
			$state = trim($dataarray[5]);
			$snamount = trim($dataarray[6]);
			return array ($resultcode, $errorcode, $errormessage, $amount, $currency, $state, $snamount);
		} else {
			$resultcode = '9001';
			$errorcode = $rc;
			$errormessage = 'libcurl error: '.$msg;
			
			return array ($resultcode, $errorcode, $errormessage);
		}
	}
	
	public static function executeDebit($configuration, $mid, $mtid, $amount, $currency, $close_flag = 1)
	{
		$language = 'en';
		$params = 'currency='.$currency.'&mid='.$mid.'&mtid='.$mtid.'&amount='.$amount.'&close='.$close_flag.'&language='.$language;

		list ($rc, $msg, $data) = self::_doHttpRequest(self::getBaseUrl('execute_debit_url', $configuration['env']), $params, $configuration['keyring_file'], $configuration['keyring_pw'], $configuration['keyring_prepaid']);

		if ($rc == 0) {
			$dataarray=explode("\n", $data,7);
			$resultcode=trim($dataarray[0]);
			$errorcode=trim($dataarray[1]);
			$errormessage=trim($dataarray[2]);
			
			return array ($resultcode, $errorcode, $errormessage);
		} else {
			$resultcode = '9001';
			$errorcode = $rc;
			$errormessage = 'libcurl error: '.$msg;
			
			return array ($resultcode, $errorcode, $errormessage);
		}
	}
	
	private static function _doHttpRequest($url, $urlparam, $keyringfile, $keyringpw, $cakeyringfile)
	{
		/* some prerquisites for the connection */
	   $ch = curl_init($url);
	   curl_setopt($ch, CURLOPT_POST, 1);  // a non-zero parameter tells the library to do a regular HTTP post.
	   curl_setopt($ch, CURLOPT_POSTFIELDS, $urlparam);  // add POST fields
	   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);  // don't allow redirects
	   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // return into a variable
	   curl_setopt($ch, CURLOPT_TIMEOUT, 240);  // maximum time, in seconds, that you'll allow the CURL functions to take
	   curl_setopt($ch, CURLOPT_SSLCERT, $keyringfile); // filename of PEM formatted certificate
	   curl_setopt($ch, CURLOPT_SSLCERTTYPE, "PEM"); // format of certificate, "PEM" or "DER"
	   curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $keyringpw); // password required to use the CURLOPT_SSLCERT certificate
	   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); // verify the peer's certificate
	   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // verify the Common name from the peer certificate
	   curl_setopt($ch, CURLOPT_CAINFO, $cakeyringfile); // file holding one or more certificates to verify the peer with
	   
	   $data = curl_exec($ch);
	   $errno = curl_errno($ch);
	   $errmsg = curl_error($ch);

		/* bug fix for PHP 4.1.0/4.1.2 (curl_errno() returns high negative
		* value in case of successful termination) */
	   if ($errno < 0) $errno = 0;
	
	   curl_close($ch);

	   return array ($errno,$errmsg,$data);
	}
}


