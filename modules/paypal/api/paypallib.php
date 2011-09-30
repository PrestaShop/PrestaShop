<?php

define('PAYPAL_API_VERSION', '60.0');

class PaypalLib extends Paypal
{
	private $_logs = array();

	public function getLogs()
	{
		return $this->_logs;
	}

	public function makeCall($host, $script, $methodName, $string)
	{
		// Making request string
		$request = 'METHOD='.urlencode($methodName).'&VERSION='.urlencode(PAYPAL_API_VERSION);
		$request .= '&PWD='.urlencode(Configuration::get('PAYPAL_API_PASSWORD')).'&USER='.urlencode(Configuration::get('PAYPAL_API_USER'));
		$request .= '&SIGNATURE='.urlencode(Configuration::get('PAYPAL_API_SIGNATURE')).$string;
		
		// Making connection
		include_once(_PS_MODULE_DIR_.'paypal/api/paypalconnect.php');
		$ppConnect = new PaypalConnect();
		$result = $ppConnect->makeConnection($host, $script, $request, true);
		$this->_logs = $ppConnect->getLogs();

		// Formating response value
		$response = explode('&', $result);
		foreach ($response as $k => $res)
		{
			$tmp = explode('=', $res);
			if (!isset($tmp[1]))
				$response[$tmp[0]] = urldecode($tmp[0]);
			else
			{
				$response[$tmp[0]] = urldecode($tmp[1]);
				unset($response[$k]);
			}
		}
		if (!Configuration::get('PAYPAL_DEBUG_MODE'))
			$this->_logs = array();
		$toExclude = array('TOKEN', 'SUCCESSPAGEREDIRECTREQUESTED', 'VERSION', 'BUILD', 'ACK', 'CORRELATIONID');
		$this->_logs[] = '<b>'.$this->l('PayPal response:').'</b>';
		foreach ($response as $k => $res)
		{
			if (!Configuration::get('PAYPAL_DEBUG_MODE') AND in_array($k, $toExclude))
				continue;
			$this->_logs[] = $k.' -> '.$res;
		}
		return $response;
	}

	public function makeSimpleCall($host, $script, $request)
	{
		// Making connection
		include_once(_PS_MODULE_DIR_.'paypal/api/paypalconnect.php');
		$ppConnect = new PaypalConnect();
		$result = $ppConnect->makeConnection($host, $script, $request);
		$this->_logs = $ppConnect->getLogs();
		return $result;
	}
}

