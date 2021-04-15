require('module-alias/register');
const FOBasePage = require('@pages/FO/FObasePage');

class AccountIdentity extends FOBasePage {
  constructor() {
    super();

    this.pageTitle = 'Identity';
    this.successfulUpdateMessage = 'Information successfully updated.';

    // Selectors
    this.createAccountForm = '#customer-form';
    this.genderRadioButton = id => `${this.createAccountForm} input[name='id_gender'][value='${id}']`;
    this.firstNameInput = `${this.createAccountForm} input[name='firstname']`;
    this.lastNameInput = `${this.createAccountForm} input[name='lastname']`;
    this.newEmailInput = `${this.createAccountForm} input[name='email']`;
    this.passwordInput = `${this.createAccountForm} input[name='password']`;
    this.newPasswordInput = `${this.createAccountForm} input[name='new_password']`;
    this.birthdateInput = `${this.createAccountForm} input[name='birthday']`;
    this.customerPrivacyCheckbox = `${this.createAccountForm} input[name='customer_privacy']`;
    this.psgdprCheckbox = `${this.createAccountForm} input[name='psgdpr']`;
    this.newsletterCheckbox = `${this.createAccountForm} input[name=newsletter]`;
    this.saveButton = `${this.createAccountForm} .form-control-submit`;
  }

  /*
  Methods
   */
  /**
   * Edit account information
   * @param page
   * @param oldPassword
   * @param customer
   * @returns {Promise<string>}
   */
  async editAccount(page, oldPassword, customer) {
    await page.$eval(this.genderRadioButton(customer.socialTitle === 'Mr.' ? 1 : 2), el => el.click());
    await this.setValue(page, this.firstNameInput, customer.firstName);
    await this.setValue(page, this.lastNameInput, customer.lastName);
    await this.setValue(page, this.newEmailInput, customer.email);
    await this.setValue(page, this.newEmailInput, customer.email);
    await this.setValue(page, this.passwordInput, oldPassword);
    await this.setValue(page, this.newPasswordInput, customer.password);

    await this.setValue(
      page,
      this.birthdateInput,
      `${customer.monthOfBirth}/${customer.dayOfBirth}/${customer.yearOfBirth}`,
    );

    await page.click(this.customerPrivacyCheckbox);
    if (await this.elementVisible(page, this.psgdprCheckbox, 500)) {
      await page.click(this.psgdprCheckbox);
    }

    await this.clickAndWaitForNavigation(page, this.saveButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }

  /**
   * Unsubscribe from the newsletter from customer edit information page
   * @param {object} page
   * @param {string} password
   *
   * @returns {Promise<string|TextContent|*>}
   */
  async unsubscribeNewsletter(page, password) {
    await this.setValue(page, this.passwordInput, password);

    await page.click(this.customerPrivacyCheckbox);
    if (await this.elementVisible(page, this.psgdprCheckbox, 500)) {
      await page.click(this.psgdprCheckbox);
    }
    await page.click(this.newsletterCheckbox);

    await this.clickAndWaitForNavigation(page, this.saveButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

module.exports = new AccountIdentity();
