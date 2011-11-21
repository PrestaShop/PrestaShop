<?php

class serviceCache
{
	private $_dateBefore;
	private $_dateNow;
	private $_idCard;
	private $_zipCode;
	
	public	function __construct($id_card, $zipcode)
	{
		$this->_dateBefore = date("Y-m-d H:i:s", mktime(date("H"), date("i") - 15, date("s"), date("m")  , date("d"), date("Y")));
		$this->_dateNow = date('Y-m-d H:i:s');
		$this->_idCard = $id_card;
		$this->_zipCode = $zipcode;
	}
	
	public function getFaisabilityAtThisTime()
	{
		if (Db::getInstance()->getValue('SELECT * FROM `'._DB_PREFIX_.'tnt_carrier_cache_service` WHERE `date` >= "'.$this->_dateBefore.'" AND `date` <= "'.$this->_dateNow.'" AND id_card = "'.(int)($this->_idCard).'" AND zipcode = "'.$this->_zipCode.'"'))
			return true;
		return false;
	}
	
	public function deletePreviousServices()
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'tnt_carrier_cache_service` WHERE `id_card` = "'.(int)($this->_idCard).'"');
	}
	
	public static function deleteServices($idCard)
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'tnt_carrier_cache_service` WHERE `id_card` = "'.(int)($idCard).'"');
	}
	
	public function putInCache($service, $serviceRelais)
	{
		if (isset($service))
			{
				if (is_array($service->Service))
					foreach ($service->Service as $k => $v)
						Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_cache_service` (`id_card`, `code`, `date`, `zipcode`) VALUES ("'.(int)($this->_idCard).'", "'.$v->serviceCode.'","'.$this->_dateNow.'", "'.$this->_zipCode.'")');
				else
						Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_cache_service` (`id_card`, `code`, `date`, `zipcode`) VALUES ("'.(int)($this->_idCard).'", "'.$service->Service->serviceCode.'","'.$this->_dateNow.'", "'.$this->_zipCode.'")');
			}
		if (isset($serviceRelais))
			{
				foreach ($serviceRelais as $v)
					Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'tnt_carrier_cache_service` (`id_card`, `code`, `date`, `zipcode`) VALUES ("'.(int)($this->_idCard).'", "'.$v->serviceCode.'","'.$this->_dateNow.'", "'.$this->_zipCode.'")');
			}
	}
	
	public function getServices()
	{
		return (Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'tnt_carrier_cache_service` WHERE id_card = "'.(int)($this->_idCard).'"'));
	}
}

?>