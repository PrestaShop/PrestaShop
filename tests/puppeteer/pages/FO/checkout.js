require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Checkout extends FOBasePage {
  constructor(page) {
    super(page);

    // Selectors
    this.checkoutPageBody = 'body#checkout';
    this.personalInformationStepSection = '#checkout-personal-information-step';
    this.deleveryStepSection = '#checkout-delivery-step';
    this.deleveryStepContinueButton = `${this.deleveryStepSection} button[name='confirmDeliveryOption']`;
    this.paymentStepSection = '#checkout-payment-step';
    this.paymentOptionInput = `${this.paymentStepSection} input[name='payment-option'][data-module-name='%NAME']`;
    this.conditionToApproveLabel = `${this.paymentStepSection} #conditions-to-approve label`;
    this.conditionToApproveCheckbox = '#conditions_to_approve\\[terms-and-conditions\\]';
    this.paymentConfirmationButton = `${this.paymentStepSection} #payment-confirmation button:not([disabled])`;
    // Personal information form
    this.personalInformationStepForm = '#checkout-personal-information-step';
    this.createAccountOptionalNotice = `${this.personalInformationStepForm} #customer-form section p`;
    this.signInLink = `${this.personalInformationStepForm} a[href="#checkout-login-form"]`;
    this.checkoutGuestForm = '#checkout-guest-form';
    this.checkoutGuestPasswordInput = `${this.checkoutGuestForm} input[name='password']`;
    // Checkout login form
    this.checkoutLoginForm = `${this.personalInformationStepForm} #checkout-login-form`;
    this.emailInput = `${this.checkoutLoginForm} input[name='email']`;
    this.passwordInput = `${this.checkoutLoginForm} input[name='password']`;
    this.personalInformationContinueButton = `${this.checkoutLoginForm} #login-form footer button`;
    // Checkout address form
    this.addressStepSection = '#checkout-addresses-step';
    this.addressStepCompanyInput = `${this.addressStepSection} input[name='company']`;
    this.addressStepAddressInput = `${this.addressStepSection} input[name='address1']`;
    this.addressStepPostCodeInput = `${this.addressStepSection} input[name='postcode']`;
    this.addressStepCityInput = `${this.addressStepSection} input[name='city']`;
    this.addressStepCountrySelect = `${this.addressStepSection} select[name='id_country']`;
    this.addressStepPhoneInput = `${this.addressStepSection} input[name='phone']`;
    this.addressStepContinueButton = `${this.addressStepSection} button[name='confirm-addresses']`;
    // Gift selectors
    this.giftCheckbox = '#input_gift';
    this.recycableGiftCheckbox = '#input_recyclable';
    this.cartSubtotalGiftWrappingDiv = '#cart-subtotal-gift_wrapping';
    this.cartSubtotalGiftWrappingValueSpan = `${this.cartSubtotalGiftWrappingDiv} span.value`;
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
    await this.clickAndWaitForNavigation(this.addressStepContinueButton);
    return this.isStepCompleted(this.addressStepSection);
  }

  /**
   * Go to Payment Step and check that delivery step is complete
   * @return {Promise<boolean|true>}
   */
  async goToPaymentStep() {
    await this.clickAndWaitForNavigation(this.deleveryStepContinueButton);
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
      this.waitForVisibleSelector(this.paymentConfirmationButton),
      this.page.click(this.conditionToApproveLabel),
    ]);
    await this.clickAndWaitForNavigation(this.paymentConfirmationButton);
  }

  /**
   * Check payment method existence
   * @param paymentModuleName
   * @returns {Promise<boolean>}
   */
  isPaymentMethodExist(paymentModuleName) {
    return this.elementVisible(this.paymentOptionInput.replace('%NAME', paymentModuleName), 2000);
  }

  /**
   * Click on sign in
   * @return {Promise<void>}
   */
  async clickOnSignIn() {
    this.page.click(this.signInLink);
  }

  /**
   * Login in FO
   * @param customer
   * @return {Promise<void>}
   */
  async customerLogin(customer) {
    await this.waitForVisibleSelector(this.emailInput);
    await this.setValue(this.emailInput, customer.email);
    await this.setValue(this.passwordInput, customer.password);
    await this.clickAndWaitForNavigation(this.personalInformationContinueButton);
  }

  /**
   * Is create account notice visible
   * @returns {boolean}
   */
  isCreateAnAccountNoticeVisible() {
    return this.elementVisible(this.createAccountOptionalNotice, 1000);
  }

  /**
   * Is password input required
   * @returns {boolean}
   */
  isPasswordRequired() {
    return this.elementVisible(`${this.checkoutGuestPasswordInput}:required`, 1000);
  }

  /**
   * Check if checkbox of condition to approve is visible
   * @returns {boolean}
   */
  isConditionToApproveCheckboxVisible() {
    return this.elementVisible(this.conditionToApproveCheckbox, 1000);
  }

  /**
   * Check if gift checkbox is visible
   * @return {boolean}
   */
  isGiftCheckboxVisible() {
    return this.elementVisible(this.giftCheckbox, 1000);
  }

  /**
   * Check if recyclable checkbox is visible
   * @return {boolean}
   */
  isRecyclableCheckboxVisible() {
    return this.elementVisible(this.recycableGiftCheckbox, 1000);
  }

  /**
   * Get gift price from cart summary
   * @return {Promise<string>}
   */
  async getGiftPrice() {
    await this.changeCheckboxValue(this.giftCheckbox, true);
    return this.getTextContent(this.cartSubtotalGiftWrappingValueSpan);
  }

  /**
   * Set address
   * @param address
   * @returns {Promise<void>}
   */
  async setAddress(address) {
    await this.setValue(this.addressStepCompanyInput, address.company);
    await this.setValue(this.addressStepAddressInput, address.address);
    await this.setValue(this.addressStepPostCodeInput, address.postalCode);
    await this.setValue(this.addressStepCityInput, address.city);
    await this.selectByVisibleText(this.addressStepCountrySelect, address.country);
    await this.setValue(this.addressStepPhoneInput, address.phone);
    await this.page.click(this.addressStepContinueButton);
  }
};
