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

/// @todo document and handle errors
class SWCurl
{
	/** @var _ch Curl Handler Resource */
	private $_ch;
	/** @var _url Url to call */
	private $_url;
	/** @var _lastResponse Last HTTP response fetched */
	private $_lastResponse;
	/** @var _params Array containing data for Methods */
	private $_params;

	public function __construct($url = null, $sslCheck = true)
	{
		$this->_ch = curl_init();
		$this->_url = $url;
		/* $this->_setOpt(CURLOPT_HEADER, true); */
		/* $this->_setOpt(CURLINFO_HEADER_OUT, true); */
		$this->_setOpt(CURLOPT_RETURNTRANSFER, true);
		if (!$sslCheck)
			$this->_setOpt(CURLOPT_SSL_VERIFYPEER, false);
	}

	public function __destruct()
	{
		curl_close($this->_ch);
	}

	private function _setOpt($flag, $value)
	{
		curl_setopt($this->_ch, $flag, $value);
	}

	public function setPostMethod(Array $params)
	{
		$this->_params['POST'] = http_build_query($params);
		curl_setopt($this->_ch, CURLOPT_POST, true);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $this->_params['POST']);
	}

	public function setCustomPost($paramString)
	{
		curl_setopt($this->_ch, CURLOPT_POST, true);
		curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $paramString);
		$this->_params['POST'] = $paramString;
	}

	public function setHttpHeader(Array $headers)
	{
		curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $headers);
	}

	public function setGetParams(Array $params)
	{
		$this->_params['GET'] = http_build_query($params);
	}

	public function send($url = null)
	{
		if (!$url)
			$url = $this->_url;
		if (!$url)
			return false;

		if (isset($this->_params['GET']))
			$url .= '?'.$this->_params['GET'];

		curl_setopt($this->_ch, CURLOPT_URL, $url);
		$this->_lastResponse = curl_exec($this->_ch);

		if ($this->_lastResponse === false)
		{
			trigger_error('Curl error: '.curl_error($this->_ch), E_USER_WARNING);
			return false;
		}

		return $this->_lastResponse;
	}

	public function getLastResponse()
	{
		return $this->_lastResponse;
	}
}
