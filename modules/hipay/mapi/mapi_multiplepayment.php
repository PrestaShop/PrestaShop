<?php
/**
 * Représente un paiement récurrent
 * Un paiement simple contient :
 * - 1 objet paramètres HIPAY_MAPI_PaymentParams
 * - 1 objet order pour le premier paiement HIPAY_MAPI_Order
 * - 1 objet installment pour le premier paiement HIPAY_MAPI_Installment
 * - 1 objet order pour le second paiement HIPAY_MAPI_Order
 * - 1 objet installment pour le second paiement HIPAY_MAPI_Installment
 *
 */
class HIPAY_MAPI_MultiplePayment extends HIPAY_MAPI_Payment {
	function __construct(HIPAY_MAPI_PaymentParams $paymentParams, HIPAY_MAPI_Order $firstOrder, HIPAY_MAPI_Installment $firstInstallment, HIPAY_MAPI_Order $nextOrder, HIPAY_MAPI_Installment $nextInstallment) {
		if ($firstInstallment->getFirst() === $nextInstallment->getFirst() || !$firstInstallment->getFirst()) {
			throw new Exception('You must define a installment object for the first payment and next payments');
		}
		
		$firstInstallment->setDelayTS();
		$nextInstallment->setDelayTS($firstInstallment->getDelayTS());
		
		try {
			parent::__construct($paymentParams, array($firstOrder, $nextOrder), array($firstInstallment, $nextInstallment));
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	/**
	 * Retourne le montant total de la somme devant être
	 * distribuée aux affiliés
	 */
	protected function _getTotalAmountForAffiliates($installement_nr) {
		$affiliates = $this->order[0]->getAffiliate();
		
		if (!HIPAY_MAPI_UTILS::is_an_array_of($affiliates, 'HIPAY_MAPI_Affiliate'))
			return false;
			
		$total_aff = 0;	
		foreach($affiliates as $aff) {
			$total_aff += $aff->getAmount();
		}
		
		return $total_aff;
	}	
}