require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddCustomer extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Creating a new Customer •';
    this.pageTitleEdit = 'Editing customer';

    // Selectors
    this.socialTitleInput = '#customer_gender_id_%ID';
    this.firstNameInput = '#customer_first_name';
    this.lastNameInput = '#customer_last_name';
    this.emailInput = '#customer_email';
    this.passwordInput = '#customer_password';
    this.yearOfBirthSelect = 'select#customer_birthday_year';
    this.monthOfBirthSelect = 'select#customer_birthday_month';
    this.dayOfBirthSelect = 'select#customer_birthday_day';
    this.enabledSwitchlabel = 'label[for=\'customer_is_enabled_%ID\']';
    this.partnerOffersSwitchlabel = 'label[for=\'customer_is_partner_offers_subscribed_%ID\']';
    this.groupAccessCheckkbox = '#customer_group_ids_%ID';
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
    await this.page.click(this.socialTitleInput.replace('%ID', customerData.socialTitle === 'Mr.' ? 0 : 1));
    await this.setValue(this.firstNameInput, customerData.firstName);
    await this.setValue(this.lastNameInput, customerData.lastName);
    await this.setValue(this.emailInput, customerData.email);
    await this.setValue(this.passwordInput, customerData.password);
    await this.page.select(this.yearOfBirthSelect, customerData.yearOfBirth);
    await this.page.select(this.monthOfBirthSelect, customerData.monthOfBirth);
    await this.page.select(this.dayOfBirthSelect, customerData.dayOfBirth);
    await this.page.click(this.enabledSwitchlabel.replace('%ID', customerData.enabled ? 1 : 0));
    await this.page.click(this.partnerOffersSwitchlabel.replace('%ID', customerData.partnerOffers ? 1 : 0));
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
        await this.changeCheckboxValue(this.groupAccessCheckkbox.replace('%ID', 0), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox.replace('%ID', 2), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox.replace('%ID', 1));
        break;
      case 'Visitor':
        await this.changeCheckboxValue(this.groupAccessCheckkbox.replace('%ID', 1), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox.replace('%ID', 2), false);
        await this.changeCheckboxValue(this.groupAccessCheckkbox.replace('%ID', 0));
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
      await this.page.click(`${checkboxSelector} + i`);
    }
  }
};
