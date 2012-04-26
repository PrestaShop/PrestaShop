<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Load the XML-RPC abstraction
require(dirname(__FILE__).'/xmlrpc.inc.php');

// Defines for the report.gettotaltransfert Method rangeType values
if (!defined('CLOUDCACHE_RANGE_TYPE_TODAY'))
{
	define('CLOUDCACHE_RANGE_TYPE_TODAY', '1');
	define('CLOUDCACHE_RANGE_TYPE_HOUR', '2');
	define('CLOUDCACHE_RANGE_TYPE_DATE', '3');

	define('CLOUDCACHE_FILE_TYPE_UNASSOCIATED', 'none');
	define('CLOUDCACHE_FILE_TYPE_ALL', 'all');
	define('CLOUDCACHE_FILE_TYPE_IMG', 'img');
	define('CLOUDCACHE_FILE_TYPE_JS', 'js');
	define('CLOUDCACHE_FILE_TYPE_CSS', 'css');
	define('CLOUDCACHE_FILE_TYPE_OTHER', 'other');
}

class CloudcacheApi
{
	private $apiKey;
	private $apiUserId;
	private $companyId;
	private $curDate;
	private $hashType;
	private $pullzoneType;
	private $availableNamespaces = array('pullzone' => 'Pull Zone');

	private $lastRpcRequest;
	private $lastRpcResponse;

	/** @var port Port of the cloudcache XML-RPC Api server */
	private $port;

	/** @var httpMethod Http method to be used when using the XML-RPC API */
	private $httpMethod;

	public function __construct()
	{
		// Declare which method is avaible for which namespace
		$tab = array(
			'user' => array('listUsers', 'update'),
			'account' => array('getBandwidth'),
			'report' => array(
				'getTotalTransfer',
				'getTotalHits',
				'getTotalTransferStats',
				'getCacheHitStats',
				'getPopularFiles',
				'getUsagePerDay',
				'getNodeHits',
				'getConnectionStats',
				'getHourlyConnectionStats',
			),
			'cache' => array('purge', 'purgeAllCache'),
			'pullzone' => array('listZones', 'create', 'update'),
			'pushzone' => array('listZones', 'create', 'update'),
			'vodzone' => array('listZones',	'create',	'update'),
//			'livezone' => array('listZones', 'create', 'update', 'delete'), // Not yet implemented
		);

		// Connection settings
		$this->port = CLOUDCACHE_API_PORT;
		$this->httpMethod = CLOUDCACHE_API_HTTP_METHOD;
		$this->apiURI = CLOUDCACHE_API_URI;
		$this->apiURL = CLOUDCACHE_API_URL;

		// Api credentials
		if (Configuration::get('PS_CIPHER_ALGORITHM'))
			$this->_cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		else
			$this->_cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);

		$this->apiKey = $this->_cipherTool->decrypt(Configuration::get('CLOUDCACHE_API_KEY'));
		$this->apiUserId = Configuration::get('CLOUDCACHE_API_USER');
		$this->companyId = Configuration::get('CLOUDCACHE_API_COMPANY_ID');

		// Random stuff needed by the API
		// Save the current timezone
		$currentTimezone = date_default_timezone_get();
		// Set the timezone to the Cloudcache one
		date_default_timezone_set('America/Los_Angeles');
		// Retreive the RFC 8601 With Cloudcache timezone
		$this->curDate = date('c');
		// Put back user timezone
		date_default_timezone_set($currentTimezone);

		$this->hashType = CLOUDCACHE_API_HASH_TYPE;
		$this->pullzoneType = CLOUDCACHE_API_PULL_ZONE_TYPE;
	}


	/**
	 * @brief Create a new empty XML-RPC message ready for cloudcache API.
	 *
	 * @param namespace Namespace of the request
	 * @param method		Method used
	 *
	 * @return The new xmlrpcmsg Object instance.
	 */
	private function _getEmptyRpcMessage($namespace, $method)
	{
		// reset last command
		$this->lastRpcRequest = $this->lastRpcResponse = null;

		return new xmlrpcmsg($namespace.'.'.$method, array(
				php_xmlrpc_encode($this->apiUserId),
				php_xmlrpc_encode(hash($this->hashType,
						$this->curDate.':'.$this->apiKey.':'.$method)),
				php_xmlrpc_encode($this->curDate),
			));
	}

	/**
	 * @brief List zones.
	 *
	 * @param namespace Namespace wanted (pullzone, pushzone, vodzone)
	 *
	 * @return List of the zones (replied by the cloudcache server)
	 */
	public function listZones($namespace)
	{
		$method = 'listZones';

		// Initialize the XML-RPC message
		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);

		switch ($namespace)
		{
			case 'pullzone':
				// For the pullzone, we add a parameter type
				$rpcMsg->addParam(php_xmlrpc_encode($this->pullzoneType));
				break;
			case 'pushzone':
			case 'vodzone':
			case 'livezone':
			default:
				break;
		}

		return $this->_sendRequest($namespace, $rpcMsg);
	}

  /**
	 * @brief Create a zone.
	 *
	 * @param namespace Namespace wanted (pullzone, pushzone, vodzone)
	 * @param values		Array describing the zone to create.
	 *
	 * @return The anwser from the server.
	 */
	public function createZone($namespace, $values)
	{
		$method = 'create';

		// $values['name'] = 'zonename';
		// $values['origin'] = 'zone origin url';
		// $values['vanity_domain'] = 'domain to use for the zone';

		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		$rpcMsg->addParam(php_xmlrpc_encode($values));

		return $this->_sendRequest($namespace, $rpcMsg);
	}

	/**
	 * @brief Update a zone
	 *
	 * @param namespace Namespace wanted (pullzone, pushzone, vodzone)
	 * @param zoneId		Id of the zone to update
	 * @param values    Array describing the new infos of the zone.
	 *
	 * @return Reply from the server.
	 */
	public function updateZone($namespace, $zoneId, $values)
	{
		$method = 'update';

		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		$rpcMsg->addParam(php_xmlrpc_encode($zoneId));
		$rpcMsg->addParam(php_xmlrpc_encode($values));

		return $this->_sendRequest($namespace, $rpcMsg);
	}

	/**
	 * @brief Retrieve the Bandwith of the wanted time area
	 *
	 * @param namespace Namespace wanted (should be 'account')
	 * @param from From when to check
	 * @param to   Until when to check
	 *
	 * @return Bandwith used (Replied from the server)
	 */
	public function getBandwidth($namespace, $from = null, $to = null)
	{
		$method = 'getBandwidth';

		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		if ($from)
			$rpcMsg->addParam(php_xmlrpc_encode($from));
		if ($to)
			$rpcMsg->addParam(php_xmlrpc_encode($to));

		return $this->_sendRequest($namespace, $rpcMsg);
	}

	/**
	 * @brief Purge the cache of the given URL.
	 *
	 * @param namespace Namespace wanted (should be 'cache')
	 * @param url				Url to purge
	 *
	 * @return Reply from the server.
	 */
	public function cachePurge($namespace, $url)
	{
		$method = 'purge';

		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		$rpcMsg->addParam(php_xmlrpc_encode($url));

		return $this->_sendRequest($namespace, $rpcMsg);
	}

	/**
	 * @brief Purge all cache of the specified zone.
	 *
	 * @param namespace Namespace wanted (should be 'cache')
	 * @param zoneId	 Zone to purge.
	 *
	 * @return Reply from the server.
	 */
	public function cachePurgeAll($namespace, $zoneId)
	{
		$method = 'purgeAllCache';

		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		$rpcMsg->addParam(php_xmlrpc_encode($zoneId));

		return $this->_sendRequest($namespace, $rpcMsg);
	}

	/**
	 * @brief Retrieve the data transfert from the given zone and dates
	 *
	 * @param namespace Namespace wanted (should be 'report')
	 * @param zoneId	  Zone to check
	 * @param rangeType	Type of date range wanted (1: today, 2:cur day,
	 *										3: date range)
	 * @param from      Date from where to retrieve the data (format Y-m-d)
	 * @param to        Date from where to retrieve the data (format Y-m-d)
	 *
	 * @return Reply from the server.
	 */
	public function getTotalTransfer($namespace, $zoneId, $rangeType,
		$from = null, $to = null)
	{
		$method = 'getTotalTransferStats';


		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		$rpcMsg->addParam(php_xmlrpc_encode(Configuration::get('CLOUDCACHE_API_COMPANY_ID')));

		$rpcMsg->addParam(php_xmlrpc_encode($zoneId));
		//$rpcMsg->addParam(php_xmlrpc_encode($rangeType));

		if ($rangeType == CLOUDCACHE_RANGE_TYPE_DATE)
		{
			if ($from)
				$rpcMsg->addParam(php_xmlrpc_encode($from));
			if ($to)
				$rpcMsg->addParam(php_xmlrpc_encode($to));
		}
		return $this->_sendRequest($namespace, $rpcMsg);
	}

	/**
	 * @brief Retrieve the data transfert from the given zone and dates
	 *
	 * @param namespace Namespace wanted (should be 'report')
	 * @param companyId Company to check
	 * @param zoneId	  Zone to check
	 * @param from      Date from where to retrieve the data (format Y-m-d)
	 * @param to        Date from where to retrieve the data (format Y-m-d)
	 *
	 * @return Reply from the server.
	 */
	public function getTotalTransferStats($namespace, $companyId, $zoneId,
		$from, $to)
	{
		$method = 'getTotalTransferStats';

		$rpcMsg = $this->_getEmptyRpcMessage($namespace, $method);
		$rpcMsg->addParam(php_xmlrpc_encode($companyId));
		$rpcMsg->addParam(php_xmlrpc_encode($from));
		$rpcMsg->addParam(php_xmlrpc_encode($to));
		$rpcMsg->addParam(php_xmlrpc_encode($zoneId));

		return $this->_sendRequest($namespace, $rpcMsg);
	}


	/**
	 * @brief Actually send the request to the cloudcache API server.
	 *
	 * @param namespace Namespace of the request
	 * @param rpcMsg	  XML-RPC message Object containing the actual request
	 *
	 * @return Reply from the server
	 */
	private function _sendRequest($namespace, $rpcMsg)
	{
		$this->lastRpcRequest = $rpcMsg;

		// Initialize the XML-RPL client
		$rpcClient = new xmlrpc_client($this->apiURI.$namespace,
								 $this->apiURL, $this->port, $this->httpMethod);

		if (file_exists(dirname(__FILE__).'/proxy.inc.php'))
		{
			include(dirname(__FILE__).'/proxy.inc.php');
			$rpcClient->setProxy($proxy->host, $proxy->port,
				$proxy->username, $proxy->password);
		}

		// Send the message
		$this->lastRpcResponse = $rpcClient->send($rpcMsg);

		return !$this->getLastFaultCode() ? php_xmlrpc_decode($this->lastRpcResponse->value()) : false;
	}

	public function getLastFaultCode()
	{
		if ($this->lastRpcResponse)
			return $this->lastRpcResponse->faultCode();
		return false;
	}

	public function getLastFaultString()
	{
		if ($this->lastRpcResponse)
			return $this->lastRpcResponse->faultString();
		return false;
	}

	public function getLastRpcResponse()
	{
		return $this->lastRpcResponse;
	}

	public function getLastRpcRequest()
	{
		return $this->lastRpcRequest;
	}

	public function getAvailableNamespaces($name = false)
	{
		return !$name ? array_keys($this->availableNamespaces) : $this->availableNamespaces;
	}
}
