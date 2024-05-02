import type {ProductOrderConfirmation} from '@data/types/product';
import {OrderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import type {Page} from 'playwright';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class OrderConfirmation extends OrderConfirmationPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    // Selectors
    this.orderConfirmationCardSection = '#content-wrapper .alert';
    this.orderConfirmationCardTitleH3 = `${this.orderConfirmationCardSection} h1.alert-heading`;
    this.orderDetailsTable = 'div.order-confirmation__details ul.order-details';
    this.orderReferenceValue = `${this.orderDetailsTable} li:nth-child(1)`;
    this.customerSupportLink = 'div.card .card-footer a.alert-link';
    this.subTotalRow = 'div.order-confirmation__subtotals div:nth-child(1) div.text-end';
    this.shippingRow = 'div.order-confirmation__subtotals div:nth-child(2) div.text-end';
    this.totalRow = 'div.order-confirmation__totals div:nth-child(1) div.text-end';
    this.paymentMethodRow = `${this.orderDetailsTable} li:nth-child(2)`;
    this.shippingMethodRow = `${this.orderDetailsTable} li:nth-child(3)`;
    this.paymentInformationBody = '#content-wrapper div:nth-child(2) div.card-body';
    this.orderDetails = 'div.order-confirmation__details ul.order-details';
    this.productRow = 'div.order-confirmation__items div.item';
    this.customizationButton = '#content-wrapper div.order-confirmation__details div.order-confirmation__items button';
    this.customizationModal = 'div[id*="product-customization-modal"]';
    this.customizationModalBody = `${this.customizationModal} div.modal-body`;
    this.customizationModalCloseButton = `${this.customizationModal} div.modal-header button`;
    this.productRowNth = (row: number) => `${this.productRow}:nth-child(${row})`;
    this.productRowImage = (row: number) => `${this.productRowNth(row)} div.item__image img`;
    this.productRowDetails = (row: number) => `${this.productRowNth(row)} div.item__details`;
    this.productRowPrices = (row: number) => `${this.productRowNth(row)} div.item__prices`;

    // Popular products section
    this.productsBlock = '#content-wrapper section.featured-products';
    this.productsBlockTitle = `${this.productsBlock} h2`;
    this.productsBlockDiv = `${this.productsBlock} div.products div.card`;
    this.allProductsLink = `${this.productsBlock} div.featured-products-footer a`;
    this.productArticle = (number: number) => `${this.productsBlock} article:nth-child(${number})`;
    this.productImg = (number: number) => `${this.productArticle(number)} img`;
    this.productQuickViewLink = (number: number) => `${this.productArticle(number)} .product-miniature__quickview `
      + 'button';
  }

  /**
   * Get product details in row
   * @param page {Page} Browser tab
   * @param row {number} Row of product
   * @returns {Promise<ProductOrderConfirmation>}
   */
  async getProductDetailsInRow(page: Page, row: number): Promise<ProductOrderConfirmation> {
    return {
      image: await this.getAttributeContent(page, this.productRowImage(row), 'srcset'),
      details: await this.getTextContent(page, this.productRowDetails(row)),
      prices: await this.getTextContent(page, this.productRowPrices(row)),
    };
  }

  /**
   * Quick view product
   * @param page {Page} Browser tab
   * @param id {number} Product row in the list
   * @return {Promise<void>}
   */
  async quickViewProduct(page: Page, id: number): Promise<void> {
    await page.locator(this.productImg(id)).hover();
    await this.waitForVisibleSelector(page, this.productQuickViewLink(id));
    await page.locator(this.productQuickViewLink(id)).click();
  }
}

export default new OrderConfirmation();
