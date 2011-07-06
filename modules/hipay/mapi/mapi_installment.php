<?php
/**
 * Représente la ligne de la commande pour un paiement récurrent
 *
 */
class HIPAY_MAPI_Installment extends HIPAY_MAPI_Item {

	/**
	 * Montant HT
	 *
	 * @var float
	 */
	protected $price;

	/**
	 * Taxes s'appliquant à ce paiement
	 *
	 * @var array
	 */
	protected $tax;

	/**
	 * Indique s'il s'agit du premier paiement (true) ou des suivants (false)
	 *
	 * @var boolean
	 */
	protected $first;

	/**
	 * Délai avant le déclenchement du premier paiement (si $first=true),
	 * ou interval entre les paiements suivants (si $first=false)
	 *
	 * @var string
	 */
	protected $paymentDelay;

	/**
	 * timestamp du premier paiement ou des paiements récurrents
	 *
	 * @var integer
	 */
	protected $_delayTS;


	/**
	 * Assigne le montant HT
	 *
	 * @param float $price
	 * @return boolean
	 */
	public function setPrice($price) {
		if ($this->_locked)
			return false;

		$price = sprintf('%.02f',(float)$price);
		if ($price < 0)
			return false;
		$this->price = $price;
		
		return true;
	}

	/**
	 * Retourne le montant HT
	 *
	 * @return float
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * Défini s'il s'agit du premier paiement ou des suivants et le délai de déclenchement
	 *
	 * @param boolean $first
	 * @param string $paymentDelay
	 * @return boolean
	 */
	public function setFirst($first,$paymentDelay) {
		if ($this->_locked)
			return false;
		if (!is_bool($first))
			return false;
			
		$paymentDelay=trim($paymentDelay);
		if ($first) {
			if (!preg_match("#[0-9]+[HDM]#",$paymentDelay))
			{
				return false;
			}
		} else {
			if (!preg_match("#[0-9]+[DM]#",$paymentDelay)) {
				return false;
			}
		}
		
		$num = (int)substr($paymentDelay, 0, strlen($paymentDelay)-1);
		if (($num < 1 && !$first) || ($num < 0 && $first) || $num > 365) {
			return false;
		}
		$this->first = $first;
		$this->paymentDelay = $paymentDelay;
		return true;
	}

	/**
	 * Assigne les taxes s'appliquant à ce paiement
	 *
	 * @param array $tax
	 * @return boolean
	 */
	public function setTax($tax) {
		if ($this->_locked)
			return false;

		if (!HIPAY_MAPI_UTILS::is_an_array_of($tax, 'HIPAY_MAPI_Tax'))
			return false;
		foreach ($tax as $obj)
			$this->tax[]= clone $obj;
			
		return true;
	}

	/**
	 * assigne le timestamp du premier paiement ou des paiements récurrents
	 *
	 */
	public function setDelayTS( $baseTS=0 )
	{
		if ((int)$baseTS <= 0) {
			$baseTS = time();
		}

		switch (substr($this->paymentDelay, -1, 1)) {
			case 'd': case 'D': $unit='day'; break;
			case 'm': case 'M': $unit='month'; break;
			case 'h': case 'H':
			default : $unit='hour'; break;
		}

		$this->_delayTS = strtotime('+'.substr($this->paymentDelay, 0, -1).' '.$unit, $baseTS);
	}

	/**
	 * Retourne les taxes s'appliquant à ce paiement
	 *
	 * @return array
	 */
	public function getTax() {
		return $this->tax;
	}

	/**
	 * Retourne s'il s'agit du premier paiement
	 *
	 * @return boolean
	 */
	public function getFirst() {
		return $this->first;
	}

	/**
	 * Retourne le délai de déclenchement
	 *
	 * @return string
	 */
	public function getPaymentDelay() {
		return $this->paymentDelay;
	}

	/**
	 * retourne le timestamp du premier paiement
	 *
	 */
	public function getDelayTS()
	{
		return $this->_delayTS;
	}

	/**
	 * Vérifie si l'objet est correctement initialisé
	 *
	 * @return float
	 */
	public function check() {
		if ($this->price < 0)
			throw new Exception('Invalid amount or not initialized');
		if (!HIPAY_MAPI_UTILS::is_an_array_of($this->tax, 'HIPAY_MAPI_Tax'))
			throw new Exception('Invalid taxes or not initialized');
		foreach($this->tax as $obj) {
			if (!$obj->check())
				return false;
		}
		
		if (!is_bool($this->first))
			throw new Exception('Premier paiement ou suivant n\'est pas initialisé');
			
		return true;
	}

	protected function init()
	{
		$this->price = -1;
		$this->tax = array();
		$this->first = '';
		$this->paymentDelay = '';
		$this->_delayTS = '';
	}

	function __construct() {
		$this->init();
		
		parent::__construct();
	}
}