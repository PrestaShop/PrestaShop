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

namespace PrestaShopBundle\Bridge\Helper\Listing;

use HelperList;
use PrestaShopBundle\Bridge\Smarty\BreadcrumbsAndTitleConfigurator;

/**
 * Assign variables needed by the legacy helper list to render a list using Smarty.
 * These variables come from the helper list configuration.
 */
class HelperListConfigurator
{
    /**
     * @var BreadcrumbsAndTitleConfigurator
     */
    private $breadcrumbsAndTitleHydrator;

    /**
     * @param BreadcrumbsAndTitleConfigurator $breadcrumbsAndTitleHydrator
     */
    public function __construct(
        BreadcrumbsAndTitleConfigurator $breadcrumbsAndTitleHydrator
    ) {
        $this->breadcrumbsAndTitleHydrator = $breadcrumbsAndTitleHydrator;
    }

    /**
     * This function sets various display options for helper list.
     *
     * @param HelperListConfiguration $helperListConfiguration
     * @param HelperList $helper
     *
     * @return void
     */
    public function setHelperDisplay(
        HelperListConfiguration $helperListConfiguration,
        HelperList $helper
    ): void {
        $breadcrumbs = $this->breadcrumbsAndTitleHydrator->getBreadcrumbs($helperListConfiguration->id);

        $helper->title = $breadcrumbs['tab']['name'];
        $helper->toolbar_btn = $helperListConfiguration->toolbarButton;
        $helper->show_toolbar = true;
        $helper->actions = $helperListConfiguration->actions;
        $helper->bulk_actions = $helperListConfiguration->bulkActions;
        $helper->currentIndex = $helperListConfiguration->legacyCurrentIndex;
        $helper->table = $helperListConfiguration->table;
        $helper->orderBy = $helperListConfiguration->orderBy;
        $helper->orderWay = $helperListConfiguration->orderWay;
        $helper->listTotal = $helperListConfiguration->listTotal;
        $helper->identifier = $helperListConfiguration->identifier;
        $helper->token = $helperListConfiguration->token;
        $helper->position_identifier = $helperListConfiguration->positionIdentifier;
        $helper->controller_name = $helperListConfiguration->legacyControllerName;
        $helper->list_id = $helperListConfiguration->listId ?? $helperListConfiguration->table;
        $helper->bootstrap = $helperListConfiguration->bootstrap;
    }
}
