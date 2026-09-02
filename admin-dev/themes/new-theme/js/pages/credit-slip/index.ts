/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const creditSlipGrid = new window.prestashop.component.Grid('credit_slip');

  creditSlipGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  creditSlipGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  creditSlipGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  creditSlipGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  creditSlipGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  window.prestashop.component.initComponents(
    [
      'TranslatableInput',
    ],
  );
});
