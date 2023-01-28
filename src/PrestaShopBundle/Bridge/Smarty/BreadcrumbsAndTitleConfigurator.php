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

use Link;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\AdminController\ControllerConfiguration;
use Tab;
use Tools;

/**
 * This class get the breadcrumb and title information configuration from Tabs,
 * and sets them in the controller configuration.
 */
class BreadcrumbsAndTitleConfigurator implements ConfiguratorInterface
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param LegacyContext $legacyContext
     * @param Configuration $configuration
     */
    public function __construct(LegacyContext $legacyContext, Configuration $configuration)
    {
        $this->link = $legacyContext->getContext()->link;
        $this->configuration = $configuration;
    }

    /**
     * Set breadcrumbs array for the controller page.
     *
     * @param ControllerConfiguration $controllerConfiguration
     */
    public function configure(ControllerConfiguration $controllerConfiguration): void
    {
        $tabs = Tab::recursiveTab($controllerConfiguration->tabId, []);

        if (!empty($tabs[0])) {
            $this->addMetaTitle($controllerConfiguration, $tabs[0]['name']);
        }

        $breadcrumbs = $this->getBreadcrumbs($controllerConfiguration->tabId);

        $controllerConfiguration->breadcrumbs[] = $breadcrumbs['tab']['name'] ?? '';

        $controllerConfiguration->templateVars['breadcrumbs2'] = $breadcrumbs;
        $controllerConfiguration->templateVars['quick_access_current_link_name'] = Tools::safeOutput($breadcrumbs['tab']['name'] . (isset($breadcrumbs['action']) ? ' - ' . $breadcrumbs['action']['name'] : ''));
        $controllerConfiguration->templateVars['quick_access_current_link_icon'] = $breadcrumbs['container']['icon'];

        $navigationPipe = $this->configuration->get('PS_NAVIGATION_PIPE') ?: '>';
        $controllerConfiguration->templateVars['navigationPipe'] = $navigationPipe;
    }

    /**
     * Get breadcrumbs configuration from tabs.
     *
     * @param int $tabId
     *
     * @return array
     */
    public function getBreadcrumbs(int $tabId): array
    {
        $tabs = Tab::recursiveTab($tabId, []);

        $dummy = ['name' => '', 'href' => '', 'icon' => ''];
        $breadcrumbs = [
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy,
        ];

        if (!empty($tabs[0])) {
            $breadcrumbs['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs['tab']['href'] = $this->link->getTabLink($tabs[0]);
            if (!isset($tabs[1])) {
                $breadcrumbs['tab']['icon'] = 'icon-' . $tabs[0]['class_name'];
            }
        }
        if (!empty($tabs[1])) {
            $breadcrumbs['container']['name'] = $tabs[1]['name'];
            $breadcrumbs['container']['href'] = $this->link->getTabLink($tabs[1]);
            $breadcrumbs['container']['icon'] = 'icon-' . $tabs[1]['class_name'];
        }

        return $breadcrumbs;
    }

    /**
     * Add an entry to the meta title.
     *
     * @param ControllerConfiguration $controllerConfiguration
     * @param string $entry new entry
     *
     * @return void
     */
    public function addMetaTitle(ControllerConfiguration $controllerConfiguration, string $entry): void
    {
        if (is_array($controllerConfiguration->metaTitle)) {
            $controllerConfiguration->metaTitle[] = $entry;
        }
    }
}
