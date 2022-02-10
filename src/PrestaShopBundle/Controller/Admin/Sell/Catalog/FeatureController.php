<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Media;
use ObjectModel;
use PrestaShopBundle\Bridge\AddActionTrait;
use PrestaShopBundle\Bridge\AddActionInterface;
use PrestaShopBundle\Bridge\Controller\ControllerBridgeInterface;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tools;
use Validate;

/**
 * Controller responsible for "Sell > Catalog > Attributes & Features > Features" page
 */
class FeatureController extends FrameworkBundleAdminController implements ControllerBridgeInterface, AddActionInterface
{
    use AddActionTrait;

    public const DEFAULT_THEME = 'default';
    public const CONTROLLER_NAME_LEGACY = 'AdminFeatures';
    public const POSITION_IDENTIFIER = 'id_feature';
    public const TABLE = 'feature';
    public const LIST_ID = 'feature';
    public const CLASS_NAME = 'Feature';
    public const IDENTIFIER = 'id_feature';

    /**
     * @var string
     */
    public $php_self;

    /**
     * @var ControllerConfiguration
     */
    public $controllerConfiguration;

    //Filters
    /**
     * @var array
     */
    protected $filterList = [];

    /**
     * @var bool
     */
    protected $ajax = false;

    /**
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     */
    public function indexAction(Request $request)
    {
        //build list and page with action
        $this->setListFields();
        //A gérer
        $this->processFilter();

        $this->buildActionList();
        $helperListConfiguration = $this->get('prestashop.core.bridge.helper_list_configuration_factory')->create([
            'table' => self::TABLE,
            'listId' => self::LIST_ID,
            'className' => self::CLASS_NAME,
            'identifier' => self::IDENTIFIER,
            'isJoinLanguageTableAuto' => true,
            'defaultOrderBy' => 'position',
            'fieldsList' => $this->controllerConfiguration->fieldsList,
        ]);
        $this->get('prestashop.core.bridge.helper_list_bridge')->getList(
            $helperListConfiguration,
            $this->getContext()->language->id
        );

        return $this->renderSmarty(
            $this->get('prestashop.core.bridge.helper_list_bridge')->renderList(
                $this->controllerConfiguration,
                $helperListConfiguration
            ),
            $this->controllerConfiguration
        );
    }

    public function renderSmarty(string $content, ControllerConfiguration $controllerConfiguration): Response
    {
        $this->setMedia();

        return $this->get('prestashop.core.bridge.smarty_bridge')->render($content, $controllerConfiguration);
    }

    /**
     * @param bool $isNewTheme
     */
    public function setMedia($isNewTheme = false)
    {
        $adminWebpath = '';

        if (defined('_PS_ADMIN_DIR_')) {
            $adminWebpath = preg_replace(
                '/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '',
                str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_)
            );
        }

        if ($this->getContext()->language->is_rtl) {
            $this->addJS(_PS_JS_DIR_ . 'rtl.js');
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/' . $this->getContext()->language->iso_code . '.css');
        }

        if ($isNewTheme) {
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/theme.css', 'all', 1);
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/main.bundle.js');

            // the multistore dropdown should be called only once, and only if multistore is used
            if ($this->get('prestashop.adapter.multistore_feature')->isUsed()) {
                $this->addJs(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/multistore_dropdown.bundle.js');
            }
            $this->addJqueryPlugin(['chosen', 'fancybox']);
        } else {
            //Bootstrap
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/theme.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/vendor/titatoggle-min.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/public/theme.css', 'all', 0);

            // add Jquery 3 and its migration script
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery-3.5.1.min.js');
            $this->addJs(_PS_JS_DIR_ . 'jquery/bo-migrate-mute.min.js');
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery-migrate-3.1.0.min.js');
            // implement $.browser object and live method, that has been removed since jquery 1.9
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery.browser-0.1.0.min.js');
            $this->addJs(_PS_JS_DIR_ . 'jquery/jquery.live-polyfill-1.1.2.min.js');

            $this->addJqueryPlugin(['scrollTo', 'alerts', 'chosen', 'autosize', 'fancybox']);
            $this->addJqueryPlugin('growl', null, false);
            $this->addJqueryUI(['ui.slider', 'ui.datepicker']);

            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/vendor/bootstrap.min.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/vendor/modernizr.min.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/modernizr-loads.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/vendor/moment-with-langs.min.js');
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/public/bundle.js');

            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

            if (!$this->controllerConfiguration->liteDisplay) {
                $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/help.js');
            }

            if (!Tools::getValue('submitFormAjax')) {
                $this->addJS(_PS_JS_DIR_ . 'admin/notifications.js');
            }

            // Specific Admin Theme
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/overrides.css', 'all', PHP_INT_MAX);
        }

        $this->addJS([
            _PS_JS_DIR_ . 'admin.js?v=' . _PS_VERSION_, // TODO: SEE IF REMOVABLE
            __PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/cldr.bundle.js',
            _PS_JS_DIR_ . 'tools.js?v=' . _PS_VERSION_,
            __PS_BASE_URI__ . $adminWebpath . '/public/bundle.js',
        ]);

        Media::addJsDef([
            'changeFormLanguageUrl' => $this->getContext()->link->getAdminLink(
                'AdminEmployees',
                true,
                [],
                ['action' => 'formLanguage']
            ),
        ]);
        Media::addJsDef(['host_mode' => (defined('_PS_HOST_MODE_') && _PS_HOST_MODE_)]);
        Media::addJsDef(['baseDir' => __PS_BASE_URI__]);
        Media::addJsDef(['baseAdminDir' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/']);
        Media::addJsDef(['currency' => [
            'iso_code' => $this->getContext()->currency->iso_code,
            'sign' => $this->getContext()->currency->sign,
            'name' => $this->getContext()->currency->name,
            'format' => $this->getContext()->currency->format,
        ]]);
        //Media::addJsDef(
        //    [
        //        'currency_specifications' => $this->preparePriceSpecifications($this->getContext()),
        //        'number_specifications' => $this->prepareNumberSpecifications($this->getContext()),
        //    ]
        //);

        Media::addJsDef([
            'prestashop' => [
                'debug' => _PS_MODE_DEV_,
            ],
        ]);

        // Execute Hook AdminController SetMedia
        $this->dispatchHook('actionAdminControllerSetMedia', []);
    }

    /**
     * Adds a new stylesheet(s) to the page header.
     *
     * @param string|array $cssUri Path to CSS file, or list of css files like this : array(array(uri => media_type), ...)
     * @param string $cssMediaType
     * @param int|null $offset
     * @param bool $checkPath
     *
     * @return void
     */
    public function addCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true)
    {
        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }

        foreach ($cssUri as $cssFile => $media) {
            if (is_string($cssFile) && strlen($cssFile) > 1) {
                if ($checkPath) {
                    $css_path = Media::getCSSPath($cssFile, $media);
                } else {
                    $css_path = [$cssFile => $media];
                }
            } else {
                if ($checkPath) {
                    $css_path = Media::getCSSPath($media, $cssMediaType);
                } else {
                    $css_path = [$media => $cssMediaType];
                }
            }

            $key = is_array($css_path) ? key($css_path) : $css_path;
            if ($css_path && (!isset($this->controllerConfiguration->cssFiles[$key]) || ($this->controllerConfiguration->cssFiles[$key] != reset($css_path)))) {
                $size = count($this->controllerConfiguration->cssFiles);
                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->controllerConfiguration->cssFiles = array_merge(array_slice($this->controllerConfiguration->cssFiles, 0, $offset), $css_path, array_slice($this->controllerConfiguration->cssFiles, $offset));
            }
        }
    }

    /**
     * Adds a new JavaScript file(s) to the page header.
     *
     * @param string|array $jsUri Path to JS file or an array like: array(uri, ...)
     * @param bool $checkPath
     */
    public function addJS($jsUri, $checkPath = true)
    {
        if (!is_array($jsUri)) {
            $jsUri = [$jsUri];
        }

        foreach ($jsUri as $jsFile) {
            $jsFile = explode('?', $jsFile);
            $version = '';
            if (isset($jsFile[1]) && $jsFile[1]) {
                $version = $jsFile[1];
            }
            $js_path = $jsFile = $jsFile[0];
            if ($checkPath) {
                $js_path = Media::getJSPath($jsFile);
            }

            if ($js_path && !in_array($js_path, $this->controllerConfiguration->jsFiles)) {
                $this->controllerConfiguration->jsFiles[] = $js_path . ($version ? '?' . $version : '');
            }
        }
    }

    /**
     * Adds jQuery plugin(s) to queued JS file list.
     *
     * @param string|array $name
     * @param string|null $folder
     * @param bool $css
     */
    public function addJqueryPlugin($name, $folder = null, $css = true)
    {
        if (!is_array($name)) {
            $name = [$name];
        }

        foreach ($name as $plugin) {
            $plugin_path = Media::getJqueryPluginPath($plugin, $folder);

            if (!empty($plugin_path['js'])) {
                $this->addJS($plugin_path['js'], false);
            }
            if ($css && !empty($plugin_path['css'])) {
                $this->addCSS(key($plugin_path['css']), 'all', null, false);
            }
        }
    }

    /**
     * Adds jQuery UI component(s) to queued JS file list.
     *
     * @param string|array $component
     * @param string $theme
     * @param bool $check_dependencies
     */
    public function addJqueryUI($component, $theme = 'base', $check_dependencies = true)
    {
        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $ui_path = Media::getJqueryUIPath($ui, $theme, $check_dependencies);
            $this->addCSS($ui_path['css'], 'all');
            $this->addJS($ui_path['js'], false);
        }
    }

    //Rework fully this todo
    public function processFilter(Request $request)
    {
        //Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
        //    'fields' => &$this->fields_list,
        //]);

        if (!isset($this->listId)) {
            $this->listId = $this->controllerConfiguration->table;
        }

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->listId)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->getContext()->cookie->{$prefix . $key});
                } elseif (stripos($key, $this->listId . 'Filter_') === 0) {
                    $this->getContext()->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->getContext()->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->listId . 'Filter_') === 0) {
                    $this->getContext()->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->getContext()->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $this->listId . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->getContext()->cookie->{$prefix . $key});
                    } else {
                        $this->getContext()->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->listId . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->getContext()->cookie->{$prefix . $key});
                    } else {
                        $this->getContext()->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->getContext()->cookie->getFamily($prefix . $this->listId . 'Filter_');
        $definition = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix . $this->listId . 'Filter_', 7 + Tools::strlen($prefix . $this->listId))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $this->listId));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = json_decode($value, true);
                    }
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';

                    // Assignment by reference
                    //if (array_key_exists('tmpTableFilter', $field)) {
                    //    //$sql_filter = &$this->_tmpTableFilter;
                    //} elseif (array_key_exists('havingFilter', $field)) {
                    if (array_key_exists('havingFilter', $field)) {
                        $sql_filter = &$this->_filterHaving;
                    } else {
                        $sql_filter = &$this->_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = $this->trans('The \'From\' date format is invalid (YYYY-MM-DD)', 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = $this->trans('The \'To\' date format is invalid (YYYY-MM-DD)', 'Admin.Notifications.Error');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $this->identifier || $key == '`' . $this->identifier . '`');
                        $alias = ($definition && !empty($definition['fields'][$filter]['shop'])) ? 'sa' : 'a';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? $alias . '.' : '') . pSQL($key) . ' = ' . (int) $value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . (float) $value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = (float) str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . $value . ' ';
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                }
            }
        }
    }

    /**
     * Add a warning message to display at the top of the page.
     *
     * @param string $msg
     */
    protected function displayWarning($msg)
    {
        $this->controllerConfiguration->warnings[] = $msg;
    }

    /**
     * @param string $key
     * @param string $filter
     *
     * @return array|false
     */
    protected function filterToField($key, $filter)
    {
        if (!isset($this->fields_list)) {
            return false;
        }

        foreach ($this->fields_list as $field) {
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key) {
                return $field;
            }
        }
        if (array_key_exists($filter, $this->fields_list)) {
            return $this->fields_list[$filter];
        }

        return false;
    }

    /**
     * Build action for list interface
     *
     * @return void
     *
     * @throws \Exception
     */
    private function buildActionList(): void
    {
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature', [
            //Used $this->generateUrl('admin_features_add')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new feature', 'Admin.Catalog.Feature'),
            'icon' => 'process-icon-new',
        ]);
        $this->addAction(self::ACTION_TYPE_HEADER_TOOLBAR, 'new_feature_value', [
            //Used $this->generateUrl('admin_features_add_value')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature_value&id_feature=' . (int) Tools::getValue('id_feature') . '&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new feature value', 'Admin.Catalog.Help'),
            'icon' => 'process-icon-new',
        ]);

        $this->addAction(self::ACTION_TYPE_LIST_HEADER_TOOLBAR, 'new', [
            //Replace by $this->generateUrl('admin_features_add')
            'href' => $this->controllerConfiguration->currentIndex . '&addfeature&token=' . $this->controllerConfiguration->token,
            'desc' => $this->trans('Add new', 'Admin.Actions'),
        ]);

        $this->addAction(self::ACTION_TYPE_ROW, 'view');
        $this->addAction(self::ACTION_TYPE_ROW, 'edit');
        $this->addAction(self::ACTION_TYPE_ROW, 'delete');

        $this->addAction(self::ACTION_TYPE_BULK, 'delete', [
            'text' => $this->trans('Delete selected', 'Admin.Actions'),
            'icon' => 'icon-trash',
            'confirm' => $this->trans('Delete selected items?', 'Admin.Notifications.Warning'),
        ]);
    }

    /**
     * Define fields in the list
     *
     * @return void
     */
    private function setListFields(): void
    {
        $this->controllerConfiguration->fieldsList = [
            'id_feature' => [
                'title' => $this->trans('ID', 'Admin.Global', []),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->trans('Name', 'Admin.Global', []),
                'width' => 'auto',
                'filter_key' => 'b!name',
            ],
            'value' => [
                'title' => $this->trans('Values', 'Admin.Global', []),
                'orderby' => false,
                'search' => false,
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'position' => [
                'title' => $this->trans('Position', 'Admin.Global', []),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'position' => 'position',
            ],
        ];
    }

    /**
     * Set the filters used for the list display.
     */
    private function getCookieFilterPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }
}
