<?php
/**
 * Objet représentant un affilié
 * montant de base : montant à partir duquel est calculé le montant qui sera distribué à l'affilié
 * cible : si l'affiliation est un piurcentage, la cible indique sur quels montant de la transaction
 * s'applique ce pourcentage
 * montant calculé : montant reversé à l'affilié
 *
 *
 */
class HIPAY_MAPI_Affiliate extends HIPAY_MAPI_lockable {
	/**
	 * Numéro de client
	 *
	 * @var int
	 */
	protected $customerId;

	/**
	 * Numéro de compte
	 *
	 * @var unknown_type
	 */
	protected $accountId;

	/**
	 * Valeur de l'affiliation (montant fixe ou pourcentage)
	 *
	 * @var float
	 */
	protected $val;

	/**
	 * Si >0, $val est un pourcentage et $percentageTarget détermine
	 * sur quels montants appliquer le pourcentage
	 *
	 * @var unknown_type
	 */
	protected $percentageTarget;

	/**
	 * Montant à reversé à l'affilié
	 *
	 * @var unknown_type
	 */
	protected $_amount;

	/**
	 * Montant de base sur lequel est calculé le montant
	 * reversé à l'affilié
	 *
	 * @var unknown_type
	 */
	protected $_baseAmount;

	/**
	 * Assigne le numéro de client
	 *
	 * @param int $customerId
	 * @return boolean
	 */
	public function setCustomerId($customerId) {
		if ($this->_locked)
			return false;

		$customerId = (int)$customerId;
		if ($customerId <= 0)
			return false;
			
		$this->customerId = $customerId;
		return true;
	}

	/**
	 * Retourne le numéro de client
	 *
	 * @return int
	 */
	public function getCustomerId() {
		return $this->customerId;
	}

	/**
	 * Assigne le numéro de compte
	 *
	 * @param int $accountId
	 * @return boolean
	 */
	public function setAccountId($accountId) {
		if ($this->_locked)
			return false;

		$accountId = (int)$accountId;
		if ($accountId <= 0)
			return false;
			
		$this->accountId = $accountId;
		return true;
	}

	/**
	 * Retourne le numéro de compte
	 *
	 * @return int
	 */
	public function getAccountId() {
		return $this->accountId;
	}

	/**
	 * Assigne le valeur de l'affiliation, qui est un montant fixe ou un pourcentage
	 * S'il s'agit d'un pourcentage, $percentageTarget représente la cible, c'est à dire sur quels
	 * montants est basé le montant de l'affiliation
	 *
	 * @param float $val
	 * @param int $percentageTarget
	 * @return boolean
	 */
	public function setValue($val, $percentageTarget = 0) {
		if ($this->_locked)
			return false;

		$val = sprintf('%.02f',(float)$val);
		$percentageTarget = (int)$percentageTarget;
		
		if ($val <= 0 || $percentageTarget < 0)
			return false;
		if ($percentageTarget > 0 && $val > 100)
			return false;
		if ($percentageTarget > 0 && $percentageTarget > HIPAY_MAPI_TTARGET_ALL)
			return false;
		
		$this->val = $val;
		$this->percentageTarget = $percentageTarget;
		$this->setAmount();
		return true;
	}

	/**
	 * Retourne la valeur de l'affiliation
	 *
	 * @return float
	 */
	public function getValue() {
		return $this->val;
	}

	/**
	 * Retourne sur quoi s'applique le pourcentage
	 *
	 * @return int
	 */
	public function getPercentageTarget() {
		return $this->percentageTarget;
	}

	/**
	 * Assigne le montant sur lequel sera calculé l'affiliation
	 *
	 * @param float $baseAmount
	 * @return boolean
	 */
	public function setBaseAmount($baseAmount) {
		if ($this->_locked)
			return false;

		$baseAmount = sprintf('%.02f',(float)$baseAmount);

		if ($baseAmount < 0)
			return false;
			
		$this->_baseAmount = $baseAmount;
		$this->setAmount();
		return true;
	}

	/**
	 * Retourne le montant sur lequel sera calculé l'affiliation
	 *
	 * @return int
	 */
	public function getBaseAmount() {
		return $this->_baseAmount;
	}


	/**
	 * Retourne le montant calculé de l'affiliation
	 *
	 * @return float
	 */
	public function getAmount() {
		return $this->_amount;
	}

	/**
	 * Assigne le montant calculé de l'affiliation
	 *
	 */
	protected function setAmount() {
		if ($this->percentageTarget > 0) {
			$this->_amount = sprintf('%.02f',($this->_baseAmount/100) * $this->val);
		} else {
			$this->_amount = sprintf('%.02f',$this->_baseAmount);
		}
	}

	/**
	 * Vérifie que l'objet est bien initialisé
	 *
	 * @return boolean
	 */
	public function check() {
		if ($this->customerId <= 0 || $this->accountId <= 0 || $this->val <= 0 || $this->percentageTarget < 0)
			throw new Exception('Customer identifier, account number, value or invalid target');
		return true;
	}

	protected function init() {
		$this->customerId = 0;
		$this->accountId = 0;
		$this->_amount = 0;
		$this->_baseAmount = 0;
		$this->val = 0;
		$this->percentageTarget = 0;
	}

	function __construct() {
		$this->init();
		parent::__construct();
	}
}
