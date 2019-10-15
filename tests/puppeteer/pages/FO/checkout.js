require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Checkout extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors
    this.checkoutPageBody = 'body#checkout';
    this.personalInformationStepSection = '#checkout-personal-information-step';
    this.addressStepSection = '#checkout-addresses-step';
    this.addressStepContinueButton = `${this.addressStepSection} button[name='confirm-addresses']`;
    this.deleveryStepSection = '#checkout-delivery-step';
    this.deleveryStepContinueButton = `${this.deleveryStepSection} button[name='confirmDeliveryOption']`;
    this.paymentStepSection = '#checkout-payment-step';
    this.paymentOptionInput = `${this.paymentStepSection} input[name='payment-option'][data-module-name='%NAME']`;
    this.conditionToApproveLabel = `${this.paymentStepSection} #conditions-to-approve label`;
    this.paymentConfirmationButton = `${this.paymentStepSection} #payment-confirmation button:not([disabled])`;
  }

  /*
  Methods
   */

  /**
   * Check if we are in checkout Page
   * @return {Promise<boolean|true>}
   */
  async isCheckoutPage() {
    return this.elementVisible(this.checkoutPageBody, 1000);
  }

  /**
   * Check if step is complete
   * @param stepSelector, step to check is complete
   * @return {Promise<boolean|true>}
   */
  async isStepCompleted(stepSelector) {
    return this.elementVisible(`${stepSelector}.-complete`, 1000);
  }

  /**
   * Go to Delivery Step and check that Address step is complete
   * @return {Promise<boolean|true>}
   */
  async goToDeliveryStep() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.addressStepContinueButton),
    ]);
    return this.isStepCompleted(this.addressStepSection);
  }

  /**
   * Go to Payment Step and check that delivery step is complete
   * @return {Promise<boolean|true>}
   */
  async goToPaymentStep() {
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.deleveryStepContinueButton),
    ]);
    return this.isStepCompleted(this.deleveryStepSection);
  }

  /**
   * Choose payment method and validate Order
   * @param paymentModuleName, payment method chosen (ex : ps_wirepayment)
   * @return {Promise<void>}
   */
  async choosePaymentAndOrder(paymentModuleName) {
    await this.page.click(this.paymentOptionInput.replace('%NAME', paymentModuleName));
    await Promise.all([
      this.page.waitForSelector(this.paymentConfirmationButton, {visible: true}),
      this.page.click(this.conditionToApproveLabel),
    ]);
    await Promise.all([
      this.page.waitForNavigation({waitUntil: 'networkidle0'}),
      this.page.click(this.paymentConfirmationButton),
    ]);
  }
};
