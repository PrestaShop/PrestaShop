<?php

class PayPalConnect extends Paypal
{
	private	$_logs = array();

	public function makeConnection($host, $script, $body, $simple_mode = false)
	{
		$this->_logs[] = $this->l('Making new connection to').' \''.$host.$script.'\'';
		if (function_exists('curl_exec'))
			$return = $this->_connectByCURL($host.$script, $body);
		if (isset($return) AND $return)
			return $return;
		$tmp = $this->_connectByFSOCK($host, $script, $body);
		if (!$simple_mode || !preg_match('/[A-Z]+=/', $tmp, $result))
			return $tmp;
		$pos = strpos($tmp, $result[0]);
		$body = substr($tmp, $pos);
		return $body;
	}

	public function getLogs()
	{
		return $this->_logs;
	}

	/************************************************************/
	/********************** CONNECT METHODS *********************/
	/************************************************************/

	private function _connectByCURL($url, $body)
	{
		$ch = @curl_init();
		if (!$ch)
			$this->_logs[] = $this->l('Connect failed with CURL method');
		else
		{
			$this->_logs[] = $this->l('Connect with CURL method successful');
			$this->_logs[] = '<b>'.$this->l('Sending this params:').'</b>';
			$this->_logs[] = $body;
			@curl_setopt($ch, CURLOPT_URL, 'https://'.$url);
			@curl_setopt($ch, CURLOPT_POST, true);
			@curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			@curl_setopt($ch, CURLOPT_HEADER, false);
			@curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			@curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			@curl_setopt($ch, CURLOPT_VERBOSE, true);
			$result = @curl_exec($ch);
			if (!$result)
				$this->_logs[] = $this->l('Send with CURL method failed ! Error:').' '.curl_error($ch);
			else
				$this->_logs[] = $this->l('Send with CURL method successful');
			@curl_close($ch);
		}
		return (isset($result) ? $result : false);
	}

	private function _connectByFSOCK($host, $script, $body)
	{
		$fp = @fsockopen('ssl://'.$host, 443, $errno, $errstr, 4);
		if (!$fp)
			$this->_logs[] = $this->l('Connect failed with fsockopen method');
		else
		{
			$header = $this->_makeHeader($host, $script, strlen($body));
			$this->_logs[] = $this->l('Connect with fsockopen method successful');
			$this->_logs[] = $this->l('Sending this params:').' '.$header.$body;
			@fputs($fp, $header.$body);
			$tmp = '';
			while (!feof($fp))
				$tmp .= trim(fgets($fp, 1024));
			fclose($fp);
			$result = $tmp;
			if (!$result)
				$this->_logs[] = $this->l('Send with fsockopen method failed !');
			else
				$this->_logs[] = $this->l('Send with fsockopen method successful');
		}
		return (isset($result) ? $result : false);
	}

	private function _makeHeader($host, $script, $lenght)
	{
		$header =	'POST '.strval($script).' HTTP/1.0'."\r\n" .
					'Host: '.strval($host)."\r\n".
					'Content-Type: application/x-www-form-urlencoded'."\r\n".
					'Content-Length: '.(int)($lenght)."\r\n".
					'Connection: close'."\r\n\r\n";
		return $header;
	}
}
