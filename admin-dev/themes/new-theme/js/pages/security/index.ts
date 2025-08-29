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
