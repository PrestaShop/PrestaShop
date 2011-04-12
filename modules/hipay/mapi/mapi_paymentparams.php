<?php
/**
 * Paramètres
 *
 */
class HIPAY_MAPI_PaymentParams extends HIPAY_MAPI_lockable {

	/**
	 * Nom d'utilisateur pour se connecter à la plateforme
	 *
	 * @var string
	 */
	protected $login;

	/**
	 * Mot de passe de connexion
	 *
	 * @var string
	 */
	protected $password;

	/**
	 * Compte sur lequel verser le montant HT des produits
	 *
	 * @var int
	 */
	protected $itemAccount;

	/**
	 * Compte sur lequel verser le montant des taxes
	 *
	 * @var int
	 */
	protected $taxAccount;

	/**
	 * Compte sur lequel verser le montant des assurances
	 *
	 * @var int
	 */
	protected $insuranceAccount;

	/**
	 * Compte sur lequel verser le montant des coûts fixes
	 *
	 * @var int
	 */
	protected $fixedCostAccount;

	/**
	 * Compte sur lequel verser le montant des frais de livraison
	 *
	 * @var int
	 */
	protected $shippingCostAccount;

	/**
	 * Langue par défaut de l'interface de paiement
	 *
	 * @var string
	 */
	protected $defaultLang;

	/**
	 * Type d'interface de paiement
	 *
	 * @var string
	 */
	protected $media;

	/**
	 * Classe d'age à laquelle s'applique cette vente
	 *
	 * @var string
	 */
	protected $rating;

	/**
	 * Type du paiement (simple ou récurrent)
	 *
	 * @var int
	 */
	protected $paymentMethod;

	/**
	 * Délai de capture
	 *
	 * @var int
	 */
	protected $captureDay;

	/**
	 * Devise dans laquelle sont exprimés les montants
	 *
	 * @var string
	 */
	protected $currency;

	/**
	 * Identifiant de cette vente pour le marchant
	 *
	 * @var int
	 */
	protected $idForMerchant;

	/**
	 * Identifiant du site marchand tel que renseigné
	 * dans l'interface marchande de la plateforme
	 *
	 * @var int
	 */
	protected $merchantSiteId;


	/**
	 * Identifiant du groupe statistique auquel appartient ce paiement
	 *
	 * @var int
	 */
	protected $statsGroupId;


	/**
	 * Données propres au marchand
	 *
	 * @var string
	 */
	protected $merchantDatas;

	/**
	 * Url sur laquelle est redirigée le client si le paiement est ok
	 *
	 * @var string
	 */
	protected $url_ok;

	/**
	 * Url sur laquelle est redirigée le client si le paiement n'est pas ok
	 *
	 * @var string
	 */
	protected $url_nok;

	/**
	 * Url sur laquelle est redirigée le client si le paiement est annulé
	 *
	 * @var string
	 */
	protected $url_cancel;

	/**
	 * Url du script qui sera appellé pour signifier le résultat du paiement
	 *
	 * @var string
	 */
	protected $url_ack;

	/**
	 * Chaine de caractère devant etre retournée par le script $url_ack
	 * pour informer de la bonne réception des informations
	 *
	 * @var string
	 */
	protected $ack_wd;

	/**
	 * Adresse email vers laquelle la plateforme enverra un email
	 * pour informer d'un nouveau paiement
	 *
	 * @var string
	 */
	protected $email_ack;

	/**
	 * code hexa de la couleur de fond de l'interface
	 *
	 * @var string
	 */
	protected $bg_color;

	/**
	 * Url du logo du marchand
	 *
	 * @var string
	 */
	protected $logo_url;


	/**
	 * Assigne le login et le mot de passe
	 *
	 * @param string $login
	 * @param string $password
	 * @return boolean
	 */
	public function setLogin($login,$password) {
		if ($this->_locked)
			return false;
		$login = HIPAY_MAPI_UTF8::forceUTF8($login);
		if (empty($login))
            return false;
		$password = HIPAY_MAPI_UTF8::forceUTF8($password);
		if (empty($password))
            return false;
		$this->login=$login;
		$this->password=$password;
		return true;
	}

	/**
	 * Retourne le login
	 *
	 * @return string
	 */
	public function getLogin() {
		return $this->login;
	}

	/**
	 * Retourne le mot de passe
	 *
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Assigne les comptes sur lesquels seront versés les différents montants
	 *
	 * @param int $itemAccount
	 * @param int $taxAccount
	 * @param int $insuranceAccount
	 * @param int $fixedCostAccount
	 * @param int $shippingCostAccount
	 * @return boolean
	 */
	public function setAccounts($itemAccount,$taxAccount=0,$insuranceAccount=0,$fixedCostAccount=0,$shippingCostAccount=0) {
		if ($this->_locked)
			return false;

		$itemAccount=(int)$itemAccount;
		$taxAccount=(int)$taxAccount;
		$insuranceAccount=(int)$insuranceAccount;
		$fixedCostAccount=(int)$fixedCostAccount;
		$shippingCostAccount=(int)$shippingCostAccount;

		if ($itemAccount<=0)
			return false;
		if ($taxAccount<=0)
			$taxAccount=$itemAccount;
		if ($insuranceAccount<=0)
			$insuranceAccount=$itemAccount;
		if ($fixedCostAccount<=0)
			$fixedCostAccount=$itemAccount;
		if ($shippingCostAccount<=0)
			$shippingCostAccount=$itemAccount;
		$this->itemAccount = $itemAccount;
		$this->taxAccount = $taxAccount;
		$this->insuranceAccount = $insuranceAccount;
		$this->fixedCostAccount = $fixedCostAccount;
		$this->shippingCostAccount = $shippingCostAccount;
		return true;
	}

	/**
	 * Retourne le numéro de compte sur lequel sera versé
	 * le montant de produits
	 *
	 * @return int
	 */
	public function getItemAccount() {
		return $this->itemAccount;
	}

	/**
	 * Retourne le numéro de compte sur lequel sera versé
	 * le montant des taxes
	 *
	 * @return int
	 */
	public function getTaxAccount() {
		return $this->taxAccount;
	}

	/**
	 * Retourne le numéro de compte sur lequel sera versé
	 * le montant des assurances
	 *
	 * @return int
	 */
	public function getInsuranceAccount() {
		return $this->insuranceAccount;
	}

	/**
	 * Retourne le numéro de compte sur lequel sera versé
	 * le montant des coûts fixes
	 *
	 * @return int
	 */
	public function getFixedCostAccount() {
		return $this->fixedCostAccount;
	}

	/**
	 * Retourne le numéro de compte sur lequel sera versé
	 * le montant des frais d'envoi
	 *
	 * @return int
	 */
	public function getShippingCostAccount() {
		return $this->shippingCostAccount;
	}

	/**
	 * Assigne la lange par défaut (AZ_az = pays_langue)
	 *
	 * @param string $defaultLang
	 * @return boolean
	 */
	public function setDefaultLang($defaultLang) {
		if ($this->_locked)
			return false;

        if (!preg_match('#^[A-Z]{2}_[a-z]{2}$#',$defaultLang))
			return false;
		$this->defaultLang=$defaultLang;
		return true;
	}

	/**
	 * Retourne la langue par défaut
	 *
	 * @return string
	 */
	public function getDefaultLang() {
		return $this->defaultLang;
	}

	/**
	 * Défini le type d'interface de paiement
	 *
	 * @param string $media
	 * @return boolean
	 */
	public function setMedia($media) {
		if ($this->_locked)
			return false;

        if (!preg_match('#^[A-Z]+$#',$media))
			return false;
		$this->media=$media;
		return true;
	}

	/**
	 * Retourne le type d'interface de paiement
	 *
	 * @return string
	 */
	public function getMedia() {
		return $this->media;
	}

	/**
	 * Défini le public visé
	 *
	 * @param string $rating
	 * @return boolean
	 */
	public function setRating($rating) {
		if ($this->_locked)
			return false;

		$rating = trim(substr($rating,0,HIPAY_MAPI_MAX_RATING_LENGTH));
		if ($rating=='')
			return false;
		$this->rating=$rating;
		return true;
	}

	/**
	 * Retourne le type de public visé
	 *
	 * @return string
	 */
	public function getRating() {
		return $this->rating;
	}

	/**
	 * Défini si le paiement est simple ou récurrent
	 *
	 * @param int $paymentMethod
	 * @return boolean
	 */
	public function setPaymentMethod($paymentMethod) {
		if ($this->_locked)
        {
			return false;
        }

		$paymentMethod = (int)$paymentMethod;
        if ($paymentMethod!=HIPAY_MAPI_METHOD_SIMPLE && $paymentMethod!=HIPAY_MAPI_METHOD_MULTI)
            return false;
		$this->paymentMethod=$paymentMethod;
		return true;
	}

	/**
	 * Retourne le type de paiement (simple ou récurrent)
	 *
	 * @return int
	 */
	public function getPaymentMethod() {
		return $this->paymentMethod;
	}

	/**
	 * Défini le délai de capture
	 *
	 * @param int $captureDay
	 * @return boolean
	 */
	public function setCaptureDay($captureDay) {
		if ($this->_locked)
			return false;

		$captureDay = (int)$captureDay;
		if (($captureDay!=HIPAY_MAPI_CAPTURE_MANUAL && $captureDay!=HIPAY_MAPI_CAPTURE_IMMEDIATE && $captureDay<=0) || $captureDay>HIPAY_MAPI_CAPTURE_MAX_DAYS)
			return false;
		$this->captureDay=$captureDay;
		return true;

	}

	/**
	 * Retourne le délai de capture
	 *
	 * @return int
	 */
	public function getCaptureDay() {
		return $this->captureDay;
	}

	/**
	 * Défini la devise
	 *
	 * @param string $currency
	 * @return boolean
	 */
	public function setCurrency($currency) {
		if ($this->_locked)
			return false;

        if (!preg_match('#^[A-Z]{3}$#',$currency))
			return false;
		$this->currency = $currency;
		return true;
	}

	/**
	 * Retourne la devise
	 *
	 * @return string
	 */
	public function getCurrency() {
		return $this->currency;
	}



	/**
	 * Défini l'identifiant du groupe statistique auquel appartient ce paiement
	 *
	 * @param int $statsGroupId
	 * @return boolean
	 */
	public function setStatsGroupId($statsGroupId) {
		if ($this->_locked)
			return false;

		$statsGroupId=(int)$statsGroupId;
		if ($statsGroupId<0)
			return false;
		$this->statsGroupId=$statsGroupId;
		return true;
	}


	/**
	 * Retourne l'identifiant du groupe statistique auquel appartient ce paiement
	 *
	 * @return int
	 */
	public function getStatsGroupId() {
		return $this->statsGroupId;
	}


	/**
	 * Défini l'identifiant de cette vente chez le marchand
	 *
	 * @param string $idForMerchant
	 * @return boolean
	 */
	public function setIdForMerchant($idForMerchant) {
		if ($this->_locked)
			return false;

		$this->idForMerchant=$idForMerchant;
		return true;
	}

	/**
	 * Retourne l'identifiant de la vente pour le marchand
	 *
	 * @return string
	 */
	public function getIdForMerchant() {
	    return $this->idForMerchant;
	}

	/**
	 * Défini l'identifiant du site marchand
	 *
	 * @param int $merchantSiteId
	 * @return boolean
	 */
	public function setMerchantSiteId($merchantSiteId) {
		if ($this->_locked)
			return false;

		$merchantSiteId=(int)$merchantSiteId;
		if ($merchantSiteId<0)
			return false;
		$this->merchantSiteId=$merchantSiteId;
		return true;
	}

	/**
	 * Retourne l'identifiant du site marchand
	 *
	 * @return int
	 */
	public function getMerchantSiteId() {
		return $this->merchantSiteId;
	}

	/**
	 * Assigne des données marchandes
	 *
	 * @param string $merchantDatas
	 * @return boolean
	 */
	public function setMerchantDatas($key,$merchantDatas) {
		if ($this->_locked)
			return false;
		if ($key=='')
			return false;
		$merchantDatas=substr($merchantDatas,0,HIPAY_MAPI_MAX_MDATAS_LENGTH);
		$this->merchantDatas[$key]=$merchantDatas;
		return true;
	}

	/**
	 * Retourne les données marchandes
	 *
	 * @return array
	 */
	public function getMerchantDatas() {
		return $this->merchantDatas;

	}

	/**
	 * Assigne l'url à appeller si le paiement est ok
	 *
	 * @param string $url_ok
	 * @return unknown
	 */
	public function setUrlOk($url_ok) {
		if ($this->_locked)
			return false;
		$url_ok = trim($url_ok);
		if (!HIPAY_MAPI_UTILS::checkURL($url_ok) && $url_ok!='')
			return false;
		$this->url_ok=$url_ok;
		return true;
	}

	/**
	 * Retourne l'url_ok
	 *
	 * @return string
	 */
	public function getUrlOk() {
		return $this->url_ok;

	}

	/**
	 * Assigne l'url à appeller si le paiement n'est pas ok
	 *
	 * @param string $url_nok
	 * @return unknown
	 */
	public function setUrlNok($url_nok) {
		if ($this->_locked)
			return false;
		$url_nok = trim($url_nok);
		if (!HIPAY_MAPI_UTILS::checkURL($url_nok) && $url_nok!='')
			return false;
		$this->url_nok=$url_nok;
		return true;
	}

	/**
	 * Retourne l'url_nok
	 *
	 * @return string
	 */
	public function getUrlNok() {
		return $this->url_nok;

	}

	/**
	 * Assigne l'url à appeller si le paiement est annulé
	 *
	 * @param string $url_cancel
	 * @return boolean
	 */
	public function setUrlCancel($url_cancel) {
		if ($this->_locked)
			return false;
		$url_cancel = trim($url_cancel);
		if (!HIPAY_MAPI_UTILS::checkURL($url_cancel) && $url_cancel!='')
			return false;
		$this->url_cancel=$url_cancel;
		return true;
	}

	/**
	 * Retourne l'url_cancel
	 *
	 * @return string
	 */
	public function getUrlCancel() {
		return $this->url_cancel;
	}


	/**
	 * Assigne l'url à appeller pour notifier le paiement
	 *
	 * @param string $url_cancel
	 * @return boolean
	 */
	public function setUrlAck($url_ack) {
		if ($this->_locked)
			return false;
		$url_ack = trim($url_ack);
		if (!HIPAY_MAPI_UTILS::checkURL($url_ack) && $url_ack!='')
			return false;
		$this->url_ack=$url_ack;
		return true;
	}

	/**
	 * Retourne l'url_ack
	 *
	 * @return string
	 */
	public function getUrlAck() {
		return $this->url_ack;
	}




	/**
	 * Assigne le mot clé d'acquittement
	 *
	 * @param string $ack_wd
	 * @return boolean
	 */
	public function setAckWd($ack_wd) {
		if ($this->_locked)
			return false;
		$ack_wd=trim($ack_wd);
		if (strlen($ack_wd)>HIPAY_MAPI_MAX_ACKWD_LENGTH)
			return false;
		$this->ack_wd=$ack_wd;
		return true;
	}

	/**
	 * Retourne le mot clé d'acquittement
	 *
	 * @return string
	 */
	public function getAckWd() {
		return $this->ack_wd;
	}

	/**
	 * Assigne l'adresse email de notification de paiement
	 *
	 * @param string $email_ack
	 * @return boolean
	 */
	public function setEmailAck($email_ack) {
		if ($this->_locked)
			return false;
		$email_ack=trim($email_ack);
		if (strlen($email_ack)>HIPAY_MAPI_MAX_ACKMAIL_LENGTH || (!HIPAY_MAPI_UTILS::checkemail($email_ack) && $email_ack!=''))
			return false;
		$this->email_ack=$email_ack;
		return true;
	}

	/**
	 * Retourne l'email de notification
	 *
	 * @return string
	 */
	public function getEmailAck() {
		return $this->email_ack;
	}

	/**
	 * Assigne la couleur de fond de l'interface (#XXXXXX)
	 *
	 * @param string $bg_color
	 * @return boolean
	 */
	public function setBackgroundColor($bg_color) {
		if ($this->_locked)
			return false;
		$bg_color = trim($bg_color);
        if (!preg_match('#^\#([0-9a-f]){6}$#i', $bg_color) && $bg_color != '')
			return false;
		$this->bg_color = $bg_color;
		return true;
	}

	/**
	 * Retourne la couleur de fond de l'interface
	 *
	 * @return string
	 */
	public function getBackgroundColor() {
		return $this->bg_color;
	}


	/**
	 * Assigne l'url du logo du marchand
	 *
	 * @param string $logo_url
	 * @return boolean
	 */
	public function setLogoUrl($logo_url) {
		if ($this->_locked)
			return false;
		$logo_url = trim($logo_url);
		if (!HIPAY_MAPI_UTILS::checkURL($logo_url) && $logo_url!='')
			return false;
		$this->logo_url=$logo_url;
		return true;
	}

	/**
	 * Retourne l'url du logo du marchand
	 *
	 * @return string
	 */
	public function getLogoUrl() {
		return $this->logo_url;
	}


	/**
	 * Vérifie que l'objet est correctement initialisé
	 *
	 * @return boolean
	 */
	public function check() {
		if ($this->login=='')
			throw new Exception('Nom d\'utilisateur manquant');
		if ($this->itemAccount<=0 || $this->taxAccount<=0 || $this->insuranceAccount <=0 ||
		$this->fixedCostAccount<=0 || $this->shippingCostAccount<=0)
			throw new Exception('Numéros de compte invalides');
		if ($this->rating=='')
			throw new Exception('Type de public visé invalide');
		if ($this->paymentMethod<0)
			throw new Exception('Type de paiement invalide');
		if ($this->captureDay==-100)
			throw new Exception('Délai de capture invalide ');
		if ($this->currency=='')
			throw new Exception('Devise non-définie');
		if ($this->idForMerchant<0)
			throw new Exception('ID chez le marchand manquant');
		if ($this->idForMerchant>0)
			if ($this->password=='')
                throw new Exception('Mot de passe manquant');
	    if ($this->statsGroupId<0)
			throw new Exception('ID groupe est négatif');
		if ($this->merchantSiteId<0)
			throw new Exception('ID du site marchand manquant');
		return true;
	}

	protected function init() {
		$this->login='';
		$this->password='';
		$this->itemAccount=0;
		$this->taxAccount=0;
		$this->insuranceAccount=0;
		$this->fixedCostAccount=0;
		$this->shippingCostAccount=0;
		$this->defaultLang=HIPAY_MAPI_DEFLANG;
		$this->media=HIPAY_MAPI_DEFMEDIA;
		$this->rating='';
		$this->paymentMethod=-1;
		$this->captureDay=-100;
		$this->currency='';
		$this->idForMerchant=-1;
		$this->statsGroupId=0;
		$this->merchantSiteId=-1;
		$this->merchantDatas=array();
		$this->url_ok='';
		$this->url_nok='';
		$this->url_cancel='';
		$this->url_ack='';
		$this->ack_wd='';
		$this->email_ack='';
		$this->bg_color='';
		$this->logo_url='';
	}

	function __construct() {
		$this->init();
		parent::__construct();
	}
}

