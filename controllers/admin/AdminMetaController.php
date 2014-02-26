<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
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
	 	$this->table = 'meta';
	 	$this->className = 'Meta';

		$this->bootstrap = true;
		$this->identifier_name = 'page';
		$this->ht_file = _PS_ROOT_DIR_.'/.htaccess';
		$this->rb_file = _PS_ROOT_DIR_.'/robots.txt';
		$this->sm_file = _PS_ROOT_DIR_.'/sitemap.xml';
		$this->rb_data = $this->getRobotsContent();

		$this->explicitSelect = true;
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		$this->fields_list = array(
			'id_meta' => array('title' => $this->l('ID'), 'align' => 'center', 'class' => 'fixed-width-xs'),
			'page' => array('title' => $this->l('Page')),
			'title' => array('title' => $this->l('Title')),
			'url_rewrite' => array('title' => $this->l('Friendly URL'))
		);
		$this->_where = ' AND a.configurable = 1';
		$this->_group = 'GROUP BY a.id_meta';

		parent::__construct();

		// Options to generate friendly urls
		$mod_rewrite = Tools::modRewriteActive();
		$general_fields = array(
			'PS_REWRITING_SETTINGS' => array(
				'title' => $this->l('Friendly URL'),
				'hint' => ($mod_rewrite ? $this->l('Enable this option only if your server allows URL rewriting (recommended).') : ''),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
				'mod_rewrite' => $mod_rewrite
			),
			'PS_ALLOW_ACCENTED_CHARS_URL' => array(
				'title' => $this->l('Accented URL'),
				'hint' => $this->l('Enable this option if you want to allow accented characters in your friendly URLs.').' '.$this->l('You should only activate this option if you are using non-latin characters ; for all the latin charsets, your SEO will be better without this option.'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool'
			),
			'PS_CANONICAL_REDIRECT' => array(
				'title' => $this->l('Redirect to the canonical URL'),
				'validation' => 'isUnsignedInt',
				'cast' => 'intval',
				'type' => 'select',
				'list' => array(
					array('value' => 0, 'name' => $this->l('No redirection (you may have duplicate content issues)')),
					array('value' => 1, 'name' => $this->l('302 Moved Temporarily (recommended while setting up your store)')),
					array('value' => 2, 'name' => $this->l('301 Moved Permanently (recommended once you have gone live)'))
				),
				'identifier' => 'value',
			),
		);

		$url_description = '';
		if ($this->checkConfiguration($this->ht_file))
		{
			$general_fields['PS_HTACCESS_DISABLE_MULTIVIEWS'] = array(
				'title' => $this->l('Disable apache multiviews'),
				'hint' => $this->l('Enable this option only if you have problems with URL rewriting.'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			);

			$general_fields['PS_HTACCESS_DISABLE_MODSEC'] = array(
				'title' => $this->l('Disable apache mod security'),
				'hint' => $this->l('Some features could not work correctly with a specific configuration of Apache mod_security. We recommend to turn it off.'),
				'validation' => 'isBool',
				'cast' => 'intval',
				'type' => 'bool',
			);
		}
		else
		{
			$url_description = $this->l('Before you can use this tool, you need to:');
			$url_description .= $this->l('Create a blank .htaccess in your root directory.');
			$url_description .= $this->l('Give it write permissions (CHMOD 666 on Unix system).');
		}

		// Options to generate robot.txt
		$robots_description = $this->l('Your robots.txt file MUST be in your website\'s root directory and nowhere else (e.g. http://www.yoursite.com/robots.txt).');
		if ($this->checkConfiguration($this->rb_file))
		{
			$robots_description .= $this->l('Generate your "robots.txt" file by clicking on the following button (this will erase the old robots.txt file)');
			$robots_submit = array('name' => 'submitRobots', 'title' => $this->l('Generate robots.txt file'));
		}
		else
		{
			$robots_description .= $this->l('Before you can use this tool, you need to:');
			$robots_description .= $this->l('Create a blank robots.txt file in your root directory.');
			$robots_description .= $this->l('Give it write permissions (CHMOD 666 on Unix system).');
		}

		$robots_options = array(
			'title' => $this->l('Robots file generation'),
			'description' => $robots_description,
		);

		if (isset($robots_submit))
			$robots_options['submit'] = $robots_submit;

		// Options for shop URL if multishop is disabled
		$shop_url_options = array(
			'title' => $this->l('Set shop URL'),
			'fields' => array(),
		);

		if (!Shop::isFeatureActive())
		{
			$this->url = ShopUrl::getShopUrls($this->context->shop->id)->where('main', '=', 1)->getFirst();
			if ($this->url)
			{
				$shop_url_options['description'] = $this->l('Here you can set the URL for your shop. If you migrate your shop to a new URL, remember to change the values below.');
				$shop_url_options['fields'] = array(
					'domain' => array(
						'title' =>	$this->l('Shop domain'),
						'validation' => 'isString',
						'type' => 'text',
						'defaultValue' => $this->url->domain,
					),
					'domain_ssl' => array(
						'title' =>	$this->l('SSL domain'),
						'validation' => 'isString',
						'type' => 'text',
						'defaultValue' => $this->url->domain_ssl,
					),
				);

				if(!defined('_PS_HOST_MODE_'))
					$shop_url_options['fields']['uri'] = array(
						'title' =>	$this->l('Base URI'),
						'validation' => 'isString',
						'type' => 'text',
						'defaultValue' => $this->url->physical_uri,
					);
				$shop_url_options['submit'] = array('title' => $this->l('Save'));
			}
		}
		else
			$shop_url_options['description'] = $this->l('The multistore option is enabled. If you want to change the URL of your shop, you must go to the "Multistore" page under the "Advanced Parameters" menu.');

		// List of options
		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Set up URLs'),
				'description' => $url_description,
				'fields' =>	$general_fields,
				'submit' => array('title' => $this->l('Save'))
			),
			'shop_url' => $shop_url_options
		);

		// Add display route options to options form
		if (Configuration::get('PS_REWRITING_SETTINGS'))
		{
			$this->fields_options['routes'] = array(
				'title' =>	$this->l('Schema of URLs'),
				'description' => $this->l('Change the pattern of your links. There are some available keywords for each route listed below, keywords with * are required. To add a keyword in your URL use {keyword} syntax. You can add text before or after the keyword if the keyword is not empty with syntax {prepend:keyword:append}. For example {-hey-:meta_title} will add "-hey-my-title" in the URL if the meta title is set. Friendly URL and rewriting Apache option must be activated on your web server to use this functionality.'),
				'fields' => array(),
				'submit' => array('title' => $this->l('Save'))
			);
			$this->addAllRouteFields();
		}

		$this->fields_options['robots'] = $robots_options;
	}

	public function initPageHeaderToolbar()
	{
		if (empty($this->display))
			$this->page_header_toolbar_btn['new_meta'] = array(
				'href' => self::$currentIndex.'&addmeta&token='.$this->token,
				'desc' => $this->l('Add a new page', null, null, false),
				'icon' => 'process-icon-new'
			);

		parent::initPageHeaderToolbar();
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

	public function addFieldRoute($route_id, $title)
	{
		$keywords = array();
		foreach (Dispatcher::getInstance()->default_routes[$route_id]['keywords'] as $keyword => $data)
			$keywords[] = ((isset($data['param'])) ? '<span class="red">'.$keyword.'*</span>' : $keyword);

		$this->fields_options['routes']['fields']['PS_ROUTE_'.$route_id] = array(
			'title' =>	$title,
			'desc' => sprintf($this->l('Keywords: %s'), implode(', ', $keywords)),
			'validation' => 'isString',
			'type' => 'text',
			'size' => 70,
			'defaultValue' => Dispatcher::getInstance()->default_routes[$route_id]['rule'],
		);
	}

	public function renderForm()
	{
		$files = Meta::getPages(true, ($this->object->page ? $this->object->page : false));
		
		$is_index = false;
		if (is_array($this->object))
			foreach ($this->object->url_rewrite as $rewrite)
				if($is_index != true)
					$is_index = ($this->object->page == 'index' && empty($rewrite)) ? true : false;

		$pages = array(
			'common' => array(
				'name' => $this->l('Default pages'),
				'query' => array(),
			),
			'module' => array(
				'name' => $this->l('Modules pages'),
				'query' => array(),
			),
		);

		foreach ($files as $name => $file)
		{
			$k = (preg_match('#^module-#', $file)) ? 'module' : 'common';
			$pages[$k]['query'][] = array(
				'id' => $file,
				'page' => $name,
			);
		}

 		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Meta tags'),
				'icon' => 'icon-tags'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'id_meta',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Page'),
					'name' => 'page',

					'options' => array(
						'optiongroup' => array(
							'label' => 'name',
							'query' => $pages,
						),
						'options' => array(
							'id' => 'id',
							'name' => 'page',
							'query' => 'query',
						),
					),
					'hint' => $this->l('Name of the related page.'),
					'required' => true,
					'empty_message' => '<p>'.$this->l('There is no page available!').'</p>',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Page title'),
					'name' => 'title',
					'lang' => true,
					'hint' => array(
						$this->l('Title of this page.'),
						$this->l('Invalid characters:').' &lt;&gt;;=#{}'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description'),
					'name' => 'description',
					'lang' => true,
					'hint' => array(
						$this->l('A short description of your shop.'),
						$this->l('Invalid characters:').' &lt;&gt;;=#{}'
					)
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords'),
					'name' => 'keywords',
					'lang' => true,
					'hint' =>  array(
						$this->l('List of keywords for search engines.'),
						$this->l('To add tags, click in the field, write something, and then press the "Enter" key.'),
						$this->l('Invalid characters:').' &lt;&gt;;=#{}'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Rewritten URL'),
					'name' => 'url_rewrite',
					'lang' => true,
					'required' => true,
					'disabled' => (bool)$is_index,
					'hint' => array(
						$this->l('For instance, "contacts" for http://example.com/shop/contacts to redirect to http://example.com/shop/contact-form.php'),
						$this->l('Only letters and hyphens are allowed.'),
					)
				),
			),
			'submit' => array(
				'title' => $this->l('Save')
			)
		);
		return parent::renderForm();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAddmeta'))
		{
			$langs = Language::getLanguages(false);

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
				$this->errors[] = Tools::displayError('The URL rewrite field must be filled in either the default or English language.');
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

		if (Tools::isSubmit('PS_ROUTE_product_rule'))
			Tools::clearCache($this->context->smarty);		

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

			// User-Agent
			fwrite($write_fd, "User-agent: *\n");
			
			// Private pages
			if (count($this->rb_data['GB']))
			{
				fwrite($write_fd, "# Private pages\n");
				foreach ($this->rb_data['GB'] as $gb)
					fwrite($write_fd, 'Disallow: /*'.$gb."\n");
			}
			
			// Directories
			if (count($this->rb_data['Directories']))
			{
				fwrite($write_fd, "# Directories\n");
				foreach ($this->rb_data['Directories'] as $dir)
					fwrite($write_fd, 'Disallow: */'.$dir."\n");
			}
			
			// Files
			if (count($this->rb_data['Files']))
			{
				fwrite($write_fd, "# Files\n");
				foreach ($this->rb_data['Files'] as $iso_code => $files)
					foreach ($files as $file)
						fwrite($write_fd, 'Disallow: /*'.$iso_code.'/'.$file."\n");
			}
			
			// Sitemap
			if (file_exists($this->sm_file) && filesize($this->sm_file))
			{
				fwrite($write_fd, "# Sitemap\n");
				fwrite($write_fd, 'Sitemap: '.(Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$_SERVER['SERVER_NAME'].__PS_BASE_URI__.'sitemap.xml'."\n");
			}

			fclose($write_fd);

			$this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
		}
	}

	public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, Context::getContext()->shop->id);
	}
	
	public function renderList()
	{
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
			$this->displayInformation($this->l('You can only display the page list in a shop context.'));
		else
			return parent::renderList();
	}

	/**
	 * Validate route syntax and save it in configuration
	 *
	 * @param string $route_id
	 */
	public function checkAndUpdateRoute($route_id)
	{
		$default_routes = Dispatcher::getInstance()->default_routes;
		if (!isset($default_routes[$route_id]))
			return;

		$rule = Tools::getValue('PS_ROUTE_'.$route_id);
		if (!Validate::isRoutePattern($rule))
			$this->errors[] = sprintf('The route %s is not valid', htmlspecialchars($rule));
		else
		{
			if (!$rule || $rule == $default_routes[$route_id]['rule'])
			{
				Configuration::updateValue('PS_ROUTE_'.$route_id, '');
				return;
			}
	
			$errors = array();
			if (!Dispatcher::getInstance()->validateRoute($route_id, $rule, $errors))
			{
				foreach ($errors as $error)
					$this->errors[] = sprintf('Keyword "{%1$s}" required for route "%2$s" (rule: "%3$s")', $error, $route_id, htmlspecialchars($rule));
			}
			else
				Configuration::updateValue('PS_ROUTE_'.$route_id, $rule);
		}
	}

	/**
	 * Called when PS_REWRITING_SETTINGS option is saved
	 */
	public function updateOptionPsRewritingSettings()
	{
		Configuration::updateValue('PS_REWRITING_SETTINGS', (int)Tools::getValue('PS_REWRITING_SETTINGS'));

		$this->updateOptionDomain(Tools::getValue('domain'));
		$this->updateOptionDomainSsl(Tools::getValue('domain_ssl'));

		if (Tools::getIsset('uri'))
			$this->updateOptionUri(Tools::getValue('uri'));

		if (Tools::generateHtaccess($this->ht_file, null, null, '', Tools::getValue('PS_HTACCESS_DISABLE_MULTIVIEWS'), false, Tools::getValue('PS_HTACCESS_DISABLE_MODSEC')))
		{
			Tools::enableCache();
			Tools::clearCache($this->context->smarty);
			Tools::restoreCacheSettings();
		}
		else
		{
			Configuration::updateValue('PS_REWRITING_SETTINGS', 0);
			// Message copied/pasted from the information tip
			$message = $this->l('Before being able to use this tool, you need to:');
			$message .= '<br />- '.$this->l('Create a blank .htaccess in your root directory.');
			$message .= '<br />- '.$this->l('Give it write permissions (CHMOD 666 on Unix system).');
			$this->errors[] = $message;
		}
	}

	public function updateOptionPsRouteProductRule()
	{
		$this->checkAndUpdateRoute('product_rule');
	}

	public function updateOptionPsRouteCategoryRule()
	{
		$this->checkAndUpdateRoute('category_rule');
	}

	public function updateOptionPsRouteLayeredRule()
	{
		$this->checkAndUpdateRoute('layered_rule');
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
	 * Update shop domain (for mono shop)
	 */
	public function updateOptionDomain($value)
	{
		if (!Shop::isFeatureActive() && $this->url && $this->url->domain != $value)
		{
			if (Validate::isCleanHtml($value))
			{
				$this->url->domain = $value;
				$this->url->update();
				Configuration::updateGlobalValue('PS_SHOP_DOMAIN', $value);
			}
			else
				$this->errors[] = Tools::displayError('This domain is not valid.');
		}
	}

	/**
	 * Update shop SSL domain (for mono shop)
	 */
	public function updateOptionDomainSsl($value)
	{
		if (!Shop::isFeatureActive() && $this->url && $this->url->domain_ssl != $value)
		{
			if (Validate::isCleanHtml($value))
			{
				$this->url->domain_ssl = $value;
				$this->url->update();
				Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL', $value);
			}
			else
				$this->errors[] = Tools::displayError('The SSL domain is not valid.');
		}
	}

	/**
	 * Update shop physical uri for mono shop)
	 */
	public function updateOptionUri($value)
	{
		if (!Shop::isFeatureActive() && $this->url && $this->url->physical_uri != $value)
		{
			$this->url->physical_uri = $value;
			$this->url->update();
		}
	}

	/**
	 * Function used to render the options for this controller
	 */
	public function renderOptions()
	{
		// If friendly url is not active, do not display custom routes form
		if (Configuration::get('PS_REWRITING_SETTINGS'))
			$this->addAllRouteFields();

		if ($this->fields_options && is_array($this->fields_options))
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
			$options = $helper->generateOptions($this->fields_options);

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
		$this->addFieldRoute('layered_rule', $this->l('Route to category with attribute selected_filter for the module block layered'));
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
			$sql = 'SELECT ml.url_rewrite, l.iso_code
					FROM '._DB_PREFIX_.'meta m
					INNER JOIN '._DB_PREFIX_.'meta_lang ml ON ml.id_meta = m.id_meta
					INNER JOIN '._DB_PREFIX_.'lang l ON l.id_lang = ml.id_lang
					WHERE l.active = 1 AND m.page IN (\''.implode('\', \'', $disallow_controllers).'\')';
			if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql))
				foreach ($results as $row)
					$tab['Files'][$row['iso_code']][] = $row['url_rewrite'];
		}

		$tab['GB'] = array(
			'orderby=','orderway=','tag=','id_currency=','search_query=','back=','n='
		);

		foreach ($disallow_controllers as $controller)
			$tab['GB'][] = 'controller='.$controller;

		return $tab;
	}
}
