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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPerformanceController extends AdminController
{
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'formSmarty':
				$this->toolbar_btn = array();
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
			break;

			case 'formFeaturesDetachables':
				$this->toolbar_btn = array();
				$this->toolbar_btn['save-feature'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
			break;

			case 'formCCC':
				$this->toolbar_btn = array();
				$this->toolbar_btn['save-ccc'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
			break;

			case 'formMediaServer':
				$this->toolbar_btn = array();
				$this->toolbar_btn['save-media'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
			break;

			case 'formCiphering':
				$this->toolbar_btn = array();
				$this->toolbar_btn['save-ciphering'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
			break;

			case 'formCaching':
				$this->toolbar_btn = array();
				$this->toolbar_btn['save-caching'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);
			break;

		}
	}

	public function initFormSmarty()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Smarty'),
				'image' => '../img/admin/prefs.gif'
			),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Templates cache:'),
					'name' => 'smarty_force_compile',
					'class' => 't',
					'br' => true,
					'values' => array(
						array(
							'id' => 'smarty_force_compile_'._PS_SMARTY_NO_COMPILE_,
							'value' => _PS_SMARTY_NO_COMPILE_,
							'label' => $this->l('Never compile cache'),
							'p' => $this->l('Templates are never recompiled, performance are better and this option should be used in production environement')
						),
						array(
							'id' => 'smarty_force_compile_'._PS_SMARTY_CHECK_COMPILE_,
							'value' => _PS_SMARTY_CHECK_COMPILE_,
							'label' => $this->l('Compile cache if templates are updated'),
							'p' => $this->l('Templates are recompiled when they are updated, if you experience compilation troubles when you update your templates files, you should use force compile instead of this option.  It should never be used in a production environment.')
						),
						array(
							'id' => 'smarty_force_compile_'._PS_SMARTY_FORCE_COMPILE_,
							'value' => _PS_SMARTY_FORCE_COMPILE_,
							'label' => $this->l('Force compile'),
							'p' => $this->l('This forces Smarty to (re)compile templates on every invocation. This is handy for development and debugging. It should never be used in a production environment.')
						)
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Cache:'),
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
					'p' => $this->l('Should be enabled except for debugging.')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button',
				'name' => 'submitSmartyConfig'
			)
		);

		$this->fields_value = array(
			'smarty_force_compile' => Configuration::get('PS_SMARTY_FORCE_COMPILE'),
			'smarty_cache' => Configuration::get('PS_SMARTY_CACHE')
		);

		$this->display = 'formSmarty';

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'smarty';
		$helper->identifier = 'smarty';
		$helper->override_folder = 'performance/';
		$helper->toolbar_fix = false;
		$helper->first_call = true;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->fields_value;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Smarty');

		return $helper->generateForm($this->fields_form);
	}

	public function initFormFeaturesDetachables()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Features detachables'),
				'image' => '../img/admin/tab-plugins.gif'
			),
			'p' => $this->l('Some features can be disabled in order to improve performance.'),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Combination:'),
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
					'p' => $this->l('These features are going to be disabled:')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Feature:'),
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
					'p' => $this->l('These features are going to be disabled:')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button',
				'name' => 'submitFeaturesDetachables',
				'id' => 'submitFeaturesDetachables'
			)
		);

		$this->fields_value = array(
			'combination' => Combination::isFeatureActive(),
			'feature' => Feature::isFeatureActive()
		);

		$this->tpl_vars = array('combination' => Combination::isCurrentlyUsed());

		$this->display = 'formFeaturesDetachables';

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'features_detachables';
		$helper->identifier = 'features_detachables';
		$helper->override_folder = 'performance/';
		$helper->tpl_vars = $this->tpl_vars;
		$helper->first_call = false;
		$helper->toolbar_fix = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->fields_value;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Features Detachables');

		return $helper->generateForm($this->fields_form);
	}

	public function initFormCCC()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('CCC (Combine, Compress and Cache)'),
				'image' => '../img/admin/arrow_in.png'
			),
			'p' => $this->l('CCC allows you to reduce the loading time of your page. With these settings you will gain performance without even touching the code of your theme. 
				Make sure, however, that your theme is compatible with PrestaShop 1.4+. Otherwise, CCC will cause problems.'),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Smart cache for CSS:'),
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
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button',
				'name' => 'submitCCC',
				'id' => 'submitCCC'
			)
		);

		$this->fields_value = array(
			'PS_CSS_THEME_CACHE' => Configuration::get('PS_CSS_THEME_CACHE'),
			'PS_JS_THEME_CACHE' => Configuration::get('PS_JS_THEME_CACHE'),
			'PS_HTML_THEME_COMPRESSION' => Configuration::get('PS_HTML_THEME_COMPRESSION'),
			'PS_JS_HTML_THEME_COMPRESSION' => Configuration::get('PS_JS_HTML_THEME_COMPRESSION'),
			'PS_HIGH_HTML_THEME_COMPRESSION' => Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION')
		);

		$this->display = 'formCCC';

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'ccc';
		$helper->identifier = 'ccc';
		$helper->override_folder = 'performance/';
		$helper->tpl_vars = $this->tpl_vars;
		$helper->first_call = false;
		$helper->toolbar_fix = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->fields_value;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('CCC (Combine, Compress and Cache)');

		return $helper->generateForm($this->fields_form);
	}

	public function initFormMediaServer()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Media servers (used only with CCC)'),
				'image' => '../img/admin/subdomain.gif'
			),
			'p' => $this->l('You must enter another domain or subdomain in order to use cookieless static content.'),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Media server #1:'),
					'name' => '_MEDIA_SERVER_1_',
					'size' => 30,
					'p' => $this->l('Name of the second domain of your shop, (e.g., myshop-media-server-1.com). If you do not have another domain, leave this field blank')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Media server #2:'),
					'name' => '_MEDIA_SERVER_2_',
					'size' => 30,
					'p' => $this->l('Name of the third domain of your shop, (e.g., myshop-media-server-2.com). If you do not have another domain, leave this field blank')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Media server #3:'),
					'name' => '_MEDIA_SERVER_3_',
					'size' => 30,
					'p' => $this->l('Name of the fourth domain of your shop, (e.g., myshop-media-server-3.com). If you do not have another domain, leave this field blank')
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button',
				'name' => 'submitMediaServers',
				'id' => 'submitMediaServers'
			)
		);

		$this->fields_value = array(
			'_MEDIA_SERVER_1_' => Tools::getValue('_MEDIA_SERVER_1_', _MEDIA_SERVER_1_),
			'_MEDIA_SERVER_2_' => Tools::getValue('_MEDIA_SERVER_2_', _MEDIA_SERVER_2_),
			'_MEDIA_SERVER_3_' => Tools::getValue('_MEDIA_SERVER_3_', _MEDIA_SERVER_3_)
		);

		$this->display = 'formMediaServer';

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'media_server';
		$helper->identifier = 'media_server';
		$helper->override_folder = 'performance/';
		$helper->tpl_vars = $this->tpl_vars;
		$helper->first_call = false;
		$helper->toolbar_fix = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->fields_value;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Media servers (used only with CCC)');

		return $helper->generateForm($this->fields_form);
	}

	public function initCiphering()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Ciphering'),
				'image' => '../img/admin/computer_key.png'
			),
			'p' => $this->l('Mcrypt is faster than our custom BlowFish class, but requires the PHP extension "mcrypt". If you change this configuration, all cookies will be reset.'),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Algorithm:'),
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
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button',
				'name' => 'submitCiphering',
				'id' => 'submitCiphering'
			)
		);

		$this->fields_value = array('PS_CIPHER_ALGORITHM' => Configuration::get('PS_CIPHER_ALGORITHM'));

		$this->display = 'formCiphering';

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'ciphering';
		$helper->identifier = 'ciphering';
		$helper->override_folder = 'performance/';
		$helper->tpl_vars = $this->tpl_vars;
		$helper->first_call = false;
		$helper->toolbar_fix = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->fields_value;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Ciphering');

		return $helper->generateForm($this->fields_form);
	}

	public function initCaching()
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

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Caching'),
				'image' => '../img/admin/computer_key.png'
			),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Use cache:'),
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
					'p' => $this->l('Enable or disable caching system')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Caching system:'),
					'name' => 'caching_system',
					'options' => array(
						'query' => $caching_system,
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'text',
					'label' => $this->l('Directory depth:'),
					'name' => 'ps_cache_fs_directory_depth',
					'size' => 30
				),
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button',
				'name' => 'submitCaching',
				'id' => 'submitCaching'
			),
			'memcachedServers' => true
		);

		$depth = Configuration::get('PS_CACHEFS_DIRECTORY_DEPTH');
		$this->fields_value = array(
			'active' => _PS_CACHE_ENABLED_,
			'caching_system' => _PS_CACHING_SYSTEM_,
			'ps_cache_fs_directory_depth' => $depth ? $depth : 1
		);

		$this->tpl_vars = array('servers' => CacheMemcache::getMemcachedServers());

		$this->display = 'formCaching';

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'caching';
		$helper->identifier = 'caching';
		$helper->override_folder = 'performance/';
		$helper->tpl_vars = $this->tpl_vars;
		$helper->first_call = false;
		$helper->toolbar_fix = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->fields_value;
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Caching');

		return $helper->generateForm($this->fields_form);
	}

	public function initContent()
	{
		if (!extension_loaded('memcache'))
			$this->warnings[] = $this->l('To use Memcached, you must install the Memcache PECL extension on your server.').' 
				<a href="http://www.php.net/manual/en/memcache.installation.php">http://www.php.net/manual/en/memcache.installation.php</a>';
		if (!extension_loaded('apc'))
			$this->warnings[] = $this->l('To use APC, you must install the APC PECL extension on your server.').' 
				<a href="http://fr.php.net/manual/fr/apc.installation.php">http://fr.php.net/manual/fr/apc.installation.php</a>';
		if (!extension_loaded('xcache'))
			$this->warnings[] = $this->l('To use Xcache, you must install the Xcache extension on your server.').' 
				<a href="http://xcache.lighttpd.net">http://xcache.lighttpd.net</a>';

		if (!is_writable(_PS_CACHEFS_DIRECTORY_))
			$this->warnings[] = $this->l('To use CacheFS the directory').' '.realpath(_PS_CACHEFS_DIRECTORY_).' '.$this->l('must be writable');

		$this->content .= $this->initFormSmarty();
		$this->content .= $this->initFormFeaturesDetachables();
		$this->content .= $this->initFormCCC();
		$this->content .= $this->initFormMediaServer();
		$this->content .= $this->initCiphering();
		$this->content .= $this->initCaching();

		$this->context->smarty->assign(array(
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAddsmarty'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				Configuration::updateValue('PS_SMARTY_FORCE_COMPILE', Tools::getValue('smarty_force_compile', _PS_SMARTY_NO_COMPILE_));
				Configuration::updateValue('PS_SMARTY_CACHE', Tools::getValue('smarty_cache', 0));
				Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitFeaturesDetachables'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Combination::isCurrentlyUsed())
					Configuration::updateValue('PS_COMBINATION_FEATURE_ACTIVE', Tools::getValue('combination'));
				Configuration::updateValue('PS_FEATURE_FEATURE_ACTIVE', Tools::getValue('feature'));
				Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitCCC'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (
					!Configuration::updateValue('PS_CSS_THEME_CACHE', (int)Tools::getValue('PS_CSS_THEME_CACHE')) OR
					!Configuration::updateValue('PS_JS_THEME_CACHE', (int)Tools::getValue('PS_JS_THEME_CACHE')) OR
					!Configuration::updateValue('PS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HTML_THEME_COMPRESSION')) OR
					!Configuration::updateValue('PS_JS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_JS_HTML_THEME_COMPRESSION')) OR
					!Configuration::updateValue('PS_HIGH_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HIGH_HTML_THEME_COMPRESSION'))
				)
					$this->_errors[] = Tools::displayError('Unknown error.');
				else
					Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitMediaServers'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Tools::getValue('_MEDIA_SERVER_1_') != NULL AND !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_1_')))
					$this->_errors[] = Tools::displayError('Media server #1 is invalid');
				if (Tools::getValue('_MEDIA_SERVER_2_') != NULL AND !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_2_')))
					$this->_errors[] = Tools::displayError('Media server #2 is invalid');
				if (Tools::getValue('_MEDIA_SERVER_3_') != NULL AND !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_3_')))
					$this->_errors[] = Tools::displayError('Media server #3 is invalid');
				if (!sizeof($this->_errors))
				{
					$baseUrls = array();
					$baseUrls['_MEDIA_SERVER_1_'] = Tools::getValue('_MEDIA_SERVER_1_');
					$baseUrls['_MEDIA_SERVER_2_'] = Tools::getValue('_MEDIA_SERVER_2_');
					$baseUrls['_MEDIA_SERVER_3_'] = Tools::getValue('_MEDIA_SERVER_3_');
					rewriteSettingsFile($baseUrls, NULL, NULL);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_1_']);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_2_']);
					unset($this->_fieldsGeneral['_MEDIA_SERVER_3_']);
					Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitCiphering') AND Configuration::get('PS_CIPHER_ALGORITHM') != (int)Tools::getValue('PS_CIPHER_ALGORITHM'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$algo = (int)Tools::getValue('PS_CIPHER_ALGORITHM');
				$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
				if ($algo)
				{
					if (!function_exists('mcrypt_encrypt'))
						$this->_errors[] = Tools::displayError('Mcrypt is not activated on this server.');
					else
					{
						if (!strstr($settings, '_RIJNDAEL_KEY_'))
						{
							$key_size = mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
							$key = Tools::passwdGen($key_size);
							$settings = preg_replace('/define\(\'_COOKIE_KEY_\', \'([a-z0-9=\/+-_]+)\'\);/i', 'define(\'_COOKIE_KEY_\', \'\1\');'."\n".'define(\'_RIJNDAEL_KEY_\', \''.$key.'\');', $settings);
						}
						if (!strstr($settings, '_RIJNDAEL_IV_'))
						{
							$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
							$iv = base64_encode(mcrypt_create_iv($iv_size, MCRYPT_RAND));
							$settings = preg_replace('/define\(\'_COOKIE_IV_\', \'([a-z0-9=\/+-_]+)\'\);/i', 'define(\'_COOKIE_IV_\', \'\1\');'."\n".'define(\'_RIJNDAEL_IV_\', \''.$iv.'\');', $settings);
						}
					}
				}
				if (!count($this->_errors))
				{
					if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
					{
						Configuration::updateValue('PS_CIPHER_ALGORITHM', $algo);
						Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					}
					else
						$this->_errors[] = Tools::displayError('Cannot overwrite settings file.');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitCaching'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$settings = file_get_contents(dirname(__FILE__).'/../../config/settings.inc.php');
				if (!Tools::getValue('active'))
					$cache_active = 0;
				else
					$cache_active = 1;
				if (!$caching_system = Tools::getValue('caching_system'))
					$this->_errors[] = Tools::displayError('Caching system is missing');
				else
					$settings = preg_replace('/define\(\'_PS_CACHING_SYSTEM_\', \'([a-z0-9=\/+-_]+)\'\);/Ui', 'define(\'_PS_CACHING_SYSTEM_\', \''.$caching_system.'\');', $settings);
				if ($cache_active && $caching_system == 'CacheMemcache' && !extension_loaded('memcache'))
					$this->_errors[] = Tools::displayError('To use Memcached, you must install the Memcache PECL extension on your server.').' <a href="http://www.php.net/manual/en/memcache.installation.php">http://www.php.net/manual/en/memcache.installation.php</a>';
				else if ($cache_active && $caching_system == 'CacheApc' && !extension_loaded('apc'))
					$this->_errors[] = Tools::displayError('To use APC cache, you must install the APC PECL extension on your server.').' <a href="http://fr.php.net/manual/fr/apc.installation.php">http://fr.php.net/manual/fr/apc.installation.php</a>';
				else if ($cache_active && $caching_system == 'CacheXcache' && !extension_loaded('xcache'))
					$this->_errors[] = Tools::displayError('To use Xcache, you must install the Xcache extension on your server.').' <a href="http://xcache.lighttpd.net">http://xcache.lighttpd.net</a>';
				else if ($cache_active && $caching_system == 'CacheFs' && !is_writable(_PS_CACHEFS_DIRECTORY_))
					$this->_errors[] = Tools::displayError('To use CacheFS the directory').' '.realpath(_PS_CACHEFS_DIRECTORY_).' '.Tools::displayError('must be writable');

				if ($caching_system == 'CacheFs')
				{
					if (!($depth = Tools::getValue('ps_cache_fs_directory_depth')))
						$this->_errors[] = Tools::displayError('Please set a directory depth');
					if (!sizeof($this->_errors))
					{
						CacheFs::deleteCacheDirectory();
						CacheFs::createCacheDirectories((int)$depth);
						Configuration::updateValue('PS_CACHEFS_DIRECTORY_DEPTH', (int)$depth);
					}
				}
				if (!sizeof($this->_errors))
				{
					$settings = preg_replace('/define\(\'_PS_CACHE_ENABLED_\', \'([0-9])\'\);/Ui', 'define(\'_PS_CACHE_ENABLED_\', \''.(int)$cache_active.'\');', $settings);
					if (file_put_contents(dirname(__FILE__).'/../../config/settings.inc.php', $settings))
						Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					else
						$this->_errors[] = Tools::displayError('Cannot overwrite settings file.');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		if (Tools::isSubmit('submitAddServer'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (!Tools::getValue('memcachedIp'))
					$this->_errors[] = Tools::displayError('Memcached IP is missing');
				if (!Tools::getValue('memcachedPort'))
					$this->_errors[] = Tools::displayError('Memcached port is missing');
				if (!Tools::getValue('memcachedWeight'))
					$this->_errors[] = Tools::displayError('Memcached weight is missing');
				if (!sizeof($this->_errors))
				{
					if (CacheMemcache::addServer(pSQL(Tools::getValue('memcachedIp')), (int)Tools::getValue('memcachedPort'), (int)Tools::getValue('memcachedWeight')))
						Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
					else
						$this->_errors[] = Tools::displayError('Cannot add Memcached server');
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}

		if (Tools::getValue('deleteMemcachedServer'))
		{
			if ($this->tabAccess['add'] === '1')
			{
				if (CacheMemcache::deleteServer((int)Tools::getValue('deleteMemcachedServer')))
					Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
				else
					$this->_errors[] = Tools::displayError('Error in deleting Memcached server');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}

		return parent::postProcess();
	}
}


