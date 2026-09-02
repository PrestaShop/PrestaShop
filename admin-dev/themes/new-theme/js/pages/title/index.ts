/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const title = new window.prestashop.component.Grid('title');

  title.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  title.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
});
