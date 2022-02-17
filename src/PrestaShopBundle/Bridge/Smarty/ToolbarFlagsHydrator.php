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

namespace PrestaShopBundle\Bridge\Smarty;

use Configuration;
use Cookie;
use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tools;

/**
 * Class ToolbarFlagsHydrator hydrate toolbar flags in the Controller configuration
 */
class ToolbarFlagsHydrator implements HydratorInterface
{
    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator, LegacyContext $legacyContext)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->cookie = $legacyContext->getContext()->cookie;
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function hydrate(ControllerConfiguration $controllerConfiguration)
    {
        $this->initToolbar($controllerConfiguration);
        $this->initPageHeaderToolbar($controllerConfiguration);

        $controllerConfiguration->templatesVars['maintenance_mode'] = !(bool) Configuration::get('PS_SHOP_ENABLE');
        $controllerConfiguration->templatesVars['debug_mode'] = (bool) _PS_MODE_DEV_;
        $controllerConfiguration->templatesVars['lite_display'] = $controllerConfiguration->liteDisplay;
        $controllerConfiguration->templatesVars['url_post'] = $this->router->generate('admin_features_index');
        $controllerConfiguration->templatesVars['show_page_header_toolbar'] = $controllerConfiguration->showPageHeaderToolbar;
        $controllerConfiguration->templatesVars['page_header_toolbar_title'] = $controllerConfiguration->pageHeaderToolbarTitle;
        $controllerConfiguration->templatesVars['title'] = $controllerConfiguration->pageHeaderToolbarTitle;
        $controllerConfiguration->templatesVars['toolbar_btn'] = $controllerConfiguration->pageHeaderToolbarButton;
        $controllerConfiguration->templatesVars['page_header_toolbar_btn'] = $controllerConfiguration->pageHeaderToolbarButton;
        $controllerConfiguration->templatesVars['help_link'] = 'https://help.prestashop.com/' . Language::getIsoById($controllerConfiguration->user->getData()->id_lang) . '/doc/'
            . Tools::getValue('controller') . '?version=' . _PS_VERSION_ . '&country=' . Language::getIsoById($controllerConfiguration->user->getData()->id_lang);
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items.
     *
     * This method will be used in add, edit...
     *
     * @param ControllerConfiguration $controllerConfiguration
     */
    public function initToolbar(ControllerConfiguration $controllerConfiguration)
    {
    }

    public function initPageHeaderToolbar(ControllerConfiguration $controllerConfiguration)
    {
        if (empty($this->toolbarTitle)) {
            $this->initToolbarTitle($controllerConfiguration);
        }

        if (count($controllerConfiguration->toolbarTitle)) {
            $controllerConfiguration->showPageHeaderToolbar = true;
        }

        if (empty($controllerConfiguration->pageHeaderToolbarTitle)) {
            $controllerConfiguration->pageHeaderToolbarTitle = $controllerConfiguration->toolbarTitle[count($controllerConfiguration->toolbarTitle) - 1];
        }
    }

    /**
     * Set default toolbarTitle to admin breadcrumb.
     */
    public function initToolbarTitle(ControllerConfiguration $controllerConfiguration)
    {
        $controllerConfiguration->toolbarTitle = array_unique($controllerConfiguration->breadcrumbs);

        if ($filter = $this->addFiltersToBreadcrumbs($controllerConfiguration)) {
            $controllerConfiguration->toolbarTitle[] = $filter;
        }
    }

    /**
     * @return string|void
     */
    public function addFiltersToBreadcrumbs(ControllerConfiguration $controllerConfiguration)
    {
        if ($controllerConfiguration->filter) {
            $filters = [];

            foreach ($controllerConfiguration->fieldsList as $field => $t) {
                if (isset($t['filter_key'])) {
                    $field = $t['filter_key'];
                }

                if (($val = Tools::getValue($controllerConfiguration->table . 'Filter_' . $field)) || $val = $this->cookie->{$this->getCookieFilterPrefix() . $controllerConfiguration->table . 'Filter_' . $field}) {
                    if (!is_array($val)) {
                        $filter_value = '';
                        if (isset($t['type']) && $t['type'] == 'bool') {
                            $filter_value = $val == 1 ? $this->translator->trans('yes', [], 'Admin.Actions') : $this->translator->trans('no', [], 'Admin.Actions');
                        } elseif (isset($t['type']) && $t['type'] == 'date' || isset($t['type']) && $t['type'] == 'datetime') {
                            $date = json_decode($val, true);
                            if (isset($date[0])) {
                                $filter_value = $date[0];
                                if (isset($date[1]) && !empty($date[1])) {
                                    $filter_value .= ' - ' . $date[1];
                                }
                            }
                        } elseif (is_string($val)) {
                            $filter_value = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
                        }
                        if (!empty($filter_value)) {
                            $filters[] = $this->translator->trans('%s: %s', [$t['title'], $filter_value], 'Admin.Actions');
                        }
                    } else {
                        $filter_value = '';
                        foreach ($val as $v) {
                            if (is_string($v) && !empty($v)) {
                                $filter_value .= ' - ' . htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                            }
                        }
                        $filter_value = ltrim($filter_value, ' -');
                        if (!empty($filter_value)) {
                            $filters[] = $this->translator->trans('%s: %s', [$t['title'], $filter_value], 'Admin.Actions');
                        }
                    }
                }
            }

            if (count($filters)) {
                return $this->translator->trans('filter by %s', [implode(', ', $filters)], 'Admin.Actions');
            }
        }
    }

    /**
     * Set the filters used for the list display.
     */
    private function getCookieFilterPrefix()
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower(get_class($this)));
    }
}
