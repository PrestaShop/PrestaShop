require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

/**
 * Checkout page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class Checkout extends FOBasePage {
  /**
   * @constructs
   * Setting up texts and selectors to use on checkout page
   */
  constructor() {
    super();

    // Selectors
    this.checkoutPageBody = 'body#checkout';
    this.paymentStepSection = '#checkout-payment-step';
    this.paymentOptionInput = name => `${this.paymentStepSection} input[name='payment-option']`
      + `[data-module-name='${name}']`;
    this.conditionToApproveLabel = `${this.paymentStepSection} #conditions-to-approve label`;
    this.conditionToApproveCheckbox = '#conditions_to_approve\\[terms-and-conditions\\]';
    this.termsOfServiceLink = '#cta-terms-and-conditions-0';
    this.termsOfServiceModalDiv = '#modal div.js-modal-content';
    this.paymentConfirmationButton = `${this.paymentStepSection} #payment-confirmation button:not([disabled])`;
    this.shippingValueSpan = '#cart-subtotal-shipping span.value';
    this.noPaymentNeededElement = `${this.paymentStepSection} div.content > p.cart-payment-step-not-needed-info`;
    this.noPaymentNeededText = 'No payment needed for this order';

    // Personal information form
    this.personalInformationStepForm = '#checkout-personal-information-step';
    this.createAccountOptionalNotice = `${this.personalInformationStepForm} `
      + '#customer-form .form-informations .form-informations-title';
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
    this.addressStepUseSameAddressCheckbox = '#use_same_address';
    this.addressStepContinueButton = `${this.addressStepSection} button[name='confirm-addresses']`;

    // Shipping method step
    this.deliveryStepSection = '#checkout-delivery-step';
    this.deliveryOptionsRadios = 'input[id*=\'delivery_option_\']';
    this.deliveryOptionLabel = id => `${this.deliveryStepSection} label[for='delivery_option_${id}']`;
    this.deliveryOptionNameSpan = id => `${this.deliveryOptionLabel(id)} span.carrier-name`;
    this.deliveryOptionAllNamesSpan = '#js-delivery .delivery-option .carriere-name-container span.carrier-name';
    this.deliveryOptionAllPricesSpan = '#js-delivery .delivery-option span.carrier-price';
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
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isCheckoutPage(page) {
    return this.elementVisible(page, this.checkoutPageBody, 1000);
  }

  /**
   * Check if step is complete
   * @param page {Page} Browser tab
   * @param stepSelector {string} String of the step to check
   * @returns {Promise<boolean>}
   */
  async isStepCompleted(page, stepSelector) {
    return this.elementVisible(page, `${stepSelector}.-complete`, 1000);
  }

  /**
   * Go to Delivery Step and check that Address step is complete
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async goToDeliveryStep(page) {
    await this.clickAndWaitForNavigation(page, this.addressStepContinueButton);
    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Choose shipping method and add a comment
   * @param page {Page} Browser tab
   * @param shippingMethod {number} Position of the shipping method
   * @param comment {string} Comment to add after selecting a shipping method
   * @returns {Promise<boolean>}
   */
  async chooseShippingMethodAndAddComment(page, shippingMethod, comment = '') {
    await this.waitForSelectorAndClick(page, this.deliveryOptionLabel(shippingMethod));
    await this.setValue(page, this.deliveryMessage, comment);
    return this.goToPaymentStep(page);
  }

  /**
   * Is shipping method exist
   * @param page {Page} Browser tab
   * @param shippingMethod {number} Position of the shipping method
   * @returns {Promise<boolean>}
   */
  isShippingMethodVisible(page, shippingMethod) {
    return this.elementVisible(page, this.deliveryOptionLabel(shippingMethod), 2000);
  }

  /**
   * Is confirm button visible and enabled
   * @param page
   * @returns {Promise<boolean>}
   */
  isPaymentConfirmationButtonVisibleAndEnabled(page) {
    // small side effect note, the selector is the one that checks for disabled
    return this.elementVisible(page, this.paymentConfirmationButton, 1000);
  }

  /**
   * Get No payment needed block content
   * @param page
   * @returns {string}
   */
  getNoPaymentNeededBlockContent(page) {
    return this.getTextContent(page, this.noPaymentNeededElement);
  }

  /**
   * Get selected shipping method name
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getSelectedShippingMethod(page) {
    // Get checkbox radios
    const optionsRadiosElement = await page.$$(this.deliveryOptionsRadios);
    let selectedOptionId = 0;

    // Get id of selected option
    for (let position = 1; position <= optionsRadiosElement.length; position++) {
      if (await (await optionsRadiosElement[position - 1].getProperty('checked')).jsonValue()) {
        selectedOptionId = position;
        break;
      }
    }

    // Return text of the selected option
    if (selectedOptionId !== 0) {
      return this.getTextContent(page, this.deliveryOptionNameSpan(selectedOptionId));
    }
    throw new Error('No selected option was found');
  }

  /**
   * Get all carriers prices
   * @param page {Page} Browser tab
   * @returns {Promise<[]>}
   */
  async getAllCarriersPrices(page) {
    return page.$$eval(this.deliveryOptionAllPricesSpan, all => all.map(el => el.textContent));
  }

  /**
   * Get shipping value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getShippingCost(page) {
    return this.getTextContent(page, this.shippingValueSpan);
  }

  /**
   * Get all carriers names
   * @param page {Page} Browser tab
   * @returns {Promise<[]>}
   */
  async getAllCarriersNames(page) {
    return page.$$eval(this.deliveryOptionAllNamesSpan, all => all.map(el => el.textContent));
  }

  /**
   * Go to Payment Step and check that delivery step is complete
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async goToPaymentStep(page) {
    await this.clickAndWaitForNavigation(page, this.deliveryStepContinueButton);
    return this.isStepCompleted(page, this.deliveryStepSection);
  }

  /**
   * Choose payment method and validate Order
   * @param page {Page} Browser tab
   * @param paymentModuleName {string} The chosen payment method
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
   * Order when no payment is needed
   * @param page
   * @returns {Promise<void>}
   */
  async orderWithoutPaymentMethod(page) {
    // Click on terms of services checkbox if visible
    if (await this.elementVisible(page, this.conditionToApproveLabel, 500)) {
      await Promise.all([
        this.waitForVisibleSelector(page, this.paymentConfirmationButton),
        page.click(this.conditionToApproveLabel),
      ]);
    }

    // Validate the order
    await this.clickAndWaitForNavigation(page, this.paymentConfirmationButton);
  }

  /**
   * Check payment method existence
   * @param page {Page} Browser tab
   * @param paymentModuleName {string} The payment module name
   * @returns {Promise<boolean>}
   */
  isPaymentMethodExist(page, paymentModuleName) {
    return this.elementVisible(page, this.paymentOptionInput(paymentModuleName), 2000);
  }

  /**
   * Click on sign in
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnSignIn(page) {
    await page.click(this.signInLink);
  }

  /**
   * Login in FO
   * @param page {Page} Browser tab
   * @param customer {object} Customer's information (email and password)
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
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isCreateAnAccountNoticeVisible(page) {
    return this.elementVisible(page, this.createAccountOptionalNotice, 1000);
  }

  /**
   * Is password input required
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isPasswordRequired(page) {
    return this.elementVisible(page, `${this.checkoutGuestPasswordInput}:required`, 1000);
  }

  /**
   * Check if checkbox of condition to approve is visible
   * @param page {Page} Browser tab
   * @returns {boolean}
   */
  isConditionToApproveCheckboxVisible(page) {
    return this.elementVisible(page, this.conditionToApproveCheckbox, 1000);
  }

  /**
   * Get terms of service page title
   * @param page {Page} Browser tab
   * @returns {Promise<text>}
   */
  async getTermsOfServicePageTitle(page) {
    await page.click(this.termsOfServiceLink);
    return this.getTextContent(page, this.termsOfServiceModalDiv);
  }

  /**
   * Check if gift checkbox is visible
   * @param page {Page} Browser tab
   * @return {boolean}
   */
  isGiftCheckboxVisible(page) {
    return this.elementVisible(page, this.giftCheckbox, 1000);
  }

  /**
   * Check if recyclable checkbox is visible
   * @param page {Page} Browser tab
   * @return {boolean}
   */
  isRecyclableCheckboxVisible(page) {
    return this.elementVisible(page, this.recycableGiftCheckbox, 1000);
  }

  /**
   * Get gift price from cart summary
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getGiftPrice(page) {
    await this.changeCheckboxValue(page, this.giftCheckbox, true);
    return this.getTextContent(page, this.cartSubtotalGiftWrappingValueSpan);
  }

  /**
   * Fill address form, used for delivery and invoice addresses
   * @param page {Page} Browser tab
   * @param address {object} Address's information to fill form with
   * @returns {Promise<void>}
   */
  async fillAddressForm(page, address) {
    await this.setValue(page, this.addressStepCompanyInput, address.company);
    await this.setValue(page, this.addressStepAddress1Input, address.address);
    await this.setValue(page, this.addressStepPostCodeInput, address.postalCode);
    await this.setValue(page, this.addressStepCityInput, address.city);
    await page.type(this.addressStepPhoneInput, address.phone, {delay: 50});
    await this.setValue(page, this.addressStepPhoneInput, address.phone);
  }

  /**
   * Set address step
   * @param page {Page} Browser tab
   * @param deliveryAddress {object} Address's information to add (for delivery)
   * @param invoiceAddress {object} Address's information to add (for invoice
   * @returns {Promise<boolean>}
   */
  async setAddress(page, deliveryAddress, invoiceAddress = null) {
    // Set delivery address
    await this.fillAddressForm(page, deliveryAddress);

    // Set invoice address if not null
    if (invoiceAddress !== null) {
      await page.uncheck(this.addressStepUseSameAddressCheckbox);
      await page.click(this.addressStepContinueButton);
      await this.fillAddressForm(page, invoiceAddress);
    } else {
      await page.check(this.addressStepUseSameAddressCheckbox);
    }

    await page.click(this.addressStepContinueButton);
    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Fill personal information form and click on continue
   * @param page {Page} Browser tab
   * @param customerData {object} Guest Customer's information to fill on form
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

    return this.isStepCompleted(page, this.personalInformationStepForm);
  }
}

module.exports = new Checkout();
