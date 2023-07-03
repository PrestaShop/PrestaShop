import addAddressPage from '@pages/BO/customers/addresses/add';
import {ViewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';

// Import data
import AddressData from '@data/faker/address';

import type {Frame, Page} from 'playwright';
import {expect} from 'chai';

/**
 * Customer block, contains functions that can be used on view/edit customer block
 * @class
 * @extends ViewOrderBasePage
 */
class CustomerBlock extends ViewOrderBasePage {
  private readonly customerInfoBlock: string;

  private readonly ViewAllDetailsLink: string;

  private readonly customerEmailLink: string;

  private readonly validatedOrders: string;

  private readonly shippingAddressBlock: string;

  private readonly shippingAddressToolTipLink: string;

  private readonly editShippingAddressButton: string;

  private readonly selectAnotherShippingAddressButton: string;

  private readonly changeOrderAddressSelect: string;

  private readonly submitAnotherAddressButton: string;

  private readonly editAddressIframe: string;

  private readonly invoiceAddressBlock: string;

  private readonly invoiceAddressToolTipLink: string;

  private readonly editInvoiceAddressButton: string;

  private readonly selectAnotherInvoiceAddressButton: string;

  private readonly privateNoteDiv: string;

  private readonly privateNoteTextarea: string;

  private readonly addNewPrivateNoteLink: string;

  private readonly privateNoteSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on view/edit customer block
   */
  constructor() {
    super();

    // Customer block
    this.customerInfoBlock = '#customerInfo';
    this.ViewAllDetailsLink = '#viewFullDetails a';
    this.customerEmailLink = '#customerEmail a';
    this.validatedOrders = '#validatedOrders span.badge';
    this.shippingAddressBlock = '#addressShipping';
    this.shippingAddressToolTipLink = `${this.shippingAddressBlock} .tooltip-link`;
    this.editShippingAddressButton = '#js-delivery-address-edit-btn';
    this.selectAnotherShippingAddressButton = `${this.shippingAddressBlock} .js-update-customer-address-modal-btn`;
    this.changeOrderAddressSelect = '#change_order_address_new_address_id';
    this.submitAnotherAddressButton = '#change-address-submit-button';
    this.editAddressIframe = 'iframe.fancybox-iframe';
    this.invoiceAddressBlock = '#addressInvoice';
    this.invoiceAddressToolTipLink = `${this.invoiceAddressBlock} .tooltip-link`;
    this.editInvoiceAddressButton = '#js-invoice-address-edit-btn';
    this.selectAnotherInvoiceAddressButton = `${this.invoiceAddressBlock} .js-update-customer-address-modal-btn`;
    this.privateNoteDiv = '#privateNote';
    this.privateNoteTextarea = '#private_note_note';
    this.addNewPrivateNoteLink = '#privateNote a.js-private-note-toggle-btn';
    this.privateNoteSaveButton = `${this.privateNoteDiv} .js-private-note-btn`;
  }

  /*
  Methods
   */
  /**
   * Get customer information
   * @param page {Frame|Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerInfoBlock(page: Frame|Page): Promise<string> {
    return this.getTextContent(page, this.customerInfoBlock);
  }

  /**
   * Go to view full details page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToViewFullDetails(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.ViewAllDetailsLink);
  }

  /**
   * Get customer email
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getCustomerEmail(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.customerEmailLink, 'href');
  }

  /**
   * Get shipping address from customer card
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getShippingAddress(page: Page): Promise<string> {
    return this.getTextContent(page, this.shippingAddressBlock);
  }

  /**
   * Get invoice address from customer card
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getInvoiceAddress(page: Page): Promise<string> {
    return this.getTextContent(page, this.invoiceAddressBlock);
  }

  /**
   * Get validated orders number
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getValidatedOrdersNumber(page: Page): Promise<number> {
    return this.getNumberFromText(page, `${this.validatedOrders}.badge-dark`);
  }

  /**
   * Edit existing shipping address
   * @param page {Page} Browser tab
   * @param addressData {AddressData} Shipping address data to edit
   * @returns {Promise<string>}
   */
  async editExistingShippingAddress(page: Page, addressData: AddressData): Promise<string> {
    await this.waitForSelectorAndClick(page, this.shippingAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.editShippingAddressButton);

    await this.waitForVisibleSelector(page, this.editAddressIframe);

    const addressFrame: Frame|null = await page.frame({url: /sell\/addresses\/order/gmi});
    await expect(addressFrame).to.be.not.null;

    await addAddressPage.createEditAddress(addressFrame!, addressData, true, false);

    await this.waitForHiddenSelector(page, this.editAddressIframe);

    return this.getShippingAddress(page);
  }

  /**
   * Select another shipping address
   * @param page {Page} Browser tab
   * @param address {string} Shipping address to select
   * @returns {Promise<string>}
   */
  async selectAnotherShippingAddress(page: Page, address: string): Promise<string> {
    await this.waitForSelectorAndClick(page, this.shippingAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.selectAnotherShippingAddressButton);

    await this.selectByVisibleText(page, this.changeOrderAddressSelect, address);
    await this.waitForSelectorAndClick(page, this.submitAnotherAddressButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Edit existing shipping address
   * @param page {Page} Browser tab
   * @param addressData {AddressData} Invoice address data to edit
   * @returns {Promise<string>}
   */
  async editExistingInvoiceAddress(page: Page, addressData: AddressData): Promise<string> {
    await this.waitForSelectorAndClick(page, this.invoiceAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.editInvoiceAddressButton);

    await this.waitForVisibleSelector(page, this.editAddressIframe);

    const addressFrame: Frame|null = await page.frame({url: /sell\/addresses\/order/gmi});
    await expect(addressFrame).to.be.not.null;

    await addAddressPage.createEditAddress(addressFrame!, addressData, true, false);

    await this.waitForHiddenSelector(page, this.editAddressIframe);

    return this.getInvoiceAddress(page);
  }

  /**
   * Select another shipping address
   * @param page {Page} Browser tab
   * @param address {string} Invoice address to select
   * @returns {Promise<string>}
   */
  async selectAnotherInvoiceAddress(page: Page, address: string): Promise<string> {
    await this.waitForSelectorAndClick(page, this.invoiceAddressToolTipLink);
    await this.waitForSelectorAndClick(page, this.selectAnotherInvoiceAddressButton);

    await this.selectByVisibleText(page, this.changeOrderAddressSelect, address);
    await this.waitForSelectorAndClick(page, this.submitAnotherAddressButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Is private note textarea visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isPrivateNoteTextareaVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.privateNoteTextarea, 2000);
  }

  /**
   * Click on add new private note link
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async clickAddNewPrivateNote(page: Page): Promise<void> {
    await page.click(this.addNewPrivateNoteLink);
    await this.waitForVisibleSelector(page, this.privateNoteTextarea);
  }

  /**
   * Set private note
   * @param page {Page} Browser tab
   * @param note {string} Private note to set
   * @returns {Promise<string>}
   */
  async setPrivateNote(page: Page, note: string): Promise<string> {
    await this.setValue(page, this.privateNoteTextarea, note);
    await page.click(this.privateNoteSaveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get private note content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getPrivateNoteContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.privateNoteTextarea);
  }
}

export default new CustomerBlock();
