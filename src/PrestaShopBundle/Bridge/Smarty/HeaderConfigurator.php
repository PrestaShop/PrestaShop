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

use Cookie;
use Country;
use Currency;
use Language;
use Link;
use Media;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Action\ActionsBarButtonsCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHookInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use QuickAccess;
use Shop;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tab;
use Tools;

/**
 * Sets template variables for header.
 * For example notification tips, employee information, quick access...
 */
class HeaderConfigurator implements ConfiguratorInterface
{
    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var LocaleInterface
     */
    private $currentLocale;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var Shop
     */
    private $shop;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param LegacyContext $legacyContext
     * @param HookDispatcherInterface $hookDispatcher
     * @param Configuration $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        LegacyContext $legacyContext,
        HookDispatcherInterface $hookDispatcher,
        Configuration $configuration
    ) {
        $this->cookie = $legacyContext->getContext()->cookie;
        $this->country = $legacyContext->getContext()->country;
        $this->link = $legacyContext->getContext()->link;
        $this->language = $legacyContext->getLanguage();
        $this->currency = $legacyContext->getContext()->currency;
        $this->currentLocale = $legacyContext->getContext()->getCurrentLocale();
        $this->shop = $legacyContext->getContext()->shop;
        $this->translator = $translator;
        $this->hookDispatcher = $hookDispatcher;
        $this->configuration = $configuration;
    }

    /**
     * Assign smarty variables for the header.
     *
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function configure(ControllerConfiguration $controllerConfiguration): void
    {
        $controllerConfiguration->templateVars['table'] = $controllerConfiguration->tableName;
        $controllerConfiguration->templateVars['token'] = $controllerConfiguration->token;
        $controllerConfiguration->templateVars['host_mode'] = (int) defined('_PS_HOST_MODE_');
        $controllerConfiguration->templateVars['stock_management'] = (int) $this->configuration->get('PS_STOCK_MANAGEMENT');
        $controllerConfiguration->templateVars['no_order_tip'] = $this->getNotificationTip('order');
        $controllerConfiguration->templateVars['no_customer_tip'] = $this->getNotificationTip('customer');
        $controllerConfiguration->templateVars['no_customer_message_tip'] = $this->getNotificationTip('customer_message');

        if ($controllerConfiguration->displayHeader) {
            $controllerConfiguration->templateVars['displayBackOfficeHeader'] = $this->getDisplayHookRender(
                $this->hookDispatcher->dispatchRenderingWithParameters('displayBackOfficeHeader', [])
            );
        }

        $menuLinksCollections = new ActionsBarButtonsCollection();

        $this->hookDispatcher->dispatchWithParameters(
            'displayBackOfficeEmployeeMenu',
            [
                'links' => $menuLinksCollections,
            ]
        );

        $controllerConfiguration->templateVars['displayBackOfficeTop'] = $this->getDisplayHookRender(
            $this->hookDispatcher->dispatchRenderingWithParameters('displayBackOfficeTop', [])
        );
        $controllerConfiguration->templateVars['displayBackOfficeEmployeeMenu'] = $menuLinksCollections;
        $controllerConfiguration->templateVars['submit_form_ajax'] = (int) Tools::getValue('submitFormAjax');

        $tabs = $this->getTabs($controllerConfiguration);
        $currentTabLevel = 0;
        foreach ($tabs as $tab) {
            $currentTabLevel = isset($tab['current_level']) ? $tab['current_level'] : $currentTabLevel;
        }

        $controllerConfiguration->templateVars['bo_query'] = Tools::safeOutput(Tools::getValue('bo_query'));
        $controllerConfiguration->templateVars['collapse_menu'] = isset($this->cookie->collapse_menu) ? (int) $this->cookie->collapse_menu : 0;
        $controllerConfiguration->templateVars['default_tab_link'] = $this->link->getAdminLink(Tab::getClassNameById((int) $controllerConfiguration->getUser()->getData()->default_tab));
        $controllerConfiguration->templateVars['employee'] = $controllerConfiguration->getUser()->getData();
        $controllerConfiguration->templateVars['help_box'] = $this->configuration->get('PS_HELPBOX');
        $controllerConfiguration->templateVars['is_multishop'] = Shop::isFeatureActive();
        $controllerConfiguration->templateVars['login_link'] = $this->link->getAdminLink('AdminLogin');
        $controllerConfiguration->templateVars['logout_link'] = $this->link->getAdminLink('AdminLogin', true, [], ['logout' => 1]);
        $controllerConfiguration->templateVars['multi_shop'] = Shop::isFeatureActive();
        $controllerConfiguration->templateVars['quick_access'] = QuickAccess::getQuickAccessesWithToken($this->language->id, (int) $controllerConfiguration->getUser()->getData()->id);
        $controllerConfiguration->templateVars['round_mode'] = $this->configuration->get('PS_PRICE_ROUND_MODE');
        $controllerConfiguration->templateVars['base_url'] = $this->shop->getBaseURL(true);
        $controllerConfiguration->templateVars['bootstrap'] = $controllerConfiguration->bootstrap;
        $controllerConfiguration->templateVars['controller_name'] = $controllerConfiguration->legacyControllerName;
        $controllerConfiguration->templateVars['country_iso_code'] = $this->country->iso_code;
        $controllerConfiguration->templateVars['currentIndex'] = $controllerConfiguration->legacyCurrentIndex;
        $controllerConfiguration->templateVars['current_tab_level'] = $currentTabLevel;
        $controllerConfiguration->templateVars['default_language'] = (int) $this->configuration->get('PS_LANG_DEFAULT');
        $controllerConfiguration->templateVars['full_language_code'] = $this->language->language_code;
        $controllerConfiguration->templateVars['full_cldr_language_code'] = $this->currentLocale->getCode();
        $controllerConfiguration->templateVars['img_dir'] = _PS_IMG_;
        $controllerConfiguration->templateVars['install_dir_exists'] = file_exists(_PS_ADMIN_DIR_ . '/../install');
        $controllerConfiguration->templateVars['iso'] = $this->language->iso_code;
        $controllerConfiguration->templateVars['iso_user'] = $this->language->iso_code;
        $controllerConfiguration->templateVars['lang_is_rtl'] = $this->language->is_rtl;
        $controllerConfiguration->templateVars['link'] = $this->link;
        $controllerConfiguration->templateVars['shop_name'] = $this->configuration->get('PS_SHOP_NAME');
        $controllerConfiguration->templateVars['tabs'] = $tabs;
        $controllerConfiguration->templateVars['version'] = _PS_VERSION_;
        $controllerConfiguration->templateVars['multishop_context'] = $controllerConfiguration->multiShopContext;

        Media::addJsDef(
            [
                'currency_specifications' => $this->preparePriceSpecifications(),
                'number_specifications' => $this->prepareNumberSpecifications(),
            ]
        );
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getNotificationTip(string $type): string
    {
        $tips = [
            'order' => [
                $this->translator->trans(
                    'Have you checked your [1][2]abandoned carts[/2][/1]?[3]Your next order could be hiding there!',
                    [
                        '[1]' => '<strong>',
                        '[/1]' => '</strong>',
                        '[2]' => '<a href="' . $this->link->getAdminLink('AdminCarts', true, [], ['action' => 'filterOnlyAbandonedCarts']) . '">',
                        '[/2]' => '</a>',
                        '[3]' => '<br>',
                    ],
                    'Admin.Navigation.Notification'
                ),
            ],
            'customer' => [
                $this->translator->trans('Are you active on social media these days?', [], 'Admin.Navigation.Notification'),
            ],
            'customer_message' => [
                $this->translator->trans('Seems like all your customers are happy :)', [], 'Admin.Navigation.Notification'),
            ],
        ];

        if (!isset($tips[$type])) {
            return '';
        }

        return $tips[$type][array_rand($tips[$type])];
    }

    /**
     * @param RenderedHookInterface $renderedHook
     *
     * @return string|null
     */
    private function getDisplayHookRender(RenderedHookInterface $renderedHook): ?string
    {
        if (!$content = $renderedHook->getContent()) {
            return null;
        }

        $result = '';

        foreach ($content as $hookContent) {
            $result .= implode($hookContent);
        }

        return $result;
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     * @param int $parentId
     * @param int $level
     *
     * @return array
     */
    private function getTabs(ControllerConfiguration $controllerConfiguration, $parentId = 0, $level = 0): array
    {
        $tabs = Tab::getTabs($this->language->id, $parentId);
        $current_id = Tab::getCurrentParentId();

        foreach ($tabs as $index => $tab) {
            if (!Tab::checkTabRights($tab['id_tab'])
                || !$tab['enabled']
                || $tab['class_name'] == 'AdminStock'
                || $tab['class_name'] == 'AdminCarrierWizard') {
                unset($tabs[$index]);

                continue;
            }

            // tab[class_name] does not contains the "Controller" suffix
            if (($tab['class_name'] . 'Controller' == $controllerConfiguration->legacyControllerName) || ($current_id == $tab['id_tab']) || $tab['class_name'] == $controllerConfiguration->legacyControllerName) {
                $tabs[$index]['current'] = true;
                $tabs[$index]['current_level'] = $level;
            } else {
                $tabs[$index]['current'] = false;
            }
            $tabs[$index]['img'] = null;
            $tabs[$index]['href'] = $this->link->getTabLink($tab);
            $tabs[$index]['sub_tabs'] = array_values($this->getTabs($controllerConfiguration, $tab['id_tab'], $level + 1));

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

    /**
     * Get the url of the first active sub-tab.
     *
     * @param array[] $subtabs
     *
     * @return string Url, or empty if no active sub-tab
     */
    private function getTabLinkFromSubTabs(array $subtabs): string
    {
        foreach ($subtabs as $tab) {
            if ($tab['active'] && $tab['enabled']) {
                return $tab['href'];
            }
        }

        return '';
    }

    /**
     * Prepare price specifications to display cldr prices in javascript context.
     *
     * @return array
     */
    private function preparePriceSpecifications(): array
    {
        /* @var PriceSpecification */
        $priceSpecification = $this->currentLocale->getPriceSpecification($this->currency->iso_code);

        return array_merge(
            ['symbol' => $priceSpecification->getSymbolsByNumberingSystem(LocaleInterface::NUMBERING_SYSTEM_LATIN)->toArray()],
            $priceSpecification->toArray()
        );
    }

    /**
     * Prepare number specifications to display cldr numbers in javascript context.
     *
     * @return array
     */
    private function prepareNumberSpecifications(): array
    {
        /* @var NumberSpecification */
        $numberSpecification = $this->currentLocale->getNumberSpecification();

        return array_merge(
            ['symbol' => $numberSpecification->getSymbolsByNumberingSystem(LocaleInterface::NUMBERING_SYSTEM_LATIN)->toArray()],
            $numberSpecification->toArray()
        );
    }
}
