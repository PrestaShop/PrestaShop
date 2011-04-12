<?php

/**
	* Utilitary functions to interract with dejala.fr
 **/
class DejalaUtils
{
	/**
	 * Order the delivery to dejala.fr
	 * @returns the HTTP status code of the request
	 **/
	public function orderDelivery($dejalaConfig, &$delivery, $mode)
	{
		if ($mode !== 'PROD')
			$serviceURL = $dejalaConfig->getRootServiceURI('TEST') . '/mystore/delivery';
		else
			$serviceURL = $dejalaConfig->getRootServiceURI('PROD') . '/mystore/delivery';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $delivery, 'POST', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$nodeList = $doc->getElementsByTagName('delivery');
			if ($nodeList->length > 0)
				$this->getNodeValue($nodeList->item(0), $delivery);
		}
		return ($responseArray);
	}

	/**
	 * Quick creates a store account at dejala.fr
	 * only works on Dejala test platform
	 * @returns the HTTP status code of the request
	 **/
	public function createInstantStore($dejalaConfig, $storeName)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI('TEST') . '/instantstore';
		$postargs['login']=$dejalaConfig->login;
		$postargs['password']=$dejalaConfig->password;
		$postargs['store_url']=$dejalaConfig->storeUrl;
		$postargs['store_name'] = $storeName;
		$postargs['platform'] = 'prestashop';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'POST', FALSE);
		return ($responseArray);
	}
	static public function wtf($var, $arrayOfObjectsToHide=null, $fontSize=11)
	{
		$text = print_r($var, true);

		if (is_array($arrayOfObjectsToHide)) {
			 
			foreach ($arrayOfObjectsToHide as $objectName) {
				 
				$searchPattern = '#('.$objectName.' Object\n(\s+)\().*?\n\2\)\n#s';
				$replace = "$1<span style=\"color: #FF9900;\">--&gt; HIDDEN - courtesy of wtf() &lt;--</span>)";
				$text = preg_replace($searchPattern, $replace, $text);
			}
		}

		// color code objects
		$text = preg_replace('#(\w+)(\s+Object\s+\()#s', '<span style="color: #079700;">$1</span>$2', $text);
		// color code object properties
		$text = preg_replace('#\[(\w+)\:(public|private|protected)\]#', '[<span style="color: #000099;">$1</span>:<span style="color: #009999;">$2</span>]', $text);
		 
		echo '<pre style="font-size: '.$fontSize.'px; line-height: '.$fontSize.'px;text-align:left;">'.$text.'</pre>';
	}
	
	public function getStoreLocation($dejalaConfig, &$location)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore';
		$postargs = array();
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$locationNodes=$doc->getElementsByTagName('location');
			if ($locationNodes->length > 0)
			{
				$locationNode = $locationNodes->item(0);
				$nodeList = $locationNode->childNodes;
				foreach ($nodeList as $element){
   				$location[$element->nodeName] = $element->textContent;
				}
			}
		}
		return ($responseArray);
	}

	public function getStoreCalendar($dejalaConfig, &$calendar)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/calendar/';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, array(), 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$nodeList = $doc->getElementsByTagName('entry');
			if ($nodeList->length > 0) {
				foreach ($nodeList as $element) {
					$calendarNode = $this->getNodeValue($element);
					$calendar['entries'][(int)($calendarNode['weekday'])] = $calendarNode;
				}
			}
			$nodeList = $doc->getElementsByTagName('exception');
			$calendar['exceptions'] = array();
			if ($nodeList->length > 0) {
				foreach ($nodeList as $element) {
					$calendar['exceptions'][] = $this->getNodeValue($element);
				}
			}
		}
		return ($responseArray);
	}

	public function getStoreContacts($dejalaConfig, &$contacts)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore';
		$postargs = array();
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$contactsNodeList = $doc->getElementsByTagName('contacts');
			if ($contactsNodeList->length > 0)
			{
				$contactNodes = $contactsNodeList->item(0)->childNodes;
				if ($contactNodes)
				foreach ($contactNodes as $contactNode)
				{
					$name = $contactNode->nodeName;
					$nodeList = $contactNode->childNodes;
					$currentContactNode = array();
					if ($nodeList)
						foreach ($nodeList as $element) {
   							$currentContactNode[$element->nodeName] = $element->textContent;
						}
					$contacts[$name] = $currentContactNode;
				}
			}
		}
		return ($responseArray);
	}

	public function getStoreProducts($dejalaConfig, &$products)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/products';
		$postargs = array();
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$productsNodeList = $doc->getElementsByTagName('product');
			foreach ($productsNodeList as $productNode)
			{
				$currentProduct = array();
				$nodeList = $productNode->childNodes;
				foreach ($nodeList as $element) {
   				$currentProduct[$element->nodeName] = $element->textContent;
				}
				if (count($currentProduct))
				$products[] = $currentProduct;
			}
		}

		usort($products, array("DejalaUtils", "cmpProducts"));
		return ($responseArray);
	}

	private static function cmpProducts($a, $b)
	{
	    if ($a['priority'] == $b['priority']) {
	    	if (($a['price'] == $b['price'])) {
	    		return ($a['id'] < $b['id']) ? -1 : 1 ;
	    	}
	        return ($a['price'] < $b['price']) ? -1 : 1 ;
	    }
	    return ($a['priority'] < $b['priority']) ? -1 : 1;
	}
	
	public function getStoreQuotation($dejalaConfig, $quotationElements, &$products)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/quotation';
		$postargs = $quotationElements;
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$productsNodeList = $doc->getElementsByTagName('product');
			foreach ($productsNodeList as $productNode)
			{
				$currentProduct = $this->getNodeValue($productNode);
				unset($currentProduct['calendar']['entries']['entry']);
				$calendarNodeList = $doc->getElementsByTagName('entry');
				foreach ($calendarNodeList as $calendarNode) {
					$calendarEntry = $this->getNodeValue($calendarNode);
					$currentProduct['calendar']['entries'][$calendarEntry['weekday']] = $calendarEntry;
				}
				$exceptionNodeList = $doc->getElementsByTagName('exception');
				$currentProduct['calendar']['exceptions'] = array();
				foreach ($exceptionNodeList as $exceptionNode) {
					$currentProduct['calendar']['exceptions'][] = $this->getNodeValue($exceptionNode);
				}
				/*$currentProduct = array();
				$nodeList = $productNode->childNodes;
				foreach ($nodeList as $element) {
   				$currentProduct[$element->nodeName] = $element->textContent;
				}
				*/
				if (count($currentProduct))
					$products[] = $currentProduct;
			}
		}
		usort($products, array("DejalaUtils", "cmpProducts"));
		return ($responseArray);
	}

	public function getStoreAttributes($dejalaConfig, &$store)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore';
		$postargs = array();
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$fatherNodeList = $doc->getElementsByTagName('store');
			if ($fatherNodeList->length > 0)
			{
				$childNodes = $fatherNodeList->item(0)->childNodes;
				foreach ($childNodes as $childNode)
				{
					$store[$childNode->nodeName] = $childNode->textContent;
				}
			}
			$nodeList = $doc->getElementsByTagName('attributes');
			$store['attributes'] = array();
			if ($nodeList->length > 0) {
				$store['attributes'] = $this->getNodeValue($nodeList->item(0));
			}
		}
		return ($responseArray);
	}

	public function getStoreProcesses($dejalaConfig, &$processes)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore';
		$postargs = array();
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$fatherNodeList = $doc->getElementsByTagName('processes');
			if ($fatherNodeList->length > 0)
			{
				$childNodes = $fatherNodeList->item(0)->childNodes;
				foreach ($childNodes as $childNode)
				{
					$processes[$childNode->nodeName] = $childNode->textContent;
				}
			}
		}
		return ($responseArray);
	}

	public function getStoreProductByID($dejalaConfig, $productID, &$product)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/products/' . $productID;
		$postargs = array();
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $postargs, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$productsNodeList = $doc->getElementsByTagName('product');
			if ($productsNodeList->length > 0)
			{
				$nodeList = $productsNodeList->item(0)->childNodes;
				foreach ($nodeList as $element) {
   				$product[$element->nodeName] = $element->textContent;
				}
			}
		}
		return ($responseArray);
	}

	public function getDelivery($dejalaConfig, &$delivery, $mode)
	{
		if ($mode !== 'PROD')
			$serviceURL = $dejalaConfig->getRootServiceURI('TEST') . '/mystore/delivery/' .  $delivery['id'];
		else
			$serviceURL = $dejalaConfig->getRootServiceURI('PROD') . '/mystore/delivery/' .  $delivery['id'];
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, array(), 'GET', TRUE);

		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			//echo 'xml='.$xml.'<br/>';
			$nodeList = $doc->getElementsByTagName('delivery');
			if ($nodeList->length > 0)
				$this->getNodeValue($nodeList->item(0), $delivery);
		}
		return ($responseArray);
	}

	/**
	 * Transforms and returns node into tree structure, use $value array if not null
	 **/
	function getNodeValue($node, &$value=NULL)
	{
		if ($node instanceof DOMElement)
		{
			$childNodes = $node->childNodes;
			$onlyText = TRUE;
			$text = '';
			foreach ($childNodes as $childNode)
			{
				if ($childNode instanceof DOMElement)
				{
					$onlyText = FALSE;
					$value[$childNode->nodeName] = $this->getNodeValue($childNode);
				}
			}
			if ($onlyText) {
				return ($value=$node->textContent);
			}
		}
		return ($value);
	}

	public function getStoreDeliveries($dejalaConfig, &$deliveries, $args = array())
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/deliveries';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $args, 'GET', TRUE);
		if (!($xml = strstr($responseArray['response'], '<?xml'))) {
			$xml = null;
		}
		else
		{
			$doc = new DOMDocument();
			$doc->loadXML($xml);
			$childNodes = $doc->getElementsByTagName('delivery');
			foreach ($childNodes as $childNode) {
				$deliveries[] = $this->getNodeValue($childNode);
			}
		}
		return ($responseArray);
	}

	public function setStoreContacts($dejalaConfig, &$contacts)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/contacts';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $contacts, 'PUT', TRUE);
		return ($responseArray);
	}
	public function setStoreLocation($dejalaConfig, &$location)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI('TEST') . '/mystore/location';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $location, 'PUT', TRUE);
		return ($responseArray);
	}
	public function setStoreProcesses($dejalaConfig, &$processes)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/processes';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $processes, 'PUT', TRUE);
		return ($responseArray);
	}
	public function setStoreProducts($dejalaConfig, &$products)
	{
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/products';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $products, 'PUT', TRUE);
		return ($responseArray);
	}
	public function setStoreCalendar($dejalaConfig, &$calendar) {
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/calendar';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $calendar, 'POST', TRUE);
		return ($responseArray);
	}

	public function setStoreAttributes($dejalaConfig, &$attributes) {
		$serviceURL = $dejalaConfig->getRootServiceURI() . '/mystore/attributes';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, $attributes, 'PUT', TRUE);
		return ($responseArray);
	}

	/**
	* Ask Dejala.fr to create an account for the store on the production platform
	**/
	public function goLive($dejalaConfig) {
		$serviceURL = $dejalaConfig->getRootServiceURI('TEST') . '/mystore/golive';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, array(), 'PUT', TRUE);
		return ($responseArray);
	}
	/**
	* teste la connexion sur le service Dejala dans le mode (TEST/PROD)
	* */
	public function ping($dejalaConfig, $mode) {
		if ($mode !== 'PROD')
			$serviceURL = $dejalaConfig->getRootServiceURI('TEST') . '/ping';
		else
			$serviceURL = $dejalaConfig->getRootServiceURI('PROD') . '/ping';
		$responseArray = $this->makeRequest($dejalaConfig, $serviceURL, array(), 'GET', TRUE);
		return ($responseArray);
	}

	public function makeRequest($dejalaConfig, $serviceURL, $args, $method='POST', $needAuth=TRUE) {
		$session = curl_init($serviceURL);
		$requestArgs = "";
		foreach ($args as $key => $value) {
			$requestArgs = $requestArgs . '&' . $key . '=' . urlencode($value);
		}
		if ($method == 'GET')
		{
			if (strlen($requestArgs) > 0)
				$requestArgs = '?' . $requestArgs;
			$session = curl_init($serviceURL . $requestArgs);
		}
		else if ($method == 'POST')
		{
			$session = curl_init($serviceURL);
			curl_setopt($session, CURLOPT_POST, true);
			curl_setopt($session, CURLOPT_POSTFIELDS, $requestArgs);
		}
		else if ($method == 'PUT')
		{
			curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($session, CURLOPT_POSTFIELDS, $requestArgs);
		}
		// 	manage authenth
		if ($needAuth)
		{
			curl_setopt($session, CURLOPT_USERPWD, $dejalaConfig->login.':'.$dejalaConfig->password);
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		}
		// SSL option
		if ($dejalaConfig->useSSL === 1) {
			curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($session, CURLOPT_PORT, 443);
		}
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Do the POST and then close the session
		$response = curl_exec($session);
		curl_close($session);
		// Get HTTP Status code from the response
		$status_code = array();
		preg_match('/\d\d\d/', $response, $status_code);
		$responseArray['status']=$status_code[0];
		$responseArray['response']=$response;
		return ($responseArray);
	}


	public function mylog($msg) {

			require_once(dirname(__FILE__) . "/MyLogUtils.php");
			$myFile = dirname(__FILE__) . "/logFile.txt";
			MyLogUtils::myLog($myFile, $msg);

	}

	// get a string of a value for Log purposes
	public function logValue($mvalue, $lvl=0) {
		require_once(dirname(__FILE__) . "/MyLogUtils.php");
		return (MyLogUtils::logValue($mvalue, $lvl));
	}


}

