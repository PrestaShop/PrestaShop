require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Creating a new Customer •';
    this.pageTitleEdit = 'Editing customer';

    // Selectors
    this.socialTitleInput = id => `#customer_gender_id_${id}`;
    this.firstNameInput = '#customer_first_name';
    this.lastNameInput = '#customer_last_name';
    this.emailInput = '#customer_email';
    this.passwordInput = '#customer_password';
    this.yearOfBirthSelect = 'select#customer_birthday_year';
    this.monthOfBirthSelect = 'select#customer_birthday_month';
    this.dayOfBirthSelect = 'select#customer_birthday_day';
    this.enabledSwitchLabel = id => `label[for='customer_is_enabled_${id}']`;
    this.partnerOffersSwitchLabel = id => `label[for='customer_is_partner_offers_subscribed_${id}']`;
    this.groupAccessCheckkbox = id => `#customer_group_ids_${id}`;
    this.selectAllGroupAccessCheckbox = 'input.js-choice-table-select-all';
    this.defaultCustomerGroupSelect = 'select#customer_default_group_id';
    this.saveCustomerButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit customer
   * @param customerData
   * @return {Promise<string>}
   */
  async createEditCustomer(customerData) {
    await this.page.click(this.socialTitleInput(customerData.socialTitle === 'Mr.' ? 0 : 1));
    await this.setValue(this.firstNameInput, customerData.firstName);
    await this.setValue(this.lastNameInput, customerData.lastName);
    await this.setValue(this.emailInput, customerData.email);
    await this.setValue(this.passwordInput, customerData.password);
    await this.page.selectOption(this.yearOfBirthSelect, customerData.yearOfBirth);
    await this.page.selectOption(this.monthOfBirthSelect, customerData.monthOfBirth);
    await this.page.selectOption(this.dayOfBirthSelect, customerData.dayOfBirth);
    await this.page.click(this.enabledSwitchLabel(customerData.enabled ? 1 : 0));
    await this.page.click(this.partnerOffersSwitchLabel(customerData.partnerOffers ? 1 : 0));
    await this.setCustomerGroupAccess(customerData.defaultCustomerGroup);
    await this.selectByVisibleText(this.defaultCustomerGroupSelect, customerData.defaultCustomerGroup);
    // Save Customer
    await this.clickAndWaitForNavigation(this.saveCustomerButton);
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Set customer group access in form
   * @param customerGroup
   * @return {Promise<void>}
   */
  async setCustomerGroupAccess(customerGroup) {
    switch (customerGroup) {
      case 'Customer':
        await this.changeCheckboxValue(this.selectAllGroupAccessCheckbox);
        break;
      case 'Guest':
        await this.changeCheckboxValue(this.groupAccessCheckkbox(0), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox(2), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox(1));
        break;
      case 'Visitor':
        await this.changeCheckboxValue(this.groupAccessCheckkbox(1), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox(2), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox(0));
        break;
      default:
        throw new Error(`${customerGroup} was not found as a group access`);
    }
  }

  /**
   * @override
   * Select, unselect checkbox
   * @param checkboxSelector, selector of checkbox
   * @param valueWanted, true if we want to select checkBox, else otherwise
   * @return {Promise<void>}
   */
  async changeCheckboxValue(checkboxSelector, valueWanted = true) {
    if (valueWanted !== (await this.isCheckboxSelected(checkboxSelector))) {
      // The selector is not visible, that why '+ i' is required here
      await this.page.$eval(`${checkboxSelector} + i`, el => el.click());
    }
  }
};
