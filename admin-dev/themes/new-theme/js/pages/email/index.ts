/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
