/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const customerGroups = new window.prestashop.component.Grid('customer_groups');

  customerGroups.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  customerGroups.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
});
