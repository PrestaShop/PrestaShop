<?php
/**
 * Représente un paiement.
 * N'est pas utilisé directement
 *
 */
class HIPAY_MAPI_Payment extends HIPAY_MAPI_XML  {

	/**
	 * Paramètres du paiement
	 *
	 * @var HIPAY_MAPI_PaymentParams
	 */
	protected $paymentParams;

	/**
	 * Objets HIPAY_MAPI_Order
	 *
	 * @var array
	 */
	protected $order;

	/**
	 * Objets HIPAY_MAPI_Item
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Total des taxes sur les produits
	 *
	 * @var array
	 */
	protected $_taxItemsAmount;

	/**
	 * Total des taxes sur les frais d'envoi
	 *
	 * @var array
	 */
	protected $_taxShippingAmount;
	/**
	 * Total des taxes sur les assurances
	 *
	 * @var array
	 */
	protected $_taxInsuranceAmount;

	/**
	 * Total des taxes sur les coûts fixes
	 *
	 * @var array
	 */
	protected $_taxFixedCostAmount;

	/**
	 * Total du montant HT des produits
	 *
	 * @var array
	 */
	protected $_itemsTotalAmount;

	/**
	 * Total des taxes
	 *
	 * @var array
	 */
	public $_taxTotalAmount;

	/**
	 * Total de la commande hors taxes
	 *
	 * @var array
	 */
	protected $_orderTotalAmountHT;

	/**
	 * Total de la commande
	 *
	 * @var array
	 */
	protected $_orderTotalAmount;

	/**
	 * Total des affilies
	 *
	 * @var array
	 */
	protected $_affiliateTotalAmount;

	public function __construct($paymentParams, $order, $items) {
		try {
			$this->init($paymentParams, $order, $items);
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	protected function init($paymentParams, $order, $items) {
		if (!($paymentParams instanceof HIPAY_MAPI_PaymentParams)
		|| !HIPAY_MAPI_UTILS::is_an_array_of($order, 'HIPAY_MAPI_Order')
		|| !HIPAY_MAPI_UTILS::is_an_array_of($items, 'HIPAY_MAPI_Item')
		|| count($items) < 1)
			throw new Exception('Wrong parameters');

		try {
			$paymentParams->check();
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
		
		foreach($order as $orderObj) {
			try {
				$orderObj->check();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}

		foreach($items as $obj) {
			try {
				$obj->check();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
		}
		
		$this->paymentParams = clone $paymentParams;
		$this->paymentParams->lock();

		foreach($order as $obj) {
			$this->order[] = clone $obj;
			end($this->order)->lock();
			$this->_taxItemsAmount[] = 0;
			$this->_taxShippingAmount[] = 0;
			$this->_taxInsuranceAmount[] = 0;
			$this->_taxFixedCostAmount[] = 0;
			$this->_itemsTotalAmount[] = 0;
			$this->_taxTotalAmount[] = 0;
			$this->_orderTotalAmount[] = 0;
			$this->_affiliateTotalAmount[] = 0;
		}
		
		foreach($items as $obj) {
			$this->items[] = clone $obj;
			end($this->items)->lock();
		}
		
		try {
			$this->compute();
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Calcul les différents montants (taxes, affiliés, totaux) de la commande
	 *
	 */
	protected function compute() {
		// Mise à jour du montant total des taxes pour chaque item du tableau _taxItemsAmount
		// Mise à jour du montant total des taxes de livraison pour chaque order du tableau _taxShippingAmount
		// Mise à jour du montant total des taxes d'assurances pour chaque order du tableau _taxInsuranceAmount
		// Mise à jour du montant total des taxes de couts fixes pour chaque order du tableau _taxFixedCostAmount
		$this->computeTaxes();
        // Mise a jour du montant total HT des produits pour chaque item du tableau _itemsTotalAmount
		$this->computeItemsAmount();
		// Pour chaque commande
		foreach($this->order as $key=>$order) {
			$this->_taxTotalAmount[$key] = $this->_taxItemsAmount[$key] + $this->_taxShippingAmount[$key] + $this->_taxInsuranceAmount[$key] + $this->_taxFixedCostAmount[$key];
			$this->_orderTotalAmountHT[$key] = $order->getShippingAmount() + $order->getInsuranceAmount() + $order->getFixedCostAmount() + $this->_itemsTotalAmount[$key];
			$this->_orderTotalAmount[$key] = $this->_orderTotalAmountHT[$key] + $this->_taxTotalAmount[$key];
//			$this->_orderTotalAmount[$key] = $order->getShippingAmount() + $order->getInsuranceAmount() + $order->getFixedCostAmount() + $this->_itemsTotalAmount[$key] +
//						$this->_taxItemsAmount[$key] +
//						$this->_taxShippingAmount[$key] +
//						$this->_taxInsuranceAmount[$key] +
//						$this->_taxFixedCostAmount[$key];
		}
		try {
			$this->computeAffiliates();
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Calcul le montant des taxes
	 *
	 */
	protected function computeTaxes() {
		// Taxes sur les produits au niveau de l'item (ligne de commande)
		// @FIXME bug et confusion entre indice d'item et indice d'order ?
		$cur = 0;
		// pour chaque ligne de commande
		foreach($this->items as $item) {
			// Liste des taxes de la ligne
			$itemTaxes = $item->getTax();
			$tItemsAmount = 0;
			
			// pour chaque taxe appliquée sur cette ligne
			foreach($itemTaxes as $tax) {
				$amount = HIPAY_MAPI_UTILS::computeTax($item->getPrice(), $tax);
				$tax->setTaxAmount($amount);
				$tax->lock();
				// mise a jour du montant total des taxes pour cet item (ligne de commande)
				$tItemsAmount += $amount;
			}
			
			if (!isset($this->order[$cur]))
				$cur = 0;
			if (!isset($this->_taxItemsAmount[$cur]))
				$this->_taxItemsAmount[$cur] = 0;
			
			// mise a jour du montant total des taxes pour cette ligne de commande
			// avec prise en compte du nombre de produits dans l'indice numLigneCommande
			// du tableau _taxItemsAmount de cette commande
			$this->_taxItemsAmount[$cur] += ($tItemsAmount*$item->getQuantity());
			$item->lock();
			$cur++;
		}

		// Taxes sur frais d'envoi, assurances, coûts fixes
        // au niveau des commandes
		$cur = 0;
		foreach($this->order as $order) {
			// Taxes sur frais d'envoi
			$taxArr =& $order->getShippingTax();
			foreach($taxArr as $key=>$tax) {
				$amount = HIPAY_MAPI_UTILS::computeTax($order->getShippingAmount(), $tax);
				$taxArr[$key]->setTaxAmount($amount);
				$taxArr[$key]->lock();

				if (!isset($this->_taxShippingAmount[$cur]))
					$this->_taxShippingAmount[$cur] = 0;
				$this->_taxShippingAmount[$cur] += $amount;
			}
			
            // Taxes sur assurances
            $taxArr =& $order->getInsuranceTax();
			foreach($taxArr as $key=>$tax) {
				$amount = HIPAY_MAPI_UTILS::computeTax($order->getInsuranceAmount(), $tax);
				$taxArr[$key]->setTaxAmount($amount);
				$taxArr[$key]->lock();
				if (!isset($this->_taxInsuranceAmount[$cur]))
					$this->_taxInsuranceAmount[$cur] = 0;
				$this->_taxInsuranceAmount[$cur] += $amount;
			}
			
            // Taxes sur coûts fixes
			$taxArr =& $order->getFixedCostTax();
			foreach($taxArr as $key=>$tax) {
				$amount = HIPAY_MAPI_UTILS::computeTax($order->getFixedCostAmount(), $tax);
				$taxArr[$key]->setTaxAmount($amount);
				$taxArr[$key]->lock();
				if (!isset($this->_taxFixedCostAmount[$cur]))
					$this->_taxFixedCostAmount[$cur] = 0;
				$this->_taxFixedCostAmount[$cur] += $amount;
			}
			
			$cur++;
		}
	}

	/**
	 * Calcul le montant total HT des produits
	 *
	 */
	protected function computeItemsAmount() {
		$itemsAmount = 0;
		$cur = 0;
		foreach($this->items as $item) {
			$mt = sprintf("%.02f", $item->getPrice() * $item->getQuantity());

			if (!isset($this->order[$cur]))
				$cur = 0;
			if (!isset($this->_itemsTotalAmount[$cur]))
				$this->_itemsTotalAmount[$cur] = 0;
			$this->_itemsTotalAmount[$cur] += $mt;
			
			$cur++;
		}
	}

	/**
	 * Retourne le montant total des taxes
	 *
	 * @param array $tItemsAmount
	 * @param array $tShippingAmount
	 * @param array $tInsuranceAmount
	 * @param array $tFixedCostAmount
	 */
	public function getTotalTaxes(&$tItemsAmount, &$tShippingAmount, &$tInsuranceAmount, &$tFixedCostAmount) {
		$tItemsAmount = $this->getItemsTaxes();
		$tShippingAmount = $this->getShippingTaxes();
		$tInsuranceAmount = $this->getInsuranceTaxes();
		$tFixedCostAmount = $this->getFixedCostTaxes();
	}

	/**
	 * Retourne le montant des taxes sur les articles
	 *
	 * @return float
	 */
	public function getItemsTaxes() {
		return $this->_taxItemsAmount;
	}

	/**
	 * Retourne le montant des taxes sur les frais de port
	 *
	 * @return float
	 */
	public function getShippingTaxes() {
		return $this->_taxShippingAmount;
	}

	/**
	 * Retourne le montant des taxes sur les assurances
	 *
	 * @return float
	 */
	public function getInsuranceTaxes() {
		return $this->_taxInsuranceAmount;
	}

	/**
	 * Retourne le montant des taxes sur les couts fixes
	 *
	 * @return float
	 */
	public function getFixedCostTaxes() {
		return $this->_taxFixedCostAmount;
	}

	/**
	 * Retourne le montant total des produits
	 *
	 * @return array
	 */
	public function getItemsTotalAmount() {
		return $this->_itemsTotalAmount;
	}

	/**
	 * Retourne le montant total des taxes
	 *
	 * @return array
	 */
	public function getTaxesTotalAmount() {
		return $this->_taxTotalAmount;
	}

	/**
	 * Retourne le montant total hors taxes de la commande
	 *
	 * @return array
	 */
	public function getOrderTotalAmountHT() {
		return $this->_orderTotalAmountHT;
	}

	/**
	 * Retourne le montant total TTC de la commande
	 *
	 * @return array
	 */
	public function getOrderTotalAmount() {
		return $this->_orderTotalAmount;
	}

	/**
	 * Retourne le montant total des affiliés
	 *
	 * @return array
	 */
	public function getAffiliateTotalAmount() {
		return $this->_affiliateTotalAmount;
	}

	/**
	 * Retourne les objets order
	 *
	 * @return array
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * Retourne les objets items
	 *
	 * @return array
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * Retourne l'objet paramètre
	 *
	 * @return HIPAY_MAPI_PaymentParams
	 */
	public function getPaymentParams() {
		return $this->paymentParams;
	}

	/**
	 * Calcul les montants à redistribuer aux affiliés
	 *
	 */
	protected function computeAffiliates() {
		foreach($this->order as $k=>$order) {
			$totalAmount = 0;
			$tAffiliate = $order->getAffiliate();
			foreach($tAffiliate as $key=>$affiliate) {
				$baseAmount = 0;
				$percentageTarget = $affiliate->getPercentageTarget();
				if ($percentageTarget > 0) {
					if ($percentageTarget & HIPAY_MAPI_TTARGET_ITEM)
						$baseAmount +=$this->_itemsTotalAmount[$k];
					if ($percentageTarget & HIPAY_MAPI_TTARGET_TAX)
						$baseAmount += $this->_taxItemsAmount[$k] + $this->_taxFixedCostAmount[$k] + $this->_taxInsuranceAmount[$k] + $this->_taxShippingAmount[$k];
					if ($percentageTarget& HIPAY_MAPI_TTARGET_INSURANCE)
						$baseAmount += $order->getInsuranceAmount();
					if ($percentageTarget & HIPAY_MAPI_TTARGET_FCOST)
						$baseAmount += $order->getFixedCostAmount();
					if ($percentageTarget & HIPAY_MAPI_TTARGET_SHIPPING)
						$baseAmount += $order->getShippingAmount();
						
					$tAffiliate[$key]->setBaseAmount($baseAmount);
				} else {
					$baseAmount = $affiliate->getValue();
					$tAffiliate[$key]->setBaseAmount($baseAmount);
				}
				
				$totalAmount += $tAffiliate[$key]->getAmount();
				$tAffiliate[$key]->lock();
			}
			
			$this->_affiliateTotalAmount[$k] = $totalAmount;
			if ($totalAmount > $this->_orderTotalAmount[$k]) {
				throw new Exception('The total amount to distribute is greather than the transaction amount ('.$totalAmount.'/'.$this->_orderTotalAmount.')');
			}
		}
	}
}