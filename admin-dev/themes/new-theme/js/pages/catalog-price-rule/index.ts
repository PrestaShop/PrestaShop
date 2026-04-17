/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const priceRuleGrid = new window.prestashop.component.Grid('catalog_price_rule');

  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  priceRuleGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
