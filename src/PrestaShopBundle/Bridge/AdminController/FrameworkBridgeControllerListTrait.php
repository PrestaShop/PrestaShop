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

use PrestaShopBundle\Bridge\AdminController\Action\ActionInterface;
use PrestaShopBundle\Bridge\AdminController\Action\ListBulkAction;
use PrestaShopBundle\Bridge\AdminController\Action\ListHeaderToolbarAction;
use PrestaShopBundle\Bridge\AdminController\Action\ListRowAction;
use PrestaShopBundle\Bridge\AdminController\Field\FieldInterface;
use PrestaShopBundle\Bridge\Exception\NotAllowedActionTypeForListException;
use PrestaShopBundle\Bridge\Helper\FiltersHelper;
use PrestaShopBundle\Bridge\Helper\HelperListBridge;
use PrestaShopBundle\Bridge\Helper\HelperListConfiguration;
use PrestaShopBundle\Bridge\Helper\ResetFiltersHelper;

/**
 * Contains the principal methods you need to horizontally migrate a controller which has a list.
 */
trait FrameworkBridgeControllerListTrait
{
    public function buildListConfiguration(
        string $identifierKey,
        string $positionIdentifierKey,
        string $defaultOrderBy,
        bool $autoJoinLangTable = true,
        bool $deleted = false,
        bool $explicitSelect = false,
        bool $useFoundRows = true
    ): HelperListConfiguration {
        $controllerConfiguration = $this->getControllerConfiguration();

        return $this->get('prestashop.bridge.helper.helper_list_configuration_factory')->create(
            $controllerConfiguration,
            $identifierKey,
            $positionIdentifierKey,
            $defaultOrderBy,
            $autoJoinLangTable,
            $deleted,
            $explicitSelect,
            $useFoundRows
        );
    }

    /**
     * @return ResetFiltersHelper
     */
    public function getResetFiltersHelper(): ResetFiltersHelper
    {
        return $this->get('prestashop.bridge.helper.reset_filters_helper');
    }

    /**
     * @return FiltersHelper
     */
    public function getFiltersHelper(): FiltersHelper
    {
        return $this->get('prestashop.bridge.helper.filters_helper');
    }

    /**
     * @return HelperListBridge
     */
    public function getHelperListBridge(): HelperListBridge
    {
        return $this->get('prestashop.bridge.helper.helper_list_bridge');
    }

    /**
     * This method add action specific for the list.
     *
     * @param ActionInterface $action
     * @param HelperListConfiguration $helperListConfiguration
     *
     * @return void
     */
    public function addActionList(ActionInterface $action, HelperListConfiguration $helperListConfiguration): void
    {
        if ($action instanceof ListBulkAction) {
            $helperListConfiguration->bulkActions[$action->getLabel()] = $action->getConfig();

            return;
        }

        if ($action instanceof ListRowAction) {
            $helperListConfiguration->actions[] = $action->getLabel();

            return;
        }

        if ($action instanceof ListHeaderToolbarAction) {
            $helperListConfiguration->toolbarButton[$action->getLabel()] = $action->getConfig();

            return;
        }

        throw new NotAllowedActionTypeForListException(sprintf('This action %s doesn\'t exist', get_class($action)));
    }

    /**
     * This methods allow you to add field to your list.
     *
     * @param FieldInterface $field
     *
     * @return void
     */
    public function addListField(FieldInterface $field, HelperListConfiguration $helperListConfiguration): void
    {
        $helperListConfiguration->fieldsList[$field->getLabel()] = $field->getConfig();
    }
}
