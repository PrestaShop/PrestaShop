/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const countryGrid = new window.prestashop.component.Grid('country');

  countryGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  countryGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
});
