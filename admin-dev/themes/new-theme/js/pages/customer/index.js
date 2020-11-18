/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
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
import LinkableItem from '../../components/linkable-item';
import ChoiceTable from '../../components/choice-table';
import ColumnTogglingExtension from '../../components/grid/extension/column-toggling-extension';
import DeleteCustomersBulkActionExtension
  from '../../components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension';
import DeleteCustomerRowActionExtension
  from '../../components/grid/extension/action/row/customer/delete-customer-row-action-extension';
import ShowcaseCard from '../../components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '../../components/showcase-card/extension/showcase-card-close-extension';

const $ = window.$;

$(() => {
  const customerGrid = new Grid('customer');

  customerGrid.addExtension(new ReloadListActionExtension());
  customerGrid.addExtension(new ExportToSqlManagerExtension());
  customerGrid.addExtension(new FiltersResetExtension());
  customerGrid.addExtension(new SortingExtension());
  customerGrid.addExtension(new BulkActionCheckboxExtension());
  customerGrid.addExtension(new SubmitBulkExtension());
  customerGrid.addExtension(new SubmitGridExtension());
  customerGrid.addExtension(new LinkRowActionExtension());
  customerGrid.addExtension(new ColumnTogglingExtension());
  customerGrid.addExtension(new DeleteCustomersBulkActionExtension());
  customerGrid.addExtension(new DeleteCustomerRowActionExtension());

  const showcaseCard = new ShowcaseCard('customersShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());

  // needed for "Group access" input in Add/Edit customer forms
  new ChoiceTable();

  // in customer view page
  // there are a lot of tables
  // where you click any row
  // and it redirects user to related page
  new LinkableItem();
});
