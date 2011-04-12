<?php
class HIPAY_MAPI_lockable extends HIPAY_MAPI_XML {
	/**
	 * Etat du vérou
	 *
	 * @var boolean
	 */
	protected $_locked;

	/**
	 * Vérouille l'objet
	 *
	 */
	public function lock() {
		$this->_locked=true;
	}

	function __construct() {
		$this->_locked=false;
	}


}
