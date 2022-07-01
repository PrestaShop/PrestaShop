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

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;

class LegacyControllerBridgeFactory
{
    /**
     * @var ControllerConfigurationFactory
     */
    private $controllerConfigurationFactory;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @param ControllerConfigurationFactory $controllerConfigurationFactory
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(
        ControllerConfigurationFactory $controllerConfigurationFactory,
        FeatureInterface $multistoreFeature
    ) {
        $this->controllerConfigurationFactory = $controllerConfigurationFactory;
        $this->multistoreFeature = $multistoreFeature;
    }

    /**
     * @param int $tabId
     * @param string $objectModelClassName
     * @param string $controllerNameLegacy
     * @param string $tableName
     *
     * @return LegacyControllerBridgeInterface
     */
    public function create(
        int $tabId,
        string $objectModelClassName,
        string $controllerNameLegacy,
        string $tableName
    ): LegacyControllerBridgeInterface {
        $controllerConfiguration = $this->controllerConfigurationFactory->create(
            $tabId,
            $objectModelClassName,
            $controllerNameLegacy,
            $tableName
        );

        return new LegacyControllerBridge(
            $controllerConfiguration,
            $this->getPropertiesMap(),
            $this->multistoreFeature
        );
    }

    /**
     * Map legacy controller properties with bridge
     *
     * @return array<string, string>
     */
    private function getPropertiesMap(): array
    {
        return [
            'id' => 'controllerConfiguration.tabId',
            'className' => 'controllerConfiguration.className',
            'controller_name' => 'controllerConfiguration.legacyControllerName',
            'php_self' => 'controllerConfiguration.legacyControllerName',
            'current_index' => 'controllerConfiguration.legacyCurrentIndex',
            'position_identifier' => 'controllerConfiguration.positionIdentifier',
            'table' => 'controllerConfiguration.tableName',
            'token' => 'controllerConfiguration.token',
            'meta_title' => 'controllerConfiguration.metaTitle',
            'breadcrumbs' => 'controllerConfiguration.breadcrumbs',
            'lite_display' => 'controllerConfiguration.liteDisplay',
            'display' => 'controllerConfiguration.display',
            'show_page_header_toolbar' => 'controllerConfiguration.showPageHeaderToolbar',
            'page_header_toolbar_title' => 'controllerConfiguration.pageHeaderToolbarTitle',
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
        ];
    }
}
