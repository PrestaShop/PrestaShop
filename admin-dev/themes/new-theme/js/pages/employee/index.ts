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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

import FiltersSubmitButtonEnablerExtension
  from '@components/grid/extension/filters-submit-button-enabler-extension';
import Grid from '@components/grid/grid';
import ReloadListActionExtension from '@components/grid/extension/reload-list-extension';
import ExportToSqlManagerExtension from '@components/grid/extension/export-to-sql-manager-extension';
import FiltersResetExtension from '@components/grid/extension/filters-reset-extension';
import SortingExtension from '@components/grid/extension/sorting-extension';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkActionExtension from '@components/grid/extension/submit-bulk-action-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import ColumnTogglingExtension from '@components/grid/extension/column-toggling-extension';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';

const {$} = window;

$(() => {
  const employeeGrid = new Grid('employee');

  employeeGrid.addExtension(new ReloadListActionExtension());
  employeeGrid.addExtension(new ExportToSqlManagerExtension());
  employeeGrid.addExtension(new FiltersResetExtension());
  employeeGrid.addExtension(new SortingExtension());
  employeeGrid.addExtension(new BulkActionCheckboxExtension());
  employeeGrid.addExtension(new SubmitBulkActionExtension());
  employeeGrid.addExtension(new SubmitRowActionExtension());
  employeeGrid.addExtension(new ColumnTogglingExtension());
  employeeGrid.addExtension(new FiltersSubmitButtonEnablerExtension());
  employeeGrid.addExtension(new LinkRowActionExtension());

  const showcaseCard = new ShowcaseCard('employeesShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());
});
