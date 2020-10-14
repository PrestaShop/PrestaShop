<?php
/*
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

namespace PrestaShop\PrestaShop\Adapter\Layout\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Entity\HelperShop;
use PrestaShop\PrestaShop\Adapter\Entity\Hook;
use PrestaShop\PrestaShop\Adapter\Entity\Language;
use PrestaShop\PrestaShop\Adapter\Entity\Media;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Layout\Query\GetLayoutVariables;
use PrestaShop\PrestaShop\Core\Domain\Layout\QueryHandler\GetLayoutVariablesHandlerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use QuickAccess;
use Shop;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tab;
use Tools;

class GetLayoutVariablesHandler implements GetLayoutVariablesHandlerInterface
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @var TabRepository
     */
    private $tabRepository;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var string
     */
    private $username;

    public function __construct(
        LegacyContext $context,
        TranslatorInterface $translator,
        TabRepository $tabRepository,
        ConfigurationInterface $configuration,
        RequestStack $requestStack,
        CsrfTokenManagerInterface $tokenManager,
        string $username
    ) {
        $this->context = $context;
        $this->translator = $translator;
        $this->tabRepository = $tabRepository;
        $this->configuration = $configuration;
        $this->requestStack = $requestStack;
        $this->tokenManager = $tokenManager;
        $this->username = $username;
        if (null !== $this->requestStack->getCurrentRequest()->attributes->get('_legacy_controller')) {
            $this->controllerName = $this->requestStack->getCurrentRequest()->attributes->get('_legacy_controller');
        } else {
            $this->controllerName = $this->context->getContext()->controller->controller_name;
        }
    }

    public function handle(GetLayoutVariables $query)
    {
        $tabs = $this->getTabs();
        $currentTabLevel = 0;
        foreach ($tabs as $tab) {
            $currentTabLevel = isset($tab['current_level']) ? $tab['current_level'] : $currentTabLevel;
        }

        $breadcrumbs = $this->getBreadcrumbs($this->tabRepository->findOneIdByClassName($this->controllerName));
        $quickAccessCurrentLinkName = Tools::safeOutput($breadcrumbs['tab']['name'] . (isset($breadcrumbs['action']) ? ' - ' . $breadcrumbs['action']['name'] : ''));
        $quickAccessCurrentLinkIcon = $breadcrumbs['container']['icon'];

        $token = Tools::getAdminToken(
            $this->controllerName .
            (string) $this->tabRepository->findOneIdByClassName($this->controllerName) .
            (string) $this->context->getContext()->employee->id
        );

        $current_index = 'index.php' . (($controller = Tools::getValue('controller')) ? '?controller=' . $controller : '');
        if ($back = Tools::getValue('back')) {
            $current_index .= '&back=' . urlencode($back);
        }

        $liteDisplay = (int) Tools::getValue('liteDisplaying');

        $isProductPage = ('AdminProducts' === $this->controllerName);

        $variables = [
            'use_new_layout' => true,
            'viewport_scale' => $isProductPage ? '0.75' : '1',
            'iso' => $this->context->getContext()->language->iso_code,
            'lang_is_rtl' => $this->context->getContext()->language->is_rtl,
            'full_language_code' => $this->context->getContext()->language->language_code,
            'full_cldr_language_code' => $this->context->getContext()->getCurrentLocale()->getCode(),
            'country_iso_code' => $this->context->getContext()->country->iso_code,
            'default_language' => (int) $this->configuration->get('PS_LANG_DEFAULT'),
            'round_mode' => $this->configuration->get('PS_PRICE_ROUND_MODE'),
            'img_dir' => _PS_IMG_,
            'shop_name' => $this->configuration->get('PS_SHOP_NAME'),
            'collapse_menu' => isset($this->context->getContext()->cookie->collapse_menu) ? (int) $this->context->getContext()->cookie->collapse_menu : 0,
            'ps_version' => _PS_VERSION_,
            'debug_mode' => (bool) _PS_MODE_DEV_,
            'maintenance_mode' => !(bool) $this->configuration->get('PS_SHOP_ENABLE'),
            'install_dir_exists' => file_exists(_PS_ADMIN_DIR_ . '/../install'),
            'quick_access' => $this->getQuickAccess(),
            'baseAdminUrl' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/',
            'is_multishop' => Shop::isFeatureActive(),
            'base_url' => $this->context->getContext()->shop->getBaseURL(),
            'employee' => $this->context->getContext()->employee,
            'logout_link' => $this->context->getContext()->link->getAdminLink('AdminLogin', true, [], ['logout' => 1]),
            'tabs' => $tabs,
            'current_tab_level' => $currentTabLevel,
            'breadcrumbs2' => $breadcrumbs,
            'quick_access_current_link_icon' => $quickAccessCurrentLinkIcon,
            'quick_access_current_link_name' => $quickAccessCurrentLinkName,
            'token' => $token,
            'no_order_tip' => $this->getNotificationTip('order'),
            'no_customer_tip' => $this->getNotificationTip('customer'),
            'no_customer_message_tip' => $this->getNotificationTip('customer_message'),
            'js_router_metadata' => $this->getRouterMetadata(),
            'help_link' => 'https://help.prestashop.com/' . Language::getIsoById($this->context->getContext()->employee->id_lang) . '/doc/'
                . Tools::getValue('controller') . '?version=' . _PS_VERSION_ . '&country=' . Language::getIsoById($this->context->getContext()->employee->id_lang),
            'js_def' => Media::getJsDef(),
            'toggle_navigation_url' => $this->context->getContext()->link->getAdminLink('AdminEmployees', true, [], [
                'action' => 'toggleMenu',
            ]),
            'shop_list' => (new HelperShop())->getRenderedShopList(),
            'js_translatable' => [],
            'enableSidebar' => false,
            // to be changed
            'page_header_toolbar' => '',
            'modal_module_list' => '',
            'toolbar_btn' => [],
            'headerTabContent' => '',
            'disableDefaultErrorOutPut' => false,
            'errors' => [],
            'informations' => [],
            'confirmations' => [],
            'warnings' => [],
            'modals' => [],
            'url' => '',
            'show_new_orders' => $this->configuration->get('PS_SHOW_NEW_ORDERS'),
            'show_new_customers' => $this->configuration->get('PS_SHOW_NEW_CUSTOMERS'),
            'show_new_messages' => $this->configuration->get('PS_SHOW_NEW_MESSAGES'),
            'multishop_context' => false, //$this->multishop_context,
            'default_tab_link' => $this->context->getContext()->link->getAdminLink(Tab::getClassNameById((int) $this->context->getContext()->employee->default_tab)),
            'controller_name' => $this->controllerName,
            'currentIndex' => $current_index, // maybe,
            'lite_display' => $liteDisplay,
            'display_header' => true,
            'display_footer' => !$liteDisplay,
            'display_header_javascript' => true,
            // deleted:
            //'php_errors' => [],
            //'search_type' => Tools::getValue('bo_search_type'),
            //'bo_query' => Tools::safeOutput(Tools::stripslashes(Tools::getValue('bo_query'))),
        ];

        if (Shop::isFeatureActive() && Shop::getTotalShops(false, null) > 1) {
            $variables['shop_context'] = Shop::getContext();
        }

        $this->context->getContext()->controller->setMedia(true);
        Hook::exec('actionAdminControllerSetMedia');

        $variables['css_files'] = $this->context->getContext()->controller->css_files;
        $variables['js_files'] = array_unique($this->context->getContext()->controller->js_files);

        return $variables;
    }

    private function getRouterMetadata(): array
    {
        return [
            'base_url' => $this->requestStack->getCurrentRequest()->getBaseUrl(),
            'token' => $this->tokenManager->getToken($this->username)->getValue(),
        ];
    }

    private function getBreadcrumbs($tab_id, $tabs = null)
    {
        if (!is_array($tabs)) {
            $tabs = [];
        }

        $tabs = Tab::recursiveTab($tab_id, $tabs);

        $dummy = ['name' => '', 'href' => '', 'icon' => ''];
        $breadcrumbs = [
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy,
        ];
        if (!empty($tabs[0])) {
            $breadcrumbs['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs['tab']['href'] = $this->context->getContext()->link->getTabLink($tabs[0]);
            if (!isset($tabs[1])) {
                $breadcrumbs['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (!empty($tabs[1])) {
            $breadcrumbs['container']['name'] = $tabs[1]['name'];
            $breadcrumbs['container']['href'] = $this->context->getContext()->link->getTabLink($tabs[1]);
            $breadcrumbs['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

        return $breadcrumbs;
    }

    private function getNotificationTip($type)
    {
        $tips = [
            'order' => [
                $this->translator->trans('Did you check your conversion rate lately?', [], 'Admin.Navigation.Notification'),
                $this->translator->trans('How about some seasonal discounts?', [], 'Admin.Navigation.Notification'),
                $this->translator->trans(
                    'Have you checked your [1][2]abandoned carts[/2][/1]?[3]Your next order could be hiding there!',
                    [
                        '[1]' => '<strong>',
                        '[/1]' => '</strong>',
                        '[2]' => '<a href="' . $this->context->getContext()->link->getAdminLink('AdminCarts', true, [], ['action' => 'filterOnlyAbandonedCarts']) . '">',
                        '[/2]' => '</a>',
                        '[3]' => '<br>',
                    ],
                    'Admin.Navigation.Notification'
                ),
            ],
            'customer' => [
                $this->translator->trans('Have you sent any acquisition email lately?', [], 'Admin.Navigation.Notification'),
                $this->translator->trans('Are you active on social media these days?', [], 'Admin.Navigation.Notification'),
                $this->translator->trans('Have you considered selling on marketplaces?', [], 'Admin.Navigation.Notification'),
            ],
            'customer_message' => [
                $this->translator->trans('That\'s more time for something else!', [], 'Admin.Navigation.Notification'),
                $this->translator->trans('No news is good news, isn\'t it?', [], 'Admin.Navigation.Notification'),
                $this->translator->trans('Seems like all your customers are happy :)', [], 'Admin.Navigation.Notification'),
            ],
        ];

        if (!isset($tips[$type])) {
            return '';
        }

        return $tips[$type][array_rand($tips[$type])];
    }

    private function getQuickAccess()
    {
        if (!(int) $this->context->getContext()->employee->id) {
            return [];
        }

        return QuickAccess::getQuickAccessesWithToken($this->context->getContext()->language->id, (int) $this->context->getContext()->employee->id);
    }

    private function getTabs($parentId = 0, $level = 0)
    {
        $tabs = Tab::getTabs($this->context->getContext()->language->id, $parentId);
        $current_id = $this->tabRepository->findOneIdByClassName($this->controllerName);

        foreach ($tabs as $index => $tab) {
            if (!Tab::checkTabRights($tab['id_tab'])
                || !$tab['enabled']
                || ($tab['class_name'] == 'AdminStock' && $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT') == 0)
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
