<?php
/**
 * Objet représentant les informations de la commande
 *
 */
class HIPAY_MAPI_Order extends HIPAY_MAPI_lockable {

	/**
	 * Montant des frais de livraison
	 *
	 * @var float
	 */
	protected $shippingAmount;

	/**
	 * Taxes s'appliquants aux frais de livraison
	 *
	 * @var array
	 */
	protected $shippingTax;

	/**
	 * Montant des assurances
	 *
	 * @var float
	 */
	protected $insuranceAmount;

	/**
	 * Taxes s'appliquants aux assurances
	 *
	 * @var array
	 */
	protected $insuranceTax;

	/**
	 * Montant des coûts fixes
	 *
	 * @var float
	 */
	protected $fixedCostAmount;

	/**
	 * Taxes s'appliquants aux coûts fixes
	 *
	 * @var array
	 */
	protected $fixedCostTax;

	/**
	 * Affiliés
	 *
	 * @var array
	 */
	protected $affiliate;

	/**
	 * Intitulé de la commande
	 *
	 * @var string
	 */
	protected $orderTitle;

	/**
	 * Informations sur la commande
	 *
	 * @var string
	 */
	protected $orderInfo;

	/**
	 * Catégorie de la commande
	 *
	 * @var int
	 */
	protected $orderCategory;

	/**
	 * Défini le montant des frais d'envoi et les taxes s'y appliquant
	 *
	 * @param float $shippingAmount
	 * @param array $shippingTax
	 * @return boolean
	 */
	public function setShipping($shippingAmount, $shippingTax) {
		if ($this->_locked)
			return false;

		$shippingAmount = sprintf('%.02f', (float)$shippingAmount);
		if ($shippingAmount < 0) {
			return false;
		}
		
		if(empty($shippingTax))	{
			return false;
		}
		if (!HIPAY_MAPI_UTILS::is_an_array_of($shippingTax, 'HIPAY_MAPI_Tax')) {
			return false;
		}
		
		$this->shippingAmount = $shippingAmount;
		foreach ($shippingTax as $obj)
			$this->shippingTax[] = clone $obj;
			
		return true;
	}

	/**
	 * Retourne le montant des frais d'envoi
	 *
	 * @return float
	 */
	public function getShippingAmount() {
		return $this->shippingAmount;
	}

	/**
	 * Retourne les taxes s'appliquants aux frais d'envoi
	 *
	 * @return array
	 */
	public function &getShippingTax() {
		return $this->shippingTax;
	}

	/**
	 * Défini le montant des assurances
	 *
	 * @param float $insuranceAmount
	 * @param array $insuranceTax
	 * @return boolean
	 */
	public function setInsurance($insuranceAmount, $insuranceTax) {
		if ($this->_locked)
			return false;

		$insuranceAmount = sprintf('%.02f', (float)$insuranceAmount);
		
		if ($insuranceAmount < 0)
			return false;
			
		if(empty($insuranceTax)) {
			return false;
		}	
		if (!HIPAY_MAPI_UTILS::is_an_array_of($insuranceTax, 'HIPAY_MAPI_Tax'))
			return false;
			
		$this->insuranceAmount = $insuranceAmount;
		foreach ($insuranceTax as $obj)
			$this->insuranceTax[] = clone $obj;
			
		return true;
	}

	/**
	 * Retourne le montant des assurances
	 *
	 * @return float
	 */
	public function getInsuranceAmount() {
		return $this->insuranceAmount;
	}

	/**
	 * Retourne les taxes s'appliquants aux assurances
	 *
	 * @return array
	 */
	public function &getInsuranceTax() {
		return $this->insuranceTax;
	}

	/**
	 * Défini le montant des coûts fixes et les taxes s'y appliquant
	 *
	 * @param float $fixedCostAmount
	 * @param array $fixedCostTax
	 * @return boolean
	 */
	public function setFixedCost($fixedCostAmount,$fixedCostTax) {
		if ($this->_locked)
			return false;

		$fixedCostAmount = sprintf('%.02f', (float)$fixedCostAmount);
		
		if ($fixedCostAmount<0)
			return false;
		
		if(empty($fixedCostTax)) {
			return false;
		}
		if (!HIPAY_MAPI_UTILS::is_an_array_of($fixedCostTax, 'HIPAY_MAPI_Tax'))
			return false;
			
		$this->fixedCostAmount = $fixedCostAmount;
		foreach ($fixedCostTax as $obj)
			$this->fixedCostTax[] = clone $obj;
		return true;
	}

	/**
	 * Retourne le montant des coûts fixes
	 *
	 * @return float
	 */
	public function getFixedCostAmount() {
		return $this->fixedCostAmount;
	}

	/**
	 * Retourne les taxes s'appliquant aux coûts fixes
	 *
	 * @return array
	 */
	public function &getFixedCostTax() {
		return $this->fixedCostTax;
	}

	/**
	 * Défini les affiliés qui recevront une rétribution pour cette commande
	 *
	 * @param array $affiliate
	 * @return boolean
	 */
	public function setAffiliate($affiliate) {
		if ($this->_locked)
			return false;

		if(empty($affiliate)) {
			return false;
		}
		if (!HIPAY_MAPI_UTILS::is_an_array_of($affiliate, 'HIPAY_MAPI_Affiliate'))
			return false;
			
		foreach ($affiliate as $obj)
			$this->affiliate[] = clone $obj;
			
		return true;
	}

	/**
	 * Retourne la liste des affiliés de cette commande
	 *
	 * @return array
	 */
	public function &getAffiliate() {
		return $this->affiliate;
	}

	/**
	 * Assigne l'intitulé de la commande
	 *
	 * @param string $orderTitle
	 * @return boolean
	 */
	public function setOrderTitle($orderTitle) {
		if ($this->_locked)
			return false;

		$orderTitle = HIPAY_MAPI_UTF8::forceUTF8($orderTitle);
		$len = HIPAY_MAPI_UTF8::strlen_utf8($orderTitle);
		if ($len < 1 || $len > HIPAY_MAPI_MAX_TITLE_LENGTH)
			return false;
			
		$this->orderTitle = $orderTitle;
		return true;
	}

	/**
	 * Retourne l'intitulé de la commande
	 *
	 * @return string
	 */
	public function getOrderTitle() {
		return $this->orderTitle;
	}

	/**
	 * Assigne les infos sur la commande
	 *
	 * @param string $orderInfo
	 * @return boolean
	 */
	public function setOrderInfo($orderInfo) {
		if ($this->_locked)
			return false;

		$orderInfo = HIPAY_MAPI_UTF8::forceUTF8($orderInfo);
		$len = HIPAY_MAPI_UTF8::strlen_utf8($orderInfo);
		if ($len > HIPAY_MAPI_MAX_INFO_LENGTH)
			return false;
			
		$this->orderInfo = $orderInfo;
		return true;
	}

	/**
	 * Retourne les infos sur la commande
	 *
	 * @return string
	 */
	public function getOrderInfo() {
		return $this->orderInfo;
	}


	/**
	 * Assigne la catégorie de la commande
	 *
	 * @param int $orderCategory
	 * @return boolean
	 */
	public function setOrderCategory($orderCategory) {
		if ($this->_locked)
			return false;

		$orderCategory = (int)$orderCategory;
		if ($orderCategory < 1)
			return false;
			
		$this->orderCategory = $orderCategory;
		return true;
	}

	/**
	 * Retourne la catégorie de la commande
	 *
	 * @return int
	 */
	public function getOrderCategory() {
		return $this->orderCategory;
	}


	/**
	 * Vérifie si l'objet est correctement initialisé
	 *
	 * @return boolean
	 */
	public function check() {
		if ($this->orderTitle == '' || $this->orderCategory < 0)
			throw new Exception('Label and/or category of order are missing');
		
		foreach($this->affiliate as $obj) {
			try {
				$obj->check();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());			
			}				
		}
		
		foreach($this->shippingTax as $obj) {
			try {
				$obj->check();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());			
			}
		}
		
		foreach($this->insuranceTax as $obj) {
			try {
				$obj->check();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());			
			}
		}
		
		foreach($this->fixedCostTax as $obj) {
			try {
				$obj->check();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());			
			}
		}
		
		return true;
	}

	protected function init() {
		$this->shippingAmount = 0;
		$this->shippingTax = array();
		$this->insuranceAmount = 0;
		$this->insuranceTax = array();
		$this->fixedCostAmount = 0;
		$this->fixedCostTax = array();
		$this->affiliate = array();
		$this->orderTitle = '';
		$this->orderInfo = '';
		$this->orderCategory = -1;
	}

	function __construct() {
		$this->init();
		parent::__construct();
	}
}