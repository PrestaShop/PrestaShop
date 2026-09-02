/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Responsible for actions in admin search listing page to list aliases.
 */
$(() => {
  const aliasGrid = new window.prestashop.component.Grid('alias');

  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  aliasGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
});
