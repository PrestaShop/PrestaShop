// Import pages
import FOBasePage from '@pages/FO/FObasePage';

// Import data
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import CarrierData from '@data/faker/carrier';

import type {Page} from 'playwright';
import {ProductDetailsBasic} from '@data/types/product';

/**
 * Checkout page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CheckoutPage extends FOBasePage {
  public readonly deleteAddressSuccessMessage: string;

  private readonly successAlert: string;

  private readonly checkoutPageBody: string;

  protected stepFormSuccess: string;

  public readonly messageIfYouSignOut: string;

  public readonly authenticationErrorMessage: string;

  private readonly paymentStepSection: string;

  private readonly paymentOptionInput: (name: string) => string;

  private readonly conditionToApproveLabel: string;

  private readonly conditionToApproveCheckbox: string;

  private readonly termsOfServiceLink: string;

  private readonly termsOfServiceModalDiv: string;

  private readonly paymentConfirmationButton: string;

  protected shippingValueSpan: string;

  private readonly blockPromoDiv: string;

  private readonly cartSummaryLine: (line: number) => string;

  private readonly cartRuleName: (line: number) => string;

  private readonly discountValue: (line: number) => string;

  private readonly noPaymentNeededElement: string;

  protected itemsNumber: string;

  protected showDetailsLink: string;

  private readonly productList: string;

  protected productRowLink: (productRow: number) => string;

  protected productDetailsImage: (productRow: number) => string;

  protected productDetailsName: (productRow: number) => string;

  protected productDetailsQuantity: (productRow: number) => string;

  protected productDetailsPrice: (productRow: number) => string;

  protected productDetailsAttributes: (productRow: number) => string;

  public readonly noPaymentNeededText: string;

  private readonly promoCodeArea: string;

  private readonly checkoutHavePromoInputArea: string;

  private readonly checkoutPromoCodeAddButton: string;

  public personalInformationStepForm: string;

  protected forgetPasswordLink: string;

  private readonly activeLink: string;

  private readonly checkoutSignInLink: string;

  private readonly checkoutGuestForm: string;

  private readonly checkoutGuestGenderInput: (pos: number) => string;

  private readonly checkoutGuestFirstnameInput: string;

  private readonly checkoutGuestLastnameInput: string;

  private readonly checkoutGuestEmailInput: string;

  private readonly checkoutGuestPasswordInput: string;

  private readonly checkoutGuestBirthdayInput: string;

  private readonly checkoutGuestOptinCheckbox: string;

  private readonly checkoutGuestCustomerPrivacyCheckbox: string;

  private readonly checkoutGuestNewsletterCheckbox: string;

  private readonly checkoutGuestGdprCheckbox: string;

  private readonly checkoutGuestContinueButton: string;

  protected signInHyperLink: string;

  protected checkoutSummary: string;

  private readonly checkoutPromoBlock: string;

  private readonly checkoutHavePromoCodeButton: string;

  private readonly checkoutRemoveDiscountLink: string;

  protected checkoutLoginForm: string;

  private readonly emailInput: string;

  private readonly passwordInput: string;

  protected personalInformationContinueButton: string;

  private readonly logoutMessage: string;

  private readonly personalInformationLogoutLink: string;

  protected personalInformationCustomerIdentity: string;

  protected personalInformationEditLink: string;

  protected loginErrorMessage: string;

  protected addressStepSection: string;

  private readonly addressStepContent: string;

  private readonly addressStepCreateAddressForm: string;

  private readonly addressStepAliasInput: string;

  private readonly addressStepCompanyInput: string;

  private readonly addressStepAddress1Input: string;

  private readonly addressStepPostCodeInput: string;

  private readonly addressStepCityInput: string;

  protected addressStepCountrySelect: string;

  private readonly addressStepPhoneInput: string;

  protected stateInput: string;

  private readonly addressStepUseSameAddressCheckbox: string;

  private readonly addressStepContinueButton: string;

  private readonly addressStepSubmitButton: string;

  protected addressStepEditButton: string;

  protected addAddressButton: string;

  protected addInvoiceAddressButton: string;

  private readonly differentInvoiceAddressLink: string;

  private readonly invoiceAddressesBlock: string;

  private readonly invoiceAddressSection: string;

  protected deliveryStepSection: string;

  protected deliveryStepEditButton: string;

  private readonly deliveryStepCarriersList: string;

  protected deliveryOptions: string;

  private readonly deliveryOptionsRadioButton: string;

  protected deliveryOptionLabel: (id: number) => string;

  private readonly deliveryOptionNameSpan: (id: number) => string;

  protected deliveryOptionAllNamesSpan: string;

  private readonly deliveryOptionAllPricesSpan: string;

  private readonly deliveryMessage: string;

  private readonly deliveryStepContinueButton: string;

  protected deliveryOption: (carrierID: number) => string;

  protected deliveryStepCarrierName: (carrierID: number) => string;

  protected deliveryStepCarrierDelay: (carrierID: number) => string;

  protected deliveryStepCarrierPrice: (carrierID: number) => string;

  private readonly deliveryAddressBlock: string;

  private readonly deliveryAddressSection: string;

  protected deliveryAddressPosition: (position: number) => string;

  protected invoiceAddressPosition: (position: number) => string;

  protected deliveryAddressEditButton: (addressID: number) => string;

  protected deliveryAddressDeleteButton: (addressID: number) => string;

  private readonly deliveryAddressRadioButton: (addressID: number) => string;

  private readonly invoiceAddressRadioButton: (addressID: number) => string;

  private readonly cartTotalATI: string;

  private readonly cartRuleAlertMessage: string;

  private readonly cartRuleAlertMessageText: string;

  private readonly giftCheckbox: string;

  private readonly giftMessageTextarea: string;

  private readonly recyclableGiftCheckbox: string;

  private readonly cartSubtotalGiftWrappingDiv: string;

  private readonly cartSubtotalGiftWrappingValueSpan: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on checkout page
   */
  constructor(theme: string = 'classic') {
    super(theme);
    this.cartRuleAlertMessageText = 'You cannot use this voucher with this carrier';
    this.deleteAddressSuccessMessage = 'Address successfully deleted.';
    this.noPaymentNeededText = 'No payment needed for this order';
    this.messageIfYouSignOut = 'If you sign out now, your cart will be emptied.';
    this.authenticationErrorMessage = 'Authentication failed.';

    // Selectors
    this.successAlert = '#notifications article.alert-success';
    this.checkoutPageBody = 'body#checkout';
    this.stepFormSuccess = '.-complete';

    // Personal information form
    this.personalInformationStepForm = '#checkout-personal-information-step';
    // Order as a guest selectors
    this.activeLink = `${this.personalInformationStepForm} .nav-link.active`;
    this.checkoutSignInLink = `${this.personalInformationStepForm} a[href="#checkout-login-form"]`;
    this.checkoutGuestForm = '#checkout-guest-form';
    this.checkoutGuestGenderInput = (pos) => `${this.checkoutGuestForm} input[name='id_gender'][value='${pos}']`;
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
    // Sign in selectors
    this.signInHyperLink = `${this.personalInformationStepForm} a[href="#checkout-login-form"]`;
    this.forgetPasswordLink = '#login-form div.forgot-password a[href*=password-recovery]';
    this.checkoutLoginForm = `${this.personalInformationStepForm} #checkout-login-form`;
    this.emailInput = `${this.checkoutLoginForm} input[name='email']`;
    this.passwordInput = `${this.checkoutLoginForm} input[name='password']`;
    this.personalInformationContinueButton = `${this.checkoutLoginForm} #login-form footer button`;
    this.logoutMessage = `${this.personalInformationStepForm} p:nth-child(3) small`;
    this.personalInformationLogoutLink = `${this.personalInformationStepForm} a[href*=mylogout]`;
    this.personalInformationCustomerIdentity = `${this.personalInformationStepForm} p.identity`;
    this.personalInformationEditLink = `${this.personalInformationStepForm} span.step-edit.text-muted`;
    this.loginErrorMessage = `${this.checkoutLoginForm} li.alert-danger`;

    // Addresses step selectors
    this.addressStepSection = '#checkout-addresses-step';
    this.addressStepContent = `${this.addressStepSection} div.content`;
    this.addressStepCreateAddressForm = `${this.addressStepSection} .js-address-form`;
    this.addressStepAliasInput = '#field-alias';
    this.addressStepCompanyInput = '#field-company';
    this.addressStepAddress1Input = '#field-address1';
    this.addressStepPostCodeInput = '#field-postcode';
    this.addressStepCityInput = '#field-city';
    this.addressStepCountrySelect = '#field-id_country';
    this.addressStepPhoneInput = '#field-phone';
    this.stateInput = '#field-id_state';
    this.addressStepUseSameAddressCheckbox = '#use_same_address';
    this.addressStepContinueButton = `${this.addressStepSection} button[name='confirm-addresses']`;
    this.addressStepSubmitButton = `${this.addressStepSection} button[type=submit]`;
    this.addressStepEditButton = `${this.addressStepSection} span.step-edit`;
    this.addAddressButton = '#checkout-addresses-step p.add-address a';
    this.addInvoiceAddressButton = '#checkout-addresses-step  p.add-address a[href*="invoice"]';
    this.differentInvoiceAddressLink = '#checkout-addresses-step form a[data-link-action="different-invoice-address"]';
    // Delivery address selectors
    this.deliveryAddressBlock = '#delivery-addresses';
    this.deliveryAddressSection = `${this.deliveryAddressBlock} article.js-address-item`;
    this.deliveryAddressEditButton = (addressID: number) => `#id_address_delivery-address-${addressID} a.edit-address`;
    this.deliveryAddressDeleteButton = (addressID: number) => `#id_address_delivery-address-${addressID} a.delete-address`;
    this.deliveryAddressRadioButton = (addressID: number) => `#id_address_delivery-address-${addressID} `
      + 'input[name="id_address_delivery"]';
    // Invoice address selectors
    this.invoiceAddressesBlock = '#invoice-addresses';
    this.invoiceAddressSection = `${this.invoiceAddressesBlock} article.js-address-item`;
    this.deliveryAddressPosition = (position: number) => `#delivery-addresses article:nth-child(${position})`;
    this.invoiceAddressPosition = (position: number) => `#invoice-addresses article:nth-child(${position})`;
    this.invoiceAddressRadioButton = (addressID: number) => `#id_address_invoice-address-${addressID}`
      + ' input[name="id_address_invoice"]';

    // Shipping method selectors
    this.deliveryStepSection = '#checkout-delivery-step';
    this.deliveryStepEditButton = `${this.deliveryStepSection} span.step-edit`;
    this.deliveryStepCarriersList = `${this.deliveryStepSection} .delivery-options-list`;
    this.deliveryOptions = '#js-delivery div.delivery-options';
    this.deliveryOptionsRadioButton = 'input[id*=\'delivery_option_\']';
    this.deliveryOptionLabel = (id: number) => `${this.deliveryStepSection} label[for='delivery_option_${id}']`;
    this.deliveryOptionNameSpan = (id: number) => `${this.deliveryOptionLabel(id)} span.carrier-name`;
    this.deliveryOptionAllNamesSpan = '#js-delivery .delivery-option .carriere-name-container span.carrier-name';
    this.deliveryOptionAllPricesSpan = '#js-delivery .delivery-option span.carrier-price';
    this.deliveryMessage = '#delivery_message';
    this.deliveryStepContinueButton = `${this.deliveryStepSection} button[name='confirmDeliveryOption']`;
    this.deliveryOption = (carrierID: number) => `${this.deliveryOptions} label[for=delivery_option_${carrierID}] span.carrier`;
    this.deliveryStepCarrierName = (carrierID: number) => `${this.deliveryOption(carrierID)}-name`;
    this.deliveryStepCarrierDelay = (carrierID: number) => `${this.deliveryOption(carrierID)}-delay`;
    this.deliveryStepCarrierPrice = (carrierID: number) => `${this.deliveryOption(carrierID)}-price`;

    // Payment step selectors
    this.paymentStepSection = '#checkout-payment-step';
    this.paymentOptionInput = (name: string) => `${this.paymentStepSection} input[name='payment-option']`
      + `[data-module-name='${name}']`;
    this.conditionToApproveLabel = `${this.paymentStepSection} #conditions-to-approve label`;
    this.conditionToApproveCheckbox = '#conditions_to_approve\\[terms-and-conditions\\]';
    this.termsOfServiceLink = '#cta-terms-and-conditions-0';
    this.termsOfServiceModalDiv = '#modal div.js-modal-content';
    this.paymentConfirmationButton = `${this.paymentStepSection} #payment-confirmation button:not([disabled])`;
    this.noPaymentNeededElement = `${this.paymentStepSection} div.content > p.cart-payment-step-not-needed-info`;

    // Checkout summary selectors
    this.checkoutSummary = '#js-checkout-summary';
    this.checkoutPromoBlock = `${this.checkoutSummary} div.block-promo`;
    this.checkoutHavePromoCodeButton = `${this.checkoutPromoBlock} p.promo-code-button a`;
    this.checkoutRemoveDiscountLink = `${this.checkoutPromoBlock} a[data-link-action='remove-voucher'] i`;
    this.cartTotalATI = '.cart-summary-totals span.value';
    this.cartRuleAlertMessage = '#promo-code div.alert-danger span.js-error-text';
    this.promoCodeArea = '#promo-code';
    this.checkoutHavePromoInputArea = `${this.promoCodeArea} input.promo-input`;
    this.checkoutPromoCodeAddButton = `${this.promoCodeArea} button.btn-primary`;
    this.shippingValueSpan = '#cart-subtotal-shipping span.value';
    this.blockPromoDiv = '.block-promo';
    this.cartSummaryLine = (line: number) => `${this.blockPromoDiv} li:nth-child(${line}).cart-summary-line`;
    this.cartRuleName = (line: number) => `${this.cartSummaryLine(line)} span.label`;
    this.discountValue = (line: number) => `${this.cartSummaryLine(line)} div span`;

    // Cart details selectors
    this.itemsNumber = `${this.checkoutSummary} div.cart-summary-products.js-cart-summary-products p:nth-child(1)`;
    this.showDetailsLink = `${this.checkoutSummary} div.cart-summary-products.js-cart-summary-products a.js-show-details`;
    this.productList = '#cart-summary-product-list';
    this.productRowLink = (productRow: number) => `${this.productList} ul li:nth-child(${productRow})`;
    this.productDetailsImage = (productRow: number) => `${this.productRowLink(productRow)} div.media-left a img`;
    this.productDetailsName = (productRow: number) => `${this.productRowLink(productRow)} div span.product-name`;
    this.productDetailsQuantity = (productRow: number) => `${this.productRowLink(productRow)} `
      + 'div.media-body span.product-quantity';
    this.productDetailsPrice = (productRow: number) => `${this.productRowLink(productRow)} div.media-body `
      + 'span.product-price.float-xs-right';
    this.productDetailsAttributes = (productRow: number) => `${this.productRowLink(productRow)} div.media-body `
      + 'div.product-line-info';

    // Gift selectors
    this.giftCheckbox = '#input_gift';
    this.giftMessageTextarea = '#gift_message';
    this.recyclableGiftCheckbox = '#input_recyclable';
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
  async isCheckoutPage(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.checkoutPageBody, 1000);
  }

  /**
   * Check if step is completed
   * @param page {Page} Browser tab
   * @param stepSelector {string} String of the step to check
   * @returns {Promise<boolean>}
   */
  async isStepCompleted(page: Page, stepSelector: string): Promise<boolean> {
    return this.elementVisible(page, `${stepSelector}${this.stepFormSuccess}`, 1000);
  }

  /**
   * Click on show details link
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async clickOnShowDetailsLink(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.showDetailsLink);

    return this.elementVisible(page, `${this.showDetailsLink}[aria-expanded=true]`, 1000);
  }

  /**
   * Get product details
   * @param page {Page} Browser tab
   * @param productRow {number} Product row in details block
   * @returns {Promise<ProductDetailsBasic>
   */
  async getProductDetails(page: Page, productRow: number): Promise<ProductDetailsBasic> {
    return {
      image: await this.getAttributeContent(page, this.productDetailsImage(productRow), 'src') ?? '',
      name: await this.getTextContent(page, this.productDetailsName(productRow)),
      quantity: await this.getNumberFromText(page, this.productDetailsQuantity(productRow)),
      price: await this.getPriceFromText(page, this.productDetailsPrice(productRow)),
    };
  }

  /**
   * Get product details
   * @param page {Page} Browser tab
   * @param productRow {number} Product row in details block
   * @returns {Promise<string}
   */
  async getProductAttributes(page: Page, productRow: number): Promise<string> {
    return this.getTextContent(page, this.productDetailsAttributes(productRow));
  }

  /**
   * Get product details
   * @param page {Page} Browser tab
   * @param productRow {number} Product row in details block
   * @returns {Promise<void}
   */
  async clickOnProductImage(page: Page, productRow: number): Promise<void> {
    return this.clickAndWaitForURL(page, this.productDetailsImage(productRow));
  }

  /**
   * Get product details
   * @param page {Page} Browser tab
   * @param productRow {number} Product row in details block
   * @returns {Promise<Page}
   */
  async clickOnProductName(page: Page, productRow: number): Promise<Page> {
    return this.openLinkWithTargetBlank(page, this.productDetailsName(productRow));
  }

  /**
   * Get items number
   * @param page {Page} Browser tab
   * @returns {Promise<string}
   */
  async getItemsNumber(page: Page): Promise<string> {
    return this.getTextContent(page, this.itemsNumber);
  }

  // Methods for personal information step
  /**
   * Click on sign in
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnSignIn(page: Page): Promise<void> {
    await page.locator(this.signInHyperLink).click();
  }

  /**
   * Logout customer
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async logOutCustomer(page: Page): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.personalInformationLogoutLink);

    return this.isStepCompleted(page, this.personalInformationStepForm);
  }

  /**
   * Go to password reminder page
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToPasswordReminderPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.forgetPasswordLink);
  }

  /**
   * Click on edit personal information step
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async clickOnEditPersonalInformationStep(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.personalInformationEditLink);
  }

  /**
   * Get customer identity
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getCustomerIdentity(page: Page): Promise<string> {
    return this.getTextContent(page, this.personalInformationCustomerIdentity);
  }

  /**
   * Get logout message
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getLogoutMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.logoutMessage);
  }

  /**
   * Login in FO
   * @param page {Page} Browser tab
   * @param customer {object} Customer's information (email and password)
   * @return {Promise<boolean>}
   */
  async customerLogin(page: Page, customer: any): Promise<boolean> {
    await this.waitForVisibleSelector(page, this.emailInput);
    await this.setValue(page, this.emailInput, customer.email);
    await this.setValue(page, this.passwordInput, customer.password);
    await this.clickAndWaitForLoadState(page, this.personalInformationContinueButton);

    return this.isStepCompleted(page, this.personalInformationStepForm);
  }

  /**
   * Get login error message
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getLoginError(page: Page): Promise<string> {
    return this.getTextContent(page, this.loginErrorMessage);
  }

  /**
   * Get active link from personal information block
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getActiveLinkFromPersonalInformationBlock(page: Page): Promise<string> {
    return this.getTextContent(page, this.activeLink);
  }

  /**
   * Is password input required
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isPasswordRequired(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.checkoutGuestPasswordInput}:required`, 1000);
  }

  /**
   * Fill personal information form and click on continue
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Guest Customer's information to fill on form
   * @return {Promise<boolean>}
   */
  async setGuestPersonalInformation(page: Page, customerData: CustomerData): Promise<boolean> {
    await this.setChecked(page, this.checkoutGuestGenderInput(customerData.socialTitle === 'Mr.' ? 1 : 2));

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
      await this.setChecked(page, this.checkoutGuestOptinCheckbox);
    }

    if (customerData.newsletter) {
      await this.setChecked(page, this.checkoutGuestNewsletterCheckbox);
    }

    // Check customer privacy input if visible
    if (await this.elementVisible(page, this.checkoutGuestCustomerPrivacyCheckbox, 500)) {
      await this.setChecked(page, this.checkoutGuestCustomerPrivacyCheckbox);
    }

    // Check gdpr input if visible
    if (await this.elementVisible(page, this.checkoutGuestGdprCheckbox, 500)) {
      await this.setChecked(page, this.checkoutGuestGdprCheckbox);
    }

    // Click on continue
    await page.locator(this.checkoutGuestContinueButton).click();

    return this.isStepCompleted(page, this.personalInformationStepForm);
  }

  // Methods for Addresses step

  /**
   * Get address ID
   * @param page {Page} Browser tab
   * @param row {number} The row of the address
   */
  async getDeliveryAddressID(page: Page, row: number = 1): Promise<number> {
    const addressSelectorValue = await this.getAttributeContent(page, this.deliveryAddressPosition(row), 'id');

    if (addressSelectorValue === '') {
      return 0;
    }
    const text: string = (/\d+/g.exec(addressSelectorValue) ?? '').toString();

    return parseInt(text, 10);
  }

  /**
   * Get invoice address ID
   * @param page  {Page} Browser tab
   * @param row {number} The row of the address
   */
  async getInvoiceAddressID(page: Page, row: number = 1): Promise<number> {
    const addressSelectorValue = await this.getAttributeContent(page, this.invoiceAddressPosition(row), 'id');

    if (addressSelectorValue === '') {
      return 0;
    }
    const text: string = (/\d+/g.exec(addressSelectorValue) ?? '').toString();

    return parseInt(text, 10);
  }

  /**
   * Click on edit address
   * @param page {Page} Browser tab
   * @param row {number} The row of the address
   */
  async clickOnEditAddress(page: Page, row: number = 1): Promise<void> {
    const addressID = await this.getDeliveryAddressID(page, row);
    await this.waitForSelectorAndClick(page, this.deliveryAddressEditButton(addressID));
  }

  /**
   * Delete address
   * @param page {Page} Browser tab
   * @param row {number} The row of the address
   */
  async deleteAddress(page: Page, row: number = 1): Promise<string> {
    const addressID = await this.getDeliveryAddressID(page, row);
    await this.waitForSelectorAndClick(page, this.deliveryAddressDeleteButton(addressID));

    return this.getTextContent(page, this.successAlert);
  }

  /**
   * Select delivery address
   * @param page {Page} Browser tab
   * @param row {number} The row of the address
   */
  async selectDeliveryAddress(page: Page, row: number = 1): Promise<void> {
    const addressID = await this.getDeliveryAddressID(page, row);
    await this.setChecked(page, this.deliveryAddressRadioButton(addressID), true);
  }

  /**
   * Select invoice address
   * @param page {Page} Browser tab
   * @param row {number} The row of the address
   */
  async selectInvoiceAddress(page: Page, row: number = 1): Promise<void> {
    const addressID = await this.getInvoiceAddressID(page, row);
    await this.setChecked(page, this.invoiceAddressRadioButton(addressID), true);
  }

  /**
   * Click on continue button from address step
   * @param page {Page} Browser tab
   */
  async clickOnContinueButtonFromAddressStep(page: Page): Promise<boolean> {
    await page.locator(this.addressStepContinueButton).click();

    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Is address form visible
   * @param page {Page} Browser tab
   */
  isAddressFormVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.addressStepCreateAddressForm, 2000);
  }

  /**
   * Fill address form, used for delivery and invoice addresses
   * @param page {Page} Browser tab
   * @param address {AddressData} Address's information to fill form with
   * @returns {Promise<void>}
   */
  async fillAddressForm(page: Page, address: AddressData): Promise<void> {
    if (await this.elementVisible(page, this.addressStepAliasInput)) {
      await this.setValue(page, this.addressStepAliasInput, address.alias);
    }
    await this.setValue(page, this.addressStepPhoneInput, address.phone);
    await this.setValue(page, this.addressStepCompanyInput, address.company);
    // Contact
    await this.setValue(page, this.addressStepPhoneInput, address.phone);

    // Address
    await this.setValue(page, this.addressStepAddress1Input, address.address);
    await this.setValue(page, this.addressStepPostCodeInput, address.postalCode);
    await this.setValue(page, this.addressStepCityInput, address.city);
    await this.selectByVisibleText(page, this.addressStepCountrySelect, address.country);
    if (await this.elementVisible(page, this.stateInput, 1000)) {
      await this.selectByVisibleText(page, this.stateInput, address.state);
    }
  }

  /**
   * Set invoice address
   * @param page {Page} Browser tab
   * @param invoiceAddress {AddressData} Address's information to fill form with
   */
  async setInvoiceAddress(page: Page, invoiceAddress: AddressData): Promise<boolean> {
    await this.fillAddressForm(page, invoiceAddress);

    if (await this.elementVisible(page, this.addressStepContinueButton, 2000)) {
      await page.locator(this.addressStepContinueButton).click();
    } else {
      await page.locator(this.addressStepSubmitButton).click();
    }

    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Set address step
   * @param page {Page} Browser tab
   * @param deliveryAddress {AddressData|null} Address's information to add (for delivery)
   * @param invoiceAddress {AddressData|null} Address's information to add (for invoice)
   * @returns {Promise<boolean>}
   */
  async setAddress(page: Page, deliveryAddress: AddressData, invoiceAddress: AddressData | null = null): Promise<boolean> {
    // Set delivery address
    await this.fillAddressForm(page, deliveryAddress);

    // Set invoice address if not null
    if (invoiceAddress !== null) {
      await this.setChecked(page, this.addressStepUseSameAddressCheckbox, false);
      await page.locator(this.addressStepContinueButton).click();
      await this.fillAddressForm(page, invoiceAddress);
    } else {
      await this.setChecked(page, this.addressStepUseSameAddressCheckbox, true);
    }

    if (await this.elementVisible(page, this.addressStepContinueButton, 2000)) {
      await page.locator(this.addressStepContinueButton).click();
    } else {
      await page.locator(this.addressStepSubmitButton).click();
    }

    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Get number od addresses
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfAddresses(page: Page): Promise<number> {
    await this.waitForSelector(page, this.deliveryAddressBlock, 'visible');

    return page.locator(this.deliveryAddressSection).count();
  }

  /**
   * Get number od addresses
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  async getNumberOfInvoiceAddresses(page: Page): Promise<number> {
    await this.waitForSelector(page, this.invoiceAddressesBlock, 'visible');

    return page.locator(this.invoiceAddressSection).count();
  }

  /**
   * Click on edit addresses step
   * @param page {Page} Browser tab
   */
  async clickOnEditAddressesStep(page: Page): Promise<void> {
    if (!await this.elementVisible(page, this.deliveryAddressBlock, 1000)) {
      await this.waitForSelectorAndClick(page, this.addressStepEditButton);
    }
  }

  /**
   * Click on new address button
   * @param page {Page} Browser tab
   */
  async clickOnAddNewAddressButton(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addAddressButton);
  }

  /**
   * Click on different invoice address link
   * @param page {Page} Browser tab
   */
  async clickOnDifferentInvoiceAddressLink(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.differentInvoiceAddressLink);
  }

  /**
   * Is invoice address block visible
   * @param page {Page} Browser tab
   */
  async isInvoiceAddressBlockVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.invoiceAddressesBlock, 3000);
  }

  /**
   * Click on new invoice address button
   * @param page {Page} Browser tab
   */
  async clickOnAddNewInvoiceAddressButton(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.addInvoiceAddressButton);
  }

  // Methods for Shipping methods step

  /**
   * Go to Delivery Step and check that Address step is complete
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async goToDeliveryStep(page: Page): Promise<boolean> {
    await this.clickAndWaitForLoadState(page, this.addressStepContinueButton);

    return this.isStepCompleted(page, this.addressStepSection);
  }

  /**
   * Check if the Delivery Step is displayed
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isDeliveryStep(page: Page): Promise<boolean> {
    await this.waitForVisibleSelector(page, this.addressStepContent);

    return this.elementVisible(page, this.addressStepContent, 1000);
  }

  /**
   * Choose delivery address
   * @param page {Page} Browser tab
   * @param position {number} Position of address to choose
   * @returns {Promise<void>}
   */
  async chooseDeliveryAddress(page: Page, position: number = 1): Promise<void> {
    await this.waitForSelectorAndClick(page, this.deliveryAddressPosition(position));
  }

  /**
   * Go to shipping Step and check that address step is complete
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async goToShippingStep(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.deliveryStepSection);
  }

  // Methods for shipping method step

  /**
   * Choose shipping method
   * @param page {Page} Browser tab
   * @param shippingMethodID {number} Position of the shipping method
   */
  async chooseShippingMethod(page: Page, shippingMethodID: number): Promise<void> {
    await this.waitForSelectorAndClick(page, this.deliveryOptionLabel(shippingMethodID));
  }

  /**
   * Get order message
   * @param page {Page} Browser tab
   */
  async getOrderMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.deliveryMessage);
  }

  /**
   * Choose shipping method and add a comment
   * @param page {Page} Browser tab
   * @param shippingMethodID {number} Position of the shipping method
   * @param comment {string} Comment to add after selecting a shipping method
   * @returns {Promise<boolean>}
   */
  async chooseShippingMethodAndAddComment(page: Page, shippingMethodID: number, comment: string = ''): Promise<boolean> {
    await this.waitForSelectorAndClick(page, this.deliveryOptionLabel(shippingMethodID));
    await this.setValue(page, this.deliveryMessage, comment);

    return this.goToPaymentStep(page);
  }

  /**
   * Choose shipping method and add a comment
   * @param page {Page} Browser tab
   * @param shippingMethodID {number} Position of the shipping method
   * @param comment {string} Comment to add after selecting a shipping method
   * @returns {Promise<void>}
   */
  async chooseShippingMethodWithoutValidation(page: Page, shippingMethodID: number, comment: string = ''): Promise<void> {
    await this.clickAndWaitForURL(page, this.deliveryOptionLabel(shippingMethodID));
    await this.setValue(page, this.deliveryMessage, comment);
  }

  /**
   * Is shipping method exist
   * @param page {Page} Browser tab
   * @param shippingMethodID {number} Position of the shipping method
   * @returns {Promise<boolean>}
   */
  isShippingMethodVisible(page: Page, shippingMethodID: number): Promise<boolean> {
    return this.elementVisible(page, this.deliveryOptionLabel(shippingMethodID), 2000);
  }

  /**
   * Get all carriers prices
   * @param page {Page} Browser tab
   * @returns {Promise<Array<string>>}
   */
  async getAllCarriersPrices(page: Page): Promise<string[]> {
    return (await page
      .locator(this.deliveryOptionAllPricesSpan)
      .allTextContents())
      .filter((el: string | null): el is string => el !== null);
  }

  /**
   * Get shipping value
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getShippingCost(page: Page): Promise<string> {
    await page.waitForTimeout(2000);

    return this.getTextContent(page, this.shippingValueSpan);
  }

  /**
   * Get all carriers names
   * @param page {Page} Browser tab
   * @returns {Promise<Array<string>>}
   */
  async getAllCarriersNames(page: Page): Promise<(string | null)[]> {
    return page.locator(this.deliveryOptionAllNamesSpan).allTextContents();
  }

  /**
   * Get carrier data
   * @param page {Page} Browser tab
   * @param carrierID {number} The carrier row in list
   */
  async getCarrierData(page: Page, carrierID: number = 1): Promise<CarrierData> {
    const priceText: string = await this.getTextContent(page, this.deliveryStepCarrierPrice(carrierID));

    return new CarrierData({
      name: await this.getTextContent(page, this.deliveryStepCarrierName(carrierID)),
      delay: await this.getTextContent(page, this.deliveryStepCarrierDelay(carrierID)),
      price: parseFloat(priceText),
      priceText,
    });
  }

  /**
   * Go to Payment Step and check that delivery step is complete
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async goToPaymentStep(page: Page): Promise<boolean> {
    await this.clickAndWaitForLoadState(page, this.deliveryStepContinueButton);

    return this.isStepCompleted(page, this.deliveryStepSection);
  }

  /**
   * Click on edit shipping method step
   * @param page {Page} Browser tab
   */
  async clickOnEditShippingMethodStep(page: Page): Promise<void> {
    if (!await this.elementVisible(page, this.deliveryStepCarriersList, 1000)) {
      await this.waitForSelectorAndClick(page, this.deliveryStepEditButton);
    }
  }

  // Methods for payment methods step

  /**
   * Is confirm button visible and enabled
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isPaymentConfirmationButtonVisibleAndEnabled(page: Page): Promise<boolean> {
    // small side effect note, the selector is the one that checks for disabled
    return this.elementVisible(page, this.paymentConfirmationButton, 1000);
  }

  /**
   * Get No payment needed block content
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getNoPaymentNeededBlockContent(page: Page): Promise<string> {
    return this.getTextContent(page, this.noPaymentNeededElement);
  }

  /**
   * Get selected shipping method name
   * @param page {Page} Browser tab
   * @return {Promise<string | null>}
   */
  async getSelectedShippingMethod(page: Page): Promise<string | null> {
    const selectedOptionId = parseInt(
      await this.getAttributeContent(page, `${this.deliveryOptionsRadioButton}[checked]`, 'value') ?? '0',
      10,
    );

    // Return text of the selected option
    if (selectedOptionId !== 0) {
      return this.getTextContent(page, this.deliveryOptionNameSpan(selectedOptionId));
    }
    throw new Error('No selected option was found');
  }

  /**
   * Set promo code
   * @param page {Page} Browser tab
   * @param code {string} The promo code
   * @param clickOnCheckoutPromoCodeLink {boolean} True if we need to click on promo code link
   * @returns {Promise<void>}
   */
  async addPromoCode(page: Page, code: string, clickOnCheckoutPromoCodeLink: boolean = true): Promise<void> {
    if (clickOnCheckoutPromoCodeLink) {
      await page.locator(this.checkoutHavePromoCodeButton).click();
    }
    await this.setValue(page, this.checkoutHavePromoInputArea, code);
    await page.locator(this.checkoutPromoCodeAddButton).click();
  }

  /**
   * Get cart rule name
   * @param page {Page} Browser tab
   * @param line {number} Cart rule line
   * @return {string}
   */
  getCartRuleName(page: Page, line: number = 1): Promise<string> {
    return this.getTextContent(page, this.cartRuleName(line));
  }

  /**
   * Choose payment method and validate Order
   * @param page {Page} Browser tab
   * @param paymentModuleName {string} The chosen payment method
   * @return {Promise<void>}
   */
  async choosePaymentAndOrder(page: Page, paymentModuleName: string): Promise<void> {
    if (await this.elementVisible(page, this.paymentOptionInput(paymentModuleName), 1000)) {
      await page.locator(this.paymentOptionInput(paymentModuleName)).click();
    }
    await Promise.all([
      this.waitForVisibleSelector(page, this.paymentConfirmationButton),
      page.locator(this.conditionToApproveLabel).click(),
    ]);
    await this.clickAndWaitForURL(page, this.paymentConfirmationButton);
  }

  /**
   * Get All tax included price
   * @param page {Page} Browser tab
   * @returns {Promise<number>}
   */
  getATIPrice(page: Page): Promise<number> {
    return this.getPriceFromText(page, this.cartTotalATI, 2000);
  }

  /**
   * Delete the discount
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async removePromoCode(page: Page): Promise<boolean> {
    await page.locator(this.checkoutRemoveDiscountLink).click();

    return this.elementNotVisible(page, this.checkoutRemoveDiscountLink, 1000);
  }

  /**
   * Get cart rule error text
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getCartRuleErrorMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.cartRuleAlertMessage);
  }

  /**
   * Order when no payment is needed
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async orderWithoutPaymentMethod(page: Page): Promise<void> {
    // Click on terms of services checkbox if visible
    if (await this.elementVisible(page, this.conditionToApproveLabel, 500)) {
      await Promise.all([
        this.waitForVisibleSelector(page, this.paymentConfirmationButton),
        page.locator(this.conditionToApproveLabel).click(),
      ]);
    }

    // Validate the order
    await this.clickAndWaitForURL(page, this.paymentConfirmationButton);
  }

  /**
   * Check payment method existence
   * @param page {Page} Browser tab
   * @param paymentModuleName {string} The payment module name
   * @returns {Promise<boolean>}
   */
  async isPaymentMethodExist(page: Page, paymentModuleName: string): Promise<boolean> {
    return this.elementVisible(page, this.paymentOptionInput(paymentModuleName), 2000);
  }

  /**
   * Check if checkbox of condition to approve is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isConditionToApproveCheckboxVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.conditionToApproveCheckbox, 1000);
  }

  /**
   * Get terms of service page title
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getTermsOfServicePageTitle(page: Page): Promise<string> {
    await page.locator(this.termsOfServiceLink).click();

    return this.getTextContent(page, this.termsOfServiceModalDiv);
  }

  /**
   * Check if gift checkbox is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  isGiftCheckboxVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.giftCheckbox, 1000);
  }

  /**
   * Set gift checkbox
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async setGiftCheckBox(page: Page): Promise<void> {
    await this.waitForSelectorAndClick(page, this.giftCheckbox);
  }

  /**
   * Is gift message textarea visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isGiftMessageTextareaVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.giftMessageTextarea, 2000);
  }

  /**
   * Set gift message
   * @param page {Page} Browser tab
   * @param message {string} Message to set
   * @returns {Promise<void>}
   */
  async setGiftMessage(page: Page, message: string): Promise<void> {
    await this.setValue(page, this.giftMessageTextarea, message);
  }

  /**
   * Check if recycled packaging checkbox is visible
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  isRecycledPackagingCheckboxVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.recyclableGiftCheckbox, 1000);
  }

  /**
   * Set recycled packaging checkbox
   * @param page {Page} Browser tab
   * @param toCheck {boolean} True if we need to check recycle packaging checkbox
   * @returns {Promise<void>}
   */
  async setRecycledPackagingCheckbox(page: Page, toCheck: boolean = true): Promise<void> {
    await this.setChecked(page, this.recyclableGiftCheckbox, toCheck);
  }

  /**
   * Get gift price from cart summary
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getGiftPrice(page: Page): Promise<string> {
    await this.setChecked(page, this.giftCheckbox, true);

    return this.getTextContent(page, this.cartSubtotalGiftWrappingValueSpan);
  }
}

const checkoutPage = new CheckoutPage();
export {checkoutPage, CheckoutPage};
