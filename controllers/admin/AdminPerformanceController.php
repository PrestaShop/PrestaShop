<?php
/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Symfony\Component\Yaml\Yaml;

/**
 * @property Configuration $object
 */
class AdminPerformanceControllerCore extends AdminController
{
    const DEBUG_MODE_SUCCEEDED = 0;
    const DEBUG_MODE_ERROR_NO_READ_ACCESS = 1;
    const DEBUG_MODE_ERROR_NO_READ_ACCESS_CUSTOM = 2;
    const DEBUG_MODE_ERROR_NO_WRITE_ACCESS = 3;
    const DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM = 4;
    const DEBUG_MODE_ERROR_NO_DEFINITION_FOUND = 5;


    public function __construct()
    {
        $this->bootstrap = true;
        $this->className = 'Configuration';
        parent::__construct();
    }

    public function initFieldsetSmarty()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Smarty'),
                'icon' => 'icon-briefcase'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'smarty_up'
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Template compilation'),
                    'name' => 'smarty_force_compile',
                    'values' => array(
                        array(
                            'id' => 'smarty_force_compile_'._PS_SMARTY_NO_COMPILE_,
                            'value' => _PS_SMARTY_NO_COMPILE_,
                            'label' => $this->l('Never recompile template files'),
                            'hint' => $this->l('This option should be used in a production environment.')
                        ),
                        array(
                            'id' => 'smarty_force_compile_'._PS_SMARTY_CHECK_COMPILE_,
                            'value' => _PS_SMARTY_CHECK_COMPILE_,
                            'label' => $this->l('Recompile templates if the files have been updated'),
                            'hint' => $this->l('Templates are recompiled when they are updated. If you experience compilation troubles when you update your template files, you should use Force Compile instead of this option. It should never be used in a production environment.')
                        ),
                        array(
                            'id' => 'smarty_force_compile_'._PS_SMARTY_FORCE_COMPILE_,
                            'value' => _PS_SMARTY_FORCE_COMPILE_,
                            'label' => $this->l('Force compilation'),
                            'hint' => $this->l('This forces Smarty to (re)compile templates on every invocation. This is handy for development and debugging. Note: This should never be used in a production environment.')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Cache'),
                    'name' => 'smarty_cache',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'smarty_cache_1',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'smarty_cache_0',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Should be enabled except for debugging.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Multi-front optimizations'),
                    'name' => 'smarty_local',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'smarty_local_1',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'smarty_local_0',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Should be enabled if you want to avoid to store the smarty cache on NFS.')
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Clear cache'),
                    'name' => 'smarty_clear_cache',
                    'values' => array(
                        array(
                            'id' => 'smarty_clear_cache_never',
                            'value' => 'never',
                            'label' => $this->l('Never clear cache files'),
                        ),
                        array(
                            'id' => 'smarty_clear_cache_everytime',
                            'value' => 'everytime',
                            'label' => $this->l('Clear cache everytime something has been modified'),
                        ),
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );

        $this->fields_value['smarty_force_compile'] = Configuration::get('PS_SMARTY_FORCE_COMPILE');
        $this->fields_value['smarty_cache'] = Configuration::get('PS_SMARTY_CACHE');
        $this->fields_value['smarty_local'] = Configuration::get('PS_SMARTY_LOCAL');
        $this->fields_value['smarty_caching_type'] = Configuration::get('PS_SMARTY_CACHING_TYPE');
        $this->fields_value['smarty_clear_cache'] = Configuration::get('PS_SMARTY_CLEAR_CACHE');
        $this->fields_value['smarty_console'] = Configuration::get('PS_SMARTY_CONSOLE');
        $this->fields_value['smarty_console_key'] = Configuration::get('PS_SMARTY_CONSOLE_KEY');
    }

    public function initFieldsetDebugMode()
    {
        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Debug mode'),
                'icon' => 'icon-bug'
            ),
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Disable non PrestaShop modules'),
                    'name' => 'native_module',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'native_module_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'native_module_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Enable or disable non PrestaShop Modules.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Disable all overrides'),
                    'name' => 'overrides',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'overrides_module_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'overrides_module_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Enable or disable all classes and controllers overrides.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Debug mode'),
                    'name' => 'debug_mode',
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'debug_mode_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'debug_mode_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Enable or disable debug mode.')
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );

        $this->fields_value['native_module'] = Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        $this->fields_value['overrides'] = Configuration::get('PS_DISABLE_OVERRIDES');
        $this->fields_value['debug_mode'] = $this->isDebugModeEnabled();
    }

    public function initFieldsetFeaturesDetachables()
    {
        $this->fields_form[2]['form'] = array(
            'legend' => array(
                'title' => $this->l('Optional features'),
                'icon' => 'icon-puzzle-piece'
            ),
            'description' => $this->l('Some features can be disabled in order to improve performance.'),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'features_detachables_up'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Combinations'),
                    'name' => 'combination',
                    'is_bool' => true,
                    'disabled' => Combination::isCurrentlyUsed(),
                    'values' => array(
                        array(
                            'id' => 'combination_1',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'combination_0',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Choose "No" to disable Product Combinations.'),
                    'desc' => Combination::isCurrentlyUsed() ? $this->l('You cannot set this parameter to No when combinations are already used by some of your products') : null
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Features'),
                    'name' => 'feature',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'feature_1',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'feature_0',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Choose "No" to disable Product Features.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Customer Groups'),
                    'name' => 'customer_group',
                    'is_bool' => true,
                    'disabled' => Group::isCurrentlyUsed(),
                    'values' => array(
                        array(
                            'id' => 'group_1',
                            'value' => 1,
                            'label' => $this->trans('Yes', array(), 'Admin.Global'),
                        ),
                        array(
                            'id' => 'group_0',
                            'value' => 0,
                            'label' => $this->trans('No', array(), 'Admin.Global')
                        )
                    ),
                    'hint' => $this->l('Choose "No" to disable Customer Groups.')
                )
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );

        $this->fields_value['combination'] = Combination::isFeatureActive();
        $this->fields_value['feature'] = Feature::isFeatureActive();
        $this->fields_value['customer_group'] = Group::isFeatureActive();
    }

    public function initFieldsetCCC()
    {
        $this->fields_form[3]['form'] = array(
            'legend' => array(
                'title' => $this->l('CCC (Combine, Compress and Cache)'),
                'icon' => 'icon-fullscreen'
            ),
            'description' => $this->l('CCC allows you to reduce the loading time of your page. With these settings you will gain performance without even touching the code of your theme. Make sure, however, that your theme is compatible with PrestaShop 1.4+. Otherwise, CCC will cause problems.'),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'ccc_up',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Smart cache for CSS'),
                    'name' => 'PS_CSS_THEME_CACHE',
                    'values' => array(
                        array(
                            'id' => 'PS_CSS_THEME_CACHE_1',
                            'value' => 1,
                            'label' => $this->l('Use CCC for CSS')
                        ),
                        array(
                            'id' => 'PS_CSS_THEME_CACHE_0',
                            'value' => 0,
                            'label' => $this->l('Keep CSS as original')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Smart cache for JavaScript'),
                    'name' => 'PS_JS_THEME_CACHE',
                    'values' => array(
                        array(
                            'id' => 'PS_JS_THEME_CACHE_1',
                            'value' => 1,
                            'label' => $this->l('Use CCC for JavaScript')
                        ),
                        array(
                            'id' => 'PS_JS_THEME_CACHE_0',
                            'value' => 0,
                            'label' => $this->l('Keep JavaScript as original')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Minify HTML'),
                    'name' => 'PS_HTML_THEME_COMPRESSION',
                    'values' => array(
                        array(
                            'id' => 'PS_HTML_THEME_COMPRESSION_1',
                            'value' => 1,
                            'label' => $this->l('Minify HTML after "Smarty compile" execution')
                        ),
                        array(
                            'id' => 'PS_HTML_THEME_COMPRESSION_0',
                            'value' => 0,
                            'label' => $this->l('Keep HTML as original')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Compress inline JavaScript in HTML'),
                    'name' => 'PS_JS_HTML_THEME_COMPRESSION',
                    'values' => array(
                        array(
                            'id' => 'PS_JS_HTML_THEME_COMPRESSION_1',
                            'value' => 1,
                            'label' => $this->l('Compress inline JavaScript in HTML after "Smarty compile" execution')
                        ),
                        array(
                            'id' => 'PS_JS_HTML_THEME_COMPRESSION_0',
                            'value' => 0,
                            'label' => $this->l('Keep inline JavaScript in HTML as original')
                        )
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Move JavaScript to the end'),
                    'name' => 'PS_JS_DEFER',
                    'values' => array(
                        array(
                            'id' => 'PS_JS_DEFER_1',
                            'value' => 1,
                            'label' => $this->l('Move JavaScript to the end of the HTML document')
                        ),
                        array(
                            'id' => 'PS_JS_DEFER_0',
                            'value' => 0,
                            'label' => $this->l('Keep JavaScript in HTML at its original position')
                        )
                    )
                ),

            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_form[3]['form']['input'][] = array(
                'type' => 'switch',
                'label' => $this->l('Apache optimization'),
                'name' => 'PS_HTACCESS_CACHE_CONTROL',
                'hint' => $this->l('This will add directives to your .htaccess file, which should improve caching and compression.'),
                'values' => array(
                    array(
                        'id' => 'PS_HTACCESS_CACHE_CONTROL_1',
                        'value' => 1,
                        'label' => $this->trans('Yes', array(), 'Admin.Global'),
                    ),
                    array(
                        'id' => 'PS_HTACCESS_CACHE_CONTROL_0',
                        'value' => 0,
                        'label' => $this->trans('No', array(), 'Admin.Global'),
                    ),
                ),
            );
        }

        $this->fields_value['PS_CSS_THEME_CACHE'] = Configuration::get('PS_CSS_THEME_CACHE');
        $this->fields_value['PS_JS_THEME_CACHE'] = Configuration::get('PS_JS_THEME_CACHE');
        $this->fields_value['PS_HTML_THEME_COMPRESSION'] = Configuration::get('PS_HTML_THEME_COMPRESSION');
        $this->fields_value['PS_JS_HTML_THEME_COMPRESSION'] = Configuration::get('PS_JS_HTML_THEME_COMPRESSION');
        $this->fields_value['PS_HTACCESS_CACHE_CONTROL'] = Configuration::get('PS_HTACCESS_CACHE_CONTROL');
        $this->fields_value['PS_JS_DEFER'] = Configuration::get('PS_JS_DEFER');
        $this->fields_value['ccc_up'] = 1;
    }

    public function initFieldsetMediaServer()
    {
        $this->fields_form[4]['form'] = array(
            'legend' => array(
                'title' => $this->l('Media servers (use only with CCC)'),
                'icon' => 'icon-link'
            ),
            'description' => $this->l('You must enter another domain, or subdomain, in order to use cookieless static content.'),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'media_server_up'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Media server #1'),
                    'name' => '_MEDIA_SERVER_1_',
                    'hint' => $this->l('Name of the second domain of your shop, (e.g. myshop-media-server-1.com). If you do not have another domain, leave this field blank.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Media server #2'),
                    'name' => '_MEDIA_SERVER_2_',
                    'hint' => $this->l('Name of the third domain of your shop, (e.g. myshop-media-server-2.com). If you do not have another domain, leave this field blank.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Media server #3'),
                    'name' => '_MEDIA_SERVER_3_',
                    'hint' => $this->l('Name of the fourth domain of your shop, (e.g. myshop-media-server-3.com). If you do not have another domain, leave this field blank.')
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );

        $this->fields_value['_MEDIA_SERVER_1_'] = Configuration::get('PS_MEDIA_SERVER_1');
        $this->fields_value['_MEDIA_SERVER_2_'] = Configuration::get('PS_MEDIA_SERVER_2');
        $this->fields_value['_MEDIA_SERVER_3_'] = Configuration::get('PS_MEDIA_SERVER_3');
    }

    public function initFieldsetCaching()
    {
        $phpdoc_langs = array('en', 'zh', 'fr', 'de', 'ja', 'pl', 'ro', 'ru', 'fa', 'es', 'tr');
        $php_lang = in_array($this->context->language->iso_code, $phpdoc_langs) ? $this->context->language->iso_code : 'en';

        $warning_memcache = ' '.$this->l('(you must install the [a]Memcache PECL extension[/a])');
        $warning_memcache = str_replace('[a]', '<a href="http://www.php.net/manual/'.substr($php_lang, 0, 2).'/memcache.installation.php" target="_blank">', $warning_memcache);
        $warning_memcache = str_replace('[/a]', '</a>', $warning_memcache);

        $warning_memcached = ' '.$this->l('(you must install the [a]Memcached PECL extension[/a])');
        $warning_memcached = str_replace('[a]', '<a href="http://www.php.net/manual/'.substr($php_lang, 0, 2).'/memcached.installation.php" target="_blank">', $warning_memcached);
        $warning_memcached = str_replace('[/a]', '</a>', $warning_memcached);

        $warning_apc = ' '.$this->l('(you must install the [a]APC PECL extension[/a])');
        $warning_apc = str_replace('[a]', '<a href="http://php.net/manual/'.substr($php_lang, 0, 2).'/apc.installation.php" target="_blank">', $warning_apc);
        $warning_apc = str_replace('[/a]', '</a>', $warning_apc);

        $warning_xcache = ' '.$this->l('(you must install the [a]Xcache extension[/a])');
        $warning_xcache = str_replace('[a]', '<a href="http://xcache.lighttpd.net" target="_blank">', $warning_xcache);
        $warning_xcache = str_replace('[/a]', '</a>', $warning_xcache);

        $warning_fs = ' '.sprintf($this->l('(the directory %s must be writable)'), realpath(_PS_CACHEFS_DIRECTORY_));

        $this->fields_form[6]['form'] = array(
            'legend' => array(
                'title' => $this->l('Caching'),
                'icon' => 'icon-desktop'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'cache_up'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Use cache'),
                    'name' => 'cache_active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'cache_active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled', array(), 'Admin.Global')
                        ),
                        array(
                            'id' => 'cache_active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled', array(), 'Admin.Global')
                        )
                    )
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Caching system'),
                    'name' => 'caching_system',
                    'hint' => $this->l('The CacheFS system should be used only when the infrastructure contains one front-end server. If you are not sure, ask your hosting company.'),
                    'values' => array(
                        array(
                            'id' => 'CacheMemcache',
                            'value' => 'CacheMemcache',
                            'label' => $this->l('Memcached via PHP::Memcache').(extension_loaded('memcache') ? '' : $warning_memcache)
                        ),
                        array(
                            'id' => 'CacheMemcached',
                            'value' => 'CacheMemcached',
                            'label' => $this->l('Memcached via PHP::Memcached').(extension_loaded('memcached') ? '' : $warning_memcached)
                        ),
                        array(
                            'id' => 'CacheApc',
                            'value' => 'CacheApc',
                            'label' => $this->l('APC').((extension_loaded('apc') || extension_loaded('apcu'))? '' : $warning_apc)
                        ),
                        array(
                            'id' => 'CacheXcache',
                            'value' => 'CacheXcache',
                            'label' => $this->l('Xcache').(extension_loaded('xcache') ? '' : $warning_xcache)
                        ),

                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Directory depth'),
                    'name' => 'ps_cache_fs_directory_depth'
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            ),
            'memcachedServers' => true
        );

        $depth = Configuration::get('PS_CACHEFS_DIRECTORY_DEPTH');
        $this->fields_value['cache_active'] = _PS_CACHE_ENABLED_;
        $this->fields_value['caching_system'] = _PS_CACHING_SYSTEM_;
        $this->fields_value['ps_cache_fs_directory_depth'] = $depth ? $depth : 1;

        $this->tpl_form_vars['servers'] = CacheMemcache::getMemcachedServers();
        $this->tpl_form_vars['_PS_CACHE_ENABLED_'] = _PS_CACHE_ENABLED_;
    }

    public function renderForm()
    {
        $this->initFieldsetSmarty();
        $this->initFieldsetDebugMode();
        $this->initFieldsetFeaturesDetachables();
        $this->initFieldsetCCC();

        if (!defined('_PS_HOST_MODE_')) {
            $this->initFieldsetMediaServer();
            $this->initFieldsetCaching();
        }

        // Reindex fields
        $this->fields_form = array_values($this->fields_form);

        // Activate multiple fieldset
        $this->multiple_fieldsets = true;

        return parent::renderForm();
    }

    public function initContent()
    {
        $this->initTabModuleList();
        $this->initToolbar();
        $this->initPageHeaderToolbar();
        $this->display = '';
        $this->content .= $this->renderForm();

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_btn['clear_cache'] = array(
            'href' => self::$currentIndex.'&token='.$this->token.'&empty_smarty_cache=1&empty_sf2_cache=1',
            'desc' => $this->l('Clear cache'),
            'icon' => 'process-icon-eraser'
        );
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = $this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error');
            return;
        }

        Hook::exec('action'.get_class($this).ucfirst($this->action).'Before', array('controller' => $this));
        if (Tools::isSubmit('submitAddServer')) {
            if ($this->access('add')) {
                if (!Tools::getValue('memcachedIp')) {
                    $this->errors[] = $this->trans('The Memcached IP is missing.', array(), 'Admin.Parameters.Notification');
                }
                if (!Tools::getValue('memcachedPort')) {
                    $this->errors[] = $this->trans('The Memcached port is missing.', array(), 'Admin.Parameters.Notification');
                }
                if (!Tools::getValue('memcachedWeight')) {
                    $this->errors[] = $this->trans('The Memcached weight is missing.', array(), 'Admin.Parameters.Notification');
                }
                if (!count($this->errors)) {
                    if (CacheMemcache::addServer(pSQL(Tools::getValue('memcachedIp')),
                        (int)Tools::getValue('memcachedPort'),
                        (int)Tools::getValue('memcachedWeight'))) {
                        Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
                    } else {
                        $this->errors[] = $this->trans('The Memcached server cannot be added.', array(), 'Admin.Parameters.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to add this.', array(), 'Admin.Notifications.Error');
            }
        }

        if (Tools::getValue('deleteMemcachedServer')) {
            if ($this->access('add')) {
                if (CacheMemcache::deleteServer((int)Tools::getValue('deleteMemcachedServer'))) {
                    Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
                } else {
                    $this->errors[] = $this->trans('There was an error when attempting to delete the Memcached server.', array(), 'Admin.Parameters.Notification');
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to delete this.', array(), 'Admin.Notifications.Error');
            }
        }

        $redirectAdmin = false;
        if ((bool)Tools::getValue('smarty_up')) {
            if ($this->access('edit')) {
                Configuration::updateValue('PS_SMARTY_FORCE_COMPILE', Tools::getValue('smarty_force_compile', _PS_SMARTY_NO_COMPILE_));

                if (Configuration::get('PS_SMARTY_CACHE') != Tools::getValue('smarty_cache')) {
                    Tools::clearSmartyCache();
                }

                Configuration::updateValue('PS_SMARTY_CACHE', Tools::getValue('smarty_cache', 0));
                Configuration::updateValue('PS_SMARTY_CLEAR_CACHE', Tools::getValue('smarty_clear_cache'));
                Configuration::updateValue('PS_SMARTY_LOCAL', Tools::getValue('smarty_local', 0));
                $redirectAdmin = true;
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        if ((bool)Tools::getValue('features_detachables_up')) {
            if ($this->access('edit')) {
                if (Tools::isSubmit('combination')) {
                    if ((!Tools::getValue('combination') && Combination::isCurrentlyUsed()) === false) {
                        Configuration::updateValue('PS_COMBINATION_FEATURE_ACTIVE', (bool)Tools::getValue('combination'));
                    }
                }

                if (Tools::isSubmit('customer_group')) {
                    if ((!Tools::getValue('customer_group') && Group::isCurrentlyUsed()) === false) {
                        Configuration::updateValue('PS_GROUP_FEATURE_ACTIVE', (bool)Tools::getValue('customer_group'));
                    }
                }

                Configuration::updateValue('PS_FEATURE_FEATURE_ACTIVE', (bool)Tools::getValue('feature'));
                $redirectAdmin = true;
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        if ((bool)Tools::getValue('ccc_up')) {
            if ($this->access('edit')) {
                $theme_cache_directory = _PS_ALL_THEMES_DIR_.$this->context->shop->theme_directory.'/cache/';
                @mkdir($theme_cache_directory, 0777, true);
                if (((bool)Tools::getValue('PS_CSS_THEME_CACHE') || (bool)Tools::getValue('PS_JS_THEME_CACHE')) && !is_writable($theme_cache_directory)) {
                    $this->errors[] = $this->trans(
                        'To use Smart Cache, the directory %directorypath% must be writable.',
                        array(
                            '%directorypath%' => realpath($theme_cache_directory)
                        ),
                        'Admin.Parameters.Notification'
                    );
                }

                if ($tmp = (int)Tools::getValue('PS_CSS_THEME_CACHE')) {
                    $version = (int)Configuration::get('PS_CCCCSS_VERSION');
                    if (Configuration::get('PS_CSS_THEME_CACHE') != $tmp) {
                        Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);
                    }
                }

                if ($tmp = (int)Tools::getValue('PS_JS_THEME_CACHE')) {
                    $version = (int)Configuration::get('PS_CCCJS_VERSION');
                    if (Configuration::get('PS_JS_THEME_CACHE') != $tmp) {
                        Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
                    }
                }

                if (!Configuration::updateValue('PS_CSS_THEME_CACHE', (int)Tools::getValue('PS_CSS_THEME_CACHE')) ||
                    !Configuration::updateValue('PS_JS_THEME_CACHE', (int)Tools::getValue('PS_JS_THEME_CACHE')) ||
                    !Configuration::updateValue('PS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_HTML_THEME_COMPRESSION')) ||
                    !Configuration::updateValue('PS_JS_HTML_THEME_COMPRESSION', (int)Tools::getValue('PS_JS_HTML_THEME_COMPRESSION')) ||
                    !Configuration::updateValue('PS_JS_DEFER', (int)Tools::getValue('PS_JS_DEFER')) ||
                    !Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', (int)Tools::getValue('PS_HTACCESS_CACHE_CONTROL'))) {
                    $this->errors[] = $this->trans('Unknown error.', array(), 'Admin.Notifications.Error');
                } else {
                    $redirectAdmin = true;
                    if (Configuration::get('PS_HTACCESS_CACHE_CONTROL')) {
                        if (is_writable(_PS_ROOT_DIR_.'/.htaccess')) {
                            Tools::generateHtaccess();
                        } else {
                            $message = $this->l('Before being able to use this tool, you need to:');
                            $message .= '<br />- '.$this->l('Create a blank .htaccess in your root directory.');
                            $message .= '<br />- '.$this->l('Give it write permissions (CHMOD 666 on Unix system).');
                            $this->errors[] = Tools::displayError($message, false);
                            Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', false);
                        }
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        if ((bool)Tools::getValue('media_server_up') && !defined('_PS_HOST_MODE_')) {
            if ($this->access('edit')) {
                if (Tools::getValue('_MEDIA_SERVER_1_') != null && !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_1_'))) {
                    $this->errors[] = $this->trans('Media server #1 is invalid', array(), 'Admin.Parameters.Notification');
                }
                if (Tools::getValue('_MEDIA_SERVER_2_') != null && !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_2_'))) {
                    $this->errors[] = $this->trans('Media server #2 is invalid', array(), 'Admin.Parameters.Notification');
                }
                if (Tools::getValue('_MEDIA_SERVER_3_') != null && !Validate::isFileName(Tools::getValue('_MEDIA_SERVER_3_'))) {
                    $this->errors[] = $this->trans('Media server #3 is invalid', array(), 'Admin.Parameters.Notification');
                }
                if (!count($this->errors)) {
                    $base_urls = array();
                    $base_urls['_MEDIA_SERVER_1_'] = Tools::getValue('_MEDIA_SERVER_1_');
                    $base_urls['_MEDIA_SERVER_2_'] = Tools::getValue('_MEDIA_SERVER_2_');
                    $base_urls['_MEDIA_SERVER_3_'] = Tools::getValue('_MEDIA_SERVER_3_');
                    if ($base_urls['_MEDIA_SERVER_1_'] || $base_urls['_MEDIA_SERVER_2_'] || $base_urls['_MEDIA_SERVER_3_']) {
                        Configuration::updateValue('PS_MEDIA_SERVERS', 1);
                    } else {
                        Configuration::updateValue('PS_MEDIA_SERVERS', 0);
                    }
                    rewriteSettingsFile($base_urls, null, null);
                    Configuration::updateValue('PS_MEDIA_SERVER_1', Tools::getValue('_MEDIA_SERVER_1_'));
                    Configuration::updateValue('PS_MEDIA_SERVER_2', Tools::getValue('_MEDIA_SERVER_2_'));
                    Configuration::updateValue('PS_MEDIA_SERVER_3', Tools::getValue('_MEDIA_SERVER_3_'));
                    Tools::clearSmartyCache();
                    Media::clearCache();

                    if (is_writable(_PS_ROOT_DIR_.'/.htaccess')) {
                        Tools::generateHtaccess(null, null, null, '', null, array($base_urls['_MEDIA_SERVER_1_'], $base_urls['_MEDIA_SERVER_2_'], $base_urls['_MEDIA_SERVER_3_']));
                        unset($this->_fieldsGeneral['_MEDIA_SERVER_1_']);
                        unset($this->_fieldsGeneral['_MEDIA_SERVER_2_']);
                        unset($this->_fieldsGeneral['_MEDIA_SERVER_3_']);
                        $redirectAdmin = true;
                    } else {
                        $message = $this->l('Before being able to use this tool, you need to:');
                        $message .= '<br />- '.$this->l('Create a blank .htaccess in your root directory.');
                        $message .= '<br />- '.$this->l('Give it write permissions (CHMOD 666 on Unix system).');
                        $this->errors[] = Tools::displayError($message, false);
                        Configuration::updateValue('PS_HTACCESS_CACHE_CONTROL', false);
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        if ((bool)Tools::getValue('cache_up')) {
            if ($this->access('edit')) {
                $config = Yaml::parse(_PS_ROOT_DIR_.'/app/config/parameters.yml');

                $cache_active = (bool)Tools::getValue('cache_active');

                if ($caching_system = preg_replace('[^a-zA-Z0-9]', '', Tools::getValue('caching_system'))) {
                    $config['parameters']['ps_caching'] = $caching_system;
                } else {
                    $cache_active = false;
                    $this->errors[] = $this->trans('The caching system is missing.', array(), 'Admin.Parameters.Notification');
                }
                if ($cache_active) {
                    if ($caching_system == 'CacheMemcache' && !extension_loaded('memcache')) {
                        $this->errors[] = $this->trans('To use Memcached, you must install the Memcache PECL extension on your server.', array(), 'Admin.Parameters.Notification').'
							<a href="http://www.php.net/manual/en/memcache.installation.php">http://www.php.net/manual/en/memcache.installation.php</a>';
                    } elseif ($caching_system == 'CacheMemcached' && !extension_loaded('memcached')) {
                        $this->errors[] = $this->trans('To use Memcached, you must install the Memcached PECL extension on your server.', array(), 'Admin.Parameters.Notification').'
							<a href="http://www.php.net/manual/en/memcached.installation.php">http://www.php.net/manual/en/memcached.installation.php</a>';
                    } elseif ($caching_system == 'CacheApc'  && !extension_loaded('apc') && !extension_loaded('apcu')) {
                        $this->errors[] = $this->trans('To use APC cache, you must install the APC PECL extension on your server.', array(), 'Admin.Parameters.Notification').'
							<a href="http://fr.php.net/manual/fr/apc.installation.php">http://fr.php.net/manual/fr/apc.installation.php</a>';
                    } elseif ($caching_system == 'CacheXcache' && !extension_loaded('xcache')) {
                        $this->errors[] = $this->trans('To use Xcache, you must install the Xcache extension on your server.', array(), 'Admin.Parameters.Notification').'
							<a href="http://xcache.lighttpd.net">http://xcache.lighttpd.net</a>';
                    } elseif ($caching_system == 'CacheXcache' && !ini_get('xcache.var_size')) {
                        $this->errors[] = $this->trans('To use Xcache, you must configure "xcache.var_size" for the Xcache extension (recommended value 16M to 64M).', array(), 'Admin.Parameters.Notification').'
							<a href="http://xcache.lighttpd.net/wiki/XcacheIni">http://xcache.lighttpd.net/wiki/XcacheIni</a>';
                    } elseif ($caching_system == 'CacheFs') {
                        if (!is_dir(_PS_CACHEFS_DIRECTORY_)) {
                            @mkdir(_PS_CACHEFS_DIRECTORY_, 0777, true);
                        } elseif (!is_writable(_PS_CACHEFS_DIRECTORY_)) {
                            $this->errors[] = $this->trans(
                                'To use CacheFS, the directory %directorypath% must be writable.',
                                array(
                                    '%directorypath%' => realpath(_PS_CACHEFS_DIRECTORY_)
                                ),
                                'Admin.Parameters.Notification'
                            );
                        }
                    }

                    if ($caching_system == 'CacheFs') {
                        if (!($depth = Tools::getValue('ps_cache_fs_directory_depth'))) {
                            $this->errors[] = $this->trans('Please set a directory depth.', array(), 'Admin.Parameters.Notification');
                        }
                        if (!count($this->errors)) {
                            CacheFs::deleteCacheDirectory();
                            CacheFs::createCacheDirectories((int)$depth);
                            Configuration::updateValue('PS_CACHEFS_DIRECTORY_DEPTH', (int)$depth);
                        }
                    } elseif ($caching_system == 'CacheMemcache' && !_PS_CACHE_ENABLED_ && _PS_CACHING_SYSTEM_ == 'CacheMemcache') {
                        Cache::getInstance()->flush();
                    } elseif ($caching_system == 'CacheMemcached' && !_PS_CACHE_ENABLED_ && _PS_CACHING_SYSTEM_ == 'CacheMemcached') {
                        Cache::getInstance()->flush();
                    }
                }

                if (!count($this->errors)) {
                    $config['parameters']['ps_cache_enable'] = $cache_active;
                    // If there is not settings file modification or if the backup and replacement of the settings file worked
                    if (file_put_contents(_PS_ROOT_DIR_.'/app/config/parameters.yml', Yaml::dump($config))) {
                        if (function_exists('opcache_invalidate')) {
                            opcache_invalidate(_PS_ROOT_DIR_.'/app/config/parameters.yml');
                        }
                        $redirectAdmin = true;
                    } else {
                        $this->errors[] = $this->trans('The settings file cannot be overwritten.', array(), 'Admin.Parameters.Notification');
                    }
                }
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', array(), 'Admin.Notifications.Error');
            }
        }

        if ((bool)Tools::getValue('empty_smarty_cache')) {
            $redirectAdmin = true;
            Tools::clearSmartyCache();
            Tools::clearXMLCache();
            Media::clearCache();
            Tools::generateIndex();
        }

        if ((bool)Tools::getValue('empty_sf2_cache')) {
            $redirectAdmin = true;

            $sf2Refresh = new \PrestaShopBundle\Service\Cache\Refresh();
            $sf2Refresh->addCacheClear(_PS_MODE_DEV_ ? 'dev' : 'prod');
            $sf2Refresh->execute();
        }

        if (Tools::isSubmit('submitAddconfiguration')) {
            Configuration::updateGlobalValue('PS_DISABLE_NON_NATIVE_MODULE', (int)Tools::getValue('native_module'));
            Configuration::updateGlobalValue('PS_DISABLE_OVERRIDES', (int)Tools::getValue('overrides'));
            if (Tools::isSubmit('debug_mode') && (bool)Tools::getValue('debug_mode')) {
                $debug_mode_status = $this->enableDebugMode();
            } else {
                $debug_mode_status = $this->disableDebugMode();
            }

            if (!empty($debug_mode_status)) {
                switch ($debug_mode_status) {
                    case self::DEBUG_MODE_ERROR_COULD_NOT_BACKUP:
                        $this->errors[] = Tools::displayError(sprintf($this->l('Error: could not write to file. Make sure that the correct permissions are set on the file %s'), _PS_ROOT_DIR_.'/config/defines.old.php'));
                        break;
                    case self::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND:
                        $this->errors[] = Tools::displayError(sprintf($this->l('Error: could not find whether debug mode is enabled. Make sure that the correct permissions are set on the file %s'), _PS_ROOT_DIR_.'/config/defines.inc.php'));
                        break;
                    case self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS:
                        $this->errors[] = Tools::displayError(sprintf($this->l('Error: could not write to file. Make sure that the correct permissions are set on the file %s'), _PS_ROOT_DIR_.'/config/defines.inc.php'));
                        break;
                    case self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM:
                        $this->errors[] = Tools::displayError(sprintf($this->l('Error: could not write to file. Make sure that the correct permissions are set on the file %s'), _PS_ROOT_DIR_.'/config/defines_custom.inc.php'));
                        break;
                    case self::DEBUG_MODE_ERROR_NO_READ_ACCESS:
                        $this->errors[] = Tools::displayError(sprintf($this->l('Error: could not read file. Make sure that the correct permissions are set on the file %s'), _PS_ROOT_DIR_.'/config/defines.inc.php'));
                        break;
                    default:
                        break;
                }
            }
            Tools::generateIndex();
        }

        if ($redirectAdmin && (!isset($this->errors) || !count($this->errors))) {
            Hook::exec('action'.get_class($this).ucfirst($this->action).'After', array('controller' => $this, 'return' => ''));
            Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getValue('token').'&conf=4');
        }
    }

    public function displayAjaxTestServer()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            die($this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error'));
        }
        /* PrestaShop demo mode*/
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'test_server') {
            $host = pSQL(Tools::getValue('sHost', ''));
            $port = (int)Tools::getValue('sPort', 0);
            $type = Tools::getValue('type', '');

            if ($host != '' && $port != 0) {
                $res = 0;

                if ($type == 'memcached') {
                    if (extension_loaded('memcached') &&
                        @fsockopen($host, $port)
                    ) {
                        $memcache = new Memcached();
                        $memcache->addServer($host, $port);

                        $res =  in_array('255.255.255', $memcache->getVersion(), true) === false;
                    }
                } else {
                    if (function_exists('memcache_get_server_status') &&
                        function_exists('memcache_connect') &&
                        @fsockopen($host, $port)
                    ) {
                        $memcache = @memcache_connect($host, $port);
                        $res      = @memcache_get_server_status($memcache, $host, $port);
                    }
                }
                die(json_encode(array($res)));
            }
        }
        die;
    }

    /**
     * Is Debug Mode enabled?
     *
     * @return bool Whether debug mode is enabled
     */
    public function isDebugModeEnabled()
    {
        // Always try the custom defines file first
        $defines_clean = '';
        if ($this->isDefinesReadable(true)) {
            $defines_clean = php_strip_whitespace(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
        }

        $m = array();
        if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', $defines_clean, $m)) {
            $defines_clean = php_strip_whitespace(_PS_ROOT_DIR_.'/config/defines.inc.php');
            if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', $defines_clean, $m)) {
                return false;
            }
        }

        if (Tools::strtolower($m[1]) === 'true') {
            return true;
        }

        return false;
    }

    /**
     * Check read permission on defines.inc.php
     *
     * @param bool $custom Whether the custom defines file should be used
     * @return bool Whether the file can be read
     */
    public function isDefinesReadable($custom = false)
    {
        if ($custom) {
            return is_readable(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
        }

        return is_readable(_PS_ROOT_DIR_.'/config/defines.inc.php');
    }

    /**
     * Enable debug mode
     *
     * @return int Whether changing debug mode succeeded or error code
     */
    public function enableDebugMode()
    {
        // Check custom defines file first
        if ($this->isDefinesReadable(true)) {
            // Take commented lines into account
            $defines_custom_clean = php_strip_whitespace(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
            $defines_custom = Tools::file_get_contents(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
            if (!empty($defines_custom_clean) && preg_match('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', $defines_custom_clean)) {
                $defines_custom = preg_replace('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', 'define(\'_PS_MODE_DEV_\', true);', $defines_custom);
                if (!@file_put_contents(_PS_ROOT_DIR_.'/config/defines_custom.inc.php', $defines_custom)) {
                    return self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM;
                }

                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
                }

                return self::DEBUG_MODE_SUCCEEDED;
            }
        }

        if (!$this->isDefinesReadable()) {
            return self::DEBUG_MODE_ERROR_NO_READ_ACCESS;
        }
        $defines_clean = php_strip_whitespace(_PS_ROOT_DIR_.'/config/defines.inc.php');
        $defines = Tools::file_get_contents(_PS_ROOT_DIR_.'/config/defines.inc.php');
        if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', $defines_clean)) {
            return self::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND;
        }
        $defines = preg_replace('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', 'define(\'_PS_MODE_DEV_\', true);', $defines);
        if (!@file_put_contents(_PS_ROOT_DIR_.'/config/defines.inc.php', $defines)) {
            return self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS;
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(_PS_ROOT_DIR_.'/config/defines.inc.php');
        }

        return self::DEBUG_MODE_SUCCEEDED;
    }

    /**
     * Disable debug mode
     *
     * @return int Whether changing debug mode succeeded or error code
     */
    public function disableDebugMode()
    {
        // Check custom defines file first
        if ($this->isDefinesReadable(true)) {
            $defines_custom_clean = php_strip_whitespace(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
            $defines_custom = Tools::file_get_contents(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
            if (!empty($defines_custom_clean) && preg_match('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', $defines_custom_clean)) {
                $defines_custom = preg_replace('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', 'define(\'_PS_MODE_DEV_\', false);', $defines_custom);
                if (!@file_put_contents(_PS_ROOT_DIR_.'/config/defines_custom.inc.php', $defines_custom)) {
                    return self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS_CUSTOM;
                }

                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate(_PS_ROOT_DIR_.'/config/defines_custom.inc.php');
                }

                return self::DEBUG_MODE_SUCCEEDED;
            }
        }

        if (!$this->isDefinesReadable()) {
            return self::DEBUG_MODE_ERROR_NO_READ_ACCESS;
        }
        $defines_clean = php_strip_whitespace(_PS_ROOT_DIR_.'/config/defines.inc.php');
        $defines = Tools::file_get_contents(_PS_ROOT_DIR_.'/config/defines.inc.php');
        if (!preg_match('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', $defines_clean)) {
            return self::DEBUG_MODE_ERROR_NO_DEFINITION_FOUND;
        }
        $defines = preg_replace('/define\(\'_PS_MODE_DEV_\', ([a-zA-Z]+)\);/Ui', 'define(\'_PS_MODE_DEV_\', false);', $defines);
        if (!@file_put_contents(_PS_ROOT_DIR_.'/config/defines.inc.php', $defines)) {
            return self::DEBUG_MODE_ERROR_NO_WRITE_ACCESS;
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(_PS_ROOT_DIR_.'/config/defines.inc.php');
        }

        return self::DEBUG_MODE_SUCCEEDED;
    }
}
