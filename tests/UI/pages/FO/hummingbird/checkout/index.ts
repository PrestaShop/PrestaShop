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

    // Shipping method selectors
    this.deliveryStepSection = 'li[data-step="checkout-delivery-step"]';
    this.deliveryAddressPosition = (position) => `#delivery-addresses div:nth-child(${position}) article`;
  }
}

export default new Checkout();
