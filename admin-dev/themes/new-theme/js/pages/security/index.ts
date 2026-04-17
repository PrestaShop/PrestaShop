/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';

const {$} = window;

$(() => {
  const employeeSessionGrid = new window.prestashop.component.Grid('security_session_employee');

  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  employeeSessionGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  const customerSessionsGrid = new window.prestashop.component.Grid('security_session_customer');

  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  customerSessionsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  new FormSubmitButton();
});
