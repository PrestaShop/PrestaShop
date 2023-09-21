import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Identity page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class AccountIdentityPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly successfulUpdateMessage: string;

  public readonly errorUpdateMessage: string;

  public readonly invalidEmailAlertMessage: string;

  public readonly invalidNumberOfCharacters: string;

  public readonly minimumScoreAlertMessage: string;

  private readonly createAccountForm: string;

  private readonly genderRadioButton: (id: number) => string;

  private readonly firstNameInput: string;

  private readonly lastNameInput: string;

  private readonly newEmailInput: string;

  private readonly invalidEmailAlertDanger: string;

  private readonly passwordInput: string;

  private readonly invalidPasswordAlertDanger: string;

  private readonly invalidNewPasswordAlertDanger: string;

  private readonly newPasswordInput: string;

  private readonly birthdateInput: string;

  private readonly customerPrivacyCheckbox: string;

  private readonly psgdprCheckbox: string;

  private readonly newsletterCheckbox: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on identity page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Identity';
    this.successfulUpdateMessage = 'Information successfully updated.';
    this.errorUpdateMessage = 'Could not update your information, please check your data.';
    this.invalidEmailAlertMessage = 'Invalid email/password combination';
    this.invalidNumberOfCharacters = 'Password must be between 8 and 72 characters long';
    this.minimumScoreAlertMessage = 'The minimum score must be: Strong';

    // Selectors
    this.createAccountForm = '#customer-form';
    this.genderRadioButton = (id: number) => `${this.createAccountForm} label[for='field-id_gender-${id}']`;
    this.firstNameInput = `${this.createAccountForm} #field-firstname`;
    this.lastNameInput = `${this.createAccountForm} #field-lastname`;
    this.newEmailInput = `${this.createAccountForm} #field-email`;
    this.invalidEmailAlertDanger = `${this.createAccountForm} div:nth-child(4) li.alert-danger`;
    this.passwordInput = `${this.createAccountForm} #field-password`;
    this.invalidPasswordAlertDanger = `${this.createAccountForm} div.field-password-policy li.alert-danger`;
    this.invalidNewPasswordAlertDanger = `${this.createAccountForm} div:nth-child(6) div.help-block`;
    this.newPasswordInput = `${this.createAccountForm} #field-new_password`;
    this.birthdateInput = `${this.createAccountForm} #field-birthday`;
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
   * @param page {Page} Browser tab
   * @param oldPassword {string} The old password
   * @param customer {object} Customer's information to fill form
   * @returns {Promise<string>}
   */
  async editAccount(page: Page, oldPassword: string, customer: any): Promise<string> {
    await page.$eval(this.genderRadioButton(customer.socialTitle === 'Mr.' ? 1 : 2), (el: HTMLElement) => el.click());
    await this.setValue(page, this.firstNameInput, customer.firstName);
    await this.setValue(page, this.lastNameInput, customer.lastName);
    await this.setValue(page, this.newEmailInput, customer.email);
    await this.setValue(page, this.passwordInput, oldPassword);
    await this.setValue(page, this.newPasswordInput, customer.password);

    await this.setValue(
      page,
      this.birthdateInput,
      `${customer.monthOfBirth}/${customer.dayOfBirth}/${customer.yearOfBirth}`,
    );

    await this.setChecked(page, this.customerPrivacyCheckbox);
    if (await this.elementVisible(page, this.psgdprCheckbox, 500)) {
      await this.setChecked(page, this.psgdprCheckbox);
    }

    await this.clickAndWaitForLoadState(page, this.saveButton);

    return this.getTextContent(page, this.notificationsBlock);
  }

  /**
   * Get invalidEmailAlert
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getInvalidEmailAlert(page:Page):Promise<string> {
    return this.getTextContent(page, this.invalidEmailAlertDanger);
  }

  /**
   * Get invalid password alert
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getInvalidPasswordAlert(page:Page):Promise<string> {
    return this.getTextContent(page, this.invalidPasswordAlertDanger);
  }

  /**
   * Get invalid new password alert
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getInvalidNewPasswordAlert(page:Page):Promise<string> {
    return this.getTextContent(page, this.invalidNewPasswordAlertDanger);
  }

  /**
   * Unsubscribe from the newsletter from customer edit information page
   * @param page {Page} Browser tab
   * @param password {string} String for the password
   * @returns {Promise<string>}
   */
  async unsubscribeNewsletter(page:Page, password:string):Promise<string> {
    await this.setValue(page, this.passwordInput, password);

    await page.click(this.customerPrivacyCheckbox);
    if (await this.elementVisible(page, this.psgdprCheckbox, 500)) {
      await page.click(this.psgdprCheckbox);
    }
    await page.click(this.newsletterCheckbox);

    await this.clickAndWaitForLoadState(page, this.saveButton);

    return this.getTextContent(page, this.alertSuccessBlock);
  }
}

const accountIdentityPage = new AccountIdentityPage();
export {accountIdentityPage, AccountIdentityPage};
