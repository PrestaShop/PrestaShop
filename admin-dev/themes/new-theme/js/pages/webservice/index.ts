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
