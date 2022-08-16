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

use Language;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use Symfony\Component\Routing\RouterInterface;
use Tools;

/**
 * This class sets help link, maintenance information, title, and others in the controller configuration.
 */
class ToolbarFlagsConfigurator implements ConfiguratorInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Documentation
     */
    private $documentation;

    /**
     * @param RouterInterface $router
     * @param Configuration $configuration
     * @param Documentation $documentation
     */
    public function __construct(RouterInterface $router, Configuration $configuration, Documentation $documentation)
    {
        $this->router = $router;
        $this->configuration = $configuration;
        $this->documentation = $documentation;
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function configure(ControllerConfiguration $controllerConfiguration): void
    {
        $this->initToolbar($controllerConfiguration);
        $this->initPageHeaderToolbar($controllerConfiguration);

        $controllerConfiguration->templateVars['maintenance_mode'] = !(bool) $this->configuration->get('PS_SHOP_ENABLE');
        $controllerConfiguration->templateVars['debug_mode'] = (bool) _PS_MODE_DEV_;
        $controllerConfiguration->templateVars['lite_display'] = $controllerConfiguration->liteDisplay;
        // @todo: fix hardcoded route
        $controllerConfiguration->templateVars['url_post'] = $this->router->generate('admin_features_index');
        $controllerConfiguration->templateVars['show_page_header_toolbar'] = $controllerConfiguration->showPageHeaderToolbar;
        $controllerConfiguration->templateVars['page_header_toolbar_title'] = $controllerConfiguration->pageHeaderToolbarTitle;
        $controllerConfiguration->templateVars['title'] = $controllerConfiguration->pageHeaderToolbarTitle;
        $controllerConfiguration->templateVars['toolbar_btn'] = $controllerConfiguration->pageHeaderToolbarButtons;
        $controllerConfiguration->templateVars['page_header_toolbar_btn'] = $controllerConfiguration->pageHeaderToolbarButtons;
        $controllerConfiguration->templateVars['help_link'] = $this->documentation->generateLink(
            Tools::getValue('controller'),
            (string) Language::getIsoById($controllerConfiguration->getUser()->getData()->id_lang)
        );
    }

    /**
     * assign default action in toolbar_btn smarty var, if they are not set.
     * uses override to specifically add, modify or remove items.
     *
     * This method will be used in add, edit...
     *
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function initToolbar(ControllerConfiguration $controllerConfiguration): void
    {
    }

    /**
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function initPageHeaderToolbar(ControllerConfiguration $controllerConfiguration): void
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
     *
     * @param ControllerConfiguration $controllerConfiguration
     *
     * @return void
     */
    public function initToolbarTitle(ControllerConfiguration $controllerConfiguration): void
    {
        $controllerConfiguration->toolbarTitle = array_unique($controllerConfiguration->breadcrumbs);
    }
}
