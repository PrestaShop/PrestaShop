/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

const {$} = window;

$(() => {
  const cspLogGrid = new window.prestashop.component.Grid('csp_log');

  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  cspLogGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
