<?php

class Core_Foundation_Net_Curl
{
	private $_errorMessage;
	private $_errorNumber;
	private $_info;
	private $_options;
	private $_session;

	public function __construct()
	{
		if  (!in_array('curl', get_loaded_extensions())) {
			throw new \RuntimeException('curl extension must be loaded');
		}
	}

	public function __destruct()
	{
		$this->close();
	}

	public function __get($name)
	{
		return $this->getInfo($name);
	}

	public function __isset($name)
	{
		return isset($this->_info[$name]);
	}

	public function setErrorMessage($value)
	{
		$this->_errorMessage = $value;
		return $this;
	}

	public function getErrorMessage()
	{
		return $this->_errorMessage;
	}

	public function setErrorNumber($value)
	{
		$this->_errorNumber = $value;
		return $this;
	}

	public function getErrorNumber()
	{
		return $this->_errorNumber;
	}

	public function setInfo($value)
	{
		$this->_info = $value;
		return $this;
	}

	public function getInfo($name = null)
	{
		if (isset($name) && isset($this->_info[$name])) {
			return $this->_info[$name];
		}

		return $this->_info;
	}

	public function setOption($name, $value)
	{
		if (!isset($this->_options)) {
			$this->_options = array();
		}

		$this->_options[$name] = $value;
		return $this;
	}

	public function getOption($name)
	{
		if (isset($this->_options[$name])) {
			return $this->_options[$name];
		}

		return null;
	}

	public function setOptions(array $value)
	{
		$this->_options = $value;
		return $this;
	}

	public function getOptions()
	{
		if (!isset($this->_options)) {
			$this->_options = array();
		}

		return $this->_options;
	}

	public function addOption($name, $value)
	{
		return $this->setOption($name, $value);
	}

	public function setUrl($value)
	{
		return $this->setOption(CURLOPT_URL, $value);
	}

	public function getUrl()
	{
		return $this->getOption(CURLOPT_URL);
	}

	public function close()
	{
		if ($this->isConnected()) {
			curl_close($this->_session);
		}
	}

	public function connect()
	{
		if (!$this->isConnected()) {
			$this->_session = curl_init();
		}

		return $this->_session;
	}

	public function exec($url = null, $options = null)
	{
		$this->connect();

		if (isset($url)) {
			$this->setUrl($url);
		}

		if (isset($options)) {
			$this->setOptions($options);
		}

		if (isset($this->_options)) {
			curl_setopt_array($this->_session, $this->getOptions());
		}

		$content = curl_exec($this->_session);
		$this->setErrorNumber(curl_errno($this->_session));
		$this->setErrorMessage(curl_error($this->_session));
		$this->setInfo(curl_getinfo($this->_session));

		return $content;
	}

	public function isConnected()
	{
		return is_resource($this->_session);
	}

	public function removeOption($name)
	{
		if (isset($this->_options[$name])) {
			unset($this->_options[$name]);
		}
	}
}
