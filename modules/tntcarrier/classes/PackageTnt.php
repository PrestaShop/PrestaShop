<?php

class PackageTnt
{
	private $_idOrder;
	private $_order;
	
	public	function __construct($id_order)
	{
		$this->_idOrder = $id_order;
		$this->_order = new Order((int)($this->_idOrder));
	}
	
	public function setShippingNumber($number)
	{
		if ($this->_order->shipping_number == '')
		{
			$this->_order->shipping_number = $number;
			$this->_order->update();
		}
		$this->insertSql($number);	
	}
	
	public function getShippingNumber()
	{
		$tab = Db::getInstance()->ExecuteS('SELECT `shipping_number` FROM `'._DB_PREFIX_.'tnt_carrier_shipping_number` WHERE `id_order` = "'.(int)($this->_idOrder).'"');
		return ($tab);
	}
	
	public function insertSql($number)
	{
		Db::getInstance()->ExecuteS('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_shipping_number` (`id_order`, `shipping_number`) 
							VALUES ("'.(int)($this->_idOrder).'", "'.$number.'")');
	}
	
	public function getOrder()
	{
		return ($this->_order);
	}
}