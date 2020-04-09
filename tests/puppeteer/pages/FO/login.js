require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

module.exports = class Login extends FOBasePage {
  constructor(page) {
    super(page);

    this.pageTitle = 'Login';

    // Selectors
    this.loginForm = '#login-form';
    this.emailInput = `${this.loginForm} input[name='email']`;
    this.passwordInput = `${this.loginForm} input[name='password']`;
    this.signInButton = `${this.loginForm} button#submit-login`;
    this.displayRegisterFormLink = '#content a[data-link-action=\'display-register-form\']';
    // Selectors for create account form
    this.createAccountForm = '#customer-form';
    this.genderRadioButton = `${this.createAccountForm} input[name='id_gender'][value='%ID']`;
    this.firstNameInput = `${this.createAccountForm} input[name='firstname']`;
    this.lastNameInput = `${this.createAccountForm} input[name='lastname']`;
    this.newEmailInput = `${this.createAccountForm} input[name='email']`;
    this.newPasswordInput = `${this.createAccountForm} input[name='password']`;
    this.birthdateInput = `${this.createAccountForm} input[name='birthday']`;
    this.customerPrivacyCheckbox = `${this.createAccountForm} input[name='customer_privacy']`;
    this.psgdprCheckbox = `${this.createAccountForm} input[name='psgdpr']`;
    this.partnerOfferCheckbox = `${this.createAccountForm} input[name='optin']`;
    this.companyInput = `${this.createAccountForm} input[name='company']`;
    this.saveButton = `${this.createAccountForm} .form-control-submit`;
  }

  /*
  Methods
   */

  /**
   * Login in FO
   * @param customer
   * @return {Promise<void>}
   */
  async customerLogin(customer) {
    await this.setValue(this.emailInput, customer.email);
    await this.setValue(this.passwordInput, customer.password);
    await this.clickAndWaitForNavigation(this.signInButton);
  }

  /**
   * Create new customer account
   * @param customer
   * @returns {Promise<void>}
   */
  async createAccount(customer) {
    await this.waitForSelectorAndClick(this.displayRegisterFormLink);
    await this.waitForSelectorAndClick(this.genderRadioButton.replace('%ID', customer.socialTitle === 'Mr.' ? 1 : 2));
    await this.setValue(this.firstNameInput, customer.firstName);
    await this.setValue(this.lastNameInput, customer.lastName);
    await this.setValue(this.newEmailInput, customer.email);
    await this.setValue(this.newPasswordInput, customer.password);
    await this.setValue(this.birthdateInput, `${customer.monthOfBirth}/${customer.dayOfBirth}/${customer.yearOfBirth}`);
    await this.page.click(this.customerPrivacyCheckbox);
    if (await this.elementVisible(this.psgdprCheckbox, 500)) {
      await this.page.click(this.psgdprCheckbox);
    }
    await this.page.click(this.saveButton);
  }

  /**
   * Go to create account page
   * @returns {Promise<void>}
   */
  async goToCreateAccountPage() {
    await this.waitForSelectorAndClick(this.displayRegisterFormLink);
  }

  /**
   * Is partner offer required
   * @returns {Promise<boolean>}
   */
  async isPartnerOfferRequired() {
    return this.elementVisible(`${this.partnerOfferCheckbox}:required`, 1000);
  }

  /**
   * Is birth date input visible
   * @returns {Promise<boolean>}
   */
  async isBirthDateVisible() {
    return this.elementVisible(this.birthdateInput, 1000);
  }

  /**
   * Is partner offer visible
   * @returns {Promise<boolean>}
   */
  async isPartnerOfferVisible() {
    return this.elementVisible(this.partnerOfferCheckbox, 1000);
  }

  /**
   * Is company input visible
   * @returns {Promise<boolean>}
   */
  async isCompanyInputVisible() {
    return this.elementVisible(this.companyInput, 1000);
  }
};
