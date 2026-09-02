/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  const taxRulesGroupGrid = new window.prestashop.component.Grid('tax_rules_group');

  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  taxRulesGroupGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());

  new FormSubmitButton();
});
