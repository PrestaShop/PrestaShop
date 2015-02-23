<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
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
	 * @var array list of php error
	 */
	public static $php_errors = array();

	/**
	 * @var bool check if header will be displayed
	 */
	protected $display_header;

	/**
	 * @var string template name for page content
	 */
	protected $template;

	/**
	 * @var string check if footer will be displayed
	 */
	protected $display_footer;

	/**
	 * @var string check if only content will be displayed
	 */
	protected $content_only = false;

	/**
	 * @var bool If ajax parameter is detected in request, set this flag to true
	 */
	public $ajax = false;
	protected $json = false;
	protected $status = '';

	protected $redirect_after = null;

	public $controller_type;
	public $php_self;
	/**
	 * check that the controller is available for the current user/visitor
	 */
	abstract public function checkAccess();

	/**
	 * check that the current user/visitor has valid view permissions
	 */
	abstract public function viewAccess();

	/**
	 * Initialize the page
	 */
	public function init()
	{
		if (_PS_MODE_DEV_ && $this->controller_type == 'admin')
			set_error_handler(array(__CLASS__, 'myErrorHandler'));
		if (!defined('_PS_BASE_URL_'))
			define('_PS_BASE_URL_', Tools::getShopDomain(true));
		if (!defined('_PS_BASE_URL_SSL_'))
			define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
	}

	/**
	 * Do the page treatment : post process, ajax process, etc.
	 */
	abstract public function postProcess();

	/**
	 * Display page view
	 */
	abstract public function display();

	/**
	 * Redirect after process if no error
	 */
	abstract protected function redirect();

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
		if (is_null($this->display_header))
			$this->display_header = true;

		if (is_null($this->display_footer))
			$this->display_footer = true;

		$this->context = Context::getContext();
		$this->context->controller = $this;
		// Usage of ajax parameter is deprecated
		$this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');

		if (!headers_sent()
			&& isset($_SERVER['HTTP_USER_AGENT'])
			&& (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
			|| strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false))
			header('X-UA-Compatible: IE=edge,chrome=1');
	}

	/**
	 * Start controller process (this method shouldn't be overriden !)
	 */
	public function run()
	{
		$this->init();
		if ($this->checkAccess())
		{
			// setMedia MUST be called before postProcess
			if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
				$this->setMedia();

			// postProcess handles ajaxProcess
			$this->postProcess();

			if (!empty($this->redirect_after))
				$this->redirect();

			if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className)))
				$this->initHeader();

			if ($this->viewAccess())
				$this->initContent();
			else
				$this->errors[] = Tools::displayError('Access denied.');

			if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className)))
				$this->initFooter();

			// default behavior for ajax process is to use $_POST[action] or $_GET[action]
			// then using displayAjax[action]
			if ($this->ajax)
			{
				$action = Tools::toCamelCase(Tools::getValue('action'), true);
				if (!empty($action) && method_exists($this, 'displayAjax'.$action))
					$this->{'displayAjax'.$action}();
				elseif (method_exists($this, 'displayAjax'))
					$this->displayAjax();
			}
			else
				$this->display();
		}
		else
		{
			$this->initCursedPage();
			$this->smartyOutputContent($this->layout);
		}
	}

	public function displayHeader($display = true)
	{
		$this->display_header = $display;
	}

	public function displayFooter($display = true)
	{
		$this->display_footer = $display;
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
	 * Assign smarty variables when access is forbidden
	 */
	abstract public function initCursedPage();

	/**
	 * Assign smarty variables for the page footer
	 */
	abstract public function initFooter();

	/**
	 * Add a new stylesheet in page header.
	 *
	 * @param mixed $css_uri Path to css file, or list of css files like this : array(array(uri => media_type), ...)
	 * @param string $css_media_type
	 * @param integer $offset
	 * @param bool $check_path
	 * @return true
	 */
	public function addCSS($css_uri, $css_media_type = 'all', $offset = null, $check_path = true)
	{
		if (!is_array($css_uri))
			$css_uri = array($css_uri);

		foreach ($css_uri as $css_file => $media)
		{
				if (is_string($css_file) && strlen($css_file) > 1)
				{
					if ($check_path)
						$css_path = Media::getCSSPath($css_file, $media);
					else
						$css_path = array($css_file => $media);
				}
				else
				{
					if ($check_path)
						$css_path = Media::getCSSPath($media, $css_media_type);
					else
						$css_path = array($media => $css_media_type);
				}

			$key = is_array($css_path) ? key($css_path) : $css_path;
			if ($css_path && (!isset($this->css_files[$key]) || ($this->css_files[$key] != reset($css_path))))
			{
				$size = count($this->css_files);
				if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset))
					$offset = $size;

				$this->css_files = array_merge(array_slice($this->css_files, 0, $offset), $css_path, array_slice($this->css_files, $offset));
			}
		}
	}

	public function removeCSS($css_uri, $css_media_type = 'all', $check_path = true)
	{
		if (!is_array($css_uri))
			$css_uri = array($css_uri);

		foreach ($css_uri as $css_file => $media)
		{
				if (is_string($css_file) && strlen($css_file) > 1)
				{
					if ($check_path)
						$css_path = Media::getCSSPath($css_file, $media);
					else
						$css_path = array($css_file => $media);
				}
				else
				{
					if ($check_path)
						$css_path = Media::getCSSPath($media, $css_media_type);
					else
						$css_path = array($media => $css_media_type);
				}

			if ($css_path && isset($this->css_files[key($css_path)]) && ($this->css_files[key($css_path)] == reset($css_path)))
				unset($this->css_files[key($css_path)]);
		}
	}

	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @param bool $check_path
	 * @return void
	 */
	public function addJS($js_uri, $check_path = true)
	{
		if (is_array($js_uri))
			foreach ($js_uri as $js_file)
			{
				$js_path = $js_file;
				if ($check_path)
					$js_path = Media::getJSPath($js_file);

				$key = is_array($js_path) ? key($js_path) : $js_path;
				if ($js_path && !in_array($js_path, $this->js_files))
					$this->js_files[] = $js_path;
			}
		else
		{
			$js_path = $js_uri;
			if ($check_path)
				$js_path = Media::getJSPath($js_uri);

			if ($js_path && !in_array($js_path, $this->js_files))
				$this->js_files[] = $js_path;
		}
	}

	public function removeJS($js_uri, $check_path = true)
	{
		if (is_array($js_uri))
			foreach ($js_uri as $js_file)
			{
				$js_path = $js_file;
				if ($check_path)
					$js_path = Media::getJSPath($js_file);
				if ($js_path && in_array($js_path, $this->js_files))
					unset($this->js_files[array_search($js_path, $this->js_files)]);
			}
		else
		{
			$js_path = $js_uri;
			if ($check_path)
				$js_path = Media::getJSPath($js_uri);

			if ($js_path)
				unset($this->js_files[array_search($js_path, $this->js_files)]);
		}
	}

	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJquery($version = null, $folder = null, $minifier = true)
	{
		$this->addJS(Media::getJqueryPath($version, $folder, $minifier), false);
	}

	/**
	 * Add a new javascript file in page header.
	 *
	 * @param mixed $js_uri
	 * @return void
	 */
	public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
	{
		$ui_path = array();
		if (!is_array($component))
			$component = array($component);

		foreach ($component as $ui)
		{
			$ui_path = Media::getJqueryUIPath($ui, $theme, $check_dependencies);
			$this->addCSS($ui_path['css'], 'all', false);
			$this->addJS($ui_path['js'], false);
		}
	}

	/**
	 * Add a new javascript file in page header.
	 *
	 * @param      $name
	 * @param null $folder
	 * @param bool $css
	 */
	public function addJqueryPlugin($name, $folder = null, $css = true)
	{
		if (!is_array($name))
			$name = array($name);
		if (is_array($name))
		{
			foreach ($name as $plugin)
			{
				$plugin_path = Media::getJqueryPluginPath($plugin, $folder);

				if (!empty($plugin_path['js']))
					$this->addJS($plugin_path['js'], false);
				if ($css && !empty($plugin_path['css']))
					$this->addCSS(key($plugin_path['css']), 'all', null, false);
			}
		}
	}

	/**
	 * @since 1.5
	 * @return bool return true if Controller is called from XmlHttpRequest
	 */
	public function isXmlHttpRequest()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	protected function smartyOutputContent($content)
	{
		$this->context->cookie->write();

		$js_tag = 'js_def';
		$this->context->smarty->assign($js_tag, $js_tag);

		if (is_array($content))
			foreach ($content as $tpl)
				$html = $this->context->smarty->fetch($tpl);
		else
			$html = $this->context->smarty->fetch($content);

		$html = trim($html);

		if ($this->controller_type == 'front' && !empty($html) && $this->getLayout())
		{
			$live_edit_content = '';
			if (!$this->useMobileTheme() && $this->checkLiveEditAccess())
				$live_edit_content = $this->getLiveEditFooter();

			$dom_available = extension_loaded('dom') ? true : false;
			$defer = (bool)Configuration::get('PS_JS_DEFER');

			if ($defer && $dom_available)
				$html = Media::deferInlineScripts($html);
			$html = trim(str_replace(array('</body>', '</html>'), '', $html))."\n";

			$this->context->smarty->assign(array(
				$js_tag => Media::getJsDef(),
				'js_files' =>  $defer ? array_unique($this->js_files) : array(),
				'js_inline' => ($defer && $dom_available) ? Media::getInlineScript() : array()
			));

			$javascript = $this->context->smarty->fetch(_PS_ALL_THEMES_DIR_.'javascript.tpl');
			echo ($defer ? $html.$javascript : preg_replace('/(?<!\$)'.$js_tag.'/', $javascript, $html)).$live_edit_content.((!isset($this->ajax) || ! $this->ajax) ? '</body></html>' : '');
		}
		else
			echo $html;
	}

	protected function isCached($template, $cacheId = null, $compileId = null)
	{
		Tools::enableCache();
		$res = $this->context->smarty->isCached($template, $cacheId, $compileId);
		Tools::restoreCacheSettings();
		return $res;
	}

	public static function myErrorHandler($errno, $errstr, $errfile, $errline)
	{
		if (error_reporting() === 0)
			return false;
		switch ($errno)
		{
			case E_USER_ERROR:
			case E_ERROR:
				die('Fatal error: '.$errstr.' in '.$errfile.' on line '.$errline);
			break;
			case E_USER_WARNING:
			case E_WARNING:
				$type = 'Warning';
			break;
			case E_USER_NOTICE:
			case E_NOTICE:
				$type = 'Notice';
			break;
			default:
				$type = 'Unknow error';
			break;
		}

		Controller::$php_errors[] = array(
			'type' => $type,
			'errline' => (int)$errline,
			'errfile' => str_replace('\\', '\\\\', $errfile), // Hack for Windows paths
			'errno' => (int)$errno,
			'errstr' => $errstr
		);
		Context::getContext()->smarty->assign('php_errors', Controller::$php_errors);
		return true;
	}

	protected function ajaxDie($value = null, $controller = null, $method = null)
	{
		if ($controller === null)
			$controller = get_class($this);

		if ($method === null)
		{
			$bt = debug_backtrace();
			$method = $bt[1]['function'];
		}

		Hook::exec('actionBeforeAjaxDie', array('controller' => $controller, 'method' => $method, 'value' => $value));
		Hook::exec('actionBeforeAjaxDie'.$controller.$method, array('value' => $value));

		die($value);
	}
}
