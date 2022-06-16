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

namespace PrestaShopBundle\Bridge\Smarty;

use Media;
use Symfony\Component\HttpFoundation\Response;
use Tools;

/**
 * This trait adds methods to get a response object with the HTML passed as parameters and with all stuff needed,
 * like header, footer, notifications.
 *
 * Developers must use this trait in a controller migrated horizontally to render a smarty template as a Symfony response.
 * He can also be used to add CSS, js, jquery plugin, and jquery UI to a response.
 */
trait SmartyTrait
{
    /**
     * @param string $content
     * @param Response|null $response
     *
     * @return Response
     */
    public function renderSmarty(string $content, Response $response = null, bool $isNewTheme = false): Response
    {
        $this->setMedia($isNewTheme);

        return $this->get('prestashop.core.bridge.smarty_bridge')->render($content, $this->controllerConfiguration, $response);
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

        Media::addJsDef([
            'prestashop' => [
                'debug' => _PS_MODE_DEV_,
            ],
        ]);

        $this->dispatchHook('actionAdminControllerSetMedia', []);
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
}
