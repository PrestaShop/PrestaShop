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
import SortingExtension from '@components/grid/extension/sorting-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import SubmitGridExtension from '@components/grid/extension/submit-grid-action-extension';
import SubmitBulkExtension from '@components/grid/extension/submit-bulk-action-extension';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import TranslatableInput from '@components/translatable-input';
import ChoiceTree from '@components/form/choice-tree';
import AsyncToggleColumnExtension from '@components/grid/extension/column/common/async-toggle-column-extension';

/**
 * Responsible for actions in Stores listing page.
 */
export default class StoresPage {
  constructor() {
    const storeGrid = new Grid('store');

    storeGrid.addExtension(new ReloadListActionExtension());
    storeGrid.addExtension(new ExportToSqlManagerExtension());
    storeGrid.addExtension(new FiltersResetExtension());
    storeGrid.addExtension(new SortingExtension());
    storeGrid.addExtension(new LinkRowActionExtension());
    storeGrid.addExtension(new SubmitGridExtension());
    storeGrid.addExtension(new SubmitBulkExtension());
    storeGrid.addExtension(new BulkActionCheckboxExtension());
    storeGrid.addExtension(new SubmitRowActionExtension());
    storeGrid.addExtension(new AsyncToggleColumnExtension());

    new TranslatableInput();

    new ChoiceTree('#store_shop_association').enableAutoCheckChildren();
  }
}
