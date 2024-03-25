// Import FO pages
import {CheckoutPage} from '@pages/FO/classic/checkout';
import type {Page} from 'playwright';
import {ProductDetailsBasic} from '@data/types/product';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Checkout extends CheckoutPage {
  private readonly productDetailsBody: (productRow: number) => string;

  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    // Selectors
    this.stepFormSuccess = '.checkout__steps--success';
    this.personalInformationEditLink = '#wrapper div.checkout__steps'
      + ' button[data-bs-target="#checkout-personal-information-step"]';

    // Personal information form
    this.personalInformationStepForm = 'li[data-step="checkout-personal-information-step"]';
    this.personalInformationCustomerIdentity = '#checkout-personal-information-step a[href*="identity"]';

    // Sign in selectors
    this.signInHyperLink = '#checkout-personal-information-step div.step__content '
      + '#contact-tab[data-bs-target="#checkout-login-form"]';
    this.forgetPasswordLink = '#login-form div.login__forgot-password a[href*=password-recovery]';
    this.personalInformationContinueButton = '#login-form button[data-link-action="sign-in"]';
    this.loginErrorMessage = `${this.checkoutLoginForm} div.alert-danger`;

    // Addresses step selectors
    this.addressStepSection = 'li[data-step="checkout-addresses-step"]';
    this.addressStepCountrySelect = 'select[name="id_country"]';
    this.stateInput = 'select[name="id_state"]';
    this.addressStepEditButton = `${this.addressStepSection} button`;
    this.addAddressButton = '.js-address-form a[href*="?newAddress=delivery"]';
    this.addInvoiceAddressButton = '.js-address-form a[href*="?newAddress=invoice"]';

    // Shipping method selectors
    this.deliveryStepSection = 'li[data-step="checkout-delivery-step"]';
    this.deliveryStepEditButton = `${this.deliveryStepSection} button`;
    this.deliveryOptionAllNamesSpan = '#js-delivery .delivery-options__container span.carrier-name';
    this.deliveryAddressPosition = (position) => `#delivery-addresses div:nth-child(${position}) article`;
    this.invoiceAddressPosition = (position) => `#invoice-addresses div:nth-child(${position}) article`;
    this.deliveryAddressEditButton = (addressID: number) => `#id_address_delivery-address-${addressID} a.address__edit`;
    this.deliveryAddressDeleteButton = (addressID: number) => `#id_address_delivery-address-${addressID} a.address__delete`;
    this.deliveryOptions = '#js-delivery .delivery-options__container';
    this.deliveryOptionLabel = (id: number) => `input#delivery_option_${id}`;
    this.deliveryOption = (carrierID: number) => `${this.deliveryOptions} label[for="delivery_option_${carrierID}"]`;
    this.deliveryStepCarrierName = (carrierID: number) => `${this.deliveryOption(carrierID)} span.carrier-name`;
    this.deliveryStepCarrierDelay = (carrierID: number) => `${this.deliveryOption(carrierID)} div.row`
      + ' > span.delivery-option__center';
    this.deliveryStepCarrierPrice = (carrierID: number) => `${this.deliveryOption(carrierID)} div.row`
      + ' > span.delivery-option__right';
    // Payment methods selectors

    // Checkout summary selectors
    this.shippingValueSpan = '#cart-subtotal-shipping span.cart-summary__value';
    this.itemsNumber = `${this.checkoutSummary} div.cart-summary__products.js-cart-summary-products p:nth-child(1)`;
    this.showDetailsLink = `${this.checkoutSummary} a.cart-summary__show.js-show-details`;
    this.productDetailsBody = (productRow: number) => `${this.productRowLink(productRow)} div.cart-summary__product__body`;
    this.productDetailsImage = (productRow: number) => `${this.productRowLink(productRow)} div.cart-summary__product__image`
      + ' a img';
    this.productDetailsPrice = (productRow: number) => `${this.productRowLink(productRow)} div.cart-summary__product__current `
      + 'span.price';
    this.productDetailsAttributes = (productRow: number) => `${this.productRowLink(productRow)} div.cart-summary__product__body `
      + 'div.product-line-info:nth-child(2)';
  }

  /**
   * Get product details
   * @param page {Page} Browser tab
   * @param productRow {number} Product row in details block
   * @returns {Promise<ProductDetailsBasic>
   */
  async getProductDetails(page: Page, productRow: number): Promise<ProductDetailsBasic> {
    return {
      image: await this.getAttributeContent(page, this.productDetailsImage(productRow), 'srcset') ?? '',
      name: await this.getTextContent(page, this.productDetailsName(productRow)),
      quantity: parseInt((await this.getTextContent(page, this.productDetailsBody(productRow))).split('Quantity x')[1], 10),
      price: await this.getPriceFromText(page, this.productDetailsPrice(productRow)),
    };
  }
}

export default new Checkout();
