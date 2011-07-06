<?php

/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class eBayRequest
{
	public $response;
	public $token;
	public $expiration;

	public $runame;
	public $username;
	public $session;

	public $itemID;
	public $fees;
	public $error;
	public $errorCode;

	private $devID;
	private $appID;
	private $certID;

	private $siteID;
	private $apiUrl;
	private $apiCall;

	private $loginUrl;

	private $findingUrl;
	private $findingVersion;

	private $compatibilityLevel;



	/******************************************************************/
	/** Constructor And Request Methods *******************************/
	/******************************************************************/


	public function __construct($apiCall = '')
	{
		/*** SAND BOX PARAMS ***/
		/*
		$this->devID = '1db92af1-2824-4c45-8343-dfe68faa0280';
		$this->appID = 'Prestash-2629-4880-ba43-368352aecc86';
		$this->certID = '6bd3f4bd-3e21-41e8-8164-7ac733218122';
		$this->siteID = 71;

		$this->apiUrl = 'https://api.sandbox.ebay.com/ws/api.dll';
		$this->apiCall = $apiCall;
		$this->compatibilityLevel = 719;

		$this->runame = 'Prestashop-Prestash-2629-4-hpehxegu';

		$this->loginURL = 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll';
		*/

		$this->devID = '1db92af1-2824-4c45-8343-dfe68faa0280';
		$this->appID = 'Prestash-70a5-419b-ae96-f03295c4581d';
		$this->certID = '71d26dc9-b36b-4568-9bdb-7cb8af16ac9b';
		$this->siteID = 71;

		$this->apiUrl = 'https://api.ebay.com/ws/api.dll';
		$this->apiCall = $apiCall;
		$this->compatibilityLevel = 719;

		$this->runame = 'Prestashop-Prestash-70a5-4-pepwa';

		$this->loginURL = 'https://signin.ebay.com/ws/eBayISAPI.dll';
	}


	public function makeRequest($request)
	{
		// Init
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, $this->apiUrl);

		// Stop CURL from verifying the peer's certificate
		curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
		
		// Set the headers
		curl_setopt($connection, CURLOPT_HTTPHEADER, $this->buildHeaders());
		
		// Set method as POST
		curl_setopt($connection, CURLOPT_POST, 1);
		
		// Set the XML body of the request
		curl_setopt($connection, CURLOPT_POSTFIELDS, $request);
		
		// Set it to return the transfer as a string from curl_exec
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        
		// Send the Request
		$response = curl_exec($connection);
        		
		// Close the connection
		curl_close($connection);
		
		// Return the response
		return $response;
	}


	private function buildHeaders()
	{
		$headers = array (
			// Regulates versioning of the XML interface for the API
			'X-EBAY-API-COMPATIBILITY-LEVEL: '.$this->compatibilityLevel,
			
			// Set the keys
			'X-EBAY-API-DEV-NAME: '.$this->devID,
			'X-EBAY-API-APP-NAME: '.$this->appID,
			'X-EBAY-API-CERT-NAME: '.$this->certID,
			
			// The name of the call we are requesting
			'X-EBAY-API-CALL-NAME: '.$this->apiCall,
			
			//SiteID must also be set in the Request's XML
			//SiteID = 0  (US) - UK = 3, Canada = 2, Australia = 15, ....
			//SiteID Indicates the eBay site to associate the call with
			'X-EBAY-API-SITEID: '.$this->siteID,
		);

		return $headers;
	}


	/******************************************************************/
	/** Authentication Methods ****************************************/
	/******************************************************************/


	function fetchToken()
	{
		// Set Api Call
        	$this->apiCall = 'FetchToken';

		$requestXml = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXml .= '<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXml .= '<RequesterCredentials><Username>'.$this->username.'</Username></RequesterCredentials>';
		$requestXml .= '<SessionID>'.$this->session.'</SessionID>';
		$requestXml .= '</FetchTokenRequest>';

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Saving Datas // Need to cast token var to string (not SimpleXML element) to persist in SESSION
	        $this->response = simplexml_load_string($responseXml);
        	$this->token = (string)$this->response->eBayAuthToken;
	        $this->expiration = $this->response->HardExpirationTime;
	}

	function getLoginUrl()
	{
		return $this->loginURL;
	}

	function login()
	{
		// Set Api Call
		$this->apiCall = 'GetSessionID';

		///Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXml .= '<GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXml .= '<Version>'.$this->compatibilityLevel.'</Version>';
		$requestXml .= '<RuName>'.$this->runame.'</RuName>';
		$requestXml .= '</GetSessionIDRequest>';

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

	        $this->response = simplexml_load_string($responseXml);
		$this->session = (string)$this->response->SessionID;
	}


	/******************************************************************/
	/** Retrieve Categories Methods ***********************************/
	/******************************************************************/


	function saveCategories()
	{
		// Set Api Call
		$this->apiCall = 'GetCategories';

		///Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>';
		$requestXml .= '<GetCategories xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXml .= '<Version>'.$this->compatibilityLevel.'</Version>';
		$requestXml .= '<RequesterCredentials>';
		$requestXml .= '<eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>';
		$requestXml .= '</RequesterCredentials>';
		$requestXml .= '<CategorySiteID>'.$this->siteID.'</CategorySiteID>';
		$requestXml .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXml .= '<LevelLimit>5</LevelLimit>';
		$requestXml .= '<ViewAllNodes>true</ViewAllNodes>';
		$requestXml .= '</GetCategories>';

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Load xml in array
	        $this->response = simplexml_load_string($responseXml);

		// Load categories multi sku compliant
		$categoriesMultiSkuCompliant = $this->GetCategoryFeatures('VariationsEnabled');

		// Save categories
		foreach ($this->response->CategoryArray->Category as $cat)
		{
			$category = array();
			foreach ($cat as $key => $value)
				$category[(string)$key] = (string)$value;
			$category['IsMultiSku'] = 0;
			if (isset($categoriesMultiSkuCompliant[$category['CategoryID']]))
				$category['IsMultiSku'] = 1;

			Db::getInstance()->autoExecute(_DB_PREFIX_.'ebay_category', array('id_category_ref' => pSQL($category['CategoryID']), 'id_category_ref_parent' => pSQL($category['CategoryParentID']), 'id_country' => '8', 'level' => pSQL($category['CategoryLevel']), 'is_multi_sku' => pSQL($category['IsMultiSku']), 'name' => pSQL($category['CategoryName'])), 'INSERT');
		}

		// Return
		return true;
	}

	function GetCategoryFeatures($featureID)
	{
		// Set Api Call
		$this->apiCall = 'GetCategoryFeatures';

		///Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<GetCategoryFeatures xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '  <DetailLevel>ReturnAll</DetailLevel>'."\n";
		$requestXml .= '  <FeatureID>'.$featureID.'</FeatureID>'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <Version>'.$this->compatibilityLevel.'</Version>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <ViewAllNodes>true</ViewAllNodes>'."\n";
		$requestXml .= '</GetCategoryFeatures>'."\n";


		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}


		// Load xml in array
		$categoriesFeatures = array();
		$response = simplexml_load_string($responseXml);

		if ($featureID == 'VariationsEnabled')
		{
			foreach ($response->Category as $cat)
				if ($cat->VariationsEnabled == true)
					$categoriesFeatures[(string)$cat->CategoryID] = true;
		}
		else
			return array();

		return $categoriesFeatures;
	}

	function getSuggestedCategories($query)
	{
		// Set Api Call
		$this->apiCall = 'GetSuggestedCategories';

		///Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<GetSuggestedCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <Version>'.$this->compatibilityLevel.'</Version>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <Query>'.substr(strtolower($query), 0, 350).'</Query>'."\n";
		$requestXml .= '</GetSuggestedCategoriesRequest>'."\n";

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Load xml in array
	        $response = simplexml_load_string($responseXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		if (isset($response->SuggestedCategoryArray->SuggestedCategory[0]->Category->CategoryID))
			return (int)$response->SuggestedCategoryArray->SuggestedCategory[0]->Category->CategoryID;
		return 0;
	}




	/******************************************************************/
	/** Add Product Methods *******************************************/
	/******************************************************************/


	function addFixedPriceItem($datas = array())
	{
		// Check data
		if (!$datas)
			return false;

		// Set Api Call
		$this->apiCall = 'AddFixedPriceItem';

		// Without variations
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<AddFixedPriceItem xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <Item>'."\n";
		$requestXml .= '    <SKU>prestashop-'.$datas['id_product'].'</SKU>';
		$requestXml .= '    <Title>'.substr($datas['name'], 0, 55).'</Title>'."\n";
		if (isset($datas['pictures']))
		{
			$requestXml .= '    <PictureDetails>'."\n";
			$requestXml .= '      <GalleryType>Gallery</GalleryType>'."\n";
			foreach ($datas['pictures'] as $picture)
				$requestXml .= '      <PictureURL>'.$picture.'</PictureURL>'."\n";
			$requestXml .= '    </PictureDetails>'."\n";
		}
		$requestXml .= '    <Description><![CDATA['.$datas['description'].']]></Description>'."\n";
		$requestXml .= '    <PrimaryCategory>'."\n";
		$requestXml .= '      <CategoryID>'.$datas['categoryId'].'</CategoryID>'."\n";
		$requestXml .= '    </PrimaryCategory>'."\n";
		$requestXml .= '    <ConditionID>1000</ConditionID>'."\n";
		$requestXml .= '    <StartPrice>'.$datas['price'].'</StartPrice>'."\n";
		$requestXml .= '    <CategoryMappingAllowed>true</CategoryMappingAllowed>'."\n";
		$requestXml .= '    <Country>FR</Country>'."\n";
		$requestXml .= '    <Currency>EUR</Currency>'."\n";
		$requestXml .= '    <DispatchTimeMax>3</DispatchTimeMax>'."\n";
		$requestXml .= '    <ListingDuration>GTC</ListingDuration>'."\n";
		$requestXml .= '    <ListingType>FixedPriceItem</ListingType>'."\n";
		$requestXml .= '    <PaymentMethods>PayPal</PaymentMethods>'."\n";
		$requestXml .= '    <PayPalEmailAddress>'.Configuration::get('EBAY_PAYPAL_EMAIL').'</PayPalEmailAddress>'."\n";
		$requestXml .= '    <PostalCode>'.Configuration::get('EBAY_SHOP_POSTALCODE').'</PostalCode>'."\n";
		$requestXml .= '    <Quantity>'.$datas['quantity'].'</Quantity>'."\n";
		$requestXml .= '    <ItemSpecifics>'."\n";
		$requestXml .= '      <NameValueList>'."\n";
		$requestXml .= '        <Name>Etat</Name>'."\n";
		$requestXml .= '        <Value>Neuf</Value>'."\n";
		$requestXml .= '      </NameValueList>'."\n";
		$requestXml .= '      <NameValueList>'."\n";
		$requestXml .= '        <Name>Marque</Name>'."\n";
		$requestXml .= '        <Value>'.$datas['brand'].'</Value>'."\n";
		$requestXml .= '      </NameValueList>'."\n";
		if (isset($datas['attributes']))
			foreach ($datas['attributes'] as $name => $value)
			{
				$requestXml .= '      <NameValueList>'."\n";
				$requestXml .= '        <Name>'.$name.'</Name>'."\n";
				$requestXml .= '        <Value>'.$value.'</Value>'."\n";
				$requestXml .= '      </NameValueList>'."\n";
			}
		$requestXml .= '    </ItemSpecifics>'."\n";
		$requestXml .= '    <ShippingDetails>'."\n";
		$requestXml .= '      <ShippingServiceOptions>'."\n";
		$requestXml .= '        <ShippingServicePriority>1</ShippingServicePriority>'."\n";
		$requestXml .= '        <ShippingService>'.$datas['shippingService'].'</ShippingService>'."\n";
		$requestXml .= '        <FreeShipping>false</FreeShipping>'."\n";
		$requestXml .= '        <ShippingServiceCost currencyID="EUR">'.$datas['shippingCost'].'</ShippingServiceCost>'."\n";
		$requestXml .= '      </ShippingServiceOptions>'."\n";
		$requestXml .= '    </ShippingDetails>'."\n";
		$requestXml .= '    <Site>France</Site>'."\n";
		$requestXml .= '  </Item>'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '</AddFixedPriceItem>'."\n";


		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Loading XML tree in array
		$this->response = simplexml_load_string($responseXml);


		// Checking Errors
		$this->error = '';
		$this->errorCode = '';
		if (isset($this->response->Errors) && isset($this->response->Ack) && (string)$this->response->Ack != 'Success' && (string)$this->response->Ack != 'Warning')
			foreach ($this->response->Errors as $e)
			{
				// if product no longer on eBay, we log the error code
				if ((int)$e->ErrorCode == 291)
					$this->errorCode = (int)$e->ErrorCode;

				// We log error message
				if ($e->SeverityCode == 'Error')
				{
					if ($this->error != '')
						$this->error .= '<br />';
					$this->error .= (string)$e->LongMessage;
					if (isset($e->ErrorParameters->Value))
						$this->error .= '<br />'.(string)$e->ErrorParameters->Value;
				}
			}

		// Checking Success
		$this->itemID = 0;
		if (isset($this->response->Ack) && ((string)$this->response->Ack == 'Success' || (string)$this->response->Ack == 'Warning'))
		{
			$this->fees = 0;
			$this->itemID = (string)$this->response->ItemID;
			if (isset($this->response->Fees->Fee))
				foreach ($this->response->Fees->Fee as $f)
					$this->fees += (float)$f->Fee;
		}
		else if ($this->error == '')
			$this->error = 'Sorry, technical problem, try again later.';

		if (!empty($this->error))
			return false;
		return true;
	}


	function reviseFixedPriceItem($datas = array())
	{
		// Check data
		if (!$datas)
			return false;

		// Set Api Call
		$this->apiCall = 'ReviseFixedPriceItem';


		// Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<ReviseFixedPriceItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <Item>'."\n";
		$requestXml .= '    <ItemID>'.$datas['itemID'].'</ItemID>'."\n";
		$requestXml .= '    <SKU>prestashop-'.$datas['id_product'].'</SKU>';
		$requestXml .= '    <Quantity>'.$datas['quantity'].'</Quantity>'."\n";
		$requestXml .= '    <StartPrice>'.$datas['price'].'</StartPrice>'."\n";
		if (Configuration::get('EBAY_SYNC_OPTION_RESYNC') != 1)
		{
			$requestXml .= '    <Title>'.substr($datas['name'], 0, 55).'</Title>'."\n";
			$requestXml .= '    <Description><![CDATA['.$datas['description'].']]></Description>'."\n";
			$requestXml .= '    <ShippingDetails>'."\n";
			$requestXml .= '      <ShippingServiceOptions>'."\n";
			$requestXml .= '        <ShippingServicePriority>1</ShippingServicePriority>'."\n";
			$requestXml .= '        <ShippingService>'.$datas['shippingService'].'</ShippingService>'."\n";
			$requestXml .= '        <FreeShipping>false</FreeShipping>'."\n";
			$requestXml .= '        <ShippingServiceCost currencyID="EUR">'.$datas['shippingCost'].'</ShippingServiceCost>'."\n";
			$requestXml .= '      </ShippingServiceOptions>'."\n";
			$requestXml .= '    </ShippingDetails>'."\n";
		}
		$requestXml .= '  </Item>'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '</ReviseFixedPriceItemRequest>'."\n";


		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Loading XML tree in array
		$this->response = simplexml_load_string($responseXml);

		// Checking Errors
		$this->error = '';
		$this->errorCode = '';
		if (isset($this->response->Errors) && isset($this->response->Ack) && (string)$this->response->Ack != 'Success' && (string)$this->response->Ack != 'Warning')
			foreach ($this->response->Errors as $e)
			{
				// if product no longer on eBay, we log the error code
				if ((int)$e->ErrorCode == 291)
					$this->errorCode = (int)$e->ErrorCode;

				// We log error message
				if ($e->SeverityCode == 'Error')
				{
					if ($this->error != '')
						$this->error .= '<br />';
					$this->error .= (string)$e->LongMessage;
					if (isset($e->ErrorParameters->Value))
						$this->error .= '<br />'.(string)$e->ErrorParameters->Value;
				}
			}

		// Checking Success
		$this->itemID = 0;
		if (isset($this->response->Ack) && ((string)$this->response->Ack == 'Success' || (string)$this->response->Ack == 'Warning'))
		{
			$this->fees = 0;
			$this->itemID = (string)$this->response->ItemID;
			if (isset($this->response->Fees->Fee))
				foreach ($this->response->Fees->Fee as $f)
					$this->fees += (float)$f->Fee;
		}
		else if ($this->error == '')
			$this->error = 'Sorry, technical problem, try again later.';

		if (!empty($this->error))
			return false;
		return true;
	}










	function addFixedPriceItemMultiSku($datas = array())
	{
		// Check data
		if (!$datas)
			return false;

		// Set Api Call
		$this->apiCall = 'AddFixedPriceItem';

		// Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<AddFixedPriceItem xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <Item>'."\n";
		$requestXml .= '    <Country>FR</Country>'."\n";
		$requestXml .= '    <Currency>EUR</Currency>'."\n";
		$requestXml .= '    <Description>'."\n";
		$requestXml .= '      <![CDATA['.$datas['description'].']]>'."\n";
		$requestXml .= '    </Description>'."\n";
		$requestXml .= '    <ConditionID>1000</ConditionID>'."\n";
		$requestXml .= '    <DispatchTimeMax>3</DispatchTimeMax>'."\n";
		$requestXml .= '    <ListingDuration>GTC</ListingDuration>'."\n";
		$requestXml .= '    <ListingType>FixedPriceItem</ListingType>'."\n";
		$requestXml .= '    <PaymentMethods>PayPal</PaymentMethods>'."\n";
		$requestXml .= '    <PayPalEmailAddress>'.Configuration::get('EBAY_PAYPAL_EMAIL').'</PayPalEmailAddress>'."\n";
		$requestXml .= '    <PostalCode>'.Configuration::get('EBAY_SHOP_POSTALCODE').'</PostalCode>'."\n";
		$requestXml .= '    <PrimaryCategory>'."\n";
		$requestXml .= '      <CategoryID>'.$datas['categoryId'].'</CategoryID>'."\n";
		$requestXml .= '    </PrimaryCategory>'."\n";
		$requestXml .= '    <Title>'.substr($datas['name'], 0, 55).'</Title>'."\n";
		if (isset($datas['pictures']))
		{
			$requestXml .= '<PictureDetails>';
			foreach ($datas['pictures'] as $picture)
				$requestXml .= '<PictureURL>'.$picture.'</PictureURL>'."\n";
			$requestXml .= '</PictureDetails>';
		}
		$requestXml .= '    <ItemSpecifics>'."\n";
		$requestXml .= '      <NameValueList>'."\n";
		$requestXml .= '        <Name>Etat</Name>'."\n";
		$requestXml .= '        <Value>Neuf</Value>'."\n";
		$requestXml .= '      </NameValueList>'."\n";
		$requestXml .= '      <NameValueList>'."\n";
		$requestXml .= '        <Name>Marque</Name>'."\n";
		$requestXml .= '        <Value>'.$datas['brand'].'</Value>'."\n";
		$requestXml .= '      </NameValueList>'."\n";
		$requestXml .= '    </ItemSpecifics>'."\n";
		$requestXml .= '    <Variations>'."\n";
		if (isset($datas['variations']))
		{
			// Generate Variations Set
			$requestXml .= '      <VariationSpecificsSet>'."\n";
			foreach ($datas['variationsList'] as $group => $v)
			{
				$requestXml .= '        <NameValueList>'."\n";
				$requestXml .= '          <Name>'.$group.'</Name>'."\n";
				foreach ($v as $attr => $val)
					$requestXml .= '          <Value>'.$attr.'</Value>'."\n";
				$requestXml .= '        </NameValueList>'."\n";
			}
			$requestXml .= '        </VariationSpecificsSet>'."\n";

			// Generate Variations
			foreach ($datas['variations'] as $key => $variation)
			{
				$requestXml .= '      <Variation>'."\n";
				$requestXml .= '        <SKU>prestashop-'.$key.'</SKU>'."\n";
				$requestXml .= '        <StartPrice>'.$variation['price'].'</StartPrice>'."\n";
				$requestXml .= '        <Quantity>'.$variation['quantity'].'</Quantity>'."\n";
				$requestXml .= '        <VariationSpecifics>'."\n";
				foreach ($variation['variations'] as $v)
				{
					$requestXml .= '          <NameValueList>'."\n";
					$requestXml .= '            <Name>'.$v['name'].'</Name>'."\n";
					$requestXml .= '            <Value>'.$v['value'].'</Value>'."\n";
					$requestXml .= '          </NameValueList>'."\n";
				}
				$requestXml .= '        </VariationSpecifics>'."\n";
				$requestXml .= '      </Variation>'."\n";
			}

			// Generate Pictures Variations
			$lastSpecificName = '';
			$attributeUsed = array();
			$requestXml .= '      <Pictures>'."\n";
			foreach ($datas['variations'] as $key => $variation)
				foreach ($variation['variations'] as $kv => $v)
					if (!isset($attributeUsed[md5($v['name'].$v['value'])]) && isset($variation['pictures'][$kv]))
					{
						if ($lastSpecificName != $v['name'])
							$requestXml .= '        <VariationSpecificName>'.$v['name'].'</VariationSpecificName>'."\n";
						$requestXml .= '        <VariationSpecificPictureSet>'."\n";
						$requestXml .= '          <VariationSpecificValue>'.$v['value'].'</VariationSpecificValue>'."\n";
						$requestXml .= '          <PictureURL>'.$variation['pictures'][$kv].'</PictureURL>'."\n";
						$requestXml .= '        </VariationSpecificPictureSet>'."\n";
						$attributeUsed[md5($v['name'].$v['value'])] = true;
						$lastSpecificName = $v['name'];
					}
			$requestXml .= '      </Pictures>'."\n";
		}
		$requestXml .= '    </Variations>'."\n";
		$requestXml .= '    <ShippingDetails>'."\n";
		$requestXml .= '      <ShippingServiceOptions>'."\n";
		$requestXml .= '        <ShippingServicePriority>1</ShippingServicePriority>'."\n";
		$requestXml .= '        <ShippingService>'.$datas['shippingService'].'</ShippingService>'."\n";
		$requestXml .= '        <FreeShipping>false</FreeShipping>'."\n";
		$requestXml .= '        <ShippingServiceCost currencyID="EUR">'.$datas['shippingCost'].'</ShippingServiceCost>'."\n";
		$requestXml .= '      </ShippingServiceOptions>'."\n";
		$requestXml .= '    </ShippingDetails>'."\n";
		$requestXml .= '    <Site>France</Site>'."\n";
		$requestXml .= '  </Item>'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '</AddFixedPriceItem>'."\n";

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Loading XML tree in array
		$this->response = simplexml_load_string($responseXml);

		// Checking Errors
		$this->error = '';
		$this->errorCode = '';
		if (isset($this->response->Errors) && isset($this->response->Ack) && (string)$this->response->Ack != 'Success' && (string)$this->response->Ack != 'Warning')
			foreach ($this->response->Errors as $e)
			{
				// if product no longer on eBay, we log the error code
				if ((int)$e->ErrorCode == 291)
					$this->errorCode = (int)$e->ErrorCode;

				// We log error message
				if ($e->SeverityCode == 'Error')
				{
					if ($this->error != '')
						$this->error .= '<br />';
					$this->error .= (string)$e->LongMessage;
					if (isset($e->ErrorParameters->Value))
						$this->error .= '<br />'.(string)$e->ErrorParameters->Value;
				}
			}

		// Checking Success
		$this->itemID = 0;
		if (isset($this->response->Ack) && ((string)$this->response->Ack == 'Success' || (string)$this->response->Ack == 'Warning'))
		{
			$this->fees = 0;
			$this->itemID = (string)$this->response->ItemID;
			if (isset($this->response->Fees->Fee))
				foreach ($this->response->Fees->Fee as $f)
					$this->fees += (float)$f->Fee;
		}
		else if ($this->error == '')
			$this->error = 'Sorry, technical problem, try again later.';

		if (!empty($this->error))
			return false;
		return true;
	}


	function reviseFixedPriceItemMultiSku($datas = array())
	{
		// Check data
		if (!$datas)
			return false;

		// Set Api Call
		$this->apiCall = 'ReviseFixedPriceItem';

		// Build the request Xml string
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<ReviseFixedPriceItem xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <Item>'."\n";
		$requestXml .= '    <ItemID>'.$datas['itemID'].'</ItemID>'."\n";
		$requestXml .= '    <Country>FR</Country>'."\n";
		$requestXml .= '    <Currency>EUR</Currency>'."\n";
		$requestXml .= '    <ConditionID>1000</ConditionID>'."\n";
		$requestXml .= '    <DispatchTimeMax>3</DispatchTimeMax>'."\n";
		$requestXml .= '    <ListingDuration>GTC</ListingDuration>'."\n";
		$requestXml .= '    <ListingType>FixedPriceItem</ListingType>'."\n";
		$requestXml .= '    <PaymentMethods>PayPal</PaymentMethods>'."\n";
		$requestXml .= '    <PayPalEmailAddress>'.Configuration::get('EBAY_PAYPAL_EMAIL').'</PayPalEmailAddress>'."\n";
		$requestXml .= '    <PostalCode>'.Configuration::get('EBAY_SHOP_POSTALCODE').'</PostalCode>'."\n";
		$requestXml .= '    <PrimaryCategory>'."\n";
		$requestXml .= '      <CategoryID>'.$datas['categoryId'].'</CategoryID>'."\n";
		$requestXml .= '    </PrimaryCategory>'."\n";
		if (isset($datas['pictures']))
		{
			$requestXml .= '<PictureDetails>';
			foreach ($datas['pictures'] as $picture)
				$requestXml .= '<PictureURL>'.$picture.'</PictureURL>'."\n";
			$requestXml .= '</PictureDetails>';
		}
		$requestXml .= '    <ItemSpecifics>'."\n";
		$requestXml .= '      <NameValueList>'."\n";
		$requestXml .= '        <Name>Etat</Name>'."\n";
		$requestXml .= '        <Value>Neuf</Value>'."\n";
		$requestXml .= '      </NameValueList>'."\n";
		$requestXml .= '      <NameValueList>'."\n";
		$requestXml .= '        <Name>Marque</Name>'."\n";
		$requestXml .= '        <Value>'.$datas['brand'].'</Value>'."\n";
		$requestXml .= '      </NameValueList>'."\n";
		$requestXml .= '    </ItemSpecifics>'."\n";
		$requestXml .= '    <Variations>'."\n";
		if (isset($datas['variations']))
		{
			// Generate Variations Set
			$requestXml .= '      <VariationSpecificsSet>'."\n";
			foreach ($datas['variationsList'] as $group => $v)
			{
				$requestXml .= '        <NameValueList>'."\n";
				$requestXml .= '          <Name>'.$group.'</Name>'."\n";
				foreach ($v as $attr => $val)
					$requestXml .= '          <Value>'.$attr.'</Value>'."\n";
				$requestXml .= '        </NameValueList>'."\n";
			}
			$requestXml .= '        </VariationSpecificsSet>'."\n";

			// Generate Variations
			foreach ($datas['variations'] as $key => $variation)
			{
				$requestXml .= '      <Variation>'."\n";
				$requestXml .= '        <SKU>prestashop-'.$key.'</SKU>'."\n";
				$requestXml .= '        <StartPrice>'.$variation['price'].'</StartPrice>'."\n";
				$requestXml .= '        <Quantity>'.$variation['quantity'].'</Quantity>'."\n";
				$requestXml .= '        <VariationSpecifics>'."\n";
				foreach ($variation['variations'] as $v)
				{
					$requestXml .= '          <NameValueList>'."\n";
					$requestXml .= '            <Name>'.$v['name'].'</Name>'."\n";
					$requestXml .= '            <Value>'.$v['value'].'</Value>'."\n";
					$requestXml .= '          </NameValueList>'."\n";
				}
				$requestXml .= '        </VariationSpecifics>'."\n";
				$requestXml .= '      </Variation>'."\n";
			}

			// Generate Pictures Variations
			$lastSpecificName = '';
			$attributeUsed = array();
			$requestXml .= '      <Pictures>'."\n";
			foreach ($datas['variations'] as $key => $variation)
				foreach ($variation['variations'] as $kv => $v)
					if (!isset($attributeUsed[md5($v['name'].$v['value'])]) && isset($variation['pictures'][$kv]))
					{
						if ($lastSpecificName != $v['name'])
							$requestXml .= '        <VariationSpecificName>'.$v['name'].'</VariationSpecificName>'."\n";
						$requestXml .= '        <VariationSpecificPictureSet>'."\n";
						$requestXml .= '          <VariationSpecificValue>'.$v['value'].'</VariationSpecificValue>'."\n";
						$requestXml .= '          <PictureURL>'.$variation['pictures'][$kv].'</PictureURL>'."\n";
						$requestXml .= '        </VariationSpecificPictureSet>'."\n";
						$attributeUsed[md5($v['name'].$v['value'])] = true;
						$lastSpecificName = $v['name'];
					}
			$requestXml .= '      </Pictures>'."\n";
		}

		$requestXml .= '    </Variations>'."\n";
		if (Configuration::get('EBAY_SYNC_OPTION_RESYNC') != 1)
		{
			$requestXml .= '    <Title>'.substr($datas['name'], 0, 55).'</Title>'."\n";
			$requestXml .= '    <Description>'."\n";
			$requestXml .= '      <![CDATA['.$datas['description'].']]>'."\n";
			$requestXml .= '    </Description>'."\n";
			$requestXml .= '    <ShippingDetails>'."\n";
			$requestXml .= '      <ShippingServiceOptions>'."\n";
			$requestXml .= '        <ShippingServicePriority>1</ShippingServicePriority>'."\n";
			$requestXml .= '        <ShippingService>'.$datas['shippingService'].'</ShippingService>'."\n";
			$requestXml .= '        <FreeShipping>false</FreeShipping>'."\n";
			$requestXml .= '        <ShippingServiceCost currencyID="EUR">'.$datas['shippingCost'].'</ShippingServiceCost>'."\n";
			$requestXml .= '      </ShippingServiceOptions>'."\n";
			$requestXml .= '    </ShippingDetails>'."\n";
		}
		$requestXml .= '    <Site>France</Site>'."\n";
		$requestXml .= '  </Item>'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '</ReviseFixedPriceItem>'."\n";

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Loading XML tree in array
		$this->response = simplexml_load_string($responseXml);

		// Checking Errors
		$this->error = '';
		$this->errorCode = '';
		if (isset($this->response->Errors) && isset($this->response->Ack) && (string)$this->response->Ack != 'Success' && (string)$this->response->Ack != 'Warning')
			foreach ($this->response->Errors as $e)
			{
				// if product no longer on eBay, we log the error code
				if ((int)$e->ErrorCode == 291)
					$this->errorCode = (int)$e->ErrorCode;

				// We log error message
				if ($e->SeverityCode == 'Error')
				{
					if ($this->error != '')
						$this->error .= '<br />';
					$this->error .= (string)$e->LongMessage;
					if (isset($e->ErrorParameters->Value))
						$this->error .= '<br />'.(string)$e->ErrorParameters->Value;
				}
			}

		// Checking Success
		$this->itemID = 0;
		if (isset($this->response->Ack) && ((string)$this->response->Ack == 'Success' || (string)$this->response->Ack == 'Warning'))
		{
			$this->fees = 0;
			$this->itemID = (string)$this->response->ItemID;
			if (isset($this->response->Fees->Fee))
				foreach ($this->response->Fees->Fee as $f)
					$this->fees += (float)$f->Fee;
		}
		else if ($this->error == '')
			$this->error = 'Sorry, technical problem, try again later.';

		if (!empty($this->error))
			return false;
		return true;
	}


	/******************************************************************/
	/** Order Methods *************************************************/
	/******************************************************************/



	function getOrders($CreateTimeFrom, $CreateTimeTo)
	{
		// Check data
		if (!$CreateTimeFrom || !$CreateTimeTo)
			return false;

		// Set Api Call
		$this->apiCall = 'GetOrders';

		// Without variations
		$requestXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$requestXml .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">'."\n";
		$requestXml .= '  <DetailLevel>ReturnAll</DetailLevel>'."\n";
		$requestXml .= '  <ErrorLanguage>fr_FR</ErrorLanguage>'."\n";
		$requestXml .= '  <WarningLevel>High</WarningLevel>'."\n";
		$requestXml .= '  <CreateTimeFrom>'.$CreateTimeFrom.'</CreateTimeFrom>'."\n";
		$requestXml .= '  <CreateTimeTo>'.$CreateTimeTo.'</CreateTimeTo>'."\n";
		$requestXml .= '  <OrderRole>Seller</OrderRole>'."\n";
		$requestXml .= '  <RequesterCredentials>'."\n";
		$requestXml .= '    <eBayAuthToken>'.Configuration::get('EBAY_API_TOKEN').'</eBayAuthToken>'."\n";
		$requestXml .= '  </RequesterCredentials>'."\n";
		$requestXml .= '</GetOrdersRequest>'."\n";

		// Send the request and get response
		$responseXml = $this->makeRequest($requestXml);
		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')
		{
			$this->error = 'Error sending '.$this->apiCall.' request';
			return false;
		}

		// Loading XML tree in array
		$this->response = simplexml_load_string($responseXml);


		// Checking Errors
		$this->error = '';
		if (isset($this->response->Errors) && isset($this->response->Ack) && (string)$this->response->Ack != 'Success' && (string)$this->response->Ack != 'Warning')
			foreach ($this->response->Errors as $e)
			{
				if ($this->error != '')
					$this->error .= '<br />';
				$this->error .= (string)$e->LongMessage;
			}

		// Checking Success
		$orderList = array();
		if (isset($this->response->OrderArray))
			foreach ($this->response->OrderArray->Order as $order)
			{
				$name = explode(' ', (string)$order->ShippingAddress->Name);
				$itemList = array();
				foreach ($order->TransactionArray->Transaction as $transaction)
				{
					$id_product = 0;
					$id_attribute = 0;
					$quantity = (string)$transaction->QuantityPurchased;
					if (isset($transaction->item->SKU))
					{
						$tmp = explode('-', (string)$transaction->item->SKU);
						$id_product = $tmp[1];
					}
					if (isset($transaction->Variation->SKU))
					{
						$tmp = explode('-', (string)$transaction->Variation->SKU);
						$id_product = $tmp[1];
						$id_product_attribute = $tmp[2];
					}
					if ($id_product > 0)
						$itemList[] = array('id_product' => $id_product, 'id_product_attribute' => $id_product_attribute, 'quantity' => $quantity, 'price' => (string)$transaction->TransactionPrice);
				}

				$orderList[] = array(
					'id_order_ref' => (string)$order->OrderID,
					'amount' => (string)$order->AmountPaid,
					'status' => (string)$order->CheckoutStatus->Status,
					'date' => substr((string)$order->CreatedTime, 0, 10).' '.substr((string)$order->CreatedTime, 11, 8),
					'name' => (string)$order->ShippingAddress->Name,
					'firstname' => $name[0],
					'familyname' => $name[1],
					'address1' => (string)$order->ShippingAddress->Street1,
					'address2' => (string)$order->ShippingAddress->Street2,
					'city' => (string)$order->ShippingAddress->CityName,
					'state' => (string)$order->ShippingAddress->StateOrProvince,
					'country_iso_code' => (string)$order->ShippingAddress->Country,
					'country_name' => (string)$order->ShippingAddress->CountryName,
					'phone' => (string)$order->ShippingAddress->Phone,
					'postalcode' => (string)$order->ShippingAddress->PostalCode,
					'shippingService' => (string)$order->ShippingServiceSelected->ShippingService,
					'shippingServiceCost' => (string)$order->ShippingServiceSelected->ShippingServiceCost,
					'email' => (string)$order->TransactionArray->Transaction[0]->Buyer->Email,
					'product_list' => $itemList,
					'object' => $order
				);
			}

		return $orderList;
	}


}



class eBayPayment extends PaymentModule
{

}


