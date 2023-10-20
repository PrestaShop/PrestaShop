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
import CustomerFormMap from '@pages/customer/customer-form-map';

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

  // Scroll to the block
  scrollToBlock();

  // Required fields : Display alert for optin checkbox
  $(CustomerFormMap.requiredFieldsFormCheckboxOptin).on('click', () => handleRequiredFieldsFormCheckboxOptin());

  function scrollToBlock(): void {
    const documentURL = new URL(document.URL);
    const documentHash = documentURL.hash.slice(1);

    if (documentHash === '') {
      return;
    }

    const element = document.getElementById(documentHash);

    if (!element) {
      return;
    }

    // Fetch its position
    let positionTop = 0;

    if (element.offsetParent) {
      let elementParent: HTMLElement|null = element;
      do {
        positionTop += elementParent.offsetTop;
        elementParent = elementParent.offsetParent ? <HTMLElement> (elementParent.offsetParent) : null;
      } while (elementParent !== null);
    }

    // Remove the header height
    positionTop -= document.querySelector('#header_infos')?.getBoundingClientRect()?.height ?? 0;
    // Remove the title bar height
    positionTop -= document.querySelector('.header-toolbar')?.getBoundingClientRect()?.height ?? 0;
    // Remove the  height of the header of the card
    positionTop -= document.querySelector('.card-header')?.getBoundingClientRect()?.height ?? 0;
    // Remove the margin-bottom of the card
    positionTop -= 10;

    // Scroll to the block
    window.scroll(0, positionTop);
  }

  function handleRequiredFieldsFormCheckboxOptin(): void {
    $(CustomerFormMap.requiredFieldsFormAlertOptin).toggleClass(
      'd-none',
      !$(CustomerFormMap.requiredFieldsFormCheckboxOptin).is(':checked'),
    );
  }
});
