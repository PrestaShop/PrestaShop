<?php


/**
  * DejalaCart class, dejalacart.php
  * Manage cart information related to dejala.fr carrier
 **/
if (!defined('_CAN_LOAD_FILES_'))
	exit;
class DejalaCart extends ObjectModel
{
	public 		$id;
	public 		$id_dejala_product;
	public 		$shipping_date;
	public 		$id_delivery;
	// 'TEST' or 'PROD'
	public		$mode;
	public		$cart_date_upd ;
	public		$delivery_price ;
	private		$wanted_cart_id ;
	
	// This field is voluntarily kept out of the database.
	public		$product ;
	
	protected 	$table = 'dejala_cart';
	protected 	$identifier = 'id_cart';
	private static $INSTANCES = array() ;
	private $djlCart ;

	static public function getInstance($id) {
		if (!isset(self::$INSTANCES[$id])) {
        	self::$INSTANCES[$id] = new DejalaCart($id);
		}
		return self::$INSTANCES[$id];
	}

	public function __construct($id = NULL, $id_lang = NULL) {
		if (isset($id) && !is_null($id)) {
			$this->wanted_cart_id = $id ;
		}
		parent::__construct($id, $id_lang) ;
		if (isset($this->id) && $this->id == $this->wanted_cart_id) {
			unset($this->wanted_cart_id) ;	
		}
	}
	
	public function getFields()
	{
		parent::validateFields();
		$fields['id_dejala_product'] = (int)($this->id_dejala_product);
		$fields['shipping_date'] = pSQL($this->shipping_date);
		$fields['id_delivery'] = (int)($this->id_delivery);
		$fields['mode'] = pSQL($this->mode);
		$fields['cart_date_upd'] = pSQL($this->cart_date_upd);
		$fields['delivery_price'] = pSQL($this->delivery_price);
		if (!isset($id_cart) && isset($original_cart_id)) $id_cart = $original_cart_id ;
		return $fields;
	}
	
	public function save($nullValues = false, $autodate = true) {
		parent::save($nullValues, $autodate) ;
		if (isset($this->wanted_cart_id)) {
			Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . $this->table . ' SET ' . $this->identifier . ' = ' . (int)$this->wanted_cart_id . ' WHERE ' . $this->identifier . ' = ' . (int)$this->id);
			$this->id = $this->wanted_cart_id ;
			unset($this->wanted_cart_id) ;			
		}		
	}
	
}


