/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const attachmentGrid = new window.prestashop.component.Grid('attachment');

  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  attachmentGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
});
