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
import FormSubmitButton from '@components/form-submit-button';
import SortingExtension from '@components/grid/extension/sorting-extension';
import BulkActionCheckboxExtension from '@components/grid/extension/bulk-action-checkbox-extension';
import SubmitBulkExtension from '@components/grid/extension/submit-bulk-action-extension';
import SubmitGridExtension from '@components/grid/extension/submit-grid-action-extension';
import SubmitRowActionExtension from '@components/grid/extension/action/row/submit-row-action-extension';
import LinkRowActionExtension from '@components/grid/extension/link-row-action-extension';
import LinkableItem from '@components/linkable-item';
import DeleteCustomersBulkActionExtension
  from '@components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension';
import DeleteCustomerRowActionExtension
  from '@components/grid/extension/action/row/customer/delete-customer-row-action-extension';
import FiltersSubmitButtonEnablerExtension
  from '@components/grid/extension/filters-submit-button-enabler-extension';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';
import AsyncToggleColumnExtension from '@components/grid/extension/column/common/async-toggle-column-extension';

const {$} = window;

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
  customerGrid.addExtension(new DeleteCustomersBulkActionExtension());
  customerGrid.addExtension(new DeleteCustomerRowActionExtension());
  customerGrid.addExtension(new FiltersSubmitButtonEnablerExtension());
  customerGrid.addExtension(new AsyncToggleColumnExtension());

  const customerDiscountsGrid = new Grid('customer_discount');
  customerDiscountsGrid.addExtension(new SubmitRowActionExtension());
  customerDiscountsGrid.addExtension(new LinkRowActionExtension());

  const customerAddressesGrid = new Grid('customer_address');
  customerAddressesGrid.addExtension(new SubmitRowActionExtension());
  customerAddressesGrid.addExtension(new SortingExtension());
  customerAddressesGrid.addExtension(new LinkRowActionExtension());

  const showcaseCard = new ShowcaseCard('customersShowcaseCard');
  showcaseCard.addExtension(new ShowcaseCardCloseExtension());

  // in customer view page
  // there are a lot of tables
  // where you click any row
  // and it redirects user to related page
  new LinkableItem();

  new FormSubmitButton();
});
