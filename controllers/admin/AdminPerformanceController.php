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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPerformanceControllerCore extends AdminController
{
	public function initFieldsetSmarty()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Smarty'),
				'image' => '../img/admin/prefs.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'smarty_up'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Template cache'),
					'name' => 'smarty_force_compile',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'smarty_force_compile_'._PS_SMARTY_NO_COMPILE_,
							'value' => _PS_SMARTY_NO_COMPILE_,
							'label' => $this->l('Never recompile template files'),
							'desc' => $this->l('Templates are never recompiled, performance is better and this option should be used in production environment')
						),
						array(
							'id' => 'smarty_force_compile_'._PS_SMARTY_CHECK_COMPILE_,
							'value' => _PS_SMARTY_CHECK_COMPILE_,
							'label' => $this->l('Recompile templates if the files have been updated'),
							'desc' => $this->l('Templates are recompiled when they are updated, if you experience compilation troubles when you update your template files, you should use Force Compile instead of this option. It should never be used in a production environment.')
						),
						array(
							'id' => 'smarty_force_compile_'._PS_SMARTY_FORCE_COMPILE_,
							'value' => _PS_SMARTY_FORCE_COMPILE_,
							'label' => $this->l('Force compilation'),
							'desc' => $this->l('This forces Smarty to (re)compile templates on every invocation. This is handy for development and debugging. It should never be used in a production environment.')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Cache'),
					'name' => 'smarty_cache',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'smarty_cache_1',
							'value' => 1,
							'label' => $this->l('Yes'),
						),
						array(
							'id' => 'smarty_cache_0',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
					'desc' => $this->l('Should be enabled except for debugging.')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Debug console'),
					'name' => 'smarty_console',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'smarty_console_none',
							'value' => 0,
							'label' => $this->l('Do not open console')
						),
						array(
							'id' => 'smarty_console_url',
							'value' => 1,
							'label' => $this->l('Open console with URL parameter (SMARTY_DEBUG)'),
							'desc' => $this->l('To open the debug console, you simply pass the SMARTY_DEBUG parameter in the URL.')
						),
						array(
							'id' => 'smarty_console_open',
							'value' => 2,
							'label' => $this->l('Always open console'),
							'desc' => $this->l('Choose this option to always force the debug console to open.')
						)
					)
				),
			)
		);

		$this->fields_value['smarty_force_compile'] = Configuration::get('PS_SMARTY_FORCE_COMPILE');
		$this->fields_value['smarty_cache'] = Configuration::get('PS_SMARTY_CACHE');
		$this->fields_value['smarty_console'] = Configuration::get('PS_SMARTY_CONSOLE');
	}

	public function initFieldsetFeaturesDetachables()
	{
		$this->fields_form[1]['form'] = array(
			'legend' => array(
				'title' => $this->l('Optional features'),
				'image' => '../img/admin/tab-plugins.gif'
			),
			'desc' => $this->l('Some features can be disabled in order to improve performance.'),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'features_detachables_up'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Combinations'),
					'name' => 'combination',
					'class' => 't',
					'is_bool' => true,
					'disabled' => Combination::isCurrentlyUsed(),
					'values' => array(
						array(
							'id' => 'combination_1',
							'value' => 1,
							'label' => $this->l('Yes'),
						),
						array(
							'id' => 'combination_0',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
					'desc' => $this->l('These features will be disabled')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Features'),
					'name' => 'feature',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'feature_1',
							'value' => 1,
							'label' => $this->l('Yes'),
						),
						array(
							'id' => 'feature_0',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
					'desc' => $this->l('These features will be disabled')
				)
			)
		);

		$this->fields_value['combination'] = Combination::isFeatureActive();
		$this->fields_value['feature'] = Feature::isFeatureActive();
	}

	public function initFieldsetCCC()
	{
		$this->fields_form[2]['form'] = array(
			'legend' => array(
				'title' => $this->l('CCC (Combine, Compress and Cache)'),
				'image' => '../img/admin/arrow_in.png'
			),
			'desc' => $this->l('CCC allows you to reduce the loading time of your page. With these settings you will gain performance without even touching the code of your theme. Make sure, however, that your theme is compatible with PrestaShop 1.4+. Otherwise, CCC will cause problems.'),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'ccc_up',
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Smart cache for CSS'),
					'name' => 'PS_CSS_THEME_CACHE',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'PS_CSS_THEME_CACHE_1',
							'value' => 1,
							'label' => $this->l('Use CCC for CSS.')
						),
						array(
							'id' => 'PS_CSS_THEME_CACHE_0',
							'value' => 0,
							'label' => $this->l('Keep CSS as original')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Smart cache for JavaScript'),
					'name' => 'PS_JS_THEME_CACHE',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'PS_JS_THEME_CACHE_1',
							'value' => 1,
							'label' => $this->l('Use CCC for JavaScript.')
						),
						array(
							'id' => 'PS_JS_THEME_CACHE_0',
							'value' => 0,
							'label' => $this->l('Keep JavaScript as original')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Minify HTML'),
					'name' => 'PS_HTML_THEME_COMPRESSION',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'PS_HTML_THEME_COMPRESSION_1',
							'value' => 1,
							'label' => $this->l('Minify HTML after "smarty compile" execution.')
						),
						array(
							'id' => 'PS_HTML_THEME_COMPRESSION_0',
							'value' => 0,
							'label' => $this->l('Keep HTML as original')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Compress inline JavaScript in HTML'),
					'name' => 'PS_JS_HTML_THEME_COMPRESSION',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'PS_JS_HTML_THEME_COMPRESSION_1',
							'value' => 1,
							'label' => $this->l('Compress inline JavaScript in HTML after "smarty compile" execution')
						),
						array(
							'id' => 'PS_JS_HTML_THEME_COMPRESSION_0',
							'value' => 0,
							'label' => $this->l('Keep inline JavaScript in HTML as original')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('High risk HTML compression'),
					'name' => 'PS_HIGH_HTML_THEME_COMPRESSION',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'PS_HIGH_HTML_THEME_COMPRESSION_1',
							'value' => 1,
							'label' => $this->l('HTML is compressed but cancels the W3C validation (only when "Minify HTML" is enabled)')
						),
						array(
							'id' => 'PS_HIGH_HTML_THEME_COMPRESSION_0',
							'value' => 0,
							'label' => $this->l('Keep W3C validation')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Apache optimization'),
					'name' => 'PS_HTACCESS_CACHE_CONTROL',
					'class' => 't',
					'desc' => $this->l('This will add directives to your .htaccess file which should improve caching and compression.'),
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'PS_HTACCESS_CACHE_CONTROL_1',
							'value' => 1,
							'label' => $this->l('Yes'),
						),
						array(
							'id' => 'PS_HTACCESS_CACHE_CONTROL_0',
							'value' => 0,
							'label' => $this->l('No'),
						),
					),
				)
			)
		);

		$this->fields_value['PS_CSS_THEME_CACHE'] = Configuration::get('PS_CSS_THEME_CACHE');
		$this->fields_value['PS_JS_THEME_CACHE'] = Configuration::get('PS_JS_THEME_CACHE');
		$this->fields_value['PS_HTML_THEME_COMPRESSION'] = Configuration::get('PS_HTML_THEME_COMPRESSION');
		$this->fields_value['PS_JS_HTML_THEME_COMPRESSION'] = Configuration::get('PS_JS_HTML_THEME_COMPRESSION');
		$this->fields_value['PS_HIGH_HTML_THEME_COMPRESSION'] = Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION');
		$this->fields_value['PS_HTACCESS_CACHE_CONTROL'] = Configuration::get('PS_HTACCESS_CACHE_CONTROL');
		$this->fields_value['ccc_up'] = 1;
	}

	public function initFieldsetMediaServer()
	{
		$this->fields_form[3]['form'] = array(
			'legend' => array(
				'title' => $this->l('Media servers (use only with CCC)'),
				'image' => '../img/admin/subdomain.gif'
			),
			'desc' => $this->l('You must enter another domain or subdomain in order to use cookieless static content.'),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'media_server_up'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Media server #1'),
					'name' => '_MEDIA_SERVER_1_',
					'size' => 30,
					'desc' => $this->l('Name of the second domain of your shop, (e.g. myshop-media-server-1.com). If you do not have another domain, leave this field blank')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Media server #2'),
					'name' => '_MEDIA_SERVER_2_',
					'size' => 30,
					'desc' => $this->l('Name of the third domain of your shop, (e.g. myshop-media-server-2.com). If you do not have another domain, leave this field blank')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Media server #3'),
					'name' => '_MEDIA_SERVER_3_',
					'size' => 30,
					'desc' => $this->l('Name of the fourth domain of your shop, (e.g. myshop-media-server-3.com). If you do not have another domain, leave this field blank')
				),
			)
		);

		$this->fields_value['_MEDIA_SERVER_1_'] = Tools::getValue('_MEDIA_SERVER_1_', _MEDIA_SERVER_1_);
		$this->fields_value['_MEDIA_SERVER_2_'] = Tools::getValue('_MEDIA_SERVER_2_', _MEDIA_SERVER_2_);
		$this->fields_value['_MEDIA_SERVER_3_'] = Tools::getValue('_MEDIA_SERVER_3_', _MEDIA_SERVER_3_);
	}

	public function initFieldsetCiphering()
	{
		$this->fields_form[4]['form'] = array(
			'legend' => array(
				'title' => $this->l('Ciphering'),
				'image' => '../img/admin/computer_key.png'
			),
			'desc' => $this->l('Mcrypt is faster than our custom BlowFish class, but requires the PHP extension "mcrypt". If you change this configuration, all cookies will be reset.'),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'ciphering_up'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Algorithm'),
					'name' => 'PS_CIPHER_ALGORITHM',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'PS_CIPHER_ALGORITHM_1',
							'value' => 1,
							'label' => $this->l('Use Rijndael with mcrypt lib.')
						),
						array(
							'id' => 'PS_CIPHER_ALGORITHM_0',
							'value' => 0,
							'label' => $this->l('Keep the custom BlowFish class.')
						)
					)
				)
			)
		);

		$this->fields_value['PS_CIPHER_ALGORITHM'] = Configuration::get('PS_CIPHER_ALGORITHM');
	}

	public function initFieldsetCaching()
	{
		$caching_system = array(
			0 => array(
				'id' => 'CacheMemcache',
				'name' => $this->l('Memcached')
			),
			1 => array(
				'id' => 'CacheApc',
				'name' => $this->l('APC')
			),
			2 => array(
				'id' => 'CacheXcache',
				'name' => $this->l('Xcache')
			),
			3 => array(
				'id' => 'CacheFs',
				'name' => $this->l('File System')
			)
		);

		$this->fields_form[5]['form'] = array(
			'legend' => array(
				'title' => $this->l('Caching'),
				'image' => '../img/admin/computer_key.png'
			),
			'desc' => $this->l('Caching systems are used to speed up your store by caching data into the server\'s memory, avoiding the exhausting task of querying the database.'),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'cache_up'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Use cache'),
					'name' => 'active',
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Enable or disable caching system')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Caching system'),
					'name' => 'caching_system',
					'options' => array(
						'query' => $caching_system,
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Directory depth'),
					'name' => 'ps_cache_fs_directory_depth',
					'size' => 30
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			),
			'memcachedServers' => true
		);

		$depth = Configuration::get('PS_CACHEFS_DIRECTORY_DEPTH');
		$this->fields_value['active'] = _PS_CACHE_ENABLED_;
		$this->fields_value['caching_system'] = _PS_CACHING_SYSTEM_;
		$this->fields_value['ps_cache_fs_directory_depth'] = $depth ? $depth : 1;

		$this->tpl_form_vars['servers'] = CacheMemcache::getMemcachedServers();
	}

	public function initFieldsetCloudCache()
	{
		if (!class_exists('CloudCache'))
			$this->fields_form[6]['form'] = array(
				'legend' => array(
					'title' => $this->l('CloudCache'),
					'image' => '../img/admin/subdomain.gif'
				),
				'desc' => $this->l('Performance matters! Improve speed and conversions the easy way.').'<br />'.
				$this->l('CloudCache supercharges your site in minutes through its state-of-the-art content delivery network.').'<br /><br />'.
				$this->l('Subscribe now using the code "presta25" and get an exclusive discount of 25% per month on every available package.').'<br /><br />
			<a style="color: blue" href="index.php?controller=AdminModules&token='.Tools::getAdminTokenLite('AdminModules').'&filtername=cloudcache" id="installCloudCache">&gt; '.$this->l('Click here to install the CloudCache module for PrestaShop').'</a><br />'
		);
	}

	public function renderForm()
	{
		// Initialize fieldset for a form
		$this->initFieldsetSmarty();
		$this->initFieldsetFeaturesDetachables();
		$this->initFieldsetCCC();
		$this->initFieldsetMediaServer();
		$this->initFieldsetCiphering();
		$this->initFieldsetCaching();
		$this->initFieldsetCloudCache();

		// Activate multiple fieldset
		$this->multiple_fieldsets = true;

		return parent::renderForm();
	}

	public function initContent()
	{
		$php_dot_net_supported_langs = array('en', 'zh', 'fr', 'de', 'ja', 'pl', 'ro', 'ru', 'fa', 'es', 'tr');
		$php_lang = in_array($this->context->language->iso_code, $php_dot_net_supported_langs) ?
			$this->context->language->iso_code : 'en';

		if (!extension_loaded('memcache'))
			$this->warnings[] = $this->l('To use Memcached, you must install the Memcache PECL extension on your server.').'
				<a href="http://www.php.net/manual/'.substr($php_lang, 0, 2).'/memcache.installation.php" target="_blank">
					http://www.php.net/manual/'.substr($php_lang, 0, 2).'/memcache.installation.php
				</a>';
		if (!extension_loaded('apc'))
		{
			$this->warnings[] = $this->l('To use APC, you must install the APC PECL extension on your server.').'
				<a href="http://php.net/manual/'.substr($php_lang, 0, 2).'/apc.installation.php" target="_blank">
					http://php.net/manual/'.substr($php_lang, 0, 2).'/apc.installation.php
				</a>';
		}
		if (!extension_loaded('xcache'))
			$this->warnings[] = $this->l('To use Xcache, you must install the Xcache extension on your server.').'
				<a href="http://xcache.lighttpd.net" target="_blank">http://xcache.lighttpd.net</a>';

		if (!is_writable(_PS_CACHEFS_DIRECTORY_))
			$this->warnings[] = sprintf($this->l('To use the CacheFS directory %s must be writable.'), realpath(_PS_CACHEFS_DIRECTORY_));

		$this->initToolbar();
		$this->display = '';
		$this->content .= $this->renderForm();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function initToolbar()
	{
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Save')
		);
	}

	public function postProcess()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}

		if (Tools::isSubmit('submitAddServer'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (!Tools::getValue('memcachedIp'))
					$this->errors[] = Tools::displayError('Memcached IP is missing');
				if (!Tools::getValue('memcachedPort'))
					$this->errors[] = Tools::displayError('Memcached port is missing');
				if (!Tools::getValue('memcachedWeight'))
					$this->errors[] = Tools::displayError('Memcached weight is missing');
				if (!count($this->errors))
				{
					if (CacheMemcache::addServer(pSQL(Tools::getValue('memcachedIp')),
						(int)Tools::getValue('memcachedPort'),
						(int)Tools::getValue('memcachedWeight')))
						Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					else
						$this->errors[] = Tools::displayError('Cannot add Memcached server');
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}

		if (Tools::getValue('deleteMemcachedServer'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (CacheMemcache::deleteServer((int)Tools::getValue('deleteMemcachedServer')))
					Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
				else
					$this->errors[] = Tools::displayError('Error in deleting Memcached server');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		$redirecAdmin = false;
		if ((bool)Tools::getValue('smarty_up'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				Configuration::updateValue('PS_SMARTY_FORCE_COMPILE', Tools::getValue('smarty_force_compile', _PS_SMARTY_NO_COMPILE_));
				Configuration::updateValue('PS_SMARTY_CACHE', Tools::getValue('smarty_cache', 0));
				Configuration::updateValue('PS_SMARTY_CONSOLE', Tools::getValue('smarty_console', 0));
				$redirecAdmin = true;
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if ((bool)Tools::getValue('features_detachables_up'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Combination::isCurrentlyUsed())
					Configuration::updateValue('PS_COMBINATION_FEATURE_ACTIVE', Tools::getValue('combination'));
				Configuration::updateValue('PS_FEATURE_FEATURE_ACTIVE', Tools::getValue('feature'));
				$redirecAdmin = true;
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if ((bool)Tools::getValue('ccc_up'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Configuration::updateValue('PS_CSS_THEME_CACHE', (int)Tools::getValue('PS_CSS_THEME_CACHE')) ||
					!Configuration::updateValue('PS_JS_THEME_CACHE', (int)Tools::getValue('PS_JS_THEME_CACHE')) ||
					!Configuration::updateValue('PS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HTML_THEME_COMPRESSION')) ||
					!Configuration::updateValue('PS_JS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_JS_HTML_THEME_COMPRESSION')) ||
					!Configuration::updateValue('PS_HIGH_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HIGH_HTML_THEME_COMPRESSION')) ||
					!Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', (int)Tools::getValue('PS_HTACCESS_CACHE_CONTROL')))
					$this->errors[] = Tools::displayError('Unknown error.');
				else
				{
					$redirecAdmin = true;
					if (Configuration::get('PS_HTACCESS_CACHE_CONTROL'))
						Tools::generateHtaccess();
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if ((bool)Tools::getValue('media_server_up'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Tools::getValue('_MEDIA_SERVER_1_') != null && !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_1_')))
					$this->errors[] = Tools::displayError('Media server #1 is invalid');
				if (Tools::getValue('_MEDIA_SERVER_2_') != null && !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_2_')))
					$this->errors[] = Tools::displayError('Media server #2 is invalid');
				if (Tools::getValue('_MEDIA_SERVER_3_') != null && !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_3_')))
					$this->errors[] = Tools::displayError('Media server #3 is invalid');
				if (!count($this->errors))
				{
					$base_urls = array();
					$base_urls['_MEDIA_SERVER_1_'] = Tools::getValue('_MEDIA_SERVER_1_');
					$base_urls['_MEDIA_SERVER_2_'] = Tools::getValue('_MEDIA_SERVER_2_');
					$base_urls['_MEDIA_SERVER_3_'] = Tools::getValue('_MEDIA_SERVER_3_');
					rewriteSettingsFile($base_urls, null, null);
					Tools::generateHtaccess(null, null, null, '', null, array($base_urls['_MEDIA_SERVER_1_'], $base_urls['_MEDIA_SERVER_2_'], $base_urls['_MEDIA_SERVER_3_']));
					unset($this->_fieldsGeneral['_MEDIA_SERVER_1_']);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_2_']);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_3_']);
					$redirecAdmin = true;
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if ((bool)Tools::getValue('ciphering_up') && Configuration::get('PS_CIPHER_ALGORITHM') != (int)Tools::getValue('PS_CIPHER_ALGORITHM'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$algo = (int)Tools::getValue('PS_CIPHER_ALGORITHM');
				$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
				if ($algo)
				{
					if (!function_exists('mcrypt_encrypt'))
						$this->errors[] = Tools::displayError('PHP "Mcrypt" extension is not activated on this server.');
					else
					{
						if (!strstr($settings, '_RIJNDAEL_KEY_'))
						{
							$key_size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
							$key = Tools::passwdGen($key_size);
							$settings = preg_replace(
								'/define\(\'_COOKIE_KEY_\', \'([a-z0-9=\/+-_]+)\'\);/i',
								'define(\'_COOKIE_KEY_\', \'\1\');'."\n".'define(\'_RIJNDAEL_KEY_\', \''.$key.'\');',
								$settings
							);
						}
						if (!strstr($settings, '_RIJNDAEL_IV_'))
						{
							$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
							$iv = base64_encode(mcrypt_create_iv($iv_size, MCRYPT_RAND));
							$settings = preg_replace(
								'/define\(\'_COOKIE_IV_\', \'([a-z0-9=\/+-_]+)\'\);/i',
								'define(\'_COOKIE_IV_\', \'\1\');'."\n".'define(\'_RIJNDAEL_IV_\', \''.$iv.'\');',
								$settings
							);
						}
					}
				}
				if (!count($this->errors))
				{
					if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
					{
						Configuration::updateValue('PS_CIPHER_ALGORITHM', $algo);
						$redirecAdmin = true;
					}
					else
						$this->errors[] = Tools::displayError('Cannot overwrite settings file.');
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if ((bool)Tools::getValue('cache_up'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
				if (!Tools::getValue('active'))
					$cache_active = 0;
				else
					$cache_active = 1;
				if (!$caching_system = Tools::getValue('caching_system'))
					$this->errors[] = Tools::displayError('Caching system is missing');
				else
					$settings = preg_replace(
						'/define\(\'_PS_CACHING_SYSTEM_\', \'([a-z0-9=\/+-_]+)\'\);/Ui',
						'define(\'_PS_CACHING_SYSTEM_\', \''.$caching_system.'\');',
						$settings
					);
				if ($cache_active && $caching_system == 'CacheMemcache' && !extension_loaded('memcache'))
					$this->errors[] = Tools::displayError('To use Memcached, you must install the Memcache PECL extension on your server.').'
						<a href="http://www.php.net/manual/en/memcache.installation.php">http://www.php.net/manual/en/memcache.installation.php</a>';
				else if ($cache_active && $caching_system == 'CacheApc' && !extension_loaded('apc'))
					$this->errors[] = Tools::displayError('To use APC cache, you must install the APC PECL extension on your server.').'
						<a href="http://fr.php.net/manual/fr/apc.installation.php">http://fr.php.net/manual/fr/apc.installation.php</a>';
				else if ($cache_active && $caching_system == 'CacheXcache' && !extension_loaded('xcache'))
					$this->errors[] = Tools::displayError('To use Xcache, you must install the Xcache extension on your server.').'
						<a href="http://xcache.lighttpd.net">http://xcache.lighttpd.net</a>';
				else if ($cache_active && $caching_system == 'CacheFs' && !is_writable(_PS_CACHEFS_DIRECTORY_))
					$this->errors[] = sprintf(
						Tools::displayError('To use CacheFS the directory %s must be writable.'),
						realpath(_PS_CACHEFS_DIRECTORY_)
					);

				if ($caching_system == 'CacheFs' && $cache_active)
				{
					if (!($depth = Tools::getValue('ps_cache_fs_directory_depth')))
						$this->errors[] = Tools::displayError('Please set a directory depth');
					if (!count($this->errors))
					{
						CacheFs::deleteCacheDirectory();
						CacheFs::createCacheDirectories((int)$depth);
						Configuration::updateValue('PS_CACHEFS_DIRECTORY_DEPTH', (int)$depth);
					}
				}
				else if ($caching_system == 'MCached' && $cache_active && !_PS_CACHE_ENABLED_ && _PS_CACHING_SYSTEM_ == 'MCached')
					Cache::getInstance()->flush();

				if (!count($this->errors))
				{
					$settings = preg_replace('/define\(\'_PS_CACHE_ENABLED_\', \'([0-9])\'\);/Ui', 'define(\'_PS_CACHE_ENABLED_\', \''.(int)$cache_active.'\');', $settings);
					if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
						$redirecAdmin = true;
					else
						$this->errors[] = Tools::displayError('Cannot overwrite settings file.');
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if ($redirecAdmin)
			Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
		else
			return parent::postProcess();
	}

	public function ajaxProcess()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
			die(Tools::displayError('This functionality has been disabled.'));
		/* PrestaShop demo mode*/
		if (Tools::isSubmit('action') && Tools::getValue('action') == 'test_server')
		{
			$host = pSQL(Tools::getValue('sHost', ''));
			$port = (int)Tools::getValue('sPort', 0);

			if ($host != '' && $port != 0)
			{
				$res = 0;

				if (function_exists('memcache_get_server_status') &&
					function_exists('memcache_connect') &&
					@fsockopen($host, $port))
				{
					$memcache = @memcache_connect($host, $port);
					$res = @memcache_get_server_status($memcache, $host, $port);
				}
				die(Tools::jsonEncode(array($res)));
			}
		}
		die;
    }

}

