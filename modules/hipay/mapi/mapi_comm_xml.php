<?php
class HIPAY_MAPI_COMM_XML
{
	/**
	 * Traitement de la réponse de la plateforme après l'envoi du
	 * flux représentant un paiement
	 *
	 * @param string $xml
	 * @param string $url
	 * @param string $err_msg
	 * @param string $err_keyword
	 * @param string $err_value
	 * @param string $err_code
	 * @return boolean
	 */
	public static function analyzeResponseXML($xml, &$url, &$err_msg, &$err_keyword, &$err_value, &$err_code)
    {
		$url = '';
		$err_msg = '';
		$err_keyword = '';
		$err_value = '';
		$err_code = '';
		try {
			$obj = @new SimpleXMLElement(trim($xml));
		} catch (Exception $e) {
			return false;
		}
		
		if (isset($obj->result[0]->url)) {
			$url = $obj->result[0]->url;
			return true;
		}
		if (isset($obj->result[0]->message))
			$err_msg = $obj->result[0]->message;
		if (isset($obj->result[0]->keyword))
			$err_keyword = $obj->result[0]->keyword;
		if (isset($obj->result[0]->value))
			$err_value = $obj->result[0]->value;
		if (isset($obj->result[0]->code))
			$err_code = $obj->result[0]->code;
		
		return false;
	}

	/**
	 * Traitement du flux XML envoyé par HiPay notifiant le résultat d'une action sur une transaction
	 *
	 * @param string $xml
	 * @param unknown_type $status
	 * @param string $date
	 * @param string $time
	 * @param string $transid
	 * @param string $origAmount
	 * @param string $origCurrency
	 * @param string $idformerchant
	 * @param array $merchantdatas
	 * @return boolean
	 */
	public static function analyzeNotificationXML($xml, &$operation, &$status, &$date, &$time, &$transid, &$origAmount, &$origCurrency, &$idformerchant, &$merchantdatas)
    {
		$operation = '';
		$status = '';
		$date = '';
		$time = '';
        $transid = '';
		$origAmount = '';
		$origCurrency = '';
		$idformerchant = '';
		$merchantdatas = array();
		
		try {
			$obj = new SimpleXMLElement(trim($xml));
		} catch (Exception $e) {
			return false;
		}
		
		if (isset($obj->result[0]->operation))
			$operation=$obj->result[0]->operation;
		else return false;

		if (isset($obj->result[0]->status))
			$status=$obj->result[0]->status;
		else return false;

		if (isset($obj->result[0]->date))
			$date=$obj->result[0]->date;
		else return false;

		if (isset($obj->result[0]->time))
			$time=$obj->result[0]->time;
		else return false;

        if (isset($obj->result[0]->transid))
            $transid=$obj->result[0]->transid;
        else return false;

		if (isset($obj->result[0]->origAmount))
			$origAmount=$obj->result[0]->origAmount;
		else return false;

		if (isset($obj->result[0]->origCurrency))
			$origCurrency=$obj->result[0]->origCurrency;
		else return false;

		if (isset($obj->result[0]->idForMerchant))
			$idformerchant=$obj->result[0]->idForMerchant;
		else return false;

		if (isset($obj->result[0]->merchantDatas)) {
			$d = $obj->result[0]->merchantDatas->children();
			foreach($d as $xml2) {
				if (preg_match('#^_aKey_#i',$xml2->getName())) {
					$indice = substr($xml2->getName(),6);
					$xml2 = (array)$xml2;
					$valeur = (string)$xml2[0];
					$merchantdatas[$indice] = $valeur;
				}
			}
		}
		
		return true;
	}
}

