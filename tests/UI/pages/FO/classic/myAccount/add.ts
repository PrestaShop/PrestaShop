// Import FO pages
import FOBasePage from '@pages/FO/FObasePage';

// Import data
import CustomerData from '@data/faker/customer';

import type {Page} from 'playwright';

/**
 * Create account page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class CreateAccountPage extends FOBasePage {
  private readonly pageTitle: string;

  public readonly formTitle: string;

  protected pageHeaderTitle: string;

  private readonly createAccountForm: string;

  private readonly genderRadioButton: (id: number) => string;

  private readonly firstNameInput: string;

  private readonly lastNameInput: string;

  private readonly newEmailInput: string;

  private readonly newPasswordInput: string;

  private readonly birthdateInput: string;

  private readonly customerPrivacyCheckbox: string;

  private readonly psgdprCheckbox: string;

  private readonly partnerOfferCheckbox: string;

  private readonly companyInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on create account page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Login';
    this.formTitle = 'Create an account';

    // Selectors
    this.pageHeaderTitle = '#main .page-header h1';
    this.createAccountForm = '#customer-form';
    this.genderRadioButton = (id: number) => `${this.createAccountForm} input[name='id_gender'][value='${id}']`;
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
   * Get form header title
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  getHeaderTitle(page: Page): Promise<string> {
    return this.getTextContent(page, this.pageHeaderTitle);
  }

  /**
   * Create new customer account
   * @param page {Page} Browser tab
   * @param customer {object} Customer's information (email and password)
   * @returns {Promise<void>}
   */
  async createAccount(page: Page, customer: CustomerData): Promise<void> {
    await this.waitForSelectorAndClick(page, this.genderRadioButton(customer.socialTitle === 'Mr.' ? 1 : 2));
    await this.setValue(page, this.firstNameInput, customer.firstName);
    await this.setValue(page, this.lastNameInput, customer.lastName);
    await this.setValue(page, this.newEmailInput, customer.email);
    await this.setValue(page, this.newPasswordInput, customer.password);

    await this.setValue(
      page,
      this.birthdateInput,
      `${customer.monthOfBirth}/${customer.dayOfBirth}/${customer.yearOfBirth}`,
    );

    await page.locator(this.customerPrivacyCheckbox).click();
    if (await this.elementVisible(page, this.psgdprCheckbox, 500)) {
      await page.locator(this.psgdprCheckbox).click();
    }
    await page.locator(this.saveButton).click();
  }

  /**
   * Is partner offer required
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isPartnerOfferRequired(page: Page): Promise<boolean> {
    return this.elementVisible(page, `${this.partnerOfferCheckbox}:required`, 1000);
  }

  /**
   * Is birthdate input visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isBirthDateVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.birthdateInput, 1000);
  }

  /**
   * Is partner offer visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isPartnerOfferVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.partnerOfferCheckbox, 1000);
  }

  /**
   * Is company input visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isCompanyInputVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.companyInput, 1000);
  }
}

const createAccountPage = new CreateAccountPage();
export {createAccountPage, CreateAccountPage};
