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

namespace PrestaShopBundle\Bridge\Helper;

use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use PrestaShopBundle\Bridge\Smarty\BreadcrumbsAndTitleHydrator;
use PrestaShopBundle\Bridge\Smarty\ToolbarFlagsHydrator;
use PrestaShopBundle\Service\Routing\Router;

/**
 * Assign variables needed by HelperList
 */
class HelperListVarsAssigner
{
    /**
     * @var BreadcrumbsAndTitleHydrator
     */
    private $breadcrumbsAndTitleHydrator;

    /**
     * @var ToolbarFlagsHydrator
     */
    private $toolbarFlagsHydrator;

    /**
     * @var Router
     */
    private $router;

    public function __construct(
        BreadcrumbsAndTitleHydrator $breadcrumbsAndTitleHydrator,
        ToolbarFlagsHydrator $toolbarFlagsHydrator,
        Router $router
    ) {
        $this->breadcrumbsAndTitleHydrator = $breadcrumbsAndTitleHydrator;
        $this->toolbarFlagsHydrator = $toolbarFlagsHydrator;
        $this->router = $router;
    }

    /**
     * This function sets various display options for helper list.
     *
    //* @param HelperList|HelperView|HelperOptions $helper
     */
    public function setHelperDisplay(
        ControllerConfiguration $controllerConfiguration,
        HelperListConfiguration $helperListConfiguration,
        $helper
    ) {
        if (empty($controllerConfiguration->breadcrumbs)) {
            $this->breadcrumbsAndTitleHydrator->hydrate($controllerConfiguration);
        }

        if (empty($controllerConfiguration->toolbarTitle)) {
            $this->toolbarFlagsHydrator->hydrate($controllerConfiguration);
        }

        $helper->title = $controllerConfiguration->toolbarTitle;
        $helper->toolbar_btn = $controllerConfiguration->toolbarButton;
        $helper->show_toolbar = true;
        $helper->actions = $controllerConfiguration->actions;
        $helper->bulk_actions = $controllerConfiguration->bulkActions;
        $helper->currentIndex = $this->router->generate('admin_features_index');
        $helper->table = $controllerConfiguration->table;
        if (isset($helper->name_controller)) {
            $helper->name_controller = $controllerConfiguration->controllerNameLegacy;
        }
        $helper->orderBy = $helperListConfiguration->orderBy;
        $helper->orderWay = $helperListConfiguration->orderWay;
        $helper->listTotal = $helperListConfiguration->listTotal;
        $helper->identifier = $helperListConfiguration->identifier;
        $helper->token = $controllerConfiguration->token;
        $helper->position_identifier = $controllerConfiguration->positionIdentifier;
        $helper->controller_name = $controllerConfiguration->controllerNameLegacy;
        $helper->list_id = $helperListConfiguration->listId ?? $controllerConfiguration->table;
        $helper->bootstrap = $controllerConfiguration->bootstrap;
    }
}
