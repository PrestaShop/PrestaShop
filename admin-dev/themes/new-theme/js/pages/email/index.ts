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

import EmailSendingTest from '@pages/email/email-sending-test';
import SmtpConfigurationToggler from '@pages/email/smtp-configuration-toggler';
import DkimConfigurationToggler from '@pages/email/dkim-configuration-toggler';

const {$} = window;

$(() => {
  const emailLogsGrid = new window.prestashop.component.Grid('email_logs');

  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  emailLogsGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());

  new EmailSendingTest();
  new SmtpConfigurationToggler();
  new DkimConfigurationToggler();
});
