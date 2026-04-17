/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import PermissionsRowSelector from './permissions-row-selector';

const {$} = window;

$(() => {
  const webserviceGrid = new window.prestashop.component.Grid('webservice_key');

  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.ColumnTogglingExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  webserviceGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  // needed for shop association input in form
  new window.prestashop.component.ChoiceTree('#webservice_key_shop_association').enableAutoCheckChildren();
  window.prestashop.component.initComponents(['MultipleChoiceTable', 'GeneratableInput']);
  // needed for key input in form
  window.prestashop.instance.generatableInput.attachOn('.js-generator-btn');

  new PermissionsRowSelector();

  window.prestashop.component.initComponents(
    [
      'MultistoreConfigField',
    ],
  );
});
