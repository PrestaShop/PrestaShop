<?php
/**
 * Gestion des taxes
 *
 */
class HIPAY_MAPI_Tax extends HIPAY_MAPI_lockable
{
	/**
	 * nom de la taxe
	 *
	 * @var string
	 */
	protected $taxName;

	/**
	 * valeur de la taxe
	 *
	 * @var float
	 */
	protected $taxVal;

	/**
	 * true si la valeur de la taxe est un pourcentage
	 *
	 * @var boolean
	 */
	protected $percentage;

	/**
	 * Montant calculé de la taxe
	 *
	 * @var float
	 */
	protected $_amount;

	/**
	 * assigne le nom de la taxe
	 *
	 * @param string $taxName nom de la taxe
	 * @return boolean true si l'assignation s'est bien déroulée
	 */
	public function setTaxName($taxName) {
		if ($this->_locked)
			return false;
			
		$taxName = HIPAY_MAPI_UTF8::forceUTF8($taxName);
		$len = HIPAY_MAPI_UTF8::strlen_utf8($taxName);
		if ($len < 1 || $len > HIPAY_MAPI_MAX_TAX_NAME_LENGTH)
			return false;
			
		$this->taxName = $taxName;
		return true;
	}

	/**
	 * retourne le nom de la taxe
	 *
	 * @return string nom de la taxe
	 */
	public function getTaxName() {
		return $this->taxName;
	}

	/**
	 * assigne la valeur de la taxe
	 *
	 * @param float $taxVal valeur de la taxe
	 * @param boolean $percentage true si la valeur de la taxe est un pourcentage
	 * @return boolean true si l'assignation s'est bien déroulée
	 */
	public function setTaxVal($taxVal, $percentage = true) {
		if ($this->_locked)
			return false;
			
		if (!is_bool($percentage))
			return false;
		$taxVal = sprintf('%.02f', (float)$taxVal);
		
		if ($percentage && ($taxVal <= 0 || $taxVal > 100))
			return false;
			
		$this->taxVal = $taxVal;
		$this->percentage = $percentage;
		
		return true;
	}

	/**
	 * retourne la valeur de la taxe
	 *
	 * @return float valeur de la taxe
	 */
	public function getTaxVal() {
		return $this->taxVal;
	}

	/**
	 * détermine si la valeur de la taxe est un pourcentage
	 *
	 * @return boolean true si la valeur de la taxe est un pourcentage
	 */
	public function isPercentage() {
		return $this->percentage;
	}

	/**
	 * effectue une vérification des propriétés de la taxe
	 *
	 * @return boolean true si les propriétés de la taxe sont correctes
	 */
	public function check() {
		if ($this->taxName == '' || $this->taxVal < 0)
			throw new Exception('Tax name or Tax value not initialized');
			
		return true;
	}

	/**
	 * initilise les propriétés de la taxe
	 *
	 */
	protected function init() {
		$this->taxVal = -1;
		$this->taxName = '';
		$this->percentage = false;
		$this->_amount = 0;
		$this->_locked = false;
	}

	/**
	 * assigne le montant calculé de la taxe
	 *
	 * @param float $amount montant calculé
	 * @return boolean true si l'assignation s'est bien déroulée
	 */
	public function setTaxAmount($amount) {
		if ($this->_locked)
			return false;

		$amount = (float)$amount;
		if ($amount < 0)
			$amount = 0;
			
		$this->_amount = sprintf("%.02f", $amount);
		return true;
	}

	/**
	 * retourne le montant calculé de la taxe
	 *
	 * @return float montant calculé
	 */
	public function getTaxAmount() {
		return $this->_amount;
	}

	/**
	 * constructeur
	 *
	 */
	function __construct() {
		$this->init();
		parent::__construct();
	}
}