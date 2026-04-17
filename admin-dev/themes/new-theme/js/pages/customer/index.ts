/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import FormSubmitButton from '@components/form-submit-button';
import LinkableItem from '@components/linkable-item';
import DeleteCustomersBulkActionExtension
  from '@components/grid/extension/action/bulk/customer/delete-customers-bulk-action-extension';
import DeleteCustomerRowActionExtension
  from '@components/grid/extension/action/row/customer/delete-customer-row-action-extension';
import ShowcaseCard from '@components/showcase-card/showcase-card';
import ShowcaseCardCloseExtension from '@components/showcase-card/extension/showcase-card-close-extension';
import CustomerFormMap from '@pages/customer/customer-form-map';

const {$} = window;

$(() => {
  const customerGrid = new window.prestashop.component.Grid('customer');

  customerGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  customerGrid.addExtension(new DeleteCustomersBulkActionExtension());
  customerGrid.addExtension(new DeleteCustomerRowActionExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  customerGrid.addExtension(new window.prestashop.component.GridExtensions.AsyncToggleColumnExtension());

  const customerDiscountsGrid = new window.prestashop.component.Grid('customer_discount');
  customerDiscountsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  customerDiscountsGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  const customerAddressesGrid = new window.prestashop.component.Grid('customer_address');
  customerAddressesGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  customerAddressesGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  customerAddressesGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  const customerOrdersGrid = new window.prestashop.component.Grid('customer_order');
  customerOrdersGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  customerOrdersGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  customerOrdersGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  const customerCartsGrid = new window.prestashop.component.Grid('customer_cart');
  customerCartsGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  customerCartsGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
  customerCartsGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());

  const customerBoughtProductsGrid = new window.prestashop.component.Grid('customer_bought_product');
  customerBoughtProductsGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());

  const customerViewedProductsGrid = new window.prestashop.component.Grid('customer_viewed_product');
  customerViewedProductsGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());

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
