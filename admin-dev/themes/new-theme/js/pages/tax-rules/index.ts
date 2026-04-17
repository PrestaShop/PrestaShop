/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  const taxRuleGrid = new window.prestashop.component.Grid('tax_rule');

  taxRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  taxRuleGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  taxRuleGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  taxRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  taxRuleGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());

  new FormSubmitButton();
});
