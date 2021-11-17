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
    this.groupAccessCheckbox = id => `#customer_group_ids_${id}`;
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
    const socialTitleElement = await this.getParentElement(
      page,
      this.socialTitleInput(customerData.socialTitle === 'Mr.' ? 0 : 1),
    );

    await socialTitleElement.click();

    // Fill form
    await this.setValue(page, this.firstNameInput, customerData.firstName);
    await this.setValue(page, this.lastNameInput, customerData.lastName);
    await this.setValue(page, this.emailInput, customerData.email);
    await this.setValue(page, this.passwordInput, customerData.password);
    await page.selectOption(this.yearOfBirthSelect, customerData.yearOfBirth);
    await page.selectOption(this.monthOfBirthSelect, customerData.monthOfBirth);
    await page.selectOption(this.dayOfBirthSelect, customerData.dayOfBirth);
    await page.check(this.statusToggleInput(customerData.enabled ? 1 : 0));
    await page.check(this.partnerOffersToggleInput(customerData.partnerOffers ? 1 : 0));
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
        await this.changeCheckboxValue(page, this.selectAllGroupAccessCheckbox);
        break;
      case 'Guest':
        await this.changeCheckboxValue(page, this.groupAccessCheckbox(0), false);
        await this.changeCheckboxValue(page, this.groupAccessCheckbox(2), false);
        await this.changeCheckboxValue(page, this.groupAccessCheckbox(1));
        break;
      case 'Visitor':
        await this.changeCheckboxValue(page, this.groupAccessCheckbox(1), false);
        await this.changeCheckboxValue(page, this.groupAccessCheckbox(2), false);
        await this.changeCheckboxValue(page, this.groupAccessCheckbox(0));
        break;
      default:
        throw new Error(`${customerGroup} was not found as a group access`);
    }
  }

  /**
   * @override
   * Select, unselect checkbox
   * @param page {Page} Browser tab
   * @param checkboxSelector {string} Selector of checkbox
   * @param valueWanted {boolean} True if we want to select checkBox, else otherwise
   * @return {Promise<void>}
   */
  async changeCheckboxValue(page, checkboxSelector, valueWanted = true) {
    if (valueWanted !== (await this.isCheckboxSelected(page, checkboxSelector))) {
      // The selector is not visible, that why '+ i' is required here
      await page.$eval(`${checkboxSelector} + i`, el => el.click());
    }
  }
}

module.exports = new AddCustomer();
