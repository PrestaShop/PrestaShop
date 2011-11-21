<?php
class TntWebService
{
	private $_login;
	private $_password;
	private $_account;
	private $authheader;
	private $authvars;
	private $header;
	private $file;
	
	public	function __construct()
	{
		$this->_login = Configuration::get('TNT_CARRIER_LOGIN');
		$this->_password = Configuration::get('TNT_CARRIER_PASSWORD');
		$this->_account = Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT');
		
		$this->_authheader = $this->genAuth();
		$this->_authvars = new SoapVar($this->_authheader, XSD_ANYXML);
		$this->_header = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $this->_authvars);
		$this->_file = "http://www.tnt.fr/service/?wsdl";
	}
	
	public function genAuth()
	{
		 return sprintf('
				<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken>
				<wsse:Username>%s</wsse:Username>
				<wsse:Password>%s</wsse:Password>
				</wsse:UsernameToken>
				</wsse:Security>', htmlspecialchars($this->_login), htmlspecialchars($this->_password));
	}
	
	public function faisabilite($dateExpedition, $codePostalDepart, $communeDepart, $codePostalArrivee, $communeArrivee, $typeDestinataire) 
	{
		$soapclient = new SoapClient($this->_file, array('trace'=>1));
		$soapclient->__setSOAPHeaders(array($this->_header));
		
		$sender = array("zipCode" => $codePostalDepart, "city" => $communeDepart);
		$receiver = array("zipCode" => $codePostalArrivee, "city" => $communeArrivee, "type" => $typeDestinataire);
		$parameters = array("accountNumber" => $this->_account, "shippingDate" => $dateExpedition, "sender" => $sender, "receiver" => $receiver);
		$services = $soapclient->feasibility(array('parameters' => $parameters));
		return ($services);
	}
	
	public function putCityInNormeTnt($city)
	{
		$city = iconv("utf-8", 'ASCII//TRANSLIT', $city);
		$city = mb_strtoupper($city, 'utf-8');
		$table = array('`' => '','\''=> '', '^' => '','À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B');
		$city = strtr($city, $table);
		$old = array("SAINT", "-");
		$new = array("ST", " ");
		return (str_replace($old, $new, $city));
	}
	
	public function getPackage($info)
	{
		$soapclient = new SoapClient($this->_file, array('trace'=>1));
		$soapclient->__setSOAPHeaders(array($this->_header));
		
		$sender = array(
			'type' => (Configuration::get('TNT_CARRIER_SHIPPING_COLLECT') ? "ENTERPRISE" : "DEPOT"), //ENTREPRISE OR DEPOT
			'typeId' => (Configuration::get('TNT_CARRIER_SHIPPING_COLLECT') ? "" : Configuration::get('TNT_CARRIER_SHIPPING_PEX')) , // code PEX if DEPOT is ON
			'name' => Configuration::get('TNT_CARRIER_SHIPPING_COMPANY'), // raison social
			'address1' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1'),
			'address2' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS2'),
			'zipCode' => Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE'),
			'city' => $this->putCityInNormeTnt(Configuration::get('TNT_CARRIER_SHIPPING_CITY')),
			'contactLastName' => Configuration::get('TNT_CARRIER_SHIPPING_LASTNAME'),
			'contactFirstName' => Configuration::get('TNT_CARRIER_SHIPPING_FIRSTNAME'),
			'emailAddress' => Configuration::get('TNT_CARRIER_SHIPPING_EMAIL'),
			'phoneNumber' => Configuration::get('TNT_CARRIER_SHIPPING_PHONE'),
			'faxNumber' => '' //may be later
		);
		
		if ($info[4] == null)
			$receiver = array(
				'type' => ($info[0]['company'] != '' ? "ENTERPRISE" : 'INDIVIDUAL'), // ENTREPRISE DEPOT DROPOFFPOINT INDIVIDUAL
				'typeId' => '', // IF DEPOT => code PEX else if DROPOFFPOINT => XETT
				'name' => ($info[0]['company'] != '' ? $info[0]['company'] : ''),
				'address1' => $info[0]['address1'],
				'address2' => $info[0]['address2'],
				'zipCode' => $info[0]['postcode'],
				'city' => $this->putCityInNormeTnt($info[0]['city']),
				'instructions' => '',
				'contactLastName' => $info[0]['lastname'],
				'contactFirstName' => $info[0]['firstname'],
				'emailAddress' => $info[0]['email'],
				'phoneNumber' => $info[0]['phone'],
				'accessCode' => '',
				'floorNumber' => '',
				'buildingId' => '',
				'sendNotification' => ''
			);
		else
			$receiver = array(
				'type' => 'DROPOFFPOINT', // ENTREPRISE DEPOT DROPOFFPOINT INDIVIDUAL
				'typeId' => $info[4]['code'], // IF DEPOT => code PEX else if DROPOFFPOINT => XETT
				'name' => $info[4]['name'],
				'address1' => $info[4]['address'],
				'address2' => '',
				'zipCode' => $info[4]['zipcode'],
				'city' => $info[4]['city'],
				'instructions' => '',
				'contactLastName' => $info[0]['lastname'],
				'contactFirstName' => $info[0]['firstname'],
				'emailAddress' => $info[0]['email'],
				'phoneNumber' => $info[0]['phone'],
				'accessCode' => '',
				'floorNumber' => '',
				'buildingId' => '',
				'sendNotification' => ''
			);
		
		foreach ($info[1]['weight'] as $k => $v)
		{
			$parcelRequest[$k] = array(
				'sequenceNumber' => $k + 1, // package number, there's only one at this moment
				'customerReference' => $info[0]['id_customer'], // customer ref
				'weight' => $v, 
				'insuranceAmount' => '',
				'priorityGuarantee' => '',
				'comment' => ''
			);
		}
		
		$parcelsRequest = array('parcelRequest' => $parcelRequest);
		
		$pickUpRequest = array(
			'media' => "EMAIL",
			'faxNumber' => "",
			'emailAddress' => Configuration::get('TNT_CARRIER_SHIPPING_EMAIL'),
			'notifySuccess' => "1",
			'service' => "",
			'lastName' => "",
			'firstName' => "",
			'phoneNumber' => Configuration::get('TNT_CARRIER_SHIPPING_PHONE'),
			'closingTime' => Configuration::get('TNT_CARRIER_SHIPPING_CLOSING'),
			'instructions' => ""
		);
		
		if (Configuration::get('TNT_CARRIER_SHIPPING_COLLECT') == 1)
		{
			$paremeters = array(
			'pickUpRequest' => $pickUpRequest,
			'shippingDate' => $info[2]['delivery_date'],
			'accountNumber' => Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'),
			'sender' => $sender,
			'receiver' => $receiver,
			'serviceCode' => $info[3]['option'],
			'quantity' => count($info[1]['weight']), //number of package; count($parcelsRequest)
			'parcelsRequest' => $parcelsRequest,
			'saturdayDelivery' => '0',//Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY'),
			//'paybackInfo' => $paybackInfo,
			'labelFormat' => (!Configuration::get('TNT_CARRIER_PRINT_STICKER') ? "STDA4" : Configuration::get('TNT_CARRIER_PRINT_STICKER'))
			);
		}
		else
		{
			$paremeters = array(
			'shippingDate' => $info[2]['delivery_date'],
			'accountNumber' => Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'),
			'sender' => $sender,
			'receiver' => $receiver,
			'serviceCode' => $info[3]['option'],
			'quantity' => count($info[1]['weight']), //number of package; count($parcelsRequest)
			'parcelsRequest' => $parcelsRequest,
			'saturdayDelivery' => '0',//Configuration::get('TNT_CARRIER_SHIPPING_DELIVERY'),
			//'paybackInfo' => $paybackInfo,
			'labelFormat' => (!Configuration::get('TNT_CARRIER_PRINT_STICKER') ? "STDA4" : Configuration::get('TNT_CARRIER_PRINT_STICKER'))
			);
		}
		$package = $soapclient->expeditionCreation(array('parameters' => $paremeters));
		return $package;
	}
	
	public function followPackage($transport) 
	{
		$soapclient = new SoapClient($this->_file, array('trace'=>1));
		$soapclient->__setSOAPHeaders(array($this->_header));
		
		$reponse = $soapclient->trackingByConsignment(array('parcelNumber' => $transport));
	
		if (isset($reponse->Parcel) && $reponse->Parcel)
		{
			$colis = $reponse->Parcel;
			$expediteur = $colis->sender;
			$destinataire = $colis->receiver;
			$evenements = $colis->events;
		
			$requestDate = new DateTime($evenements->requestDate);
			$processDate = new DateTime($evenements->processDate);
			$arrivalDate = new DateTime($evenements->arrivalDate);
			$deliveryDepartureDate = new DateTime($evenements->deliveryDepartureDate);
			$deliveryDate = new DateTime($evenements->deliveryDate);
		}
		
		$packageParam = array(
			'number' => (isset($colis->consignmentNumber) ? $colis->consignmentNumber : ''),
			'status' => (isset($colis->shortStatus) ? $colis->shortStatus : ''),
			'account_number' => (isset($colis->accountNumber) ? $colis->accountNumber : ''),
			'service' => (isset($colis->service) ? $colis->service : ''),
			'reference' => (isset($colis->reference) ? $colis->reference : ''),
			'weight' => (isset($colis->weight) ? $colis->weight : ''),
			'expediteur_name' => (isset($expediteur->name) ? $expediteur->name : ''),
			'expediteur_addr1' => (isset($expediteur->address1) ? $expediteur->address1 : ''),
			'expediteur_addr2' => (isset($expediteur->address2) ? $expediteur->address2 : ''),
			'expediteur_zipcode' => (isset($expediteur->zipCode) ? $expediteur->zipCode : ''),
			'expediteur_city' => (isset($expediteur->city) ? $expediteur->city : ''),
			'destinataire_name' => (isset($destinataire->name) ? $destinataire->name : ''),
			'destinataire_addr1' => (isset($destinataire->address1) ? $destinataire->address1 : ''),
			'destinataire_addr2' => (isset($destinataire->address2) ? $destinataire->address2 : ''),
			'destinataire_zipcode' => (isset($destinataire->zipCode) ? $destinataire->zipCode : ''),
			'destinataire_city' => (isset($destinataire->city) ? $destinataire->city : ''),
			'request' => (isset($evenements->requestDate) ? $evenements->requestDate : ''),
			'requestDate' => (isset($requestDate) && isset($evenements->requestDate) ? $requestDate : ''),
			'process' => (isset($evenements->processDate) ? $evenements->processDate : ''),
			'process_date' => (isset($processDate) && isset($evenements->processDate) ? $processDate : ''),
			'process_center' => (isset($evenements->processCenter) ? $evenements->processCenter : ''),
			'arrival' => (isset($evenements->arrivalDepartureDate) ? $evenements->arrivalDepartureDate : ''),
			'arrival_date' => (isset($arrivalDate) ? $arrivalDate : ''),
			'arrival_center' => (isset($evenements->arrivalCenter) ? $evenements->arrivalCenter : ''),
			'delivery_departure' => (isset($evenements->deliveryDepartureDate) ? $evenements->deliveryDepartureDate : ''),
			'delivery_departure_date' => (isset($deliveryDepartureDate) ? $deliveryDepartureDate : ''),
			'delivery_departure_center' => (isset($evenements->deliveryDepartureCenter) ? $evenements->deliveryDepartureCenter : ''),
			'delivery' => (isset($evenements->deliveryDate) ? $evenements->deliveryDate : ''),
			'delivery_date' => (isset($deliveryDate) ? $deliveryDate : ''),
			'long_status' => (isset($colis->longStatus) ? $colis->longStatus : ''),
			'linkPicture' => (isset($colis->primaryPODUrl) ? $colis->primaryPODUrl : '')
			);			
		return $packageParam;
	}
}
?>