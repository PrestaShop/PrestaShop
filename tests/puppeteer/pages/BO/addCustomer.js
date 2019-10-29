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
    this.selectAllGroupAccessCheckbox = '.choice-table .table-bordered label .md-checkbox-control';
    this.defaultCustomerGroupSelect = 'select#customer_default_group_id';
    this.saveCustomerButton = 'div.card-footer button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit customer
   * @param customerData
   * @return {Promise<textContent>}
   */
  async createEditCustomer(customerData) {
    if (customerData.socialTitle === 'Mr.') await this.page.click(this.socialTitleInput.replace('%ID', '0'));
    else await this.page.click(this.socialTitleInput.replace('%ID', '1'));
    await this.setValue(this.firstNameInput, customerData.firstName);
    await this.setValue(this.lastNameInput, customerData.lastName);
    await this.setValue(this.emailInput, customerData.email);
    await this.setValue(this.passwordInput, customerData.password);
    await this.page.select(this.yearOfBirthSelect, customerData.yearOfBirth);
    await this.page.select(this.monthOfBirthSelect, customerData.monthOfBirth);
    await this.page.select(this.dayOfBirthSelect, customerData.dayOfBirth);
    if (customerData.enabled) await this.page.click(this.enabledSwitchlabel.replace('%ID', '1'));
    else await this.page.click(this.enabledSwitchlabel.replace('%ID', '0'));
    if (customerData.partnerOffers) await this.page.click(this.partnerOffersSwitchlabel.replace('%ID', '1'));
    else await this.page.click(this.partnerOffersSwitchlabel.replace('%ID', '0'));
    await this.page.click(this.selectAllGroupAccessCheckbox);
    await this.selectByVisibleText(this.defaultCustomerGroupSelect, customerData.defaultCustomerGroup);
    // Save Customer
    await this.clickAndWaitForNavigation(this.saveCustomerButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }
};
