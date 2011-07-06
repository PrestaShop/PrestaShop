<?php
/**
 * Représente un paiement simple
 * Un paiement simple contient :
 * - 1 objet paramètres HIPAY_MAPI_PaymentParams
 * - 1 objet order HIPAY_MAPI_Order
 * - n objet produits représentant les lignes de la commande HIPAY_MAPI_Product
 *
 */
class HIPAY_MAPI_SimplePayment extends HIPAY_MAPI_Payment {

	function __construct(HIPAY_MAPI_PaymentParams $paymentParams, HIPAY_MAPI_Order $order, $items) {
		try {
			parent::__construct($paymentParams, array($order), $items);
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
}