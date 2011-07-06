<?php
/**
 * Représente une ligne de la commande pour un paiement simple
 *
 */
class HIPAY_MAPI_Product extends HIPAY_MAPI_Item {
	/**
	 * Nom du produit
	 *
	 * @var string
	 */
	protected $name;
	
	/**
	 * Informations sur le produit
	 *
	 * @var string
	 */
	protected $info;
	
	/**
	 * quantité
	 *
	 * @var int
	 */
	protected $quantity;
	
	/**
	 * Réference produit
	 *
	 * @var string
	 */
	protected $ref;
	
	/**
	 * Catégorie du produit
	 *
	 * @var int
	 */
	protected $category;
	
	/**
	 * Montant unitaire HT du produit
	 *
	 * @var float
	 */
	protected $price;
	
	/**
	 * Taxes s'appliquant à ce produit
	 *
	 * @var array
	 */
	protected $tax;

	/**
	 * Assigne le nom du produit
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function setName($name) {
		if ($this->_locked)
			return false;

		$name = HIPAY_MAPI_UTF8::forceUTF8($name);
		$len = HIPAY_MAPI_UTF8::strlen_utf8($name);
		
		if ($len < 1 || $len > HIPAY_MAPI_MAX_PRODUCT_NAME_LENGTH)
			return false;
			
		$this->name = $name;
		return true;
	}

	/**
	 * Retourne le nom du produit
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Assigne les informations sur le produit
	 *
	 * @param string $info
	 * @return boolean
	 */
	public function setInfo($info) {
		if ($this->_locked)
			return false;

		$info = HIPAY_MAPI_UTF8::forceUTF8($info);
		$len = HIPAY_MAPI_UTF8::strlen_utf8($info);
		
		if ($len > HIPAY_MAPI_MAX_PRODUCT_INFO_LENGTH)
			return false;
			
		$this->info = $info;
		return true;
	}

	/**
	 * Retourne les informations sur le produit
	 *
	 * @return string
	 */
	public function getInfo() {
		return $this->info;
	}

	/**
	 * Assigne la quantité de produit
	 *
	 * @param int $quantity
	 * @return boolean
	 */
	public function setQuantity($quantity) {
		if ($this->_locked)
			return false;

		$quantity = (int)$quantity;
		if ($quantity < 1)
			return false;
			
		$this->quantity = $quantity;
		return true;
	}

	/**
	 * Retourne la quantité de produit
	 *
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * Assigne la réference du produit
	 *
	 * @param string $ref
	 * @return boolean
	 */
	public function setRef($ref) {
		if ($this->_locked)
			return false;

		$ref = HIPAY_MAPI_UTF8::forceUTF8($ref);
		$len = HIPAY_MAPI_UTF8::strlen_utf8($ref);
		if ($len > HIPAY_MAPI_MAX_PRODUCT_REF_LENGTH)
			return false;
			
		$this->ref = $ref;
		return true;
	}

	/**
	 * Retourne la réference du produit
	 *
	 * @return string
	 */
	public function getRef() {
		return $this->ref;
	}


	/**
	 * Assigne la catégorie du produit
	 *
	 * @param int $category
	 * @return boolean
	 */
	public function setCategory($category) {
		if ($this->_locked)
			return false;

		$category = (int)$category;
		if ($category < 1)
			return false;
			
		$this->category = $category;
		return true;
	}

	/**
	 * Retourne la catégorie du produit
	 *
	 * @return int
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Assigne le montant unitaire HT du produit
	 *
	 * @param float $price
	 * @return boolean
	 */
	public function setPrice($price) {
		if ($this->_locked)
			return false;

		$price = sprintf('%.02f', (float)$price);
		if ($price < 0)
			return false;
			
		$this->price = $price;
		return true;
	}

	/**
	 * Retourne montant unitaire HT du produit
	 *
	 * @return float
	 */
	public function getPrice() {
		return $this->price;
	}

	/**
	 * Assigne les taxes s'appliquant à ce produit
	 *
	 * @param array $tax
	 * @return boolean
	 */
	public function setTax($tax) {
		if ($this->_locked)
			return false;

		if (empty($tax))
			return false;
		if (!HIPAY_MAPI_UTILS::is_an_array_of($tax,'HIPAY_MAPI_Tax'))
			return false;
			
		foreach ($tax as $obj)
			$this->tax[]= clone $obj;
			
		return true;
	}

	/**
	 * Retourne les taxes s'appliquant à ce produit
	 *
	 * @return array
	 */
	public function getTax() {
		return $this->tax;
	}

	/**
	 * Vérifie que l'objet est correctement initialisé
	 *
	 * @return boolean
	 */
	public function check() {
		if ($this->name == '' || $this->quantity < 0 || $this->category < 0 || $this->price < 0 || !HIPAY_MAPI_UTILS::is_an_array_of($this->tax, 'HIPAY_MAPI_Tax'))
			throw new Exception('Object not initialized. Please precise a product name, quantity, price, category and taxes');
		
		foreach($this->tax as $obj) {
			if (!$obj->check())
				return false;
		}
		
		return true;
	}

	protected function init() {
		$this->name = '';
		$this->info = '';
		$this->quantity = -1;
		$this->ref = '';
		$this->category = -1;
		$this->price = -1;
		$this->tax = array();
	}

	function __construct() {
		$this->init();
		parent::__construct();
	}
}