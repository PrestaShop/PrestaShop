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

$(() => {
  const orderStatesGrid = new window.prestashop.component.Grid('order_states');

  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  orderStatesGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());

  const orderReturnStatusesGrid = new window.prestashop.component.Grid('order_return_states');

  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  orderReturnStatusesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
});
