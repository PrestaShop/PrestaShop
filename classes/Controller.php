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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
abstract class ControllerCore
{
	/**
	 * @var Context
	 */
	protected $context;

	/**
	 * @var array list of css files
	 */
	public $css_files = array();

	/**
	 * @var array list of javascript files
	 */
	public $js_files = array();

	/**
	 * @var bool check if header will be displayed
	 */
	protected $displayHeader = false;

	/**
	 * @var string template name for page content
	 */
	protected $template;

	/**
	 * @var string check if footer will be displayed
	 */
	protected $displayFooter = false;

	/**
	 * @var bool If ajax parameter is detected in request, set this flag to true
	 */
	protected $ajax = false;

	/**
	 * Initialize the page
	 */
	abstract public function init();

	/**
	 * Do the page treatment : post process, ajax process, etc.
	 */
	abstract public function postProcess();

	/**
	 * Display page view
	 */
	abstract public function display();

	/**
	 * Set default media list for controller
	 */
	abstract public function setMedia();

	/**
	 * Get an instance of a controller
	 *
	 * @param string $class_name
	 * @param bool $auth
	 * @param bool $ssl
	 */
	public static function getController($class_name, $auth = false, $ssl = false)
	{
		return new $class_name($auth, $ssl);
	}

	public function __construct()
	{
		$this->displayHeader(true);
		$this->displayFooter(true);
		$this->context = Context::getContext();
		$this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');
	}

	/**
	 * Start controller process (this method shouldn't be overriden !)
	 */
	public function run()
	{
		$this->init();
		$this->postProcess();
		if ($this->displayHeader)
		{
			$this->setMedia();
			$this->initHeader();
		}
		$this->initContent();
		if ($this->displayFooter)
			$this->initFooter();

		if ($this->ajax)
			$this->displayAjax();
		else
			$this->display();
	}

	public function displayHeader($display = true)
	{
		$this->displayHeader = $display;
	}

	public function displayFooter($display = true)
	{
		$this->displayFooter = $display;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
	}
	
	/**
	 * Assign smarty variables for the page header
	 */
	abstract public function initHeader();
	
	/**
	 * Assign smarty variables for the page main content
	 */
	abstract public function initContent();

	/**
	 * Assign smarty variables for the page footer
	 */
	abstract public function initFooter();
	
	/**
	 * Add a new stylesheet in page header.
	 *
	 * @param mixed $css_uri Path to css file, or list of css files like this : array(array(uri => media_type), ...)
	 * @param string $css_media_type
	 * @return true
	 */
	public function addCSS($css_uri, $css_media_type = 'all')
	{
		if (is_array($css_uri))
		{
			foreach ($css_uri as $file => $media_type)
				self::addCSS($file, $media_type);
			return true;
		}

		// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
		$url_data = parse_url($css_uri);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);

		// check if css files exists
		if (!file_exists($file_uri))
			return true;

		// adding file to the big array...
		$this->css_files[$css_uri] = $css_media_type;

		return true;
	}

	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJS($js_uri)
	{
		if (is_array($js_uri))
		{
			foreach ($js_uri as $file)
				self::addJS($file);
			return true;
		}

		if (in_array($js_uri, $this->js_files))
			return true;

		// remove PS_BASE_URI on _PS_ROOT_DIR_ for the following
		$url_data = parse_url($js_uri);
		$file_uri = _PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, $url_data['path']);

		// check if js files exists
		if (!preg_match('/^http(s?):\/\//i', $file_uri) && !file_exists($file_uri))
			return true;

		// adding file to the big array...
		$this->js_files[] = $js_uri;

		return true;
	}
}
