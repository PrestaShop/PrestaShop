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

import Grid from '../../components/grid/grid';
import FiltersResetExtension from '../../components/grid/extension/filters-reset-extension';
import ReloadListActionExtension from '../../components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension from '../../components/grid/extension/export-to-sql-manager-extension';
import BulkActionCheckboxExtension from '../../components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkActionExtension from '../../components/grid/extension/submit-bulk-action-extension';
import SortingExtension from '../../components/grid/extension/sorting-extension';
import SubmitRowActionExtension from '../../components/grid/extension/action/row/submit-row-action-extension';
import ColumnTogglingExtension from '../../components/grid/extension/column-toggling-extension';
import PermissionsRowSelector from './permissions-row-selector';
import LinkRowActionExtension from '../../components/grid/extension/link-row-action-extension';

const {$} = window;

$(() => {
  const webserviceGrid = new Grid('webservice_key');

  webserviceGrid.addExtension(new ReloadListActionExtension());
  webserviceGrid.addExtension(new ExportToSqlManagerExtension());
  webserviceGrid.addExtension(new FiltersResetExtension());
  webserviceGrid.addExtension(new ColumnTogglingExtension());
  webserviceGrid.addExtension(new SortingExtension());
  webserviceGrid.addExtension(new SubmitBulkActionExtension());
  webserviceGrid.addExtension(new SubmitRowActionExtension());
  webserviceGrid.addExtension(new BulkActionCheckboxExtension());
  webserviceGrid.addExtension(new LinkRowActionExtension());

  // needed for shop association input in form
  new window.prestashop.component.ChoiceTree('#webservice_key_shop_association').enableAutoCheckChildren();
  window.prestashop.component.initComponents(['MultipleChoiceTable', 'GeneratableInput']);
  // needed for key input in form
  window.prestashop.instance.generatableInput.attachOn('.js-generator-btn');

  new PermissionsRowSelector();
});
