<?php
class AdminController extends Controller
{
	public $path;

	/** @var string Associated table name */
	public $table;

	/** @var string Object identifier inside the associated table */
	protected $identifier = false;

	/** @var string Tab name */
	public $className;

	/** @var array tabAccess */
	public $tabAccess;

	/** @var integer Tab id */
	public $id = -1;

	/** @var string Security token */
	public $token;

	/** @var string shop | group_shop */
	public $shopLinkType;

	/** @var string Default ORDER BY clause when $_orderBy is not defined */
	protected $_defaultOrderBy = false;

	public function __construct()
	{
		parent::__construct();
		$this->id = Tab::getIdFromClassName($this->className);
		$this->_conf = array(
			1 => $this->l('Deletion successful'), 2 => $this->l('Selection successfully deleted'),
			3 => $this->l('Creation successful'), 4 => $this->l('Update successful'),
			5 => $this->l('Status update successful'), 6 => $this->l('Settings update successful'),
			7 => $this->l('Image successfully deleted'), 8 => $this->l('Module downloaded successfully'),
			9 => $this->l('Thumbnails successfully regenerated'), 10 => $this->l('Message sent to the customer'),
			11 => $this->l('Comment added'), 12 => $this->l('Module installed successfully'),
			13 => $this->l('Module uninstalled successfully'), 14 => $this->l('Language successfully copied'),
			15 => $this->l('Translations successfully added'), 16 => $this->l('Module transplanted successfully to hook'),
			17 => $this->l('Module removed successfully from hook'), 18 => $this->l('Upload successful'),
			19 => $this->l('Duplication completed successfully'), 20 => $this->l('Translation added successfully but the language has not been created'),
			21 => $this->l('Module reset successfully'), 22 => $this->l('Module deleted successfully'),
			23 => $this->l('Localization pack imported successfully'), 24 => $this->l('Refund Successful'),
			25 => $this->l('Images successfully moved'),
		);
		if (!$this->identifier) $this->identifier = 'id_'.$this->table;
		if (!$this->_defaultOrderBy) $this->_defaultOrderBy = $this->identifier;
		$className = get_class($this);
		if ($className == 'AdminCategories' OR $className == 'AdminProducts')
			$className = 'AdminCatalog';
		$this->token = Tools::getAdminToken($className.(int)$this->id.(int)$this->context->employee->id);

		if (!Shop::isMultiShopActivated())
			$this->shopLinkType = '';
	}

	/**
	 * Check rights to view the current tab
	 *
	 * @return boolean
	 */
	public function viewAccess($disable = false)
	{
		if ($disable)
			return true;
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

		if ($this->tabAccess['view'] === '1')
			return true;
		return false;
	}

	/**
	 * Check for security token
	 */
	public function checkToken()
	{
		$token = Tools::getValue('token');
		return (!empty($token) AND $token === $this->token);
	}

	public function run()
	{
		$this->checkAccess();
		$this->init();
		$this->postProcess();
		$this->setMedia();
		$this->initHeader();
		$this->initFooter();
		//$adminObj->displayConf();
		//$adminObj->displayErrors();
		$this->display();
	}

	/**
	 * Check if the token is valid, else display a warning page
	 */
	public function checkAccess()
	{
		if (!$this->checkToken())
		{
			// If this is an XSS attempt, then we should only display a simple, secure page
			// ${1} in the replacement string of the regexp is required, because the token may begin with a number and mix up with it (e.g. $17)
			$url = preg_replace('/([&?]token=)[^&]*(&.*)?$/', '${1}'.$this->token.'$2', $_SERVER['REQUEST_URI']);
			if (false === strpos($url, '?token=') AND false === strpos($url, '&token='))
				$url .= '&token='.$this->token;

			$this->context->smarty->assign('url', htmlentities($url));
			$this->context->smarty->display(_PS_ADMIN_DIR_.'/themes/invalid_token.tpl');
			die;
		}
	}

	public function display()
	{

	}

	/**
	 * Assign smarty variables for the header
	 */
	public function initHeader()
	{
		// Shop context
		if (Shop::isMultiShopActivated())
		{
			if (Context::shop() == Shop::CONTEXT_ALL)
				$shop_context = 'all';
			elseif (Context::shop() == Shop::CONTEXT_GROUP)
			{
				$shop_context = 'group';
				$shop_name = $this->context->shop->getGroup()->name;
			}
			else
			{
				$shop_context = 'shop';
				$shop_name = $this->context->shop->name;
			}
			$this->context->smarty->assign(array(
				'shop_name' => $shop_name,
				'shop_context' => $shop_context,
			));
				$youEditFieldFor = sprintf($this->l('A modification of this field will be applied for the shop %s'), '<b>'.Context::getContext()->shop->name.'</b>');
		}

			// Multishop
		$is_multishop = Shop::isMultiShopActivated();// && Context::shop() != Shop::CONTEXT_ALL;
		/*if ($is_multishop)
		{
			if (Context::shop() == Shop::CONTEXT_GROUP)
			{
				$shop_context = 'group';
				$shop_name = $this->context->shop->getGroup()->name;
			}
			elseif (Context::shop() == Shop::CONTEXT_SHOP)
			{
				$shop_context = 'shop';
				$shop_name = $this->context->shop->name;
			}*/



		// Quick access
		$quick_access = QuickAccess::getQuickAccesses($this->context->language->id);
		foreach ($quick_access AS $index => $quick)
		{
			preg_match('/tab=(.+)(&.+)?$/', $quick['link'], $adminTab);
			if (isset($adminTab[1]))
			{
				if (strpos($adminTab[1], '&'))
					$adminTab[1] = substr($adminTab[1], 0, strpos($adminTab[1], '&'));
				$quick_access[$index]['link'] .= '&token='.Tools::getAdminToken($adminTab[1].(int)(Tab::getIdFromClassName($adminTab[1])).(int)($this->context->employee->id));
			}
		}

		// Tab list
		$tabs = Tab::getTabs($this->context->language->id, 0);
		foreach ($tabs AS $index => $tab)
		{
			if (Tab::checkTabRights($tab['id_tab']) === true)
			{
				$img_exists_cache = Tools::file_exists_cache(_PS_ADMIN_DIR_.'/themes/'.$this->context->employee->bo_theme.'/img/t/'.$tab['class_name'].'.gif');
				$img = ($img_exists_cache ? 'themes/'.Context::getContext()->employee->bo_theme.'/img/' : _PS_IMG_).'t/'.$tab['class_name'].'.gif';

				if (trim($tab['module']) != '')
					$img = _MODULE_DIR_.$tab['module'].'/'.$tab['class_name'].'.gif';

				$tabs[$index]['current'] = ($tab['class_name'] == $this->className) || (Tab::getCurrentParentId() == $tab['id_tab']);
				$tabs[$index]['img'] = $img;
				$tabs[$index]['token'] = Tools::getAdminToken($tab['class_name'].(int)($tab['id_tab']).(int)$this->context->employee->id);

				$sub_tabs = Tab::getTabs($this->context->language->id, $tab['id_tab']);
				foreach ($sub_tabs AS $index2 => $sub_tab)
				{
					if (Tab::checkTabRights($sub_tab) === true)
					{
						$sub_tabs[$index2]['token'] = Tools::getAdminTokenLite($sub_tab['class_name']);
					}
					else
						unset($sub_tabs[$index2]);
				}
				$tabs[$index]['sub_tabs'] = $sub_tabs;
			}
			else
				unset($tabs[$index]);
		}
		// Breadcrumbs
		$home_token = Tools::getAdminToken('AdminHome'.intval(Tab::getIdFromClassName('AdminHome')).(int)$this->context->employee->id);

		$tabs_breadcrumb = array();
		$tabs_breadcrumb = recursiveTab($this->id, $tabs_breadcrumb);
		$tabs_breadcrumb = array_reverse($tabs_breadcrumb);

		foreach ($tabs_breadcrumb AS $key => $item)
		for ($i = 0; $i < (count($tabs_breadcrumb) - 1); $i++)
			$tabs_breadcrumb[$key]['token'] = Tools::getAdminToken($item['class_name'].intval($item['id_tab']).(int)$this->context->employee->id);

		/* Hooks are volontary out the initialize array (need those variables already assigned) */
		$this->context->smarty->assign(array(
			'img_dir' => _PS_IMG_,
			'iso' => $this->context->language->iso_code,
			'class_name' => $this->className,
			'iso_user' => $this->context->language->id,
			'country_iso_code' => $this->context->country->iso_code,
			'version' => _PS_VERSION_,
			'help_box' => Configuration::get('PS_HELPBOX'),
			'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
			'brightness' => Tools::getBrightness(empty($this->context->employee->bo_color) ? '#FFFFFF' : $this->context->employee->bo_color) < 128 ? 'white' : '#383838',
			'edit_field' => isset($youEditFieldFor) ? $youEditFieldFor : '\'\'',
			'lang_iso' => $this->context->language->iso_code,
			'link' => $this->context->link,
			'bo_color' => isset($this->context->employee->bo_color) ? Tools::htmlentitiesUTF8($this->context->employee->bo_color) : null,
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS'),
			'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS'),
			'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES'),
			'token_admin_orders' => Tools::getAdminTokenLite('AdminOrders'),
			'token_admin_customers' => Tools::getAdminTokenLite('AdminCustomers'),
			'token_admin_employees' => Tools::getAdminTokenLite('AdminEmployees'),
			'token_admin_search' => Tools::getAdminTokenLite('AdminSearch'),
			'first_name' => Tools::substr($this->context->employee->firstname, 0, 1),
			'last_name' => htmlentities($this->context->employee->lastname, ENT_COMPAT, 'UTF-8'),
			'base_url' => $this->context->shop->getBaseURL(),
			'employee' => $this->context->employee,
			'search_type' => Tools::getValue('bo_search_type'),
			'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
			'quick_access' => $quick_access,
			'multi_shop' => Shop::isMultiShopActivated(),
			'shop_list' => (Shop::isMultiShopActivated() ? generateShopList() : null), //@TODO refacto
			'tab' => $tab,
			'current_parent_id' => (int)Tab::getCurrentParentId(),
			'tabs' => $tabs,
			'install_dir_exists' => file_exists(_PS_ADMIN_DIR_.'/../install'),
			'home_token' => $home_token,
			'tabs_breadcrumb' => $tabs_breadcrumb,
			'is_multishop' => $is_multishop,

		));
		$this->context->smarty->assign(array(
			'HOOK_HEADER' => Module::hookExec('backOfficeHeader'),
			'HOOK_TOP' => Module::hookExec('backOfficeTop'),
		));

		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));
		//$this->context->smarty->display(_PS_ADMIN_DIR_.'/themes/header.tpl');
	}

	/**
	 * Assign smarty variables for the page main content
	 */
	public function initContent()
	{
	}

	/**
	 * Assign smarty variables for the footer
	 */
	public function initFooter()
	{
		$this->context->smarty->assign(array(
			'ps_version' => _PS_VERSION_,
			'end_time' => number_format(microtime(true) - $this->timerStart, 3, '.', ''),
			'iso_is_fr' => strtoupper($this->context->language->iso_code) == 'FR',
		));

		$this->context->smarty->assign(array(
			'HOOK_FOOTER' => Module::hookExec('backOfficeFooter'),
		));
	}

	public function setMedia()
	{
		$this->addCSS(_PS_JS_DIR_.'jquery/datepicker/datepicker.css', 'all');
		$this->addCSS(_PS_CSS_DIR_.'admin.css', 'all');
		$this->addCSS(_PS_CSS_DIR_.'jquery.cluetip.css', 'all');

		$this->addCSS($this->path.'/themes/'.'default/admin.css', 'all');
		if ($this->context->language->is_rtl)
			$this->addCSS(_THEME_CSS_DIR_.'rtl.css');

		$this->addJS(_PS_JS_DIR_.'jquery/jquery-1.4.4.min.js');
		$this->addJS(_PS_JS_DIR_.'jquery/jquery.hoverIntent.minified.js');
		$this->addJS(_PS_JS_DIR_.'jquery/jquery.cluetip.js');
		$this->addJS(_PS_JS_DIR_.'admin.js');
		$this->addJS(_PS_JS_DIR_.'toggle.js');
		$this->addJS(_PS_JS_DIR_.'tools.js');
		$this->addJS(_PS_JS_DIR_.'ajax.js');
		$this->addJS(_PS_JS_DIR_.'notifications.js');
	}

	public static function translate($string, $class, $addslashes = FALSE, $htmlentities = TRUE)
	{
		$class = strtolower($class);
		// if the class is extended by a module, use modules/[module_name]/xx.php lang file
		//$currentClass = get_class($this);
		if(false AND Module::getModuleNameFromClass($class))
		{
			$string = str_replace('\'', '\\\'', $string);
			return Module::findTranslation(Module::$classInModule[$class], $string, $class);
		}
		global $_LANGADM;
		$_LANGADM = array_change_key_case($_LANGADM);

        //if ($class == __CLASS__)
        //        $class = 'AdminTab';

		$key = md5(str_replace('\'', '\\\'', $string));

		$str = (key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : ((key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : $string);
		$str = $htmlentities ? htmlentities($str, ENT_QUOTES, 'utf-8') : $str;
		return str_replace('"', '&quot;', ($addslashes ? addslashes($str) : stripslashes($str)));
	}
	/**
	 * use translations files to replace english expression.
	 *
	 * @param mixed $string term or expression in english
	 * @param string $class
	 * @param boolan $addslashes if set to true, the return value will pass through addslashes(). Otherwise, stripslashes().
	 * @param boolean $htmlentities if set to true(default), the return value will pass through htmlentities($string, ENT_QUOTES, 'utf-8')
	 * @return string the translation if available, or the english default text.
	 */
	protected function l($string, $class = 'AdminTab', $addslashes = FALSE, $htmlentities = TRUE)
	{
		$class = get_class($this);
		return self::translate($string, $class, $addslashes, $htmlentities);
	}

	public function init()
	{
		ob_start();
		$this->timerStart = microtime(true);

		if (isset($_GET['logout']))
			$this->context->employee->logout();

		if (!isset($this->context->employee) || !$this->context->employee->isLoggedBack())
			Tools::redirectAdmin('login.php?redirect='.$_SERVER['REQUEST_URI']);

		// Set current index
		$currentIndex = $_SERVER['SCRIPT_NAME'].(($tab = Tools::getValue('tab')) ? '?tab='.$tab : '');
		if ($back = Tools::getValue('back'))
			$currentIndex .= '&back='.urlencode($back);
		AdminTab::$currentIndex = $currentIndex;

		$iso = $this->context->language->iso_code;
		include(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');
		include(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');
		include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');

		/* Server Params */
		$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$protocol_content = (isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$this->context->link = $link;
		//define('_PS_BASE_URL_', Tools::getShopDomain(true));
		//define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

		/*$path = _PS_ADMIN_DIR_.'/themes/';
		if (empty($this->context->employee->bo_theme) OR !file_exists($path.$this->context->employee->bo_theme.'/admin.css'))
		{
			if (file_exists($path.'oldschool/admin.css'))
				$this->context->employee->bo_theme = 'oldschool';
			elseif (file_exists($path.'origins/admin.css'))
				$this->context->employee->bo_theme = 'origins';
			else
				foreach (scandir($path) as $theme)
					if ($theme[0] != '.' AND file_exists($path.$theme.'/admin.css'))
					{
						$employee->bo_theme = $theme;
						break;
					}
			$this->context->employee->update();
		}*/

		// Change shop context ?
		if (Shop::isMultiShopActivated() && Tools::getValue('setShopContext') !== false)
		{
			$this->context->cookie->shopContext = Tools::getValue('setShopContext');
			$url = parse_url($_SERVER['REQUEST_URI']);
			$query = (isset($url['query'])) ? $url['query'] : '';
			parse_str($query, $parseQuery);
			unset($parseQuery['setShopContext']);
			Tools::redirectAdmin($url['path'] . '?' . http_build_query($parseQuery));
		}

		$shopID = '';
		if ($this->context->cookie->shopContext)
		{
			$split = explode('-', $this->context->cookie->shopContext);
			if (count($split) == 2 && $split[0] == 's')
				$shopID = (int)$split[1];
		}
		$this->context->shop = new Shop($shopID);

		/* Filter memorization */
		if (isset($_POST) AND !empty($_POST) AND isset($this->table))
			foreach ($_POST AS $key => $value)
				if (is_array($this->table))
				{
					foreach ($this->table AS $table)
						if (strncmp($key, $table.'Filter_', 7) === 0 OR strncmp($key, 'submitFilter', 12) === 0)
							$this->context->cookie->$key = !is_array($value) ? $value : serialize($value);
				}
				elseif (strncmp($key, $this->table.'Filter_', 7) === 0 OR strncmp($key, 'submitFilter', 12) === 0)
					$this->context->cookie->$key = !is_array($value) ? $value : serialize($value);

		if (isset($_GET) AND !empty($_GET) AND isset($this->table))
			foreach ($_GET AS $key => $value)
				if (is_array($this->table))
				{
					foreach ($this->table AS $table)
						if (strncmp($key, $table.'OrderBy', 7) === 0 OR strncmp($key, $table.'Orderway', 8) === 0)
							$this->context->cookie->$key = $value;
				}
				elseif (strncmp($key, $this->table.'OrderBy', 7) === 0 OR strncmp($key, $this->table.'Orderway', 12) === 0)
					$this->context->cookie->$key = $value;
	}

	public function displayErrors()
	{
		p($this->_errors);
	}

	public function postProcess()
	{

	}

}