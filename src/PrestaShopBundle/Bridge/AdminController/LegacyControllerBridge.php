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
declare(strict_types=1);

namespace PrestaShopBundle\Bridge\AdminController;

use Context;
use Employee;
use Media;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Tools;

/**
 * The bridge class is used as a proxy between the internal controller configuration and the legacy code.
 *
 * It is accessible via the Context and/or the hooks, but to avoid legacy code accessing the controller configuration
 * directly this object provides access to the configuration fields via a whitelist thus protecting th other elements
 * of the ControllerConfiguration which shouldn't be accessed directly.
 *
 * It also allows integrating legacy functions that were usable on AdminController thus we don't need to implement them
 * in the framework controller.
 *
 * @property int $id
 * @property string $className
 * @property string $controller_name
 * @property string $php_self
 * @property string $current_index
 * @property string|null $position_identifier
 * @property string $table
 * @property string $token
 * @property Employee $user
 * @property array $meta_title
 * @property array $breadcrumbs
 * @property bool $lite_display
 * @property string $display
 * @property bool $show_page_header_toolbar
 * @property string $page_header_toolbar_title
 * @property array $toolbar_btn
 * @property array $toolbar_title
 * @property bool $display_header
 * @property bool $display_header_javascript
 * @property bool $display_footer
 * @property bool $bootstrap
 * @property array $css_files
 * @property array $js_files
 * @property string $tpl_folder
 * @property array $errors
 * @property array $warnings
 * @property array $confirmations
 * @property array $informations
 * @property bool $json
 * @property string $template
 * @property array $tpl_vars
 * @property array $modals
 * @property int $multishop_context
 * @property bool $multishop_context_group
 */
class LegacyControllerBridge implements LegacyControllerBridgeInterface
{
    /**
     * @var ControllerConfiguration
     */
    private $controllerConfiguration;

    /**
     * @var FeatureInterface
     */
    private $multiStoreFeature;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var string[] maps legacy controller properties with the bridge
     */
    private $propertiesMap = [
        'id' => 'controllerConfiguration.tabId',
        'className' => 'controllerConfiguration.objectModelClassName',
        'controller_name' => 'controllerConfiguration.legacyControllerName',
        'php_self' => 'controllerConfiguration.legacyControllerName',
        'current_index' => 'controllerConfiguration.legacyCurrentIndex',
        'position_identifier' => 'controllerConfiguration.positionIdentifierKey',
        'table' => 'controllerConfiguration.tableName',
        'token' => 'controllerConfiguration.token',
        'meta_title' => 'controllerConfiguration.metaTitle',
        'breadcrumbs' => 'controllerConfiguration.breadcrumbs',
        'lite_display' => 'controllerConfiguration.liteDisplay',
        'display' => 'controllerConfiguration.displayType',
        'show_page_header_toolbar' => 'controllerConfiguration.showPageHeaderToolbar',
        'page_header_toolbar_title' => 'controllerConfiguration.pageHeaderToolbarTitle',
        'page_header_toolbar_btn' => 'controllerConfiguration.pageHeaderToolbarActions',
        'toolbar_btn' => 'controllerConfiguration.pageHeaderToolbarActions',
        'toolbar_title' => 'controllerConfiguration.toolbarTitle',
        'display_header' => 'controllerConfiguration.displayHeader',
        'display_header_javascript' => 'controllerConfiguration.displayHeaderJavascript',
        'display_footer' => 'controllerConfiguration.displayFooter',
        'bootstrap' => 'controllerConfiguration.bootstrap',
        'css_files' => 'controllerConfiguration.cssFiles',
        'js_files' => 'controllerConfiguration.jsFiles',
        'tpl_folder' => 'controllerConfiguration.templateFolder',
        'errors' => 'controllerConfiguration.errors',
        'warnings' => 'controllerConfiguration.warnings',
        'confirmations' => 'controllerConfiguration.confirmations',
        'informations' => 'controllerConfiguration.informations',
        'json' => 'controllerConfiguration.json',
        'template' => 'controllerConfiguration.template',
        'tpl_vars' => 'controllerConfiguration.templateVars',
        'modals' => 'controllerConfiguration.modals',
        'multishop_context' => 'controllerConfiguration.multiShopContext',
        'multishop_context_group' => 'controllerConfiguration.multiShopContextGroup',
        'redirect_after' => 'controllerConfiguration.redirectAfter',
        'lockedToAllShopContext' => 'controllerConfiguration.lockedToAllShopContext',
    ];

    /**
     * @param ControllerConfiguration $controllerConfiguration
     * @param FeatureInterface $multiStoreFeature
     * @param LegacyContext $legacyContext
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        ControllerConfiguration $controllerConfiguration,
        FeatureInterface $multiStoreFeature,
        LegacyContext $legacyContext,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->controllerConfiguration = $controllerConfiguration;
        $this->multiStoreFeature = $multiStoreFeature;
        $this->legacyContext = $legacyContext;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@intheritedoc}
     */
    public function setMedia($isNewTheme = false): void
    {
        $adminWebpath = '';

        if (defined('_PS_ADMIN_DIR_')) {
            $adminWebpath = preg_replace(
                '/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '',
                str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_)
            );
        }

        if ($this->getContext()->language->is_rtl) {
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . $this->bo_theme . '/public/rtl.css?v=' . _PS_VERSION_, 'all', 0);
        }

        if ($isNewTheme) {
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/theme.css?v=' . _PS_VERSION_, 'all', 1);
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/main.bundle.js?v=' . _PS_VERSION_);

            // the multistore dropdown should be called only once, and only if multistore is used
            if ($this->multiStoreFeature->isUsed()) {
                $this->addJs(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/multistore_dropdown.bundle.js?v=' . _PS_VERSION_);
            }
            $this->addJqueryPlugin(['chosen', 'fancybox']);
        } else {
            //Bootstrap
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/theme.css?v=' . _PS_VERSION_, 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/vendor/titatoggle-min.css', 'all', 0);
            $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/public/theme.css?v=' . _PS_VERSION_, 'all', 0);

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
            $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/public/bundle.js?v=' . _PS_VERSION_);

            $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');

            if (!$this->controllerConfiguration->liteDisplay) {
                $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/js/help.js?v=' . _PS_VERSION_);
            }

            if (!Tools::getValue('submitFormAjax')) {
                $this->addJS(_PS_JS_DIR_ . 'admin/notifications.js?v=' . _PS_VERSION_);
            }
        }

        // Specific Admin Theme
        $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/' . self::DEFAULT_THEME . '/css/overrides.css', 'all', PHP_INT_MAX);

        $this->addCSS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/create_product_default_theme.css?v=' . _PS_VERSION_, 'all', 0);
        $this->addJS([
            _PS_JS_DIR_ . 'admin.js?v=' . _PS_VERSION_, // TODO: SEE IF REMOVABLE
            __PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/cldr.bundle.js?v=' . _PS_VERSION_,
            _PS_JS_DIR_ . 'tools.js?v=' . _PS_VERSION_,
            __PS_BASE_URI__ . $adminWebpath . '/public/bundle.js?v=' . _PS_VERSION_,
        ]);

        // This is handled as an external common dependency for both themes, but once new-theme is the only one it should be integrated directly into the main.bundle.js file
        $this->addJS(__PS_BASE_URI__ . $adminWebpath . '/themes/new-theme/public/create_product.bundle.js?v=' . _PS_VERSION_);

        Media::addJsDef([
            'changeFormLanguageUrl' => $this->getContext()->link->getAdminLink(
                'AdminEmployees',
                true,
                [],
                ['action' => 'formLanguage']
            ),
        ]);
        Media::addJsDef(['baseDir' => __PS_BASE_URI__]);
        Media::addJsDef(['baseAdminDir' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/']);
        Media::addJsDef(['currency' => [
            'iso_code' => $this->getContext()->currency->iso_code,
            'sign' => $this->getContext()->currency->sign,
            'name' => $this->getContext()->currency->name,
            'format' => $this->getContext()->currency->format,
        ]]);

        Media::addJsDef([
            'prestashop' => [
                'debug' => _PS_MODE_DEV_,
            ],
        ]);

        $this->hookDispatcher->dispatchWithParameters('actionAdminControllerSetMedia');
    }

    /**
     * {@intheritedoc}
     */
    public function addCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true): void
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
     * {@intheritedoc}
     */
    public function addJS($jsUri, $checkPath = true): void
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
            $jsPath = $jsFile = $jsFile[0];
            if ($checkPath) {
                $jsPath = Media::getJSPath($jsFile);
            }

            if ($jsPath && !in_array($jsPath, $this->controllerConfiguration->jsFiles)) {
                $this->controllerConfiguration->jsFiles[] = $jsPath . ($version ? '?' . $version : '');
            }
        }
    }

    /**
     * {@intheritedoc}
     */
    public function addJqueryPlugin($name, $folder = null, $css = true): void
    {
        if (!is_array($name)) {
            $name = [$name];
        }

        foreach ($name as $plugin) {
            $pluginPath = Media::getJqueryPluginPath($plugin, $folder);

            if (!empty($pluginPath['js'])) {
                $this->addJS($pluginPath['js'], false);
            }
            if ($css && !empty($pluginPath['css'])) {
                if (is_array($pluginPath['css'])) {
                    $this->addCSS((string) key($pluginPath['css']), 'all', null, false);
                } else {
                    $this->addCSS($pluginPath['css'], 'all', null, false);
                }
            }
        }
    }

    /**
     * {@intheritedoc}
     */
    public function addJqueryUI($component, $theme = 'base', $checkDependencies = true): void
    {
        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $uiPath = Media::getJqueryUIPath($ui, $theme, $checkDependencies);
            $this->addCSS($uiPath['css'], 'all');
            $this->addJS($uiPath['js'], false);
        }
    }

    /**
     * This whole bridge is used as legacy $context->controller, but all properties are held in configuration,
     * so we use a "magic" getter with a help of properties map and property accessor
     * to allow legacy code retrieving properties from configuration as if it would be in legacy controller
     *
     * @param string $name
     *
     * @return mixed
     */
    public function &__get(string $name)
    {
        return $this->getPropertyReference($name);
    }

    /**
     * This whole bridge is used as legacy $context->controller, but all properties are held in configuration,
     * so we use a "magic" setter with a help of properties map and property accessor
     * to allow legacy code setting properties in configuration as if it would be in legacy controller.
     *
     * @param string $name
     * @param mixed $value
     *
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $modifiedField = &$this->getPropertyReference($name);
        $modifiedField = $value;
    }

    /**
     * We need to return a reference in order for legacy codes to do manipulation like:
     *    $controller->toolbar_button['new_product'] = ['text' => 'New Product'];
     *
     * That's why we can't use a PropertyAccessor component because it does not return references, only values.
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    private function &getPropertyReference(string $name)
    {
        if (!isset($this->propertiesMap[$name])) {
            throw new InvalidArgumentException(sprintf('No mapping found for %s', $name));
        }

        $propertyPath = explode('.', $this->propertiesMap[$name]);
        $currentReference = &$this;
        foreach ($propertyPath as $property) {
            if (!property_exists($currentReference, $property)) {
                throw new InvalidArgumentException(sprintf('Could not find property %s', implode('.', $propertyPath)));
            }
            $currentReference = &$currentReference->{$property};
        }

        return $currentReference;
    }

    /**
     * @return Context
     */
    private function getContext(): Context
    {
        return $this->legacyContext->getContext();
    }
}
