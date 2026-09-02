/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const orderMessageGrid = new window.prestashop.component.Grid('order_message');

  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  orderMessageGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
