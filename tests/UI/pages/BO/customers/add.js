require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add customer page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddCustomer extends BOBasePage {
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
    this.socialTitleInput = id => `#customer_gender_id_${id}`;
    this.firstNameInput = '#customer_first_name';
    this.lastNameInput = '#customer_last_name';
    this.emailInput = '#customer_email';
    this.passwordInput = '#customer_password';
    this.yearOfBirthSelect = 'select#customer_birthday_year';
    this.monthOfBirthSelect = 'select#customer_birthday_month';
    this.dayOfBirthSelect = 'select#customer_birthday_day';
    this.statusToggleInput = toggle => `#customer_is_enabled_${toggle}`;
    this.partnerOffersToggleInput = toggle => `#customer_is_partner_offers_subscribed_${toggle}`;
    // Group access selector
    this.groupAccessCheckbox = id => `#customer_group_ids_${id}`;
    this.visitorChecbox = this.groupAccessCheckbox(0);
    this.guestChecbox = this.groupAccessCheckbox(1);
    this.customerChecbox = this.groupAccessCheckbox(2);

    this.selectAllGroupAccessCheckbox = 'input.js-choice-table-select-all';
    this.defaultCustomerGroupSelect = 'select#customer_default_group_id';
    this.saveCustomerButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit customer
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Data to set on new customer form
   * @return {Promise<void>}
   */
  async fillCustomerForm(page, customerData) {
    // Click on label for social input
    await this.setHiddenCheckboxValue(page, this.socialTitleInput(customerData.socialTitle === 'Mr.' ? 0 : 1));

    // Fill form
    await this.setValue(page, this.firstNameInput, customerData.firstName);
    await this.setValue(page, this.lastNameInput, customerData.lastName);
    await this.setValue(page, this.emailInput, customerData.email);
    await this.setValue(page, this.passwordInput, customerData.password);
    await page.selectOption(this.yearOfBirthSelect, customerData.yearOfBirth);
    await page.selectOption(this.monthOfBirthSelect, customerData.monthOfBirth);
    await page.selectOption(this.dayOfBirthSelect, customerData.dayOfBirth);
    await this.setChecked(page, this.statusToggleInput(customerData.enabled ? 1 : 0));
    await this.setChecked(page, this.partnerOffersToggleInput(customerData.partnerOffers ? 1 : 0));
    await this.setCustomerGroupAccess(page, customerData.defaultCustomerGroup);
    await this.selectByVisibleText(page, this.defaultCustomerGroupSelect, customerData.defaultCustomerGroup);
  }


  /**
   * Fill form for add/edit customer and get successful message after saving
   * @param page {Page} Browser tab
   * @param customerData {CustomerData} Data to set on new customer form
   * @return {Promise<string>}
   */
  async createEditCustomer(page, customerData) {
    // Fill form
    await this.fillCustomerForm(page, customerData);

    // Save Customer
    await this.clickAndWaitForNavigation(page, this.saveCustomerButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Set customer group access in form
   * @param page {Page} Browser tab
   * @param customerGroup {string} Value to set on customer group input
   * @return {Promise<void>}
   */
  async setCustomerGroupAccess(page, customerGroup) {
    switch (customerGroup) {
      case 'Customer':
        await this.setCheckedWithIcon(page, this.selectAllGroupAccessCheckbox);
        break;
      case 'Guest':
        await this.setCheckedWithIcon(page, this.visitorChecbox, false);
        await this.setCheckedWithIcon(page, this.customerChecbox, false);
        await this.setCheckedWithIcon(page, this.guestChecbox);
        break;
      case 'Visitor':
        await this.setCheckedWithIcon(page, this.guestChecbox, false);
        await this.setCheckedWithIcon(page, this.customerChecbox, false);
        await this.setCheckedWithIcon(page, this.visitorChecbox);
        break;
      default:
        throw new Error(`${customerGroup} was not found as a group access`);
    }
  }
}

module.exports = new AddCustomer();
