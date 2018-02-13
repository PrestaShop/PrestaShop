<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @property Meta $object
 */
class AdminMetaControllerCore extends AdminController
{
    public $table = 'meta';
    public $className = 'Meta';
    public $lang = true;

    /** @var ShopUrl */
    protected $url = false;
    protected $toolbar_scroll = false;

    public function __construct()
    {
        $this->table = 'meta';
        $this->className = 'Meta';

        $this->bootstrap = true;
        $this->identifier_name = 'page';
        $this->ht_file = _PS_ROOT_DIR_.'/.htaccess';
        $this->rb_file = _PS_ROOT_DIR_.'/robots.txt';
        $this->rb_data = Tools::getRobotsContent();

        parent::__construct();

        $this->explicitSelect = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_meta' => array('title' => $this->trans('ID', array(), 'Admin.Global'), 'align' => 'center', 'class' => 'fixed-width-xs'),
            'page' => array('title' => $this->trans('Page', array(), 'Admin.Shopparameters.Feature')),
            'title' => array('title' => $this->trans('Page title', array(), 'Admin.Shopparameters.Feature')),
            'url_rewrite' => array('title' => $this->trans('Friendly URL', array(), 'Admin.Global'))
        );
        $this->_where = ' AND a.configurable = 1';
        $this->_group = 'GROUP BY a.id_meta';

        $this->sm_file = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$this->context->shop->id.'_index_sitemap.xml';
        // Options to generate friendly urls
        $mod_rewrite = Tools::modRewriteActive();
        $general_fields = array(
            'PS_REWRITING_SETTINGS' => array(
                'title' => $this->trans('Friendly URL', array(), 'Admin.Global'),
                'hint' => ($mod_rewrite ? $this->trans('Enable this option only if your server allows URL rewriting (recommended).', array(), 'Admin.Shopparameters.Help') : ''),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool',
                'desc' => (!$mod_rewrite ? $this->trans('URL rewriting (mod_rewrite) is not active on your server, or it is not possible to check your server configuration. If you want to use Friendly URLs, you must activate this mod.', array(), 'Admin.Shopparameters.Help') : '')
            ),
            'PS_ALLOW_ACCENTED_CHARS_URL' => array(
                'title' => $this->trans('Accented URL', array(), 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Enable this option if you want to allow accented characters in your friendly URLs.', array(), 'Admin.Shopparameters.Help').' '.$this->trans('You should only activate this option if you are using non-latin characters ; for all the latin charsets, your SEO will be better without this option.', array(), 'Admin.Shopparameters.Help'),
                'validation' => 'isBool',
                'cast' => 'intval',
                'type' => 'bool'
            ),
            'PS_CANONICAL_REDIRECT' => array(
                'title' => $this->trans('Redirect to the canonical URL', array(), 'Admin.Shopparameters.Feature'),
                'validation' => 'isUnsignedInt',
                'cast' => 'intval',
                'type' => 'select',
                'list' => array(
                    array('value' => 0, 'name' => $this->trans('No redirection (you may have duplicate content issues)', array(), 'Admin.Shopparameters.Feature')),
                    array('value' => 1, 'name' => $this->trans('302 Moved Temporarily (recommended while setting up your store)', array(), 'Admin.Shopparameters.Feature')),
                    array('value' => 2, 'name' => $this->trans('301 Moved Permanently (recommended once you have gone live)', array(), 'Admin.Shopparameters.Feature'))
                ),
                'identifier' => 'value',
            ),
        );

        $url_description = '';
        if (!defined('_PS_HOST_MODE_')) {
            if ($this->checkConfiguration($this->ht_file)) {
                $general_fields['PS_HTACCESS_DISABLE_MULTIVIEWS'] = array(
                    'title' => $this->trans('Disable Apache\'s MultiViews option', array(), 'Admin.Shopparameters.Feature'),
                    'hint' => $this->trans('Enable this option only if you have problems with URL rewriting.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                );

                $general_fields['PS_HTACCESS_DISABLE_MODSEC'] = array(
                    'title' => $this->trans('Disable Apache\'s mod_security module', array(), 'Admin.Shopparameters.Feature'),
                    'hint' => $this->trans('Some of PrestaShop\'s features might not work correctly with a specific configuration of Apache\'s mod_security module. We recommend to turn it off.', array(), 'Admin.Shopparameters.Help'),
                    'validation' => 'isBool',
                    'cast' => 'intval',
                    'type' => 'bool',
                );
            } else {
                $url_description = $this->trans('Before you can use this tool, you need to:', array(), 'Admin.Shopparameters.Notification');
                $url_description .= $this->trans('1) Create a blank .htaccess file in your root directory.', array(), 'Admin.Shopparameters.Notification');
                $url_description .= $this->trans('2) Give it write permissions (CHMOD 666 on Unix system).', array(), 'Admin.Shopparameters.Notification');
            }
        }

        // Options to generate robot.txt
        $robots_description = $this->trans('Your robots.txt file MUST be in your website\'s root directory and nowhere else (e.g. http://www.example.com/robots.txt).', array(), 'Admin.Shopparameters.Notification');
        if ($this->checkConfiguration($this->rb_file)) {
            $robots_description .= $this->trans('Generate your "robots.txt" file by clicking on the following button (this will erase the old robots.txt file)', array(), 'Admin.Shopparameters.Notification');
            $robots_submit = array('name' => 'submitRobots', 'title' => $this->trans('Generate robots.txt file', array(), 'Admin.Shopparameters.Feature'));
        } else {
            $robots_description .= $this->trans('Before you can use this tool, you need to:', array(), 'Admin.Shopparameters.Notification');
            $robots_description .= $this->trans('1) Create a blank robots.txt file in your root directory.', array(), 'Admin.Shopparameters.Notification');
            $robots_description .= $this->trans('2) Give it write permissions (CHMOD 666 on Unix system).', array(), 'Admin.Shopparameters.Notification');
        }

        $robots_options = array(
            'title' => $this->trans('Robots file generation', array(), 'Admin.Shopparameters.Feature'),
            'description' => $robots_description,
        );

        if (isset($robots_submit)) {
            $robots_options['submit'] = $robots_submit;
        }

        if (!defined('_PS_HOST_MODE_')) {
            // Options for shop URL if multishop is disabled
            $shop_url_options = array(
                'title' => $this->trans('Set shop URL', array(), 'Admin.Shopparameters.Feature'),
                'fields' => array(),
            );

            if (!Shop::isFeatureActive()) {
                $this->url = ShopUrl::getShopUrls($this->context->shop->id)->where('main', '=', 1)->getFirst();
                if ($this->url) {
                    $shop_url_options['description'] = $this->trans('Here you can set the URL for your shop. If you migrate your shop to a new URL, remember to change the values below.', array(), 'Admin.Shopparameters.Notification');
                    $shop_url_options['fields'] = array(
                        'domain' => array(
                            'title' =>    $this->trans('Shop domain', array(), 'Admin.Shopparameters.Feature'),
                            'validation' => 'isString',
                            'type' => 'text',
                            'defaultValue' => $this->url->domain,
                        ),
                        'domain_ssl' => array(
                            'title' =>    $this->trans('SSL domain', array(), 'Admin.Shopparameters.Feature'),
                            'validation' => 'isString',
                            'type' => 'text',
                            'defaultValue' => $this->url->domain_ssl,
                        ),
                        'uri' => array(
                            'title' =>    $this->trans('Base URI', array(), 'Admin.Shopparameters.Feature'),
                            'validation' => 'isString',
                            'type' => 'text',
                            'defaultValue' => $this->url->physical_uri,
                        )
                    );
                    $shop_url_options['submit'] = array('title' => $this->trans('Save', array(), 'Admin.Actions'));
                }
            } else {
                $shop_url_options['description'] = $this->trans('The multistore option is enabled. If you want to change the URL of your shop, you must go to the "Multistore" page under the "Advanced Parameters" menu.', array(), 'Admin.Shopparameters.Notification');
            }
        }

        // List of options
        $this->fields_options = array(
            'general' => array(
                'title' =>    $this->trans('Set up URLs', array(), 'Admin.Shopparameters.Feature'),
                'description' => $url_description,
                'fields' =>    $general_fields,
                'submit' => array('title' => $this->trans('Save', array(), 'Admin.Actions'))
            )
        );

        if (!defined('_PS_HOST_MODE_')) {
            $this->fields_options['shop_url'] = $shop_url_options;
        } else {
            $this->fields_options['manage_domain_name'] = array(
                'title' => $this->trans('Manage domain name', array(), 'Admin.Shopparameters.Feature'),
                'description' => $this->trans('You can search for a new domain name or add a domain name that you already own. You will be redirected to your PrestaShop account.', array(), 'Admin.Shopparameters.Help'),
                'buttons' => array(
                    array(
                        'title' => $this->trans('Add a domain name', array(), 'Admin.Shopparameters.Feature'),
                        'href' => 'https://www.prestashop.com/cloud/',
                        'class' => 'pull-right', 'icon' => 'process-icon-new',
                        'js' => 'return !window.open(this.href);'
                    )
                )
            );
        }

        // Add display route options to options form
        if (Configuration::get('PS_REWRITING_SETTINGS') || Tools::getValue('PS_REWRITING_SETTINGS')) {
            if (Configuration::get('PS_REWRITING_SETTINGS')) {
                $this->addAllRouteFields();
            }
            $this->fields_options['routes']['title'] = $this->trans('Schema of URLs', array(), 'Admin.Shopparameters.Feature');
            $this->fields_options['routes']['description'] = $this->trans('This section enables you to change the default pattern of your links. In order to use this functionality, PrestaShop\'s "Friendly URL" option must be enabled, and Apache\'s URL rewriting module (mod_rewrite) must be activated on your web server.', array(), 'Admin.Shopparameters.Notification').'<br />'.$this->trans('There are several available keywords for each route listed below; note that keywords with * are required!', array(), 'Admin.Shopparameters.Notification').'<br />'.$this->trans('To add a keyword in your URL, use the {keyword} syntax. If the keyword is not empty, you can add text before or after the keyword with syntax {prepend:keyword:append}. For example {-hey-:meta_title} will add "-hey-my-title" in the URL if the meta title is set.', array(), 'Admin.Shopparameters.Notification');
            $this->fields_options['routes']['submit'] = array('title' => $this->trans('Save', array(), 'Admin.Actions'));
        }

        $this->fields_options['robots'] = $robots_options;
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_meta'] = array(
                'href' => self::$currentIndex.'&addmeta&token='.$this->token,
                'desc' => $this->trans('Add a new page', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    public function initProcess()
    {
        parent::initProcess();
        // This is a composite page, we don't want the "options" display mode
        if ($this->display == 'options') {
            $this->display = '';
        }
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
        foreach (Dispatcher::getInstance()->default_routes[$route_id]['keywords'] as $keyword => $data) {
            $keywords[] = ((isset($data['param'])) ? '<span class="red">'.$keyword.'*</span>' : $keyword);
        }

        $this->fields_options['routes']['fields']['PS_ROUTE_'.$route_id] = array(
            'title' =>    $title,
            'desc' => $this->trans('Keywords: %keywords%', array('%keywords%' => implode(', ', $keywords)), 'Admin.Shopparameters.Feature'),
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
        if (is_object($this->object) && is_array($this->object->url_rewrite) && count($this->object->url_rewrite)) {
            foreach ($this->object->url_rewrite as $rewrite) {
                if ($is_index != true) {
                    $is_index = ($this->object->page == 'index' && empty($rewrite)) ? true : false;
                }
            }
        }

        $pages = array(
            'common' => array(
                'name' => $this->trans('Default pages', array(), 'Admin.Shopparameters.Feature'),
                'query' => array(),
            ),
            'module' => array(
                'name' => $this->trans('Module pages', array(), 'Admin.Shopparameters.Feature'),
                'query' => array(),
            ),
        );

        foreach ($files as $name => $file) {
            $k = (preg_match('#^module-#', $file)) ? 'module' : 'common';
            $pages[$k]['query'][] = array(
                'id' => $file,
                'page' => $name,
            );
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Meta tags', array(), 'Admin.Shopparameters.Feature'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'hidden',
                    'name' => 'id_meta',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->trans('Page name', array(), 'Admin.Shopparameters.Feature'),
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
                    'hint' => $this->trans('Name of the related page.', array(), 'Admin.Shopparameters.Help'),
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Page title', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'title',
                    'lang' => true,
                    'hint' => array(
                        $this->trans('Title of this page.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Shopparameters.Help').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Meta description', array(), 'Admin.Global'),
                    'name' => 'description',
                    'lang' => true,
                    'hint' => array(
                        $this->trans('A short description of your shop.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'tags',
                    'label' => $this->trans('Meta keywords', array(), 'Admin.Global'),
                    'name' => 'keywords',
                    'lang' => true,
                    'hint' =>  array(
                        $this->trans('List of keywords for search engines.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('To add tags, click in the field, write something, and then press the "Enter" key.', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Invalid characters:', array(), 'Admin.Notifications.Info').' &lt;&gt;;=#{}'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->trans('Rewritten URL', array(), 'Admin.Shopparameters.Feature'),
                    'name' => 'url_rewrite',
                    'lang' => true,
                    'required' => true,
                    'disabled' => (bool)$is_index,
                    'hint' => array(
                        $this->trans('For instance, "contacts" for http://example.com/shop/contacts to redirect to http://example.com/shop/contact-form.php', array(), 'Admin.Shopparameters.Help'),
                        $this->trans('Only letters and hyphens are allowed.', array(), 'Admin.Shopparameters.Help'),
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->trans('Save', array(), 'Admin.Actions')
            )
        );
        return parent::renderForm();
    }

    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_ && Tools::isSubmit('submitOptionsmeta')
            && (Tools::getValue('domain') != Configuration::get('PS_SHOP_DOMAIN') || Tools::getValue('domain_ssl') != Configuration::get('PS_SHOP_DOMAIN_SSL'))) {
            $this->errors[] = $this->trans('This functionality has been disabled.', array(), 'Admin.Notifications.Error');
            return;
        }

        if (Tools::isSubmit('submitAddmeta')) {
            $default_language = Configuration::get('PS_LANG_DEFAULT');
            if (Tools::getValue('page') != 'index') {
                $defaultLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
                $englishLangIsValidated = Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
            } else {    // index.php can have empty rewrite rule
                $defaultLangIsValidated = !Tools::getValue('url_rewrite_'.$default_language) || Validate::isLinkRewrite(Tools::getValue('url_rewrite_'.$default_language));
                $englishLangIsValidated = !Tools::getValue('url_rewrite_1') || Validate::isLinkRewrite(Tools::getValue('url_rewrite_1'));
            }

            if (!$defaultLangIsValidated && !$englishLangIsValidated) {
                $this->errors[] = $this->trans('The URL rewrite field must be filled in either the default or English language.', array(), 'Admin.Notifications.Error');
                return false;
            }

            foreach (Language::getIDs(false) as $id_lang) {
                $current = Tools::getValue('url_rewrite_'.$id_lang);
                if (strlen($current) == 0) {
                    // Prioritize default language first
                    if ($defaultLangIsValidated) {
                        $_POST['url_rewrite_'.$id_lang] = Tools::getValue('url_rewrite_'.$default_language);
                    } else {
                        $_POST['url_rewrite_'.$id_lang] = Tools::getValue('url_rewrite_1');
                    }
                }
            }

            Hook::exec('actionAdminMetaSave');
        } elseif (Tools::isSubmit('submitRobots')) {
            $this->generateRobotsFile();
        }

        if (Tools::isSubmit('PS_ROUTE_product_rule')) {
            Tools::clearCache($this->context->smarty);
        }

        return parent::postProcess();
    }

    public function generateRobotsFile()
    {
        if (!Tools::generateRobotsFile(true)) {
            $this->errors[] = $this->trans('Cannot write into file: %filename%. Please check write permissions.', array( '%filename' => $this->rb_file), 'Admin.Notifications.Error');
        } else {
            $this->redirect_after = self::$currentIndex.'&conf=4&token='.$this->token;
        }
    }

    public function getList($id_lang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, Context::getContext()->shop->id);
    }

    public function renderList()
    {
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $this->displayInformation($this->trans('You can only display the page list in a shop context.', array(), 'Admin.Shopparameters.Notification'));
        } else {
            return parent::renderList();
        }
    }

    /**
     * Validate route syntax and save it in configuration
     *
     * @param string $route_id
     */
    public function checkAndUpdateRoute($route_id)
    {
        $default_routes = Dispatcher::getInstance()->default_routes;
        if (!isset($default_routes[$route_id])) {
            return;
        }

        $rule = Tools::getValue('PS_ROUTE_'.$route_id);
        if (!Validate::isRoutePattern($rule)) {
            $this->errors[] = sprintf('The route %s is not valid', htmlspecialchars($rule));
        } else {
            if (!$rule || $rule == $default_routes[$route_id]['rule']) {
                Configuration::updateValue('PS_ROUTE_'.$route_id, '');
                return;
            }

            $errors = array();
            if (!Dispatcher::getInstance()->validateRoute($route_id, $rule, $errors)) {
                foreach ($errors as $error) {
                    $this->errors[] = sprintf('Keyword "{%1$s}" required for route "%2$s" (rule: "%3$s")', $error, $route_id, htmlspecialchars($rule));
                }
            } else {
                Configuration::updateValue('PS_ROUTE_'.$route_id, $rule);
            }
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

        if (Tools::getIsset('uri')) {
            $this->updateOptionUri(Tools::getValue('uri'));
        }

        if (Tools::generateHtaccess($this->ht_file, null, null, '', Tools::getValue('PS_HTACCESS_DISABLE_MULTIVIEWS'), false, Tools::getValue('PS_HTACCESS_DISABLE_MODSEC'))) {
            Tools::enableCache();
            Tools::clearCache($this->context->smarty);
            Tools::restoreCacheSettings();
            Tools::clearSf2Cache();
        } else {
            Configuration::updateValue('PS_REWRITING_SETTINGS', 0);
            // Message copied/pasted from the information tip
            $message = $this->trans('Before being able to use this tool, you need to:', array(), 'Admin.Shopparameters.Notification');
            $message .= '<br />- '.$this->trans('Create a blank .htaccess in your root directory.', array(), 'Admin.Shopparameters.Notification');
            $message .= '<br />- '.$this->trans('Give it write permissions (CHMOD 666 on Unix system).', array(), 'Admin.Shopparameters.Notification');
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
     *
     * @param string $value
     *
     * @throws PrestaShopException
     */
    public function updateOptionDomain($value)
    {
        if (!Shop::isFeatureActive() && $this->url && $this->url->domain != $value) {
            if (Validate::isCleanHtml($value)) {
                $this->url->domain = $value;
                $this->url->update();
                Configuration::updateGlobalValue('PS_SHOP_DOMAIN', $value);
            } else {
                $this->errors[] = $this->trans('This domain is not valid.', array(), 'Admin.Notifications.Error');
            }
        }
    }

    /**
     * Update shop SSL domain (for mono shop)
     *
     * @param string $value
     *
     * @throws PrestaShopException
     */
    public function updateOptionDomainSsl($value)
    {
        if (!Shop::isFeatureActive() && $this->url && $this->url->domain_ssl != $value) {
            if (Validate::isCleanHtml($value)) {
                $this->url->domain_ssl = $value;
                $this->url->update();
                Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL', $value);
            } else {
                $this->errors[] = $this->trans('The SSL domain is not valid.', array(), 'Admin.Notifications.Error');
            }
        }
    }

    /**
     * Update shop physical uri for mono shop)
     *
     * @param string $value
     *
     * @throws PrestaShopException
     */
    public function updateOptionUri($value)
    {
        if (!Shop::isFeatureActive() && $this->url && $this->url->physical_uri != $value) {
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
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $this->addAllRouteFields();
        }

        if ($this->fields_options && is_array($this->fields_options)) {
            $helper = new HelperOptions($this);
            $this->setHelperDisplay($helper);
            $helper->toolbar_scroll = true;
            $helper->toolbar_btn = array(
                'save' => array(
                    'href' => '#',
                    'desc' => $this->trans('Save', array(), 'Admin.Actions')
                )
            );
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
        $this->addFieldRoute('product_rule', $this->trans('Route to products', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('category_rule', $this->trans('Route to category', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('layered_rule', $this->trans('Route to category which has the "selected_filter" attribute for the "Layered Navigation" (blocklayered) module', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('supplier_rule', $this->trans('Route to supplier', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('manufacturer_rule', $this->trans('Route to brand', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('cms_rule', $this->trans('Route to page', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('cms_category_rule', $this->trans('Route to page category', array(), 'Admin.Shopparameters.Feature'));
        $this->addFieldRoute('module', $this->trans('Route to modules', array(), 'Admin.Shopparameters.Feature'));
    }

    /**
     * Check if a file is writable
     *
     * @param string $file
     * @return bool
     */
    public function checkConfiguration($file)
    {
        if (file_exists($file)) {
            return is_writable($file);
        }
        return is_writable(dirname($file));
    }
}
