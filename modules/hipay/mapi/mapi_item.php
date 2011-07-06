<?php
/**
 * Représente une ligne de la commande
 *
 */
class HIPAY_MAPI_Item extends HIPAY_MAPI_lockable {

	function __construct() {
		parent::__construct();
	}

	public function getName() {
		return '';
	}
	
	public function getInfo() {
		return '';
	}
	
	public function getQuantity() {
		return 1;
	}
	
	public function getRef() {
		return '';
	}
	
	public function getCategory() {
		return 1;
	}
	
	public function getPrice() {
		return 0;
	}
	
	public function getTax() {
		return array();
	}
	
	public function check() {
		return false;
	}
}