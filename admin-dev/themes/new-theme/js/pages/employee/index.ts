/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';

const {$} = window;

$(() => {
  const employeeGrid = new window.prestashop.component.Grid('employee');

  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  employeeGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  const showcaseCard = new ShowcaseCard('employeesShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());
});
