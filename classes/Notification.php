<?php

/**
  * Notification class, Notification.php
  * Notifications management
  * @notification classes
  *
  * @author PrestaShop <support@prestashop.com>
  * @copyright PrestaShop
  * @license http://www.opensource.org/licenses/osl-3.0.php Open-source licence 3.0
  * @version 1.2
  *
  */

class Notification
{	
	public $types;
	
	public function __construct()
	{
		$this->types = array('order', 'message', 'customer');
	}
	
	public function getLastElements()
	{
		global $cookie;
		$notifications = array();
		$employee_infos = Db::getInstance()->getRow('SELECT id_last_order, id_last_message, id_last_customer FROM `'._DB_PREFIX_.'employee` WHERE `id_employee` = '.$cookie->id_employee);
				
		foreach ($this->types as $type)
		{
			$notifications[$type] = Notification::getLastElementsIdsByType($type, $employee_infos['id_last_'.$type]);
		}
		
		return $notifications;		
	}
	
	public static function getLastElementsIdsByType($type, $id_last_element)
	{
		if($type == 'order' || $type == 'message')
			return Db::getInstance()->ExecuteS('SELECT id_order FROM `'._DB_PREFIX_.(($type == 'order') ? $type.'s' : $type).'` WHERE `id_'.$type.'` > '.$id_last_element.' ORDER BY `id_'.$type.'` DESC LIMIT 5');
		else
			return Db::getInstance()->ExecuteS('SELECT id_'.$type.' FROM `'._DB_PREFIX_.$type.'` WHERE `id_'.$type.'` > '.$id_last_element.' ORDER BY `id_'.$type.'` DESC LIMIT 5');
	}	
	
	public function updateEmployeeLastElement($type)
	{	
		global $cookie;
		
		if (in_array($type, $this->types))
		{
			// We update the last item viewed
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'employee` SET `id_last_'.$type.'` = (SELECT MAX(`id_'.$type.'`) FROM `'.
				_DB_PREFIX_.(($type == 'order') ? $type.'s' : $type).'`) WHERE `id_employee` = '.$cookie->id_employee);
			return true;		
		} else { 
			return false;
		}
	}	
}
?>