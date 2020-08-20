require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class Checkout extends FOBasePage {
  constructor() {
    super();

    // Selectors
    this.checkoutPageBody = 'body#checkout';
    this.paymentStepSection = '#checkout-payment-step';
    this.paymentOptionInput = name => `${this.paymentStepSection} input[name='payment-option']`
      + `[data-module-name='${name}']`;
    this.conditionToApproveLabel = `${this.paymentStepSection} #conditions-to-approve label`;
    this.conditionToApproveCheckbox = '#conditions_to_approve\\[terms-and-conditions\\]';
    this.paymentConfirmationButton = `${this.paymentStepSection} #payment-confirmation button:not([disabled])`;
    // Personal information form
    this.personalInformationStepForm = '#checkout-personal-information-step';
    this.createAccountOptionalNotice = `${this.personalInformationStepForm} #customer-form section p`;
    this.signInLink = `${this.personalInformationStepForm} a[href="#checkout-login-form"]`;
    this.checkoutGuestForm = '#checkout-guest-form';
    this.checkoutGuestGenderInput = pos => `${this.checkoutGuestForm} input[name='id_gender'][value='${pos}']`;
    this.checkoutGuestFirstnameInput = `${this.checkoutGuestForm} input[name='firstname']`;
    this.checkoutGuestLastnameInput = `${this.checkoutGuestForm} input[name='lastname']`;
    this.checkoutGuestEmailInput = `${this.checkoutGuestForm} input[name='email']`;
    this.checkoutGuestPasswordInput = `${this.checkoutGuestForm} input[name='password']`;
    this.checkoutGuestBirthdayInput = `${this.checkoutGuestForm} input[name='birthday']`;
    this.checkoutGuestOptinCheckbox = `${this.checkoutGuestForm} input[name='optin']`;
    this.checkoutGuestCustomerPrivacyCheckbox = `${this.checkoutGuestForm} input[name='customer_privacy']`;
    this.checkoutGuestNewsletterCheckbox = `${this.checkoutGuestForm} input[name='newsletter']`;
    this.checkoutGuestGdprCheckbox = `${this.checkoutGuestForm} input[name='psgdpr']`;
    this.checkoutGuestContinueButton = `${this.checkoutGuestForm} button[name='continue']`;
    // Checkout login form
    this.checkoutLoginForm = `${this.personalInformationStepForm} #checkout-login-form`;
    this.emailInput = `${this.checkoutLoginForm} input[name='email']`;
    this.passwordInput = `${this.checkoutLoginForm} input[name='password']`;
    this.personalInformationContinueButton = `${this.checkoutLoginForm} #login-form footer button`;
    // Checkout address form
    this.addressStepSection = '#checkout-addresses-step';
    this.addressStepCompanyInput = `${this.addressStepSection} input[name='company']`;
    this.addressStepAddress1Input = `${this.addressStepSection} input[name='address1']`;
    this.addressStepPostCodeInput = `${this.addressStepSection} input[name='postcode']`;
    this.addressStepCityInput = `${this.addressStepSection} input[name='city']`;
    this.addressStepPhoneInput = `${this.addressStepSection} input[name='phone']`;
    this.addressStepContinueButton = `${this.addressStepSection} button[name='confirm-addresses']`;
    // Shipping method step
    this.deliveryStepSection = '#checkout-delivery-step';
    this.deliveryOptionLabel = id => `${this.deliveryStepSection} label[for='delivery_option_${id}']`;
    this.deliveryMessage = '#delivery_message';
    this.deliveryStepContinueButton = `${this.deliveryStepSection} button[name='confirmDeliveryOption']`;
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
   * @param page
   * @return {Promise<boolean>}
   */
  async isCheckoutPage(page) {
    return this.elementVisible(page, this.checkoutPageBody, 1000);
  }

  /**
   * Check if step is complete
   * @param page
   * @param stepSelector, step to check is complete
   * @param stepSelector
   * @returns {Promise<boolean>}
   */
  async isStepCompleted(page, stepSelector) {
    return this.elementVisible(page, `${stepSelector}.-complete`, 1000);
  }

  /**
   * Go to Delivery Step and check that Address step is complete
   * @param page
   * @return {Promise<boolean>}
   */
  async goToDeliveryStep(page) {
    await this.clickAndWaitForNavigation(page, this.addressStepContinueButton);
    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Choose shipping method and add a comment
   * @param page
   * @param shippingMethod
   * @param comment
   * @returns {Promise<boolean>}
   */
  async chooseShippingMethodAndAddComment(page, shippingMethod, comment) {
    await this.waitForSelectorAndClick(page, this.deliveryOptionLabel(shippingMethod));
    await this.setValue(page, this.deliveryMessage, comment);
    return this.goToPaymentStep(page);
  }

  /**
   * Go to Payment Step and check that delivery step is complete
   * @param page
   * @return {Promise<boolean>}
   */
  async goToPaymentStep(page) {
    await this.clickAndWaitForNavigation(page, this.deliveryStepContinueButton);
    return this.isStepCompleted(page, this.deliveryStepSection);
  }

  /**
   * Choose payment method and validate Order
   * @param page
   * @param paymentModuleName, payment method chosen (ex : ps_wirepayment)
   * @return {Promise<void>}
   */
  async choosePaymentAndOrder(page, paymentModuleName) {
    await page.click(this.paymentOptionInput(paymentModuleName));
    await Promise.all([
      this.waitForVisibleSelector(page, this.paymentConfirmationButton),
      page.click(this.conditionToApproveLabel),
    ]);
    await this.clickAndWaitForNavigation(page, this.paymentConfirmationButton);
  }

  /**
   * Check payment method existence
   * @param page
   * @param paymentModuleName
   * @returns {Promise<boolean>}
   */
  isPaymentMethodExist(page, paymentModuleName) {
    return this.elementVisible(page, this.paymentOptionInput(paymentModuleName), 2000);
  }

  /**
   * Click on sign in
   * @param page
   * @return {Promise<void>}
   */
  async clickOnSignIn(page) {
    page.click(this.signInLink);
  }

  /**
   * Login in FO
   * @param page
   * @param customer
   * @return {Promise<boolean>}
   */
  async customerLogin(page, customer) {
    await this.waitForVisibleSelector(page, this.emailInput);
    await this.setValue(page, this.emailInput, customer.email);
    await this.setValue(page, this.passwordInput, customer.password);
    await this.clickAndWaitForNavigation(page, this.personalInformationContinueButton);
    return this.isStepCompleted(page, this.personalInformationStepForm);
  }

  /**
   * Is create account notice visible
   * @param page
   * @returns {boolean}
   */
  isCreateAnAccountNoticeVisible(page) {
    return this.elementVisible(page, this.createAccountOptionalNotice, 1000);
  }

  /**
   * Is password input required
   * @param page
   * @returns {boolean}
   */
  isPasswordRequired(page) {
    return this.elementVisible(page, `${this.checkoutGuestPasswordInput}:required`, 1000);
  }

  /**
   * Check if checkbox of condition to approve is visible
   * @param page
   * @returns {boolean}
   */
  isConditionToApproveCheckboxVisible(page) {
    return this.elementVisible(page, this.conditionToApproveCheckbox, 1000);
  }

  /**
   * Check if gift checkbox is visible
   * @param page
   * @return {boolean}
   */
  isGiftCheckboxVisible(page) {
    return this.elementVisible(page, this.giftCheckbox, 1000);
  }

  /**
   * Check if recyclable checkbox is visible
   * @param page
   * @return {boolean}
   */
  isRecyclableCheckboxVisible(page) {
    return this.elementVisible(page, this.recycableGiftCheckbox, 1000);
  }

  /**
   * Get gift price from cart summary
   * @param page
   * @return {Promise<string>}
   */
  async getGiftPrice(page) {
    await this.changeCheckboxValue(page, this.giftCheckbox, true);
    return this.getTextContent(page, this.cartSubtotalGiftWrappingValueSpan);
  }

  /**
   * Set address
   * @param page
   * @param address
   * @returns {Promise<boolean>}
   */
  async setAddress(page, address) {
    await this.setValue(page, this.addressStepCompanyInput, address.company);
    await this.setValue(page, this.addressStepAddress1Input, address.address);
    await this.setValue(page, this.addressStepPostCodeInput, address.postalCode);
    await this.setValue(page, this.addressStepCityInput, address.city);
    await page.type(this.addressStepPhoneInput, address.phone, {delay: 50});
    await this.setValue(page, this.addressStepPhoneInput, address.phone);
    await page.click(this.addressStepContinueButton);
    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Fill personal information form and click on continue
   * @param page
   * @param customerData
   * @return {Promise<boolean>}
   */
  async setGuestPersonalInformation(page, customerData) {
    await page.check(this.checkoutGuestGenderInput(customerData.socialTitle === 'Mr.' ? 1 : 2));

    await this.setValue(page, this.checkoutGuestFirstnameInput, customerData.firstName);
    await this.setValue(page, this.checkoutGuestLastnameInput, customerData.lastName);
    await this.setValue(page, this.checkoutGuestEmailInput, customerData.email);
    await this.setValue(page, this.checkoutGuestPasswordInput, customerData.password);

    // Fill birthday input
    await this.setValue(
      page,
      this.checkoutGuestBirthdayInput,
      `${customerData.monthOfBirth.padStart(2, '0')}/`
      + `${customerData.dayOfBirth.padStart(2, '0')}/`
      + `${customerData.yearOfBirth}`,
    );

    if (customerData.partnerOffers) {
      await page.check(this.checkoutGuestOptinCheckbox);
    }

    if (customerData.newsletter) {
      await page.check(this.checkoutGuestNewsletterCheckbox);
    }

    // Check customer privacy input if visible
    if (await this.elementVisible(page, this.checkoutGuestCustomerPrivacyCheckbox, 500)) {
      await page.check(this.checkoutGuestCustomerPrivacyCheckbox);
    }

    // Check gdpr input if visible
    if (await this.elementVisible(page, this.checkoutGuestGdprCheckbox, 500)) {
      await page.check(this.checkoutGuestGdprCheckbox);
    }

    // Click on continue
    await page.click(this.checkoutGuestContinueButton);
    return this.isStepCompleted(page, this.personalInformationStepForm, 2000);
  }
}

module.exports = new Checkout();
