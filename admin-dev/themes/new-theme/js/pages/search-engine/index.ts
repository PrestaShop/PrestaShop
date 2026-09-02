/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const searchEngineGrid = new window.prestashop.component.Grid('search_engine');

  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  searchEngineGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
});
