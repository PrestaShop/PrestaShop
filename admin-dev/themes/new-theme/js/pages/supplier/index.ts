/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const supplierGrid = new window.prestashop.component.Grid('supplier');
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  supplierGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
});
