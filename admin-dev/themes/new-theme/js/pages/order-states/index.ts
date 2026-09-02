/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
