/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import Grid from '@components/grid/grid';
import ReloadListActionExtension from '@components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension from '@components/grid/extension/export-to-sql-manager-extension';
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';
import FormSubmitButton from '@components/form-submit-button';
import SortingExtension from '@components/grid/extension/sorting-extension';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkExtension from '@components/grid/extension/submit-bulk-action-extension';
import SubmitGridExtension from '@components/grid/extension/submit-grid-action-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import ColumnTogglingExtension from '@components/grid/extension/column-toggling-extension';
import FiltersSubmitButtonEnablerExtension
  from '@components/grid/extension/filters-submit-button-enabler-extension';

const {$} = window;

$(() => {
  const employeeSessionGrid = new Grid('security_session_employee');

  employeeSessionGrid.addExtension(new ReloadListActionExtension());
  employeeSessionGrid.addExtension(new ExportToSqlManagerExtension());
  employeeSessionGrid.addExtension(new FiltersResetExtension());
  employeeSessionGrid.addExtension(new SortingExtension());
  employeeSessionGrid.addExtension(new BulkActionCheckboxExtension());
  employeeSessionGrid.addExtension(new SubmitBulkExtension());
  employeeSessionGrid.addExtension(new SubmitGridExtension());
  employeeSessionGrid.addExtension(new SubmitRowActionExtension());
  employeeSessionGrid.addExtension(new LinkRowActionExtension());
  employeeSessionGrid.addExtension(new ColumnTogglingExtension());
  employeeSessionGrid.addExtension(new FiltersSubmitButtonEnablerExtension());

  const customersSessionsGrid = new Grid('security_session_customer');

  customersSessionsGrid.addExtension(new ReloadListActionExtension());
  customersSessionsGrid.addExtension(new ExportToSqlManagerExtension());
  customersSessionsGrid.addExtension(new FiltersResetExtension());
  customersSessionsGrid.addExtension(new SortingExtension());
  customersSessionsGrid.addExtension(new BulkActionCheckboxExtension());
  customersSessionsGrid.addExtension(new SubmitBulkExtension());
  customersSessionsGrid.addExtension(new SubmitGridExtension());
  customersSessionsGrid.addExtension(new SubmitRowActionExtension());
  customersSessionsGrid.addExtension(new LinkRowActionExtension());
  customersSessionsGrid.addExtension(new ColumnTogglingExtension());
  customersSessionsGrid.addExtension(new FiltersSubmitButtonEnablerExtension());

  new FormSubmitButton();
});
