/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  const backupGrid = new window.prestashop.component.Grid('backup');

  backupGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  backupGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  backupGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  backupGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  backupGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
});
