<?php
class ContextCore
{
	protected static $instance;

	public $cart;
	public $customer;
	public $cookie;
	public $link;
	public $country;
	public $employee;
	public $controller;
	public $lang;
	public $currency;
	public $tab;
	
	/**
	 * Create a context without singleton constraint
	 * @param array $data
	 */
	public function __construct($cart = null, 
								$customer = null,
								$cookie = null,
								$link = null,
								$country = null,
								$employee = null,
								$lang = null,
								$currency = null,
								$tab = null)
	{
		$this->cart = $cart;
		$this->customer = $customer;
		$this->cookie = $cookie;
		$this->link = $link;
		$this->country = $country;
		$this->employee = $employee;
		$this->lang = $lang;
		$this->currency = $currency;
		$this->tab = $tab;
	}
	
	/**
	 * Get a singleton context
	 *
	 * @return Context
	 */
	public static function getContext()
	{
		if (!isset(self::$instance))
			self::$instance = new self();
		return self::$instance;
	}

	public function setData($cart = null, 
							$customer = null,
							$cookie = null,
							$link = null,
							$country = null,
							$employee = null,
							$lang = null,
							$currency = null,
							$tab = null)
	{
		$this->cart = $cart;
		$this->customer = $customer;
		$this->cookie = $cookie;
		$this->link = $link;
		$this->country = $country;
		$this->employee = $employee;
		$this->lang = $lang;
		$this->currency = $currency;
		$this->tab = $tab;
	}
	
	/*public function __get($var)
	{
		return (isset($this->data[$var]) ? $this->data[$var] : null);
	}
	
	public function __set($var, $value)
	{
		$this->data[$var] = $value;
	}
	
	public function __isset($var)
	{
		return isset($this->data[$var]);
	}
	
	public function __unset($var)
	{
		if (isset($this->data[$var]))
			unset($this->data[$var]);
	}*/
}