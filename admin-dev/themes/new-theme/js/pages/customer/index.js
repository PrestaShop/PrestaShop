/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

import Grid from '../../components/grid/grid';
import ReloadListActionExtension from '../../components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension from '../../components/grid/extension/export-to-sql-manager-extension';
import FiltersResetExtension from '../../components/grid/extension/filters-reset-extension';
import SortingExtension from '../../components/grid/extension/sorting-extension';
import BulkActionCheckboxExtension from '../../components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkExtension from '../../components/grid/extension/submit-bulk-action-extension';
import SubmitGridExtension from '../../components/grid/extension/submit-grid-action-extension';
import LinkRowActionExtension from '../../components/grid/extension/link-row-action-extension';
import LinkableItem from "../../components/linkable-item";
import ChoiceTable from "../../components/choice-table";
import HelperCard from "../../components/helper-card";

const $ = window.$;

$(() => {
  // in customer view page
  // there are a lot of tables
  // where you click any row
  // and it redirects user to related page
  new LinkableItem();
  new ChoiceTable();

  const customerGrid = new Grid('customer');

  customerGrid.addExtension(new ReloadListActionExtension());
  customerGrid.addExtension(new ExportToSqlManagerExtension());
  customerGrid.addExtension(new FiltersResetExtension());
  customerGrid.addExtension(new SortingExtension());
  customerGrid.addExtension(new BulkActionCheckboxExtension());
  customerGrid.addExtension(new SubmitBulkExtension());
  customerGrid.addExtension(new SubmitGridExtension());
  customerGrid.addExtension(new LinkRowActionExtension());

  // needed for "Group access" input in Add/Edit customer forms
  new ChoiceTable();

  new new HelperCard();
});
