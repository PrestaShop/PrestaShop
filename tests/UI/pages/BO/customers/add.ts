import BOBasePage from '@pages/BO/BObasePage';

import type CustomerData from '@data/faker/customer';

import type {Frame, Page} from 'playwright';

/**
 * Add customer page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCustomer extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  public readonly updateSuccessfullMessage: string;

  private readonly socialTitleInput: (id: number) => string;

  private readonly firstNameInput: string;

  private readonly lastNameInput: string;

  private readonly emailInput: string;

  private readonly passwordInput: string;

  private readonly yearOfBirthSelect: string;

  private readonly monthOfBirthSelect: string;

  private readonly dayOfBirthSelect: string;

  private readonly statusToggleInput: (toggle: number) => string;

  private readonly partnerOffersToggleInput: (toggle: number) => string;

  private readonly companyInput: string;

  private readonly allowedOutstandingAmountInput: string;

  private readonly riskRatingSelect: string;

  private readonly groupAccessCheckbox: (id: number) => string;

  private readonly visitorCheckbox: string;

  private readonly guestCheckbox: string;

  private readonly customerCheckbox: string;

  private readonly selectAllGroupAccessCheckbox: string;

  private readonly defaultCustomerGroupSelect: string;

  private readonly saveCustomerButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add customer page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Creating a new Customer •';
    this.pageTitleEdit = 'Editing customer';
    this.updateSuccessfullMessage = 'Update successful';

    // Selectors
    this.socialTitleInput = (id: number) => `#customer_gender_id_${id}`;
    this.firstNameInput = '#customer_first_name';
    this.lastNameInput = '#customer_last_name';
    this.emailInput = '#customer_email';
    this.passwordInput = '#customer_password';
    this.yearOfBirthSelect = 'select#customer_birthday_year';
    this.monthOfBirthSelect = 'select#customer_birthday_month';
    this.dayOfBirthSelect = 'select#customer_birthday_day';
    this.statusToggleInput = (toggle: number) => `#customer_is_enabled_${toggle}`;
    this.partnerOffersToggleInput = (toggle: number) => `#customer_is_partner_offers_subscribed_${toggle}`;
    this.companyInput = '#customer_company_name';
    this.allowedOutstandingAmountInput = '#customer_allowed_outstanding_amount';
    this.riskRatingSelect = '#customer_risk_id';

    // Group access selector
    this.groupAccessCheckbox = (id: number) => `#customer_group_ids_${id}`;
    this.visitorCheckbox = this.groupAccessCheckbox(0);
    this.guestCheckbox = this.groupAccessCheckbox(1);
    this.customerCheckbox = this.groupAccessCheckbox(2);

    this.selectAllGroupAccessCheckbox = 'input.js-choice-table-select-all';
    this.defaultCustomerGroupSelect = 'select#customer_default_group_id';
    this.saveCustomerButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit customer
   * @param page {Frame|Page} Browser tab
   * @param customerData {CustomerData} Data to set on new customer form
   * @return {Promise<void>}
   */
  async fillCustomerForm(page: Frame|Page, customerData: CustomerData): Promise<void> {
    // Click on label for social input
    await this.setHiddenCheckboxValue(page, this.socialTitleInput(customerData.socialTitle === 'Mr.' ? 0 : 1));

    // Fill form
    await this.setValue(page, this.firstNameInput, customerData.firstName);
    await this.setValue(page, this.lastNameInput, customerData.lastName);
    await this.setValue(page, this.emailInput, customerData.email);
    await this.setValue(page, this.passwordInput, customerData.password);
    await this.selectByVisibleText(page, this.yearOfBirthSelect, customerData.yearOfBirth);
    await this.selectByVisibleText(page, this.monthOfBirthSelect, customerData.monthOfBirth);
    await this.selectByVisibleText(page, this.dayOfBirthSelect, customerData.dayOfBirth);
    await this.setChecked(page, this.statusToggleInput(customerData.enabled ? 1 : 0));
    await this.setChecked(page, this.partnerOffersToggleInput(customerData.partnerOffers ? 1 : 0));
    await this.setCustomerGroupAccess(page, customerData.defaultCustomerGroup);
    await this.selectByVisibleText(page, this.defaultCustomerGroupSelect, customerData.defaultCustomerGroup);
  }

  /**
   * Fill form for add/edit B2B customer
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Data to set on new customer form
   * @return {Promise<void>}
   */
  async fillB2BCustomerForm(page: Page, customerData: CustomerData): Promise<void> {
    // Click on label for social input
    await this.setHiddenCheckboxValue(page, this.socialTitleInput(customerData.socialTitle === 'Mr.' ? 0 : 1));

    // Fill form
    await this.setValue(page, this.firstNameInput, customerData.firstName);
    await this.setValue(page, this.lastNameInput, customerData.lastName);
    await this.setValue(page, this.emailInput, customerData.email);
    await this.setValue(page, this.passwordInput, customerData.password);
    await this.selectByVisibleText(page, this.yearOfBirthSelect, customerData.yearOfBirth);
    await this.selectByVisibleText(page, this.monthOfBirthSelect, customerData.monthOfBirth);
    await this.selectByVisibleText(page, this.dayOfBirthSelect, customerData.dayOfBirth);
    await this.setChecked(page, this.statusToggleInput(customerData.enabled ? 1 : 0));
    await this.setChecked(page, this.partnerOffersToggleInput(customerData.partnerOffers ? 1 : 0));
    await this.setCustomerGroupAccess(page, customerData.defaultCustomerGroup);
    await this.selectByVisibleText(page, this.defaultCustomerGroupSelect, customerData.defaultCustomerGroup);
    await this.setValue(page, this.companyInput, customerData.company);
    await this.setValue(page, this.allowedOutstandingAmountInput, customerData.allowedOutstandingAmount);
    await this.selectByVisibleText(page, this.riskRatingSelect, customerData.riskRating);
  }

  /**
   * Fill form for add/edit customer and get successful message after saving
   * @param page {Frame|Page} Browser tab
   * @param customerData {CustomerData} Data to set on new customer form
   * @param waitForNavigation {boolean} True if we need save and waitForNavigation, false if not
   * @return {Promise<string>}
   */
  async createEditCustomer(page: Frame|Page, customerData: CustomerData, waitForNavigation: boolean = true): Promise<string> {
    // Fill form
    await this.fillCustomerForm(page, customerData);

    // Save Customer
    if (waitForNavigation) {
      await this.clickAndWaitForURL(page, this.saveCustomerButton);
      return this.getAlertSuccessBlockParagraphContent(page);
    }
    await this.waitForSelectorAndClick(page, this.saveCustomerButton);
    return '';
  }

  /**
   * Fill form for add/edit B2B customer and get successful message after saving
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Data to set on new customer form
   * @return {Promise<string>}
   */
  async createEditB2BCustomer(page: Page, customerData: CustomerData): Promise<string> {
    // Fill form
    await this.fillB2BCustomerForm(page, customerData);

    // Save Customer
    await this.clickAndWaitForURL(page, this.saveCustomerButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set customer group access in form
   * @param page {Frame|Page} Browser tab
   * @param customerGroup {string} Value to set on customer group input
   * @return {Promise<void>}
   */
  async setCustomerGroupAccess(page: Frame|Page, customerGroup: string): Promise<void> {
    switch (customerGroup) {
      case 'Customer':
        await this.setCheckedWithIcon(page, this.visitorCheckbox, false);
        await this.setCheckedWithIcon(page, this.customerCheckbox);
        await this.setCheckedWithIcon(page, this.guestCheckbox, false);
        break;
      case 'Guest':
        await this.setCheckedWithIcon(page, this.visitorCheckbox, false);
        await this.setCheckedWithIcon(page, this.customerCheckbox, false);
        await this.setCheckedWithIcon(page, this.guestCheckbox);
        break;
      case 'Visitor':
        await this.setCheckedWithIcon(page, this.guestCheckbox, false);
        await this.setCheckedWithIcon(page, this.customerCheckbox, false);
        await this.setCheckedWithIcon(page, this.visitorCheckbox);
        break;
      default:
        throw new Error(`${customerGroup} was not found as a group access`);
    }
  }
}

export default new AddCustomer();
