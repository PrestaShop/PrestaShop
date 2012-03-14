<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7445 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminMetaControllerCore extends AdminController
{
	public $table = 'meta';
	public $className = 'Meta';
	public $lang = true;
	protected $toolbar_scroll = false;

	public function __construct()
	{
		parent::__construct();

		$this->ht_file = _PS_ROOT_DIR_.'/.htaccess';
		$this->rb_file = _PS_ROOT_DIR_.'/robots.txt';
		$this->sm_file = _PS_ROOT_DIR_.'/sitemap.xml';
		$this->rb_data = $this->getRobotsContent();

		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldsDisplay = array(
			'id_meta' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'page' => array('title' => $this->l('Page'), 'width' => 120),
			'title' => array('title' => $this->l('Title'), 'width' => 120),
			'url_rewrite' => array('title' => $this->l('Friendly URL'), 'width' => 120)
		);
		$this->_group = 'GROUP BY a.id_meta';

		// Options to generate friendly urls
		$mod_rewrite = Tools::modRewriteActive();
		$general_fields = array(
			'PS_REWRITING_SETTINGS' => array(
				'title' => $this->l('Friendly URL'),
				'desc' => ($mod_rewrite ? $this->l('Enable only if your server allows URL rewriting (recommended)') : ''),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'rewriting_settings',
				'mod_rewrite' => $mod_rewrite
			),
			'PS_CANONICAL_REDIRECT' => array(
				'title' => $this->l('Automatically redirect to Canonical URL'),
				'desc' => $this->l('Recommended, but your theme must be compliant'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool'
			),
		);

		$url_description = '';
		if ($this->checkConfiguration($this->ht_file))
			$general_fields['PS_HTACCESS_DISABLE_MULTIVIEWS'] = array(
				'title' => $this->l('Disable apache multiviews'),
				'desc' => $this->l('Enable this option only if you have problems with URL rewriting on some pages.'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			);
		else
		{
			$url_description = $this->l('Before being able to use this tool, you need to:');
			$url_description .= '<br />- '.$this->l('create a blank .htaccess in your root directory');
			$url_description .= '<br />- '.$this->l('give it write permissions (CHMOD 666 on Unix system)');
		}

		// Options to generate robot.txt
		$robots_description = $this->l('Your robots.txt file MUST be in your website\'s root directory and nowhere else.');
		$robots_description .= '<br />'.$this->l('e.g. http://www.yoursite.com/robots.txt');
		if ($this->checkConfiguration($this->rb_file))
		{
			$robots_description .= '<br />'.$this->l('Generate your "robots.txt" file by clicking on the following button (this will erase your old robots.txt file):');
			$robots_submit = array('name' => 'submitRobots', 'title' => $this->l('Generate robots.txt file'));
		}
		else
		{
			$robots_description .= '<br />'.$this->l('Before being able to use this tool, you need to:');
			$robots_description .= '<br />- '.$this->l('create a blank robots.txt file in your root directory');
			$robots_description .= '<br />- '.$this->l('give it write permissions (CHMOD 666 on Unix system)');
		}

		$robots_options = array(
			'title' => $this->l('Robots file generation'),
			'description' => $robots_description,
		);

		if (isset($robots_submit))
			$robots_options['submit'] = $robots_submit;

		// List of options
		$this->options = array(
			'general' => array(
				'title' =>	$this->l('Set up URLs'),
				'description' => $url_description,
				'fields' =>	$general_fields,
				'submit' => array()
			),
			'robots' => $robots_options,
			'routes' => array(
				'title' =>	$this->l('Schema of URLs'),
				'description' => $this->l('Change the pattern of your links. There are some available keywords for each route listed below, keywords with * are required. To add a keyword in your URL use {keyword} syntax. You can add some text before or after the keyword IF the keyword is not empty with syntax {prepend:keyword:append}, for example {-hey-:meta_title} will add "-hey-my-title" in URL if meta title is set, or nothing. Friendly URL and rewriting Apache option must be activated on your web server to use this functionality.'),
				'fields' => array(),
			),
		);

		// Add display route options to options form
		if (Configuration::get('PS_REWRITING_SETTINGS') && Tools::getValue('PS_REWRITING_SETTINGS'))
			$this->addAllRouteFields();
	}

	public function initProcess()
	{
		parent::initProcess();
		// This is a composite page, we don't want the "options" display mode
		if ($this->display == 'options')
			$this->display = '';
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}
	
	public function addFieldRoute($routeID, $title)
	{
		$keywords = array();
		foreach (Dispatcher::getInstance()->default_routes[$routeID]['keywords'] as $keyword => $data)
			$keywords[] = ((isset($data['param'])) ? '<span class="red">'.$keyword.'*</span>' : $keyword);

		$this->options['routes']['fields']['PS_ROUTE_'.$routeID] = array(
			'title' =>	$title,
			'desc' => sprintf($this->l('Keywords: %s'), implode(', ', $keywords)),
			'validation' => 'isString',
			'type' => 'text',
			'size' => 70,
			'defaultValue' => Dispatcher::getInstance()->default_routes[$routeID]['rule'],
		);
	}

	public function renderForm()
	{
		$files = Meta::getPages(true, ($this->object->page ? $this->object->page : false));
		$pages = array();
		foreach ($files as $file)
			$pages[] = array('page' => $file);

 		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Meta-Tags'),
				'image' => '../img/admin/metatags.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_meta',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Page:'),
					'name' => 'page',
					'options' => array(
						'query' => $pages,
						'id' => 'page',
						'name' => 'page',
					),
					'desc' => $this->l('Name of the related page'),
					'required' => true,
					'empty_message' => '<p>'.$this->l('There is no page available!').'</p>',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Page title:'),
					'name' => 'title',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Title of this page'),
					'size' => 30
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'description',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('A short description of your shop'),
					'size' => 50
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords:'),
					'name' => 'keywords',
					'lang' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('List of keywords for search engines'),
					'size' => 50
				),
				array(
					'type' => 'text',
					'label' => $this->l('Rewritten URL:'),
					'name' => 'url_rewrite',
					'lang' => true,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('e.g. "contacts" for http://mysite.com/shop/contacts to redirect to http://mysite.com/shop/contact-form.php'),
					'size' => 50
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);
		return parent::renderForm();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAddmeta'))
		{
			$langs = Language::getLanguages(true);
			$default_language = Configuration::get('PS_LANG_DEFAULT');
			if (Tools::getValue('page') != 'index')
			{
				$defaultLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
				$englishLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
			}
			else
			{	// index.php can have empty rewrite rule
				$defaultLangIsValidated = !Tools::getValue('url_rewrite_'.$default_language) || Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
				$englishLangIsValidated = !Tools::getValue('url_rewrite_1') || Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
			}

			if (!$defaultLangIsValidated && !$englishLangIsValidated)
			{
				$this->errors[] = Tools::displayError('URL rewrite field must be filled at least in default or English language.');
				return false;
			}

			foreach ($langs as $lang)
			{
				$current = Tools::getValue('url_rewrite_'.$lang['id_lang']);
				if (strlen($current) == 0)
					// Prioritize default language first
					if ($defaultLangIsValidated)
						$_POST['url_rewrite_'.$lang['id_lang']] = Tools::getValue('url_rewrite_'.$default_language);
					else
						$_POST['url_rewrite_'.$lang['id_lang']] = Tools::getValue('url_rewrite_1');
			}
			Hook::exec('actionAdminMetaSave');
		}
		else if (Tools::isSubmit('submitRobots'))
			$this->generateRobotsFile();

		return parent::postProcess();
	}

	public function generateRobotsFile()
	{
		if (!$write_fd = @fopen($this->rb_file, 'w'))
			$this->errors[] = sprintf(Tools::displayError('Cannot write into file: %s. Please check write permissions.'), $this->rb_file);
		else
		{
			// PS Comments
			fwrite($write_fd, "# robots.txt automaticaly generated by PrestaShop e-commerce open-source solution\n");
			fwrite($write_fd, "# http://www.prestashop.com - http://www.prestashop.com/forums\n");
			fwrite($write_fd, "# This file is to prevent the crawling and indexing of certain parts\n");
			fwrite($write_fd, "# of your site by web crawlers and spiders run by sites like Yahoo!\n");
			fwrite($write_fd, "# and Google. By telling these \"robots\" where not to go on your site,\n");
			fwrite($write_fd, "# you save bandwidth and server resources.\n");
			fwrite($write_fd, "# For more information about the robots.txt standard, see:\n");
			fwrite($write_fd, "# http://www.robotstxt.org/wc/robots.html\n");

			//GoogleBot specific
			fwrite($write_fd, "# GoogleBot specific\n");
			fwrite($write_fd, "User-agent: Googlebot\n");
			foreach ($this->rb_data['GB'] as $gb)
				fwrite($write_fd, 'Disallow: '.__PS_BASE_URI__.$gb."\n");

			// User-Agent
			fwrite($write_fd, "# All bots\n");
			fwrite($write_fd, "User-agent: *\n");

			// Directories
			fwrite($write_fd, "# Directories\n");
			foreach ($this->rb_data['Directories'] as $dir)
				fwrite($write_fd, 'Disallow: '.__PS_BASE_URI__.$dir."\n");

			// Files
			fwrite($write_fd, "# Files\n");
			foreach ($this->rb_data['Files'] as $file)
				fwrite($write_fd, 'Disallow: '.__PS_BASE_URI__.$file."\n");

			// Sitemap
			fwrite($write_fd, "# Sitemap\n");
			if (file_exists($this->sm_file))
				if (filesize($this->sm_file))
					fwrite(
						$write_fd,
						'Sitemap: '.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].__PS_BASE_URI__.'sitemap.xml'."\n"
					);
			fwrite($write_fd, "\n");
			fclose($write_fd);

			$this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
		}
	}

	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, Context::getContext()->shop->id);
	}

	/**
	 * Validate route syntax and save it in configuration
	 *
	 * @param string $routeID
	 */
	public function checkAndUpdateRoute($routeID)
	{
		$default_routes = Dispatcher::getInstance()->default_routes;
		if (!isset($default_routes[$routeID]))
			return;

		$rule = Tools::getValue('PS_ROUTE_'.$routeID);
		if (!$rule || $rule == $default_routes[$routeID]['rule'])
		{
			Configuration::updateValue('PS_ROUTE_'.$routeID, '');
			return;
		}

		$errors = array();
		if (!Dispatcher::getInstance()->validateRoute($routeID, $rule, $errors))
		{
			foreach ($errors as $error)
				$this->errors[] = sprintf('Keyword "{%1$s}" required for route "%2$s" (rule: "%3$s")', $error, $routeID, htmlspecialchars($rule));
		}
		else
			Configuration::updateValue('PS_ROUTE_'.$routeID, $rule);
	}

	/**
	 * Called when PS_REWRITING_SETTINGS option is saved
	 */
	public function updateOptionPsRewritingSettings()
	{
		Configuration::updateValue('PS_REWRITING_SETTINGS', (int)Tools::getValue('PS_REWRITING_SETTINGS'));
		Tools::generateHtaccess($this->ht_file, null, null, '', Tools::getValue('PS_HTACCESS_DISABLE_MULTIVIEWS'));
	}

	public function updateOptionPsRouteProductRule()
	{
		$this->checkAndUpdateRoute('product_rule');
	}

	public function updateOptionPsRouteCategoryRule()
	{
		$this->checkAndUpdateRoute('category_rule');
	}

	public function updateOptionPsRouteSupplierRule()
	{
		$this->checkAndUpdateRoute('supplier_rule');
	}

	public function updateOptionPsRouteManufacturerRule()
	{
		$this->checkAndUpdateRoute('manufacturer_rule');
	}

	public function updateOptionPsRouteCmsRule()
	{
		$this->checkAndUpdateRoute('cms_rule');
	}

	public function updateOptionPsRouteCmsCategoryRule()
	{
		$this->checkAndUpdateRoute('cms_category_rule');
	}

	/**
	 * Function used to render the options for this controller
	 */
	public function renderOptions()
	{
		// If friendly url is not active, do not display custom routes form
		if (Configuration::get('PS_REWRITING_SETTINGS'))
			$this->addAllRouteFields();

		if ($this->options && is_array($this->options))
		{
			$helper = new HelperOptions($this);
			$this->setHelperDisplay($helper);
			$helper->toolbar_scroll = true;
			$helper->toolbar_btn = array('save' => array(
								'href' => '#',
								'desc' => $this->l('Save')
							));
			$helper->id = $this->id;
			$helper->tpl_vars = $this->tpl_option_vars;
			$options = $helper->generateOptions($this->options);

			return $options;
		}
	}

	/**
	 * Add all custom route fields to the options form
	 */
	public function addAllRouteFields()
	{
		$this->addFieldRoute('product_rule', $this->l('Route to products'));
		$this->addFieldRoute('category_rule', $this->l('Route to category'));
		$this->addFieldRoute('supplier_rule', $this->l('Route to supplier'));
		$this->addFieldRoute('manufacturer_rule', $this->l('Route to manufacturer'));
		$this->addFieldRoute('cms_rule', $this->l('Route to CMS page'));
		$this->addFieldRoute('cms_category_rule', $this->l('Route to CMS category'));
		$this->addFieldRoute('module', $this->l('Route to modules'));
	}

	/**
	 * Check if a file is writable
	 *
	 * @param string $file
	 * @return bool
	 */
	public function checkConfiguration($file)
	{
		if (file_exists($file))
			return is_writable($file);
		return is_writable(dirname($file));
	}

	public function getRobotsContent()
	{
		$tab = array();

		// Directories
		$tab['Directories'] = array('classes/', 'config/', 'download/', 'mails/', 'modules/', 'translations/', 'tools/');

		// Files
		$disallow_controllers = array(
			'addresses', 'address', 'authentication', 'cart', 'discount', 'footer',
			'get-file', 'header', 'history', 'identity', 'images.inc', 'init', 'my-account', 'order', 'order-opc',
			'order-slip', 'order-detail', 'order-follow', 'order-return', 'order-confirmation', 'pagination', 'password',
			'pdf-invoice', 'pdf-order-return', 'pdf-order-slip', 'product-sort', 'search', 'statistics','attachment', 'guest-tracking'
		);

		// Rewrite files
		$tab['Files'] = array();
		if (Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$sql = 'SELECT ml.url_rewrite
					FROM '._DB_PREFIX_.'meta m
					INNER JOIN '._DB_PREFIX_.'meta_lang ml ON ml.id_meta = m.id_meta
					WHERE m.page IN (\''.implode('\', \'', $disallow_controllers).'\')';
			if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
				foreach ($results as $row)
					$tab['Files'][] = $row['url_rewrite'];
		}

		$tab['GB'] = array(
			'*orderby=','*orderway=','*tag=','*id_currency=','*search_query=','*id_lang=','*back=','*utm_source=','*utm_medium=','*utm_campaign=','*n='
		);

		foreach ($disallow_controllers as $controller)
			$tab['GB'][] = '*controller='.$controller;

		return $tab;
	}
}
