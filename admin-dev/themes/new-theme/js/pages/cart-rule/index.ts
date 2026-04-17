/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const cartRuleGrid = new window.prestashop.component.Grid('cart_rule');

  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  cartRuleGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
