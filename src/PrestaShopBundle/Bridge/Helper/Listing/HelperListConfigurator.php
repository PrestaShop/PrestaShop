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
use PrestaShopBundle\Bridge\Exception\InvalidRowActionException;
use PrestaShopBundle\Bridge\Smarty\BreadcrumbsAndTitleConfigurator;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        $breadcrumbs = $this->breadcrumbsAndTitleHydrator->getBreadcrumbs($helperListConfiguration->getTabId());

        $helper->title = $breadcrumbs['tab']['name'];
        //@todo: probably we can add optionsResolver here to check array structure of actions/fields at least
        $helper->toolbar_btn = $this->resolveToolbarActions($helperListConfiguration->getToolbarActions());
        $helper->actions = $this->resolveRowActions($helperListConfiguration->getRowActions());
        $helper->bulk_actions = $this->resolveBulkActions($helperListConfiguration->getBulkActions());
        $helper->show_toolbar = true;
        $helper->currentIndex = $helperListConfiguration->getLegacyCurrentIndex();
        $helper->table = $helperListConfiguration->getTableName();
        $helper->orderBy = $helperListConfiguration->orderBy;
        $helper->orderWay = $helperListConfiguration->orderWay;
        $helper->listTotal = $helperListConfiguration->listTotal;
        $helper->identifier = $helperListConfiguration->getIdentifier();
        $helper->token = $helperListConfiguration->getToken();
        $helper->position_identifier = $helperListConfiguration->getPositionIdentifier();
        $helper->controller_name = $helperListConfiguration->getLegacyControllerName();
        $helper->list_id = $helperListConfiguration->getListId() ?? $helperListConfiguration->getTableName();
        $helper->bootstrap = $helperListConfiguration->isBootstrap();
    }

    //@todo: maybe resolvers could be moved to ListConfiguration::addBulkAction,
    //      then IDE should autocomplete the options when filling them
    //      and it would better follow "fail fast" principle
    protected function resolveToolbarActions(array $toolbarActions): array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefined(['href', 'desc', 'class'])
            ->setDefaults(['class' => ''])
            ->setAllowedTypes('class', ['string'])
            ->setAllowedTypes('href', ['string'])
            ->setAllowedTypes('desc', ['string'])
        ;

        $resolvedActions = [];
        foreach ($toolbarActions as $label => $actionConfig) {
            $resolvedActions[$label] = $optionsResolver->resolve($actionConfig);
        }

        return $resolvedActions;
    }

    protected function resolveBulkActions(array $bulkActions): array
    {
        $optionsResolver = new OptionsResolver();
        $optionsResolver
            ->setDefaults([
                'text' => '',
                'icon' => null,
                'confirm' => null,
            ])
            ->setAllowedTypes('text', ['string'])
            ->setAllowedTypes('icon', ['string', 'null'])
            ->setAllowedTypes('confirm', ['string', 'null'])
        ;

        $resolvedActions = [];
        foreach ($bulkActions as $label => $actionConfig) {
            $resolvedActions[$label] = $optionsResolver->resolve($actionConfig);
        }

        return $resolvedActions;
    }

    /**
     * @param array $rowActions
     *
     * @return array
     */
    protected function resolveRowActions(array $rowActions): array
    {
        $availableRowActions = $this->getAvailableListRowActions();

        foreach ($rowActions as $action) {
            if (!in_array($action, $availableRowActions, true)) {
                throw new InvalidRowActionException(sprintf('Invalid row action "%s"', $action));
            }
        }

        return $rowActions;
    }

    // @todo: how are we planning to allow extending it? do we really need to validate them at all?
    protected function getAvailableListRowActions(): array
    {
        return [
            'view',
            'edit',
            'delete',
            'duplicate',
        ];
    }
}
