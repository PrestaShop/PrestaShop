/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

class StatePage {
  constructor() {
    const stateGrid = new window.prestashop.component.Grid('state');
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());
    stateGrid.addExtension(new window.prestashop.component.GridExtensions.ModalFormSubmitExtension());
  }
}

$(() => {
  new StatePage();
});
