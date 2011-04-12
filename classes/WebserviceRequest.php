<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class WebserviceRequestCore
{
	/** @var array Errors triggered at execution */
	private $_errors = array();
	
	/** @var string Status header sent at return */
	private $_status = 'HTTP/1.1 200 OK';
	
	/** @var boolean Set if return should display content or not */
	private $_outputEnabled = true;
	
	/** @var boolean Set if the management is specific or if it is classic (entity management) */
	private $_specificManagement = false;
	
	/** @var string Base PrestaShop webservice URL */
	private $_wsUrl;
	
	/** @var string PrestaShop Webservice Documentation URL */
	private $_docUrl = 'http://prestashop.com/docs/1.4/webservice';
	
	/** @var boolean Set if the authentication key was checked */
	private $_authenticated = false;
	
	/** @var string HTTP Method to support */
	private $_method;
	
	/** @var array The segment of the URL */
	private $_urlSegment = array();
	
	/** @var array The segment list of the URL after the "api" segment */
	private $_urlFragments = array();
	
	/** @var int The time in microseconds of the start of the execution of the web service request */
	private $_startTime = 0;
	
	/** @var array The list of each resources manageable via web service */
	private $_resourceList;
	
	/** @var array The configuration parameters of the current resource */
	private $_resourceConfiguration;
	
	/** @var array The permissions for the current key */
	private $_keyPermissions;
	
	/** @var string The XML string to display if web service call succeed */
	private $_xmlOutput = '';
	
	/** @var array The list of objects to display */
	private $_objects;
	
	/** @var ObjectModel The current object to support, it extends the PrestaShop ObjectModel */
	private $_object;
	
	/** @var string The schema to display. If null, no schema have to be displayed and normal management has to be performed */
	private $_schemaToDisplay;
	
	/** @var string The fields to display. These fields will be displayed when retrieving objects */
	private $_fieldsToDisplay = 'minimum';
	
	/** @var array The type of images (general, categories, manufacturers, suppliers, stores...) */
	private $_imageTypes = array(
		'general' => array(
			'header' => array(),
			'mail' => array(),
			'invoice' => array(),
			'store_icon' => array(),
		),
		'products' => array(),
		'categories' => array(),
		'manufacturers' => array(),
		'suppliers' => array(),
		'stores' => array()
	);
	
	/** @var string The image type (product, category, general,...) */
	private $_imageType = NULL;
	
	/** @var string The product image declination id */
	private $_productImageDeclinationId = NULL;
	
	/** @var string The file path of the image to display. If not null, the image will be displayed, even if the XML output was not empty */
	private $_imgToDisplay;
	
	/** @var string The extension of the image to display */
	private $_imgExtension = 'jpg';
	
	/** @var int The maximum size supported when uploading images, in bytes */
	private $_imgMaxUploadSize = 3000000;
	
	/** @var array The list of supported mime types */
	private $_acceptedImgMimeTypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
	
	/** @var boolean If the current image management has to manage a "default" image (i.e. "No product available") */
	private $_defaultImage = false;
	
	/** @var string If we are in PUT or POST case, we use this attribute to store the xml string value during process */
	private $_inputXml;
	
	/** @var WebserviceRequest Object instance for singleton */
	private static $_instance;
	
	/** @var string Key used for authentication */
	private $_key;
	
	public $_imageResource = null;
	
	
	static public function getResources()
	{
		$resources = array(
			'addresses' => array('description' => 'The Customer, Manufacturer and Customer addresses','class' => 'Address'),
			'carriers' => array('description' => 'The Carriers','class' => 'Carrier'),
			'carts' => array('description' => 'Customer\'s carts', 'class' => 'Cart'),
			'categories' => array('description' => 'The product categories','class' => 'Category'),
			'combinations' => array('description' => 'The product combinations','class' => 'Combination'),
			'configurations' => array('description' => 'Shop configuration', 'class' => 'Configuration'),
			'countries' => array('description' => 'The countries','class' => 'Country'),
			'currencies' => array('description' => 'The currencies', 'class' => 'Currency'),
			'customers' => array('description' => 'The e-shop\'s customers','class' => 'Customer'),
			'deliveries' => array('description' => 'Product delivery', 'class' => 'Delivery'),
			'groups' => array('description' => 'The customer\'s groups','class' => 'Group'),
			'guests' => array('description' => 'The guests', 'class' => 'Guest'),
			'images' => array('description' => 'The images', 'specific_management' => true),
			'image_types' => array('description' => 'The image types', 'class' => 'ImageType'),
			'languages' => array('description' => 'Shop languages', 'class' => 'Language'),
			'manufacturers' => array('description' => 'The product manufacturers','class' => 'Manufacturer'),
			'order_details' => array('description' => 'Details of an order', 'class' => 'OrderDetail'),
			'order_discounts' => array('description' => 'Discounts of an order', 'class' => 'OrderDiscount'),
			'order_histories' => array('description' => 'The Order histories','class' => 'OrderHistory'),
			'orders' => array('description' => 'The Customers orders','class' => 'Order'),
			'order_states' => array('description' => 'The Order states','class' => 'OrderState'),
			'price_ranges' => array('description' => 'Price ranges', 'class' => 'RangePrice'),
			'product_features' => array('description' => 'The product features','class' => 'Feature'),
			'product_feature_values' => array('description' => 'The product feature values','class' => 'FeatureValue'),
			'product_options' => array('description' => 'The product options','class' => 'AttributeGroup'),
			'product_option_values' => array('description' => 'The product options value','class' => 'Attribute'),
			'products' => array('description' => 'The products','class' => 'Product'),
			'states' => array('description' => 'The available states of countries','class' => 'State'),
			'stores' => array('description' => 'The stores', 'class' => 'Store'),
			'suppliers' => array('description' => 'The product suppliers','class' => 'Supplier'),
			'tags' => array('description' => 'The Products tags','class' => 'Tag'),
			'translated_configurations' => array('description' => 'Shop configuration', 'class' => 'Configuration', 'parameters_attribute' => 'webserviceParametersI18n'),
			'weight_ranges' => array('description' => 'Weight ranges', 'class' => 'RangeWeight'),
			'zones' => array('description' => 'The Countries zones','class' => 'Zone'),
			'employees' => array('description' => 'The Employees', 'class' => 'Employee'),
			'stock_movements' => array('description' => 'Stock movements', 'class' => 'StockMvt'),
			'stock_movement_reasons' => array('description' => 'The stock movement reason', 'class' => 'StockMvtReason'),
		);
		ksort($resources);
		return $resources;
	}
	
	/**
	 * Get WebserviceRequest object instance (Singleton)
	 *
	 * @return object WebserviceRequest instance
	 */
	public static function getInstance()
	{
		if(!isset(self::$_instance))
			self::$_instance = new WebserviceRequest();
		return self::$_instance;
	}
	
	

	/**
	 * Start Webservice request
	 * 	Check webservice activation
	 * 	Check autentication
	 * 	Check resource
	 * 	Check HTTP Method
	 * 	Execute the action
	 * 	Display the result
	 *	
	 * @param string $key
	 * @param string $method
	 * @param string $url
	 * @param string $params
	 * @param string $inputXml
	 *
	 * @return array Returns an array of results (headers, content, type of resource...)
	 */
	public function fetch($key, $method, $url, $params, $bad_class_name, $inputXml = NULL)
	{
		// Time logger
		$this->_startTime = microtime(true);
		
		// Two global vars, for compatibility with the PS core...
		global $webservice_call, $display_errors;
		$webservice_call = true;
		$display_errors = strtolower(ini_get('display_errors')) != 'off';
		
		// Error handler
		set_error_handler(array('WebserviceRequest', 'webserviceErrorHandler'));
		ini_set('html_errors', 'off');
		$this->_wsUrl = Tools::getHttpHost(true).__PS_BASE_URI__.'api/';
		
		$this->_key = trim($key);
		
		// Check webservice activation and request authentication
		if ($this->isActivated() && $this->authenticate())
		{
			if ($bad_class_name)
			{
				$this->setError(500, 'Bad override class name for this key. Please update class_name field');
			}
			
			//parse request url
			$this->_method = $method;
			$this->_urlSegment = explode('/', $url);
			$this->_urlFragments = $params;
			$this->_inputXml = $inputXml;
			
			// check method and resource
			if ($this->checkResource() && $this->checkHTTPMethod())
			{
				// if the resource is a core entity...
				if (!isset($this->_resourceList[$this->_urlSegment[0]]['specific_management']) || !$this->_resourceList[$this->_urlSegment[0]]['specific_management'])
				{
					// load resource configuration
					if ($this->_urlSegment[0] != '')
					{
						$object = new $this->_resourceList[$this->_urlSegment[0]]['class']();

						if (isset($this->_resourceList[$this->_urlSegment[0]]['parameters_attribute']))
							$this->_resourceConfiguration = $object->getWebserviceParameters($this->_resourceList[$this->_urlSegment[0]]['parameters_attribute']);
						else
							$this->_resourceConfiguration = $object->getWebserviceParameters();
					}
					
					// execute the action
					switch ($this->_method)
					{
						case 'GET':
						case 'HEAD':
							if ($this->executeEntityGetAndHead())
								$this->writeXmlAfterGet();
							break;
						case 'POST':
							if (array_key_exists(1, $this->_urlSegment))
								$this->setError(400, 'id is forbidden when adding a new resource');
							elseif ($this->executeEntityPost())
								$this->writeXmlAfterModification();
							break;
						case 'PUT':
							if ($this->executeEntityPut())
								$this->writeXmlAfterModification();
							break;
						case 'DELETE':
							$this->executeEntityDelete();
							break;
					}
				}
				// if the management is specific
				else
				{
					$this->_specificManagement = $this->_urlSegment[0];
					switch($this->_specificManagement)
					{
						case 'images':
							$this->manageImages();
							break;
					}
				}
			}
		}
		if ($this->_outputEnabled)
			return $this->returnOutput();
		unset($webservice_call);
		unset ($display_errors);
	}
	
	/**
	 * Set the return header status
	 *
	 * @param int $num
	 * @return void
	 */
	public function setStatus($num)
	{
		switch ($num)
		{
			case 200 :
				$this->_status = 'HTTP/1.1 200 OK';
				break;
			case 201 :
				$this->_status = 'HTTP/1.1 201 Created';
				break;
			case 204 :
				$this->_status = 'HTTP/1.1 204 No Content';
				break;
			case 400 :
				$this->_status = 'HTTP/1.1 400 Bad Request';
				break;
			case 401 :
				$this->_status = 'HTTP/1.0 401 Unauthorized';
				break;
			case 404 :
				$this->_status = 'HTTP/1.1 404 Not Found';
				break;
			case 405 :
				$this->_status = 'HTTP/1.1 405 Method Not Allowed';
				break;
			case 500 :
				$this->_status = 'HTTP/1.1 500 Internal Server Error';
				break;
		}
	}
	
	/**
	 * Set a webservice error
	 *
	 * @param int $num
	 * @param string $label
	 * @return void
	 */
	public function setError($num, $label)
	{
		global $display_errors;
		$this->setStatus($num);
		$this->_errors[] = $display_errors ? $label : 'Internal error';
	}
	
	/**
	 * Set a webservice error and propose a new value near from the available values
	 *
	 * @param int $num
	 * @param string $label
	 * @param array $values
	 * @return void
	 */
	public function setErrorDidYouMean($num, $label, $value, $values)
	{
		$this->setError($num, $label.'. Did you mean: "'.$this->getClosest($value, $values).'"?'.(count($values) > 1 ? ' The full list is: "'.implode('", "', $values).'"' : ''));
	}
	
	/**
	 * Return the nearest value picked in the values list
	 *
	 * @param string $input
	 * @param array $words
	 * @return string
	 */
	private function getClosest($input, $words)
	{
		$shortest = -1;
		foreach ($words as $word)
		{
			$lev = levenshtein($input, $word);
			if ($lev == 0)
			{
				$closest = $word;
				$shortest = 0;
				break;
			}
			if ($lev <= $shortest || $shortest < 0)
			{
				$closest = $word;
				$shortest = $lev;
			}
		}
		return $closest;
	}
	
	/**
	 * Used to replace the default PHP error handler, in order to display PHP errors in a XML format 
	 *
	 * @param string $errno contains the level of the error raised, as an integer
	 * @param array $errstr contains the error message, as a string
	 * @param array $errfile errfile, which contains the filename that the error was raised in, as a string
	 * @param array $errline errline, which contains the line number the error was raised at, as an integer
	 * @return boolean Always return true to avoid the default PHP error handler
	 */
	public function webserviceErrorHandler($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno))
			return;
		switch($errno)
		{
			case E_ERROR:
				WebserviceRequest::getInstance()->setError(500, '[PHP Error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_WARNING:
				WebserviceRequest::getInstance()->setError(500, '[PHP Warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_PARSE:
				WebserviceRequest::getInstance()->setError(500, '[PHP Parse #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_NOTICE:
				WebserviceRequest::getInstance()->setError(500, '[PHP Notice #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_CORE_ERROR:
				WebserviceRequest::getInstance()->setError(500, '[PHP Core #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_CORE_WARNING:
				WebserviceRequest::getInstance()->setError(500, '[PHP Core warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_COMPILE_ERROR:
				WebserviceRequest::getInstance()->setError(500, '[PHP Compile #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_COMPILE_WARNING:
				WebserviceRequest::getInstance()->setError(500, '[PHP Compile warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_USER_ERROR:
				WebserviceRequest::getInstance()->setError(500, '[PHP Error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_USER_WARNING:
				WebserviceRequest::getInstance()->setError(500, '[PHP User warning #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_USER_NOTICE:
				WebserviceRequest::getInstance()->setError(500, '[PHP User notice #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_STRICT:
				WebserviceRequest::getInstance()->setError(500, '[PHP Strict #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			case E_RECOVERABLE_ERROR:
				WebserviceRequest::getInstance()->setError(500, '[PHP Recoverable error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
				break;
			default:
				WebserviceRequest::getInstance()->setError(500, '[PHP Unknown error #'.$errno.'] '.$errstr.' ('.$errfile.', line '.$errline.')');
		}
		return true;
	}
	
	/**
	 * Check if there is one or more error
	 *
	 * @return boolean
	 */
	private function hasErrors()
	{
		return (boolean)$this->_errors;
	}
	
	/**
	 * Check request authentication
	 *
	 * @return boolean
	 */
	private function authenticate()
	{
		if (!$this->hasErrors())
		{
			if (is_null($this->_key))
			{
				$this->setError(401, 'Please enter the authentication key as the login. No password required');
			}
			else
			{
				if (empty($this->_key))
				{
					$this->setError(401, 'Authentication key is empty');
				}
				elseif (strlen($this->_key) != '32')
				{
					$this->setError(401, 'Invalid authentication key format');
				}
				else
				{
					$keyValidation = WebserviceKey::isKeyActive($this->_key);
					if (is_null($keyValidation))
					{
						$this->setError(401, 'Authentification key does not exist');
					}
					elseif($keyValidation === true)
					{
						$this->_keyPermissions = WebserviceKey::getPermissionForAccount($this->_key);
					}
					else
					{
						$this->setError(401, 'Authentification key is not active');
					}
					
					if (!$this->_keyPermissions)
					{
						$this->setError(401, 'No permission for this authentication key');
					}
				}
			}
			if ($this->hasErrors())
			{
				header('WWW-Authenticate: Basic realm="Welcome to PrestaShop Webservice, please enter the authentication key as the login. No password required."');
				$this->setStatus(401);
				return false;
			}
			else
			{
				// only now we can say the access is authenticated
				$this->_authenticated = true;
				return true;
			}
		}
	}
	
	/**
	 * Check webservice activation
	 *
	 * @return boolean
	 */
	private function isActivated()
	{
		if (!Configuration::get('PS_WEBSERVICE'))
		{
			$this->setError(404, 'The PrestaShop webservice is disabled. Please activate it in the PrestaShop Back Office');
			return false;
		}
		return true;
	}
	
	/**
	 * Check HTTP method
	 *
	 * @return boolean
	 */
	private function checkHTTPMethod()
	{
		if (!in_array($this->_method, array('GET', 'POST', 'PUT', 'DELETE', 'HEAD')))
			$this->setError(405, 'Method '.$this->_method.' is not valid');
		elseif (($this->_method == 'PUT' || $this->_method == 'DELETE') && !array_key_exists(1, $this->_urlSegment))
			$this->setError(401, 'Method '.$this->_method.' needs you to specify an id');
		elseif ($this->_urlSegment[0] && !in_array($this->_method, $this->_keyPermissions[$this->_urlSegment[0]]))
			$this->setError(405, 'Method '.$this->_method.' is not allowed for the resource '.$this->_urlSegment[0].' with this authentication key');
		else
			return true;
		return false;
	}
	
	/**
	 * Check resource validity
	 *
	 * @return boolean
	 */
	private function checkResource()
	{
		$this->_resourceList = WebserviceRequest::getResources();
		$resourceNames = array_keys($this->_resourceList);
		if ($this->_urlSegment[0] == '')
			$this->_resourceConfiguration['objectsNodeName'] = 'resources';
		elseif (in_array($this->_urlSegment[0], $resourceNames))
		{
			if (!in_array($this->_urlSegment[0], array_keys($this->_keyPermissions)))
			{
				$this->setError(401, 'Resource of type "'.$this->_urlSegment[0].'" is not allowed with this authentication key');
				return false;
			}
		}
		else
		{
			$this->setErrorDidYouMean(400, 'Resource of type "'.$this->_urlSegment[0].'" does not exists', $this->_urlSegment[0], $resourceNames);
			return false;
		}
		return true;
	}
	
	/**
	 * Execute GET and HEAD requests
	 * 
	 * Build filter
	 * Build fields display
	 * Build sort
	 * Build limit
	 * 
	 * @return boolean
	 */
	private function executeEntityGetAndHead()
	{
		if ($this->_resourceConfiguration['objectsNodeName'] != 'resources')
		{
			//construct SQL filter
			$sql_filter = '';
			$sql_join = '';
			if ($this->_urlFragments)
			{
				// if we have to display the schema
				if (array_key_exists('schema', $this->_urlFragments))
				{
					if ($this->_urlFragments['schema'] == 'blank')
					{
						$this->_schemaToDisplay = 'blank';
						return true;
					}
					elseif ($this->_urlFragments['schema'] == 'synopsis')
					{
						$this->_schemaToDisplay = 'synopsis';
						return true;
					}
					else
					{
						$this->setError(400, 'Please select a schema of type \'synopsis\' to get the whole schema informations (which fields are required, which kind of content...) or \'blank\' to get an empty schema to fill before using POST request');
						return false;
					}
				}
				else
				{
					// if there are filters
					if (isset($this->_urlFragments['filter']))
						foreach ($this->_urlFragments['filter'] as $field => $url_param)
						{
							$available_filters = array_keys($this->_resourceConfiguration['fields']);
							if ($field != 'sort' && $field != 'limit')
								if (!in_array($field, $available_filters))
								{
									// if there are linked tables
									if (isset($this->_resourceConfiguration['linked_tables']) && isset($this->_resourceConfiguration['linked_tables'][$field]))
									{
										// contruct SQL join for linked tables
										$sql_join .= 'LEFT JOIN `'._DB_PREFIX_.pSQL($this->_resourceConfiguration['linked_tables'][$field]['table']).'` '.pSQL($field).' ON (main.`'.pSQL($this->_resourceConfiguration['fields']['id']['sqlId']).'` = '.pSQL($field).'.`'.pSQL($this->_resourceConfiguration['fields']['id']['sqlId']).'`)'."\n";
						
										// construct SQL filter for linked tables
										foreach ($url_param as $field2 => $value)
										{
											if (isset($this->_resourceConfiguration['linked_tables'][$field]['fields'][$field2]))
											{
												$linked_field = $this->_resourceConfiguration['linked_tables'][$field]['fields'][$field2];
												$sql_filter .= $this->getSQLRetrieveFilter($linked_field['sqlId'], $value, $field.'.');
											}
											else
											{
												$list = array_keys($this->_resourceConfiguration['linked_tables'][$field]['fields']);
												$this->setErrorDidYouMean(400, 'This filter does not exist for this linked table', $field2, $list);$this->setErrorDidYouMean(400, 'This declination does not exist', $this->_urlSegment[4], $normalImageSizeNames);
												return false;
											}
										}
									}
									// if there are filters on linked tables but there are no linked table
									elseif (is_array($url_param))
									{
										if (isset($this->_resourceConfiguration['linked_tables']))
											$this->setErrorDidYouMean(400, 'This linked table does not exist', $field, array_keys($this->_resourceConfiguration['linked_tables']));
										else
											$this->setError(400, 'There is no existing linked table for this resource');
										return false;
									}
									else
									{
										$this->setErrorDidYouMean(400, 'This filter does not exist', $field, $available_filters);
										return false;
									}
								}
								elseif ($url_param == '')
								{
									$this->setError(400, 'The filter "'.$field.'" is malformed.');
									return false;
								}
								else
								{
									if (isset($this->_resourceConfiguration['fields'][$field]['getter']))
									{
										$this->setError(400, 'The field "'.$field.'" is dynamic. It is not possible to filter GET query with this field.');
										return false;
									}
									else
									{
										if (isset($this->_resourceConfiguration['retrieveData']['tableAlias']))
											$sql_filter .= $this->getSQLRetrieveFilter($this->_resourceConfiguration['fields'][$field]['sqlId'], $url_param, $this->_resourceConfiguration['retrieveData']['tableAlias'].'.');
										else
											$sql_filter .= $this->getSQLRetrieveFilter($this->_resourceConfiguration['fields'][$field]['sqlId'], $url_param);
									}
								}
						}
				}
			}
	
			// set the fields to display in the list : "full", "minimum", "field_1", "field_1,field_2,field_3" //TODO manage linked_tables too
			if (isset($this->_urlFragments['display']))
			{
				$this->_fieldsToDisplay = $this->_urlFragments['display'];
				if ($this->_fieldsToDisplay != 'full')
				{
					preg_match('#^\[(.*)\]$#Ui', $this->_fieldsToDisplay, $matches);
					if (count($matches))
					{
						$fieldsToTest = explode(',', $matches[1]);
						
						// looks for synthax errors...
						foreach ($fieldsToTest as $fieldToDisplay)
						{
							preg_match('#^associations\[([^\]]+)\]$#Ui', $fieldToDisplay, $matches2);
							$error = false;
							if (isset($matches2[1]))// if it's an association
							{
								if (!array_key_exists($matches2[1], $this->_resourceConfiguration['associations'])) // if this association does not exists
									$error = true;
							}
							elseif (!isset($this->_resourceConfiguration['fields'][$fieldToDisplay]) && $fieldToDisplay != 'associations') // if it's a field and this field does not exists OR if it's the associations
							{
								$error = true;
							}
							if ($error)
							{
								$this->setError(400,'Unable to display this field. However, these are available: '.implode(', ', array_keys($this->_resourceConfiguration['fields'])));
								return false;
							}
						}
							$this->_fieldsToDisplay = !$this->hasErrors() ? $fieldsToTest : 'minimal';
					}
					else
					{
						$this->setError(400, 'The \'display\' syntax is wrong. You can set \'full\' or \'[field_1,field_2,field_3,...]\'. These are available: '.implode(', ', array_keys($this->_resourceConfiguration['fields'])));
						return false;
					}
				}
			}
			// construct SQL Sort
			$sql_sort = '';
			$available_filters = array_keys($this->_resourceConfiguration['fields']);
			if (isset($this->_urlFragments['sort']))
			{
				preg_match('#^\[(.*)\]$#Ui', $this->_urlFragments['sort'], $matches);
				if (count($matches) > 1)
					$sorts = explode(',', $matches[1]);
				else
					$sorts = array($this->_urlFragments['sort']);
		
				$sql_sort .= ' ORDER BY ';
				
				foreach ($sorts as $sort)
				{
					$delimiterPosition = strrpos($sort, '_');
					if ($delimiterPosition !== false)
					{
						$fieldName = substr($sort, 0, $delimiterPosition);
						$direction = strtoupper(substr($sort, $delimiterPosition + 1));
					}
					if ($delimiterPosition === false || !in_array($direction, array('ASC', 'DESC')))
					{
						$this->setError(400, 'The "sort" value has to be formed as this example: "field_ASC" or \'[field_1_DESC,field_2_ASC,field_3_ASC,...]\' ("field" has to be an available field)');
						return false;
					}
					elseif (!in_array($fieldName, $available_filters))
					{
						$this->setError(400, 'Unable to filter by this field. However, these are available: '.implode(', ', $available_filters));
						return false;
					}
					else
					{
						$sql_sort .= (isset($this->_resourceConfiguration['retrieveData']['tableAlias']) ? $this->_resourceConfiguration['retrieveData']['tableAlias'].'.' : '').'`'.pSQL($this->_resourceConfiguration['fields'][$fieldName]['sqlId']).'` '.$direction.', ';// ORDER BY `field` ASC|DESC
					}
				}
				$sql_sort = rtrim($sql_sort, ', ')."\n";
			}

			//construct SQL Limit
			$sql_limit = '';
			if (isset($this->_urlFragments['limit']))
			{
				$limitArgs = explode(',', $this->_urlFragments['limit']);
				if (count($limitArgs) > 2)
				{
					$this->setError(400, 'The "limit" value has to be formed as this example: "5,25" or "10"');
					return false;
				}
				else
				{
					$sql_limit .= ' LIMIT '.(int)($limitArgs[0]).(isset($limitArgs[1]) ? ', '.(int)($limitArgs[1]) : '')."\n";// LIMIT X|X, Y
				}
			}


			$this->_objects = array();
			if (!isset($this->_urlSegment[1]) || !strlen($this->_urlSegment[1]))
			{
				$this->_resourceConfiguration['retrieveData']['params'][] = $sql_join;
				$this->_resourceConfiguration['retrieveData']['params'][] = $sql_filter;
				$this->_resourceConfiguration['retrieveData']['params'][] = $sql_sort;
				$this->_resourceConfiguration['retrieveData']['params'][] = $sql_limit;
				//list entities
				$tmp = new $this->_resourceConfiguration['retrieveData']['className']();
				$sqlObjects = call_user_func_array(array($tmp, $this->_resourceConfiguration['retrieveData']['retrieveMethod']), $this->_resourceConfiguration['retrieveData']['params']);
				if ($sqlObjects)
					foreach ($sqlObjects as $sqlObject)
						$this->_objects[] = new $this->_resourceConfiguration['retrieveData']['className']($sqlObject[$this->_resourceConfiguration['fields']['id']['sqlId']]);
			}
			else
			{
				//get entity details
				$object = new $this->_resourceConfiguration['retrieveData']['className']($this->_urlSegment[1]);
				if ($object->id)
					$this->_objects[] = $object;
				else
				{
					$this->setStatus(404);
					$this->_outputEnabled = false;
					return false;
				}
			}

		}
		return true;
	}
	
	/**
	 * Execute POST method on a PrestaShop entity
	 * 
	 * @return boolean
	 */
	private function executeEntityPost()
	{
		$this->_object = new $this->_resourceConfiguration['retrieveData']['className']();
		return $this->saveEntityFromXml($this->_inputXml, 201);
	}
	
	/**
	 * Execute PUT method on a PrestaShop entity
	 * 
	 * @return boolean
	 */
	private function executeEntityPut()
	{
		$this->_object = new $this->_resourceConfiguration['retrieveData']['className']($this->_urlSegment[1]);

		if ($this->_object->id)
		{
			return $this->saveEntityFromXml($this->_inputXml, 200);
		}
		else
		{
			$this->setStatus(404);
			$this->_outputEnabled = false;
			return false;
		}
	}
	
	/**
	 * Execute DELETE method on a PrestaShop entity
	 * 
	 * @return boolean
	 */
	private function executeEntityDelete()
	{
		$object = new $this->_resourceConfiguration['retrieveData']['className'](intval($this->_urlSegment[1]));
		if (!$object->id)
			$this->setStatus(404);
		else
		{
			if (isset($this->_resourceConfiguration['objectMethods']) && isset($this->_resourceConfiguration['objectMethods']['delete']))
				$result = $object->{$this->_resourceConfiguration['objectMethods']['delete']}();
			else
				$result = $object->delete();
			if (!$result)
				$this->setStatus(500);
		}
		$output = false;
	}
	
	/**
	 * Write XML output after GET and HEAD action
	 * 
	 * @return void
	 */
	private function writeXmlAfterGet()
	{
		// list entities
		if (!isset($this->_urlSegment[1]) || !strlen($this->_urlSegment[1]))
		{
			if (($this->_resourceConfiguration['objectsNodeName'] != 'resources' && count($this->_objects)) || 
			($this->_resourceConfiguration['objectsNodeName'] == 'resources' && count($this->_resourceList)) ||
			($this->_schemaToDisplay != null))
			{
				if ($this->_resourceConfiguration['objectsNodeName'] != 'resources')
				{
					if (!is_null($this->_schemaToDisplay))
					{
						$this->_fieldsToDisplay = 'full';
						$this->_xmlOutput .= $this->getXmlFromEntity();
					}
					// display specific resources list
					else
					{
						$this->_xmlOutput .= '<'.$this->_resourceConfiguration['objectsNodeName'].'>'."\n";
						if ($this->_fieldsToDisplay == 'minimum')
							foreach ($this->_objects as $object)
								$this->_xmlOutput .= '<'.$this->_resourceConfiguration['objectNodeName'].(array_key_exists('id', $this->_resourceConfiguration['fields']) ? ' id="'.$object->id.'" xlink:href="'.$this->_wsUrl.$this->_resourceConfiguration['objectsNodeName'].'/'.$object->id.'"' : '').' />'."\n";
						else
							foreach ($this->_objects as $object)
								$this->_xmlOutput .= $this->getXmlFromEntity($object);
						$this->_xmlOutput .= '</'.$this->_resourceConfiguration['objectsNodeName'].'>'."\n";
					}
				}
				// display all resourceources list
				else
				{
					$this->_xmlOutput .= '<api shop_name="'.Configuration::get('PS_SHOP_NAME').'" get="true" put="false" post="false" delete="false" head="true">'."\n";
					foreach ($this->_resourceList as $resourceName => $resource)
						if (in_array($resourceName, array_keys($this->_keyPermissions)))
						{
							$this->_xmlOutput .= '<'.$resourceName.' xlink:href="'.$this->_wsUrl.$resourceName.'"
								get="'.(in_array('GET', $this->_keyPermissions[$resourceName]) ? 'true' : 'false').'"
								put="'.(in_array('PUT', $this->_keyPermissions[$resourceName]) ? 'true' : 'false').'"
								post="'.(in_array('POST', $this->_keyPermissions[$resourceName]) ? 'true' : 'false').'"
								delete="'.(in_array('DELETE', $this->_keyPermissions[$resourceName]) ? 'true' : 'false').'"
								head="'.(in_array('HEAD', $this->_keyPermissions[$resourceName]) ? 'true' : 'false').'"
							>
							<description>'.$resource['description'].'</description>';
							if (!isset($resource['specific_management']) || !$resource['specific_management'])
							$this->_xmlOutput .= '
							<schema type="blank" xlink:href="'.$this->_wsUrl.$resourceName.'?schema=blank" />
							<schema type="synopsis" xlink:href="'.$this->_wsUrl.$resourceName.'?schema=synopsis" />';
							$this->_xmlOutput .= '
							</'.$resourceName.'>'."\n";
						}
					$this->_xmlOutput .= '</api>'."\n";
				}
			
			}
			else
				$this->_xmlOutput .= '<'.$this->_resourceConfiguration['objectsNodeName'].' />'."\n";
		}
		//display one resource
		else
		{
			$this->_fieldsToDisplay = 'full';
			$this->_xmlOutput .= $this->getXmlFromEntity($this->_objects[0]);
		}
	}
	
	/**
	 * Write XML output after POST and PUT action
	 * 
	 * @return void
	 */
	private function writeXmlAfterModification()
	{
		$this->_fieldsToDisplay = 'full';
		$this->_xmlOutput .= $this->getXmlFromEntity($this->_object);
	}
	
	/**
	 * save Entity Object from XML
	 * 
	 * @param string $xmlString
	 * @param int $successReturnCode
	 * @return boolean
	 */
	private function saveEntityFromXml($xmlString, $successReturnCode)
	{
		$xml = new SimpleXMLElement($xmlString);
		$attributes = $xml->children()->{$this->_resourceConfiguration['objectNodeName']}->children();
		$i18n = false;
		// attributes
		foreach ($this->_resourceConfiguration['fields'] as $fieldName => $fieldProperties)
		{
			$sqlId = $fieldProperties['sqlId'];
			if (isset($attributes->$fieldName) && isset($fieldProperties['sqlId']) && (!isset($fieldProperties['i18n']) || !$fieldProperties['i18n']))
			{
				if (isset($fieldProperties['setter']))
				{
					// if we have to use a specific setter
					if (!$fieldProperties['setter'])
					{
						// if it's forbidden to set this field
						$this->setError(400, 'parameter "'.$fieldName.'" not writable. Please remove this attribute of this XML');
						return false;
					}
					else
						$this->_object->$fieldProperties['setter']((string)$attributes->$fieldName);
				}
				else
					$this->_object->$sqlId = (string)$attributes->$fieldName;
			}
			elseif (isset($fieldProperties['required']) && $fieldProperties['required'] && !$fieldProperties['i18n'])
			{
				$this->setError(400, 'parameter "'.$fieldName.'" required');
				return false;
			}
			elseif (!isset($fieldProperties['required']) || !$fieldProperties['required'])
				$this->_object->$sqlId = null;
			
			if (isset($fieldProperties['i18n']) && $fieldProperties['i18n'])
			{
				$i18n = true;
				foreach ($attributes->$fieldName->language as $lang)
					$this->_object->{$fieldName}[(int)$lang->attributes()->id] = (string)$lang;
			}
		}
				
		if (!$this->hasErrors())
		{
			if ($i18n && ($retValidateFieldsLang = $this->_object->validateFieldsLang(false, true)) !== true)
			{
				$this->setError(400, 'Validation error: "'.$retValidateFieldsLang.'"');
				return false;
			}
			elseif (($retValidateFields = $this->_object->validateFields(false, true)) !== true)
			{
				$this->setError(400, 'Validation error: "'.$retValidateFields.'"');
				return false;
			}
			else
			{
				// Call alternative method for add/update
				$objectMethod = ($this->_method == 'POST' ? 'add' : 'update');
				if (isset($this->_resourceConfiguration['objectMethods']) && array_key_exists($objectMethod, $this->_resourceConfiguration['objectMethods']))
					$objectMethod = $this->_resourceConfiguration['objectMethods'][$objectMethod];
				$result = $this->_object->{$objectMethod}();
				if($result)
				{
					if (isset($attributes->associations))
						foreach ($attributes->associations->children() as $association)
						{
							// associations
							if (isset($this->_resourceConfiguration['associations'][$association->getName()]))
							{
								$assocItems = $association->children();
								$values = array();
								foreach ($assocItems as $assocItem)
								{
									$fields = $assocItem->children();
									$entry = array();
									foreach ($fields as $fieldName => $fieldValue)
										$entry[$fieldName] = (string)$fieldValue;
									$values[] = $entry;
								}
								$setter = $this->_resourceConfiguration['associations'][$association->getName()]['setter'];
								if (!is_null($setter) && !$this->_object->$setter($values))
								{
									$this->setError(500, 'Error occurred while setting the '.$association->getName().' value');
									return false;
								}
							}
							elseif ($association->getName() != 'i18n')
							{
								$this->setError(400, 'The association "'.$association->getName().'" does not exists');
								return false;
							}
						}
					if (!$this->hasErrors())
					{
						$this->setStatus($successReturnCode);
						return true;
					}
				}
				else
					$this->setError(500, 'Unable to save resource');
			}
		}
	}
	
	/**
	 * get SQL retrieve Filter
	 * 
	 * @param string $sqlId
	 * @param string $filterValue
	 * @param string $tableAlias = 'main.'
	 * @return string
	 */
	private function getSQLRetrieveFilter($sqlId, $filterValue, $tableAlias = 'main.')
	{
		$ret = '';
		// "LIKE" case (=%[foo]%, =%[foo], =[foo]%)
		preg_match('/^(.*)\[(.*)\](.*)$/', $filterValue, $matches);
		if (count($matches) > 1)
		{
			if ($matches[1] == '%' || $matches[3] == '%')
				$ret .= ' AND '.$tableAlias.'`'.pSQL($sqlId).'` LIKE "'.$matches[1].pSQL($matches[2]).$matches[3]."\"\n";// AND field LIKE %value%
			elseif ($matches[1] == '' && $matches[3] == '')
			{
				// "OR" case
				preg_match('/^([^\|]+)(\|([^\|]+))+$/', $matches[2], $matches2);
				preg_match('/^(.+)$/', $matches[2], $matches4);
				if (count($matches2) > 0 || count($matches4) > 1)
				{
					$values = explode('|', $matches[2]);
					$ret .= ' AND (';
					$temp = '';
					foreach ($values as $value)
						$temp .= $tableAlias.'`'.pSQL($sqlId).'` = "'.pSQL($value).'" OR ';// AND (field = value3 OR field = value7 OR field = value9)
					$ret .= rtrim($temp, 'OR ').')'."\n";
				}
				else // "AND" case
				{
					preg_match('/^(\d+),(\d+)$/', $matches[2], $matches3);
					if (count($matches3) > 0)
					{
						$values = explode(',', $matches[2]);
						$ret .= ' AND '.$tableAlias.'`'.pSQL($sqlId).'` BETWEEN "'.$values[0].'" AND "'.$values[1]."\"\n";// AND field BETWEEN value3 AND value4
					}
				}
			}
			elseif ($matches[1] == '>')
				$ret .= ' AND '.$tableAlias.'`'.pSQL($sqlId).'` > "'.pSQL($matches[2])."\"\n";// AND field > value3
			elseif ($matches[1] == '<')
				$ret .= ' AND '.$tableAlias.'`'.pSQL($sqlId).'` < "'.pSQL($matches[2])."\"\n";// AND field < value3
			elseif ($matches[1] == '!')
				$ret .= ' AND '.$tableAlias.'`'.pSQL($sqlId).'` != "'.pSQL($matches[2])."\"\n";// AND field IS NOT value3
		}
		else
			$ret .= ' AND '.$tableAlias.'`'.pSQL($sqlId).'` = "'.pSQL($filterValue)."\"\n";
		return $ret;
	}
	
	
	public function getPrice($id_shop = null, $id_product, $id_product_attribute = null, $id_country = null, $id_state = null, $county = null, $id_currency = null, $id_group = null, $quantity = null, 
		$use_tax = null, $decimals = null, $only_reduc = null, $use_reduc = null, $with_ecotax = null, $specific_price_output = null, $divisor = null)
	{
		$id_shop = (isset($id_shop) ? $id_shop : (int)Shop::getCurrentShop());

		// TO CHECK
		$id_product_attribute = (isset($id_product_attribute) ? $id_product_attribute : Product::getDefaultAttribute($id_product));// FIXME 


		$id_country = (isset($id_country) ? $id_country : (int)(Configuration::get('PS_COUNTRY_DEFAULT')));
		$id_state = (isset($id_state) ? $id_state : 0);
		$id_currency = (isset($id_currency) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
		$id_group = (isset($id_group) ? $id_group : Configuration::get('_PS_DEFAULT_CUSTOMER_GROUP_'));
		$quantity = (isset($quantity) ? $quantity : 1);
		$use_tax = (isset($use_tax) ? $use_tax : Configuration::get('PS_TAX'));
		$decimals = (isset($decimals) ? $decimals : 6);

				
		$only_reduc = (isset($only_reduc) ? $only_reduc : false);
		$use_reduc = (isset($use_reduc) ? $use_reduc : true);
		$use_ecotax = (isset($use_ecotax) ? $use_ecotax : true);
		$specific_price_output = null;
 		$county = (isset($county) ? $county : 0);
		// UNUSED
		$divisor = null;
		$price = Product::priceCalculation($id_shop, $id_product, $id_product_attribute, $id_country, $id_state, $county, $id_currency, $id_group, $quantity, 
			$use_tax, $decimals, $only_reduc, $use_reduc, $use_ecotax, $specific_price_output, $divisor);

		return Tools::ps_round($price, 2);
	}
	
	/**
	 * get XML From Object Entity
	 * 
	 * @param ObjectModel $object = null
	 * @return string
	 */
	private function getXmlFromEntity($object = null)
	{
		// two modes are available : 'schema', or 'display entity'
	
		$ret = '<'.$this->_resourceConfiguration['objectNodeName'].'>'."\n";
		// display fields
		foreach ($this->_resourceConfiguration['fields'] as $key => $field)
		{
			if ($this->_fieldsToDisplay == 'full' || in_array($key, $this->_fieldsToDisplay))
			{
				if ($key != 'id')//TODO remove this condition
				{
					// get the field value with a specific getter
					if (isset($field['getter']) && $this->_schemaToDisplay != 'blank' && $object != null)
						$object->$key = $object->$field['getter']();
			
					// display i18n fields
					if (isset($field['i18n']) && $field['i18n'])
					{
						$ret .= '<'.$field['sqlId'];
						if ($this->_schemaToDisplay == 'synopsis')
						{
							if (array_key_exists('required', $field) && $field['required'])
								$ret .= ' required="true"';
							if (array_key_exists('maxSize', $field) && $field['maxSize'])
								$ret .= ' maxSize="'.$field['maxSize'].'"';
							if (array_key_exists('validateMethod', $field) && $field['validateMethod'])
								$ret .= ' format="'.implode(' ', $field['validateMethod']).'" ';
						}
						$ret .= ">\n";
						
					
						
						if (!is_null($this->_schemaToDisplay))
						{
							$languages = Language::getLanguages();
							foreach ($languages as $language)
								$ret .= '<language id="'.$language['id_lang'].'" '.($this->_schemaToDisplay == 'synopsis' ? 'format="isUnsignedId" xlink:href="'.$this->_wsUrl.'languages/'.$language['id_lang'].'"' : '').'></language>'."\n";
						}
						else
						{
							if (!is_null($object->$key))
							{
								if (is_array($object->$key))
									foreach ($object->$key as $idLang => $value)
										$ret .= '<language id="'.$idLang.'" xlink:href="'.$this->_wsUrl.'languages/'.$idLang.'"><![CDATA['.$value.']]></language>'."\n";
							}
						}
						
						
						
						$ret .= '</'.$field['sqlId'].'>'."\n";
					}
					else
					{
						// display not i18n field value
						$ret .= '<'.$field['sqlId'];
						
						if (array_key_exists('xlink_resource', $field) && $this->_schemaToDisplay != 'blank')
						{
							if (!is_array($field['xlink_resource']))
								$ret .= ' xlink:href="'.$this->_wsUrl.$field['xlink_resource'].'/'.($this->_schemaToDisplay != 'synopsis' ? $object->$key : '').'"';
							else
								$ret .= ' xlink:href="'.$this->_wsUrl.$field['xlink_resource']['resourceName'].'/'.(isset($field['xlink_resource']['subResourceName']) ? $field['xlink_resource']['subResourceName'].'/'.$object->id.'/' : '').($this->_schemaToDisplay != 'synopsis' ? $object->$key : '').'"';
						}
						
						if (isset($field['getter']) && $this->_schemaToDisplay != 'blank')
							$ret .= ' not_filterable="true"';
						if ($this->_schemaToDisplay == 'synopsis')
						{
							if (array_key_exists('required', $field) && $field['required'])
								$ret .= ' required="true"';
							if (array_key_exists('maxSize', $field) && $field['maxSize'])
								$ret .= ' maxSize="'.$field['maxSize'].'"';
							if (array_key_exists('validateMethod', $field) && $field['validateMethod'])
								$ret .= ' format="'.implode(' ', $field['validateMethod']).'"';
						}
						$ret .= '>';
						if ($this->_resourceConfiguration['objectNodeName'] == 'product' && $key == 'price')
							$ret .= $this->getPrice(null, $object->id, null, null, null, null, null, null, 
							null, null, null, null, null, null, null, null);
						else if (is_null($this->_schemaToDisplay))
							$ret .= '<![CDATA['.$object->$key.']]>';
						$ret .= '</'.$field['sqlId'].'>'."\n";
					}
				}
				else
					// display id
					if (is_null($this->_schemaToDisplay))
						$ret .= '<id><![CDATA['.$object->id.']]></id>'."\n";
			}
		}
	
		// specific display virtual fields for product
		if ($this->_resourceConfiguration['objectNodeName'] == 'product' && isset($this->_urlFragments['price']))
		{
			foreach ($this->_urlFragments['price'] as $name => $value)
			{
				$id_country = (isset($value['country']) ? $value['country'] : (int)(Configuration::get('PS_COUNTRY_DEFAULT')));
				$id_state = (isset($value['state']) ? $value['state'] : 0);
				$id_currency = (isset($value['currency']) ? $value['currency'] : Configuration::get('PS_CURRENCY_DEFAULT'));
				$id_group = (isset($value['group']) ? $value['group'] : Configuration::get('_PS_DEFAULT_CUSTOMER_GROUP_'));
				$quantity = (isset($value['quantity']) ? $value['quantity'] : 1);
				$use_tax = (isset($value['use_tax']) ? $value['use_tax'] : Configuration::get('PS_TAX'));
				$decimals = (isset($value['decimals']) ? $value['decimals'] : 6);
				$id_product_attribute = (isset($value['product_attribute']) ? $value['product_attribute'] : null);
				$id_county = (isset($value['county']) ? $value['county'] : null);
				
				$only_reduc = (isset($value['only_reduction']) ? $value['only_reduction'] : false);
				$use_reduc = (isset($value['use_reduction']) ? $value['use_reduction'] : true);
				$use_ecotax = (isset($value['use_ecotax']) ? $value['use_ecotax'] : true);
				$specific_price_output = null;
				$county = (isset($value['county']) ? $value['county'] : 0);
				
				
				
				$price = $this->getPrice(null, $object->id, $id_product_attribute, $id_country, $id_state, $id_county, $id_currency, $id_group, $quantity, 
									$use_tax, $decimals, $only_reduc, $use_reduc, $use_ecotax, null, null);
				$name = strtolower($name);
				$ret .= '<'.$name.'>'.$price.'</'.$name.'>'."\n";
			}
		}
		
		// display associations
		$associationsRet = '';
		if (isset($this->_resourceConfiguration['associations']))
		{
			foreach ($this->_resourceConfiguration['associations'] as $assocName => $association)
			{
				if ($this->_fieldsToDisplay == 'full' || is_array($this->_fieldsToDisplay) && (in_array('associations['.$assocName.']', $this->_fieldsToDisplay) || in_array('associations', $this->_fieldsToDisplay)))
				{
					$associationsRet .= '<'.$assocName.' node_type="'.$this->_resourceConfiguration['associations'][$assocName]['resource'].'">'."\n";
					$getter = $this->_resourceConfiguration['associations'][$assocName]['getter'];
					
					// if we are not in schema
					if (method_exists($object, $getter))
					{
						$associationResources = $object->$getter();
						if (is_array($associationResources))
							foreach ($associationResources as $associationResource)
							{
								$associationsRet .= '<'.$this->_resourceConfiguration['associations'][$assocName]['resource'].(isset($this->_resourceList[$assocName]) ? ' xlink:href="'.$this->_wsUrl.$assocName.'/'.$associationResource['id'].'"' : '').'>'."\n";
								foreach ($associationResource as $fieldName => $fieldValue)
									$associationsRet .= '<'.$fieldName.'><![CDATA['.$fieldValue.']]></'.$fieldName.'>'."\n";
								$associationsRet .= '</'.$this->_resourceConfiguration['associations'][$assocName]['resource'].'>'."\n";
							}
					}
					if (!is_null($this->_schemaToDisplay))
					{
						$associationsRet .= '<'.$this->_resourceConfiguration['associations'][$assocName]['resource'].'>'."\n";
						if (isset($this->_resourceConfiguration['associations'][$assocName]['fields']))
						{
							foreach ($this->_resourceConfiguration['associations'][$assocName]['fields'] as $fieldName => $fieldAttributes)
							{
								// if shouldn't be modified (calculated fields etc..)
								if (!array_key_exists('setter',$fieldAttributes) && $fieldName != 'id')
								{
									$associationsRet .= '<'.$fieldName.
									(isset($fieldAttributes['required']) && $fieldAttributes['required'] ? ' required="true"' : '');
									if (isset($fieldAttributes['required']))
										unset($fieldAttributes['required']);
									if (count($fieldAttributes) > 0)
									{
										$associationsRet .= ' format="'.explode(',', $fieldAttributes).'"';
										echo $fieldName.'^'.$fieldAttributes;
									}
									$associationsRet .= '/>'."\n";
								}
							}
						}
						$associationsRet .= '</'.$this->_resourceConfiguration['associations'][$assocName]['resource'].'>'."\n";
					}
					$associationsRet .= '</'.$assocName.'>'."\n";
				}
			}
			if ($associationsRet != '')
				$ret .= '<associations>'."\n".$associationsRet.'</associations>'."\n";
		}
		$ret .= '</'.$this->_resourceConfiguration['objectNodeName'].'>'."\n";
		return $ret;
	}
	
	/**
	 *  Display XML and die the script
	 *  
	 * @return void
	 */
	private function returnOutput()
	{
		$return = array();
		
		// write headers
		
		$return['status'] = $this->_status;
		
		$return['x_powered_by'] = 'X-Powered-By: PrestaShop Webservice';
		// write this header only now (avoid hackers happiness...)
		if ($this->_authenticated)
			$return['ps_ws_version'] = 'PSWS-Version: '._PS_VERSION_;
		$return['execution_time'] =  'Execution-Time: '.round(microtime(true) - $this->_startTime,3);

		// display image content if needed
		if ($this->_imgToDisplay)
		{
			switch ($this->_imgExtension)
			{
				case 'jpg':
					$this->_imageResource = @imagecreatefromjpeg($this->_imgToDisplay);
					break;
				case 'gif':
					$this->_imageResource = @imagecreatefromgif($this->_imgToDisplay);
					break;
			}
			if(!$this->_imageResource)
				$this->setError(500, 'Unable to load the image "'.str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $this->_imgToDisplay).'"');
			else
			{
				$return['type'] = 'image';
				switch ($this->_imgExtension)
				{
					case 'jpg':
						$return['content_type'] = 'Content-Type: image/jpeg';
						break;
					case 'gif':
						$return['content_type'] = 'Content-Type: image/gif';
						break;
				}
				restore_error_handler();
				return $return;
			}
		}
		
		// if errors appends when creating return xml, we replace the usual xml content by the nice error handler content
		if ($this->hasErrors())
		{
			$this->_xmlOutput = '<errors>'."\n";
			foreach ($this->_errors as $error)
				$this->_xmlOutput .= '<error><![CDATA['.$error.']]></error>'."\n";
			$this->_xmlOutput .= '</errors>'."\n";
		}
		restore_error_handler();
		// display xml content if needed
		if (strlen($this->_xmlOutput) > 0)
		{
			$xml_start = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$xml_start .= '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink">'."\n";
			$xml_end = '</prestashop>'."\n";
			
			$return['type'] = 'xml';
			$return['content_type'] = 'Content-Type: text/xml';
			$return['content_sha1'] = 'Content-Sha1: '.sha1($this->_xmlOutput);
			$return['content'] = $xml_start.$this->_xmlOutput.$xml_end;
			return $return;
		}
	}
	
	/**
	 * Management of images URL segment
	 * 
	 * @return boolean
	 */
	private function manageImages()
	{
		/*
		 * available cases api/... :
		 *   
		 *   images ("types_list") (N-1)
		 *   	GET    (xml)
		 *   images/general ("general_list") (N-2)
		 *   	GET    (xml)
		 *   images/general/[header,+] ("general") (N-3)
		 *   	GET    (bin)
		 *   	PUT    (bin)
		 *   
		 *   
		 *   images/[categories,+] ("normal_list") (N-2)
		 *   	GET    (xml)
		 *   images/[categories,+]/[1,+] ("normal") (N-3)
		 *   	GET    (bin)
		 *   	PUT    (bin)
		 *   	DELETE
		 *   	POST   (bin) (if image does not exists)
		 *   images/[categories,+]/[1,+]/[small,+] ("normal_resized") (N-4)
		 *   	GET    (bin)
		 *   images/[categories,+]/default ("display_list_of_langs") (N-3)
		 *   	GET    (xml)
		 *   images/[categories,+]/default/[en,+] ("normal_default_i18n")  (N-4)
		 *   	GET    (bin)
		 *   	POST   (bin) (if image does not exists)
		 *      PUT    (bin)
		 *      DELETE    (bin)
		 *   images/[categories,+]/default/[en,+]/[small,+] ("normal_default_i18n_resized")  (N-5)
		 *   
		 *   
		 *   	GET    (bin)
		 *   images/product ("product_list")  (N-2)
		 *   	GET    (xml) (list of image)
		 *   images/product/[1,+] ("product_description")  (N-3)
		 *   	GET    (xml) (legend, declinations, xlink to images/product/[1,+]/bin)
		 *   images/product/[1,+]/bin ("product_bin")  (N-4)
		 *   	GET    (bin)
		 *      POST   (bin) (if image does not exists)
		 *   images/product/[1,+]/[1,+] ("product_declination")  (N-4)
		 *   	GET    (bin)
		 *   	POST   (xml) (legend)
		 *   	PUT    (xml) (legend)
		 *      DELETE
		 *   images/product/[1,+]/[1,+]/bin ("product_declination_bin") (N-5)
		 *   	POST   (bin) (if image does not exists)
		 *   	GET    (bin)
		 *   	PUT    (bin)
		 *   images/product/[1,+]/[1,+]/[small,+] ("product_declination_resized") (N-5)
		 *   	GET    (bin)
		 *   images/product/default ("product_default") (N-3)
		 *   	GET    (bin)
		 *   images/product/default/[en,+] ("product_default_i18n") (N-4)
		 *   	GET    (bin)
		 *      POST   (bin)
		 *      PUT   (bin)
		 *      DELETE
		 *   images/product/default/[en,+]/[small,+] ("product_default_i18n_resized") (N-5)
		 * 		GET    (bin)
		 * 
		 * */
		
		// Pre configuration...
		if (count($this->_urlSegment) == 1)
			$this->_urlSegment[1] = '';
		if (count($this->_urlSegment) == 2)
			$this->_urlSegment[2] = '';
		if (count($this->_urlSegment) == 3)
			$this->_urlSegment[3] = '';
		if (count($this->_urlSegment) == 4)
			$this->_urlSegment[4] = '';
		if (count($this->_urlSegment) == 5)
			$this->_urlSegment[5] = '';
		
		$this->_imageType = $this->_urlSegment[1];
		
		switch ($this->_urlSegment[1])
		{
			// general images management : like header's logo, invoice logo, etc...
			case 'general':
				return $this->manageGeneralImages();
				break;
			// normal images management : like the most entity images (categories, manufacturers..)...
			case 'categories':
			case 'manufacturers':
			case 'suppliers':
			case 'stores':
				switch ($this->_urlSegment[1])
				{
					case 'categories':
						$directory = _PS_CAT_IMG_DIR_;
						break;
					case 'manufacturers':
						$directory = _PS_MANU_IMG_DIR_;
						break;
					case 'suppliers':
						$directory = _PS_SUPP_IMG_DIR_;
						break;
					case 'stores':
						$directory = _PS_STORE_IMG_DIR_;
						break;
				}
				return $this->manageDeclinatedImages($directory);
				break;
			
			// product image management : many image for one entity (product)
			case 'products':
				return $this->manageProductImages();
				break;
			
			// images root node management : many image for one entity (product)
			case '':
				$this->_xmlOutput .= '<image_types>'."\n";
				foreach ($this->_imageTypes as $imageTypeName => $imageType)
					$this->_xmlOutput .= '<'.$imageTypeName.' xlink:href="'.$this->_wsUrl.$this->_urlSegment[0].'/'.$imageTypeName.'" get="true" put="false" post="false" delete="false" head="true" upload_allowed_mimetypes="'.implode(', ', $this->_acceptedImgMimeTypes).'" />'."\n";
				$this->_xmlOutput .= '</image_types>'."\n";
				return true;
				break;
			
			default:
				$this->setErrorDidYouMean(400, 'Image of type "'.$this->_urlSegment[1].'" does not exists', $this->_urlSegment[1], array_keys($this->_imageTypes));
				return false;
		}
	}
	
	/**
	 * Management of general images
	 * 
	 * @return boolean
	 */
	private function manageGeneralImages()
	{
		$path = '';
		$alternative_path = '';
		switch ($this->_urlSegment[2])
		{
			// Set the image path on display in relation to the header image
			case 'header':
				if (in_array($this->_method, array('GET','HEAD','PUT')))
					$path = _PS_IMG_DIR_.'logo.jpg';
				else
				{
					$this->setError(405, 'This method is not allowed with general image resources.');
					return false;
				}
				break;
			
			// Set the image path on display in relation to the mail image
			case 'mail':
				if (in_array($this->_method, array('GET','HEAD','PUT')))
				{
					$path = _PS_IMG_DIR_.'logo_mail.jpg';
					$alternative_path = _PS_IMG_DIR_.'logo.jpg';
				}
				else
				{
					$this->setError(405, 'This method is not allowed with general image resources.');
					return false;
				}
				break;
			
			// Set the image path on display in relation to the invoice image
			case 'invoice':
				if (in_array($this->_method, array('GET','HEAD','PUT')))
				{
					$path = _PS_IMG_DIR_.'logo_invoice.jpg';
					$alternative_path = _PS_IMG_DIR_.'logo.jpg';
				}
				else
				{
					$this->setError(405, 'This method is not allowed with general image resources.');
					return false;
				}
				break;
			
			// Set the image path on display in relation to the icon store image
			case 'store_icon':
				if (in_array($this->_method, array('GET','HEAD','PUT')))
				{
					$path = _PS_IMG_DIR_.'logo_stores.gif';
					$this->_imgExtension = 'gif';
				}
				else
				{
					$this->setError(405, 'This method is not allowed with general image resources.');
					return false;
				}
				break;
			
			// List the general image types
			case '':
				$this->_xmlOutput .= '<general_image_types>'."\n";
				foreach ($this->_imageTypes['general'] as $generalImageTypeName => $generalImageType)
					$this->_xmlOutput .= '<'.$generalImageTypeName.' xlink:href="'.$this->_wsUrl.$this->_urlSegment[0].'/'.$this->_urlSegment[1].'/'.$generalImageTypeName.'" get="true" put="true" post="false" delete="false" head="true" upload_allowed_mimetypes="'.implode(', ', $this->_acceptedImgMimeTypes).'" />'."\n";
				$this->_xmlOutput .= '</general_image_types>'."\n";
				return true;
				break;
			
			// If the image type does not exist...
			default:
				$this->setErrorDidYouMean(400, 'General image of type "'.$this->_urlSegment[2].'" does not exists', $this->_urlSegment[2], array_keys($this->_imageTypes['general']));
				return false;
		}
		// The general image type is valid, now we try to do action in relation to the method
		switch($this->_method)
		{
			case 'GET':
			case 'HEAD':
				$this->_imgToDisplay = ($alternative_path != '' && file_exists($alternative_path)) ? $alternative_path : $path;
				return true;
				break;
			case 'PUT':
				if ($this->writePostedImageOnDisk($path, NULL, NULL))
				{
					$this->_imgToDisplay = $path;
					return true;
				}
				else
				{
					$this->setError(400, 'Error while copying image to the directory');
					return false;
				}
				break;
		}
	}
	
	/**
	 * Management of normal images (as categories, suppliers, manufacturers and stores)
	 * 
	 * @param string $directory the file path of the root of the images folder type
	 * @return boolean
	 */
	private function manageDeclinatedImages($directory)
	{
		$product = ($this->_imageType == 'products');
		$image_type = $this->_urlSegment[1];
		if ($product)
		{
			$image_size = $this->_urlSegment[4];
			$image_id = $this->_urlSegment[3];//TODO
		}
		
		/*
		 *ok    GET    (bin)
		 *ok images/product ("product_list")  (N-2)
		 *ok	GET    (xml) (list of image)
		 *ok images/product/[1,+] ("product_description")  (N-3)
		 *   	GET    (xml) (legend, declinations, xlink to images/product/[1,+]/bin)
		 *ok images/product/[1,+]/bin ("product_bin")  (N-4)
		 *ok 	GET    (bin)
		 *      POST   (bin) (if image does not exists)
		 *ok images/product/[1,+]/[1,+] ("product_declination")  (N-4)
		 *ok 	GET    (bin)
		 *   	POST   (xml) (legend)
		 *   	PUT    (xml) (legend)
		 *      DELETE
		 *ok images/product/[1,+]/[1,+]/bin ("product_declination_bin") (N-5)
		 *   	POST   (bin) (if image does not exists)
		 *ok 	GET    (bin)
		 *   	PUT    (bin)
		 *   images/product/[1,+]/[1,+]/[small,+] ("product_declination_resized") (N-5)
		 *ok 	GET    (bin)
		 *ok images/product/default ("product_default") (N-3)
		 *ok 	GET    (bin)
		 *ok images/product/default/[en,+] ("product_default_i18n") (N-4)
		 *ok 	GET    (bin)
		 *      POST   (bin)
		 *      PUT   (bin)
		 *      DELETE
		 *ok images/product/default/[en,+]/[small,+] ("product_default_i18n_resized") (N-5)
		 *ok	GET    (bin)
		 * 
		 * */
		
		
		// Get available image sizes for the current image type
		$normalImageSizes = ImageType::getImagesTypes($image_type);
		$normalImageSizeNames = array();
		foreach ($normalImageSizes as $normalImageSize)
			$normalImageSizeNames[] = $normalImageSize['name'];
		switch ($this->_urlSegment[2])
		{
			// Match the default images
			case 'default':
				$this->_defaultImage = true;
				// Get the language iso code list
				$langList = Language::getIsoIds(true);
				$langs = array();
				$defaultLang = Configuration::get('PS_LANG_DEFAULT');
				foreach ($langList as $lang)
				{
					if ($lang['id_lang'] == $defaultLang)
						$defaultLang = $lang['iso_code'];
					$langs[] = $lang['iso_code'];
				}
				
				
				// Display list of languages
				if($this->_urlSegment[3] == '' && $this->_method == 'GET')
				{
					$this->_xmlOutput .= '<languages>'."\n";
					foreach ($langList as $lang)
						$this->_xmlOutput .= '<language iso="'.$lang['iso_code'].'" xlink:href="'.$this->_wsUrl.'images/'.$image_type.'/default/'.$lang['iso_code'].'" get="true" put="true" post="true" delete="true" head="true" upload_allowed_mimetypes="'.implode(', ', $this->_acceptedImgMimeTypes).'" />'."\n";
					$this->_xmlOutput .= '</languages>'."\n";
					return true;
				}
				else
				{
					$lang_iso = $this->_urlSegment[3];
					$image_size = $this->_urlSegment[4];
					if ($image_size != '')
						$filename = $directory.$lang_iso.'-default-'.$image_size.'.jpg';
					else
						$filename = $directory.$lang_iso.'.jpg';
					$filename_exists = file_exists($filename);
					return $this->manageDeclinatedImagesCRUD($filename_exists, $filename, $normalImageSizes, $directory);//TODO
				}
				break;
			
			// Display the list of images
			case '':
				// Check if method is allowed
				if ($this->_method != 'GET')
				{
					$this->setError(405, 'This method is not allowed for listing category images.');
					return false;
				}
				$this->_xmlOutput .= '<image_types>'."\n";
				foreach ($normalImageSizes as $imageSize)
					$this->_xmlOutput .= '<image_type id="'.$imageSize['id_image_type'].'" name="'.$imageSize['name'].'" xlink:href="'.$this->_wsUrl.'image_types/'.$imageSize['id_image_type'].'" />'."\n";
				$this->_xmlOutput .= '</image_types>'."\n";
				$this->_xmlOutput .= '<images>'."\n";
				$nodes = scandir($directory);
				$lastId = 0;
				foreach ($nodes as $node)
					// avoid too much preg_match...
					if ($node != '.' && $node != '..' && $node != '.svn')
					{
						if ($product)
						{
							preg_match('/^(\d+)-(\d+)\.jpg*$/Ui', $node, $matches);
							if (isset($matches[1]) && $matches[1] != $lastId)
							{
								$lastId = $matches[1];
								$id = $matches[1];
								$this->_xmlOutput .= '<image id="'.$id.'" xlink:href="'.$this->_wsUrl.'images/'.$image_type.'/'.$id.'" />'."\n";
							}
						}
						else
						{
							preg_match('/^(\d+)\.jpg*$/Ui', $node, $matches);
							if (isset($matches[1]))
							{
								$id = $matches[1];
								$this->_xmlOutput .= '<image id="'.$id.'" xlink:href="'.$this->_wsUrl.'images/'.$image_type.'/'.$id.'" />'."\n";
							}
						}
					}
				$this->_xmlOutput .= '</images>'."\n";
				return true;
				break;
			
			default:
				// If id is detected
				$object_id = $this->_urlSegment[2];
				if (Validate::isUnsignedId($object_id))
				{
					// For the product case
					if ($product)
					{
						// Get available image ids
						$available_image_ids = array();
						$nodes = scandir($directory);
						foreach ($nodes as $node)
							// avoid too much preg_match...
							if ($node != '.' && $node != '..' && $node != '.svn')
							{
								preg_match('/^'.intval($object_id).'-(\d+)\.jpg*$/Ui', $node, $matches);
								if (isset($matches[1]))
									$available_image_ids[] = $matches[1];
							}
						/*
						if (!count($available_image_ids))
						{
							//$this->setError(400, 'This image id does not exist');
							d('TODO');
						}*/
						
						// If an image id is specified
						if ($this->_urlSegment[3] != '')
						{
							if ($this->_urlSegment[3] == 'bin')
							{
								if ($this->_method == 'POST')
								{
									$orig_filename = $directory.$object_id.'-'.$this->_productImageDeclinationId.'-'.$available_image_ids[0].'.jpg';//TODO get the default one
									$orig_filename_exists = file_exists($orig_filename);
									
								}
								else
								{
									$orig_filename = $directory.$object_id.'-'.$available_image_ids[0].'.jpg';//TODO get the default one
									$orig_filename_exists = file_exists($orig_filename);
								}
								return $this->manageDeclinatedImagesCRUD($orig_filename_exists, $orig_filename, $normalImageSizes, $directory);
							}
							elseif (!Validate::isUnsignedId($object_id) || !in_array($this->_urlSegment[3], $available_image_ids))
							{
								$this->setError(400, 'This image id does not exist');
								return false;
							}
							$image_id = $this->_urlSegment[3];
							$orig_filename = $directory.$object_id.'-'.$image_id.'.jpg';
							$image_size = $this->_urlSegment[4];
							$filename = $directory.$object_id.'-'.$image_id.'-'.$image_size.'.jpg';
						}
						else
						{
							if ($available_image_ids)
							{
								$this->_xmlOutput .= '<image id="'.$object_id.'">'."\n";
								foreach ($available_image_ids as $available_image_id)
									$this->_xmlOutput .= '<declination id="'.$available_image_id.'" xlink:href="'.$this->_wsUrl.'images/'.$image_type.'/'.$object_id.'/'.$available_image_id.'" />'."\n";
								$this->_xmlOutput .= '</image>'."\n";
								return true;
							}
							else
							{
								$this->setStatus(404);
								return false;
							}
						}
						
					}
					// for all other cases
					else
					{
						$orig_filename = $directory.$object_id.'.jpg';
						$image_size = $this->_urlSegment[3];
						$filename = $directory.$object_id.'-'.$image_size.'.jpg';
					}
					$orig_filename_exists = file_exists($orig_filename);
						
					// If a size was given try to display it
					if ($image_size != '')
					{
						// Check the given size
						if ($product && $image_size == 'bin')
						{
							$this->_imgToDisplay = $directory.$object_id.'-'.$image_id.'.jpg';
							return true;
						}
						elseif (!in_array($image_size, $normalImageSizeNames))
						{
							$this->setErrorDidYouMean(400, 'This image size does not exist', $image_size, $normalImageSizeNames);
							return false;
						}
						//d($filename);
						// Display the resized specific image
						if (file_exists($filename))
						{
							$this->_imgToDisplay = $filename;
							return true;
						}
						else
						{
							$this->setError(500, 'This image does not exist on disk');
							return false;
						}
					}
					// Management of the original image (GET, PUT, POST, DELETE)
					else
					{
						return $this->manageDeclinatedImagesCRUD($orig_filename_exists, $orig_filename, $normalImageSizes, $directory);
					}
				}
				else
				{
					$this->setError(400, 'The image id is invalid. Please set a valid id or the "default" value');
					return false;
				}
		}
	}
	
	private function manageProductImages()
	{
		// add a new declinated image to the product
		$max = 0;
		foreach (scandir(_PS_PROD_IMG_DIR_) as $dir)
		{
			$matches = array();
			preg_match('/^'.intval($this->_urlSegment[2]).'-(\d+)\.jpg*$/Ui', $dir, $matches);
			if (isset($matches[1]))
				$max = max($max, (int)($matches[1]));
		}
		$this->_productImageDeclinationId = $max++; 
		$this->manageDeclinatedImages(_PS_PROD_IMG_DIR_);
	}
	
	/**
	 * Management of normal images CRUD
	 * 
	 * @param boolean $filename_exists if the filename exists
	 * @param string $filename the image path
	 * @param array $imageSizes The
	 * @param string $directory
	 * @return boolean
	 */
	private function manageDeclinatedImagesCRUD($filename_exists, $filename, $imageSizes, $directory)
	{
		switch ($this->_method)
		{
			// Display the image
			case 'GET':
			case 'HEAD':
				if ($filename_exists)
					$this->_imgToDisplay = $filename;
				else
				{
					$this->setError(500, 'This image does not exist on disk');
					return false;
				}
				break;
			// Modify the image
			case 'PUT':
				if ($filename_exists)
					if ($this->writePostedImageOnDisk($filename, NULL, NULL, $imageSizes, $directory))
					{
						$this->_imgToDisplay = $filename;
						return true;
					}
					else
					{
						$this->setError(500, 'Unable to save this image.');
						return false;
					}
				else
				{
					$this->setError(500, 'This image does not exist on disk');
					return false;
				}
				break;
			// Delete the image
			case 'DELETE':
				if ($filename_exists)
					return $this->deleteImageOnDisk($filename, $imageSizes, $directory);
				else
				{
					$this->setError(500, 'This image does not exist on disk');
					return false;
				}
				break;
			// Add the image
			case 'POST':
				if ($filename_exists)
				{
					$this->setError(400, 'This image already exists. To modify it, please use the PUT method');
					return false;
				}
				else
				{
					if ($this->writePostedImageOnDisk($filename, NULL, NULL, $imageSizes, $directory))
					{
						$this->_imgToDisplay = $filename;
						return true;
					}
					else
					{
						$this->setError(500, 'Unable to save this image.');
						return false;
					}
				}
				break;
			default : 
				$this->setError(405, 'This method is not allowed.');
				return false;
		}
	}
	
	/**
	 * 	Delete the image on disk
	 * 
	 * @param string $filePath the image file path
	 * @param array $imageTypes The differents sizes
	 * @param string $parentPath The parent path
	 * @return boolean
	 */
	private function deleteImageOnDisk($filePath, $imageTypes = NULL, $parentPath = NULL)
	{
		$this->_outputEnabled = false;
		if (file_exists($filePath))
		{
			// delete image on disk
			@unlink($filePath);
			
			// Delete declinated image if needed
			if ($imageTypes)
			{
				foreach ($imageTypes as $imageType)
				{
					if ($this->_defaultImage)// TODO products images too !!
						$declination_path = $parentPath.$this->_urlSegment[3].'-default-'.$imageType['name'].'.jpg';
					else
						$declination_path = $parentPath.$this->_urlSegment[2].'-'.$imageType['name'].'.jpg';
					if (!@unlink($declination_path))
					{
						$this->setError(204);
						return false;
					}
				}
			}
			return true;
		}
		else
		{
			$this->setStatus(204);
			return false;
		}
	}
	
	/**
	 * Write the image on disk
	 * 
	 * @param string $basePath
	 * @param string $newPath
	 * @param int $destWidth
	 * @param int $destHeight
	 * @param array $imageTypes
	 * @param string $parentPath
	 * @return string
	 */
	private function writeImageOnDisk($basePath, $newPath, $destWidth = NULL, $destHeight = NULL, $imageTypes = NULL, $parentPath = NULL)
	{
		list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($basePath);
		if (!$sourceWidth)
		{
			$this->setError(400, 'Image width was null');
			return false;
		}
		if ($destWidth == NULL) $destWidth = $sourceWidth;
		if ($destHeight == NULL) $destHeight = $sourceHeight;
		switch ($type)
		{
			case 1:
				$sourceImage = imagecreatefromgif($basePath);
				break;
			case 3:
				$sourceImage = imagecreatefrompng($basePath);
				break;
			case 2:
			default:
				$sourceImage = imagecreatefromjpeg($basePath);
				break;
		}
	
		$widthDiff = $destWidth / $sourceWidth;
		$heightDiff = $destHeight / $sourceHeight;
		
		if ($widthDiff > 1 AND $heightDiff > 1)
		{
			$nextWidth = $sourceWidth;
			$nextHeight = $sourceHeight;
		}
		else
		{
			if ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 2 OR ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 AND $widthDiff > $heightDiff))
			{
				$nextHeight = $destHeight;
				$nextWidth = (int)(($sourceWidth * $nextHeight) / $sourceHeight);
				$destWidth = ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destWidth : $nextWidth);
			}
			else
			{
				$nextWidth = $destWidth;
				$nextHeight = (int)($sourceHeight * $destWidth / $sourceWidth);
				$destHeight = ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destHeight : $nextHeight);
			}
		}
		
		$borderWidth = (int)(($destWidth - $nextWidth) / 2);
		$borderHeight = (int)(($destHeight - $nextHeight) / 2);
		
		// Build the image
		if (
			!($destImage = imagecreatetruecolor($destWidth, $destHeight)) ||	
			!($white = imagecolorallocate($destImage, 255, 255, 255)) ||
			!imagefill($destImage, 0, 0, $white) ||
			!imagecopyresampled($destImage, $sourceImage, $borderWidth, $borderHeight, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight) ||
			!imagecolortransparent($destImage, $white)
		)
		{
			$this->setError(500, 'Unable to build the image "'.str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $newPath).'".');
			return false;
		}
			
		// Write it on disk
		$imaged = false;
		switch ($this->_imgExtension)
		{
			case 'gif':
				$imaged = imagegif($destImage, $newPath);
				break;
			case 'png':
				$imaged = imagepng($destImage, $newPath, 7);
				break;
			case 'jpeg':
			default:
				$imaged = imagejpeg($destImage, $newPath, 90);
				break;
		}
		imagedestroy($destImage);
		if (!$imaged)
		{
			$this->setError(500, 'Unable to write the image "'.str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $newPath).'".');
			return false;
		}
		
		// Write image declinations if present
		if ($imageTypes)
		{
			foreach ($imageTypes as $imageType)
			{
				if ($this->_defaultImage)
					$declination_path = $parentPath.$this->_urlSegment[3].'-default-'.$imageType['name'].'.jpg';
				else
				{
					if ($this->_imageType == 'products')
					{
						$declination_path = $parentPath.$this->_urlSegment[2].'-'.$this->_productImageDeclinationId.'-'.$imageType['name'].'.jpg';
					}
					else
						$declination_path = $parentPath.$this->_urlSegment[2].'-'.$imageType['name'].'.jpg';
				}
				if (!$this->writeImageOnDisk($basePath, $declination_path, $imageType['width'], $imageType['height']))
				{
					$this->setError(500, 'Unable to save the declination "'.$imageType['name'].'" of this image.');
					return false;
				}
			}
		}
		return !$this->hasErrors() ? $newPath : false;
	}
	
	/**
	 * Write the posted image on disk
	 * 
	 * @param string $sreceptionPath
	 * @param int $destWidth
	 * @param int $destHeight
	 * @param array $imageTypes
	 * @param string $parentPath
	 * @return boolean
	 */
	private function writePostedImageOnDisk($receptionPath, $destWidth = NULL, $destHeight = NULL, $imageTypes = NULL, $parentPath = NULL)
	{
		if ($this->_method == 'PUT')
		{
			if (isset($_FILES['image']['tmp_name']) AND $_FILES['image']['tmp_name'])
			{
				$file = $_FILES['image'];
				if ($file['size'] > $this->_imgMaxUploadSize)
				{
					$this->setError(400, 'The image size is too large (maximum allowed is '.($this->_imgMaxUploadSize/1000).' KB)');
					return false;
				}
				// Get mime content type
				$mime_type = false;
				if (Tools::isCallable('finfo_open'))
				{
					$const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
					$finfo = finfo_open($const);
					$mime_type = finfo_file($finfo, $file['tmp_name']);
					finfo_close($finfo);
				}
				elseif (Tools::isCallable('mime_content_type'))
					$mime_type = mime_content_type($file['tmp_name']);
				elseif (Tools::isCallable('exec'))
					$mime_type = trim(exec('file -b --mime-type '.escapeshellarg($file['tmp_name'])));
				if (empty($mime_type) || $mime_type == 'regular file')
					$mime_type = $file['type'];
				if (($pos = strpos($mime_type, ';')) !== false)
					$mime_type = substr($mime_type, 0, $pos);
				
				// Check mime content type
				if(!$mime_type || !in_array($mime_type, $this->_acceptedImgMimeTypes))
				{
					$this->setError(400, 'This type of image format not recognized, allowed formats are: '.implode('", "', $this->_acceptedImgMimeTypes));
					return false;
				}
				// Check error while uploading
				elseif ($file['error'])
				{
					$this->setError(400, 'Error while uploading image. Please change your server\'s settings');
					return false;
				}
				
				// Try to copy image file to a temporary file
				if (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName))
				{
					$this->setError(400, 'Error while copying image to the temporary directory');
					return false;
				}
				// Try to copy image file to the image directory
				else
				{
					return $this->writeImageOnDisk($tmpName, $receptionPath, $destWidth, $destHeight, $imageTypes, $parentPath);
				}
				unlink($tmpName);
			}
			else
			{
				$this->setError(400, 'Please set an "image" parameter with image data for value');
				return false;
			}
		}
		else
		{
			$this->setError(405, 'Method '.$this->_method.' is not allowed for an image resource');
			return false;
		}
	}
}