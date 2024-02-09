// Import FO pages
import {CheckoutPage} from '@pages/FO/classic/checkout';

/**
 * Cart page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Checkout extends CheckoutPage {
  /**
   * @constructs
   */
  constructor() {
    super('hummingbird');

    // Selectors
    this.stepFormSuccess = '.checkout__steps--success';

    // Personal information form
    this.personalInformationStepForm = 'li[data-step="checkout-personal-information-step"]';

    // Sign in selectors
    this.signInHyperLink = '#checkout-personal-information-step div.step__content '
      + '#contact-tab[data-bs-target="#checkout-login-form"]';
    this.personalInformationContinueButton = '#login-form button[data-link-action="sign-in"]';

    // Addresses step selectors
    this.addressStepSection = 'li[data-step="checkout-addresses-step"]';
    this.addressStepCountrySelect = 'select[name="id_country"]';
    this.stateInput = 'select[name="id_state"]';
    this.addressStepEditButton = `${this.addressStepSection} button`;

    // Shipping method selectors
    this.deliveryStepSection = 'li[data-step="checkout-delivery-step"]';
    this.deliveryOptionAllNamesSpan = '#js-delivery .delivery-options__container span.carrier-name';
    this.deliveryAddressPosition = (position) => `#delivery-addresses div:nth-child(${position}) article`;
    this.deliveryAddressEditButton = (addressID: number) => `#id_address_delivery-address-${addressID} a.address__edit`;
    this.deliveryOptions = '#js-delivery .delivery-options__container';
    this.deliveryOptionLabel = (id: number) => `input#delivery_option_${id}`;
    this.deliveryOption = (carrierID: number) => `${this.deliveryOptions} label[for="delivery_option_${carrierID}"]`;
    this.deliveryStepCarrierName = (carrierID: number) => `${this.deliveryOption(carrierID)} span.carrier-name`;
    this.deliveryStepCarrierDelay = (carrierID: number) => `${this.deliveryOption(carrierID)} div.row`
      + ' > span.delivery-option__center';
    this.deliveryStepCarrierPrice = (carrierID: number) => `${this.deliveryOption(carrierID)} div.row`
      + ' > span.delivery-option__right';

    // Checkout summary selectors
    this.shippingValueSpan = '#cart-subtotal-shipping span.cart-summary__value';
  }
}

export default new Checkout();
