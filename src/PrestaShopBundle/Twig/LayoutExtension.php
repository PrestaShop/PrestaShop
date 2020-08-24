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

namespace PrestaShopBundle\Twig;

use Exception;
use Link;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Currency\CurrencyDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use QuickAccess;
use Shop;
use Tab;
use Tools;
use Twig\Extension\GlobalsInterface;

/**
 * This class is used by Twig_Environment and provide layout methods callable from a twig template.
 */
class LayoutExtension extends \Twig_Extension implements GlobalsInterface
{
    /** @var LegacyContext */
    private $context;

    /** @var string */
    private $environment;

    /** @var Configuration */
    private $configuration;

    /** @var CurrencyDataProvider */
    private $currencyDataProvider;

    /**
     * Constructor.
     *
     * Keeps the Context to look inside language settings.
     *
     * @param LegacyContext $context
     * @param string $environment
     * @param Configuration $configuration
     * @param CurrencyDataProvider $currencyDataProvider
     */
    public function __construct(
        LegacyContext $context,
        $environment,
        Configuration $configuration,
        CurrencyDataProvider $currencyDataProvider
    ) {
        $this->context = $context;
        $this->environment = $environment;
        $this->configuration = $configuration;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * Provides globals for Twig templates.
     *
     * @return array the base globals available in twig templates
     */
    public function getGlobals()
    {
        /*
         * As this is a twig extension we need to be very resilient and prevent it from crashing
         * the environment, for example the command debug:twig should not fail because of this extension
         */

        try {
            $defaultCurrency = $this->context->getEmployeeCurrency() ?: $this->currencyDataProvider->getDefaultCurrency();
        } catch (\Exception $e) {
            $defaultCurrency = null;
        }
        try {
            $rootUrl = $this->context->getRootUrl();
        } catch (\Exception $e) {
            $rootUrl = null;
        }

        if ((int) $this->context->getContext()->employee->id) {
            $quick_access = QuickAccess::getQuickAccessesWithToken($this->context->getContext()->language->id, (int) $this->context->getContext()->employee->id);
        } else {
            $quick_access = [];
        }

        $tabs = $this->getTabs();
        $currentTabLevel = 0;
        foreach ($tabs as $tab) {
            $currentTabLevel = isset($tab['current_level']) ? $tab['current_level'] : $currentTabLevel;
        }

        $variables = [
            'theme' => $this->context->getContext()->shop->theme,
            'default_currency' => $defaultCurrency,
            'root_url' => $rootUrl,
            'js_translatable' => [],
            'iso' => $this->context->getContext()->language->iso_code,
            'lang_is_rtl' => $this->context->getContext()->language->is_rtl,
            'full_language_code' => $this->context->getContext()->language->language_code,
            'country_iso_code' => $this->context->getContext()->country->iso_code,
            'default_language' => (int) Configuration::get('PS_LANG_DEFAULT'),
            'round_mode' => Configuration::get('PS_PRICE_ROUND_MODE'),
            'img_dir' => _PS_IMG_,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'collapse_menu' => isset($this->context->getContext()->cookie->collapse_menu) ? (int) $this->context->getContext()->cookie->collapse_menu : 0,
            'default_tab_link' => $this->context->getContext()->link->getAdminLink(Tab::getClassNameById((int) $this->context->getContext()->employee->default_tab)),
            'ps_version' => _PS_VERSION_,
            'debug_mode' => (bool) _PS_MODE_DEV_,
            'maintenance_mode' => !(bool) Configuration::get('PS_SHOP_ENABLE'),
            'install_dir_exists' => file_exists(_PS_ADMIN_DIR_ . '/../install'),
            'quick_access' => $quick_access,
            'baseAdminUrl' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/',
            'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
            'search_type' => Tools::getValue('bo_search_type'),
            'is_multishop' => Shop::isFeatureActive(),
            'base_url' => $this->context->getContext()->shop->getBaseURL(),
            'show_new_orders' => Configuration::get('PS_SHOW_NEW_ORDERS'),
            'show_new_customers' => Configuration::get('PS_SHOW_NEW_CUSTOMERS'),
            'show_new_messages' => Configuration::get('PS_SHOW_NEW_MESSAGES'),
            'employee' => $this->context->getContext()->employee,
            'logout_link' => $this->context->getContext()->link->getAdminLink('AdminLogin', true, [], ['logout' => 1]),
            // to be changed
            'viewport_scale' => '1',
            'meta_title' => 'META_TITLE',
            'smarty' => ['get' => ['controller' => 'CONTROLLER_NAME']],
            'js_router_metadata' => [
                'base_url' => '',
                'token' => ''
            ],
            'display_header' => true,
            'display_footer' => true,
            'display_header_javascript' => true,
            'full_cldr_language_code' => 'en-US',
            'token' => '',
            'currentIndex' => '',
            'page_header_toolbar' => '',
            'modal_module_list' => '',
            'current_tab_level' => 0,
            'css_files' => $this->context->getContext()->controller->css_files,
            'quick_access_current_link_icon' => '',
            'quick_access_current_link_name' => '',
            'no_order_tip' => '',
            'no_customer_tip' => '',
            'no_customer_message_tip' => '',
            'tabs' => $tabs,
            'breadcrumbs2' => [],
            'toolbar_btn' => [],
            'title' => 'Controller Title',
            'headerTabContent' => '',
            'disableDefaultErrorOutPut' => false,
            'errors' => [],
            'informations' => [],
            'confirmations' => [],
            'warnings' => [],
            'lite_display' => false,
            'php_errors' => [],
            'modals' => [],
        ];

        if (Shop::isFeatureActive() && Shop::getTotalShops(false, null) > 1) {
            $variables['shop_context'] = Shop::getContext();
        }

        return $variables;
    }

    /**
     * Define available filters.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('configuration', [$this, 'getConfiguration']),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getLegacyLayout', [$this, 'getLegacyLayout']),
            new \Twig_SimpleFunction('getAdminLink', [$this, 'getAdminLink']),
            new \Twig_SimpleFunction('getAdminToken', [$this, 'getAdminLink']),
            new \Twig_SimpleFunction('getQuickLink', [$this, 'getQuickLink']),
            new \Twig_SimpleFunction('matchQuickLink', [$this, 'matchQuickLink']),
            new \Twig_SimpleFunction('youtube_link', [$this, 'getYoutubeLink']),
        ];
    }

    /**
     * Returns a legacy configuration key.
     *
     * @param string $key
     *
     * @return array An array of functions
     */
    public function getConfiguration($key)
    {
        return $this->configuration->get($key);
    }

    /**
     * Get admin legacy layout into old controller context.
     *
     * Parameters can be set manually into twig template or sent from controller
     * For details : check Resources/views/Admin/Layout.html.twig
     *
     * @param string $controllerName The legacy controller name
     * @param string $title The page title to override default one
     * @param array $headerToolbarBtn The header toolbar to override
     * @param string $displayType The legacy display type variable
     * @param bool $showContentHeader Can force header toolbar (buttons and title) to be hidden with false value
     * @param array|string $headerTabContent Tabs labels
     * @param bool $enableSidebar Allow to use right sidebar to display docs for instance
     * @param string $helpLink If specified, will be used instead of legacy one
     * @param string $metaTitle
     * @param bool $useRegularH1Structure allows complex <h1> structure if set to false
     *
     * @throws Exception if legacy layout has no $content var replacement
     *
     * @return string The html layout
     */
    public function getLegacyLayout(
        $controllerName = '',
        $title = '',
        $headerToolbarBtn = [],
        $displayType = '',
        $showContentHeader = true,
        $headerTabContent = '',
        $enableSidebar = false,
        $helpLink = '',
        $jsRouterMetadata = [],
        $metaTitle = '',
        $useRegularH1Structure = true
    ) {
        if ($this->environment == 'test') {
            return <<<'EOF'
<html>
  <head>
    <title>Test layout</title>
    {% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}
  </head>
  <body>
    {% block content_header %}{% endblock %}
    {% block content %}{% endblock %}
    {% block content_footer %}{% endblock %}
    {% block javascripts %}{% endblock %}
    {% block extra_javascripts %}{% endblock %}
    {% block translate_javascripts %}{% endblock %}
  </body>
</html>
EOF;
        }

        $layout = $this->context->getLegacyLayout(
            $controllerName,
            $title,
            $headerToolbarBtn,
            $displayType,
            $showContentHeader,
            $headerTabContent,
            $enableSidebar,
            $helpLink,
            $jsRouterMetadata,
            $metaTitle,
            $useRegularH1Structure
        );

        //test if legacy template from "content.tpl" has '{$content}'
        if (false === strpos($layout, '{$content}')) {
            throw new Exception('PrestaShopBundle\Twig\LayoutExtension cannot find the {$content} string in legacy layout template', 1);
        }

        $content = str_replace(
            [
                '{$content}',
                'var currentIndex = \'index.php\';',
                '</head>',
                '</body>',
            ],
            [
                '{% block content_header %}{% endblock %}
                 {% block content %}{% endblock %}
                 {% block content_footer %}{% endblock %}
                 {% block sidebar_right %}{% endblock %}',
                'var currentIndex = \'' . $this->context->getAdminLink($controllerName) . '\';',
                '{% block stylesheets %}{% endblock %}{% block extra_stylesheets %}{% endblock %}</head>',
                '{% block javascripts %}{% endblock %}{% block extra_javascripts %}{% endblock %}{% block translate_javascripts %}{% endblock %}</body>',
            ],
            $layout
        );

        return $content;
    }

    /**
     * This is a Twig port of the Smarty {$link->getAdminLink()} function.
     *
     * @param string $controllerName
     * @param bool $withToken
     * @param array<string> $extraParams
     *
     * @return string
     */
    public function getAdminLink($controllerName, $withToken = true, $extraParams = [])
    {
        return $this->context->getAdminLink($controllerName, $withToken, $extraParams);
    }

    /**
     * @param $tokenName
     *
     * @return bool|string
     */
    public function getAdminToken($tokenName)
    {
        return Tools::getAdminToken($tokenName);
    }

    /**
     * @param $url
     *
     * @return bool
     */
    public function matchQuickLink($url)
    {
        return $this->context->getContext()->link->matchQuickLink($url);
    }

    /**
     * @param $url
     *
     * @return string
     */
    public function getQuickLink($url)
    {
        return Link::getQuickLink($url);
    }

    /**
     * KISS function to get an embeded iframe from Youtube.
     */
    public function getYoutubeLink($watchUrl)
    {
        $embedUrl = str_replace(['watch?v=', 'youtu.be/'], ['embed/', 'youtube.com/embed/'], $watchUrl);

        return '<iframe width="560" height="315" src="' . $embedUrl .
            '" frameborder="0" allowfullscreen class="youtube-iframe m-x-auto"></iframe>';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_layout_extension';
    }

    private function getTabs($parentId = 0, $level = 0)
    {
        $tabs = Tab::getTabs($this->context->getContext()->language->id, $parentId);
        $current_id = Tab::getCurrentParentId('');

        foreach ($tabs as $index => $tab) {
            if (!Tab::checkTabRights($tab['id_tab'])
                || !$tab['enabled']
                || ($tab['class_name'] == 'AdminStock' && Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
                || $tab['class_name'] == 'AdminCarrierWizard') {
                unset($tabs[$index]);

                continue;
            }

            // tab[class_name] does not contains the "Controller" suffix
            if (($tab['class_name'] . 'Controller' == get_class($this)) || ($current_id == $tab['id_tab']) || $tab['class_name'] == '') {
                $tabs[$index]['current'] = true;
                $tabs[$index]['current_level'] = $level;
            } else {
                $tabs[$index]['current'] = false;
            }
            $tabs[$index]['img'] = null;
            $tabs[$index]['href'] = $this->context->getContext()->link->getTabLink($tab);
            $tabs[$index]['sub_tabs'] = array_values($this->getTabs($tab['id_tab'], $level + 1));

            $subTabHref = $this->getTabLinkFromSubTabs($tabs[$index]['sub_tabs']);
            if (!empty($subTabHref)) {
                $tabs[$index]['href'] = $subTabHref;
            } elseif (0 == $tabs[$index]['id_parent'] && '' == $tabs[$index]['icon']) {
                unset($tabs[$index]);
            } elseif (empty($tabs[$index]['icon'])) {
                $tabs[$index]['icon'] = 'extension';
            }

            if (array_key_exists($index, $tabs) && array_key_exists('sub_tabs', $tabs[$index])) {
                foreach ($tabs[$index]['sub_tabs'] as $sub_tab) {
                    if ((int) $sub_tab['current'] == true) {
                        $tabs[$index]['current'] = true;
                        $tabs[$index]['current_level'] = $sub_tab['current_level'];
                    }
                }
            }
        }

        return $tabs;
    }

    private function getTabLinkFromSubTabs(array $subtabs)
    {
        foreach ($subtabs as $tab) {
            if ($tab['active'] && $tab['enabled']) {
                return $tab['href'];
            }
        }

        return '';
    }
}
