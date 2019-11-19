require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddPageEmployee extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Add new • prestashop';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.firstNameInput = '#employee_firstname';
    this.lastNameInput = '#employee_lastname';
    this.emailInput = '#employee_email';
    this.passwordInput = '#employee_password';
    this.defaultPageSelect = '#employee_default_page';
    this.languageSelect = '#employee_language';
    this.activeSwitchlabel = 'label[for=\'employee_active_%ID\']';
    this.permissionProfileSelect = '#employee_profile';
    this.saveButton = 'div.card-footer button';
    this.cancelButton = 'div.card-footer a';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page Employee
   * @param employeeData
   * @return {Promise<textContent>}
   */
  async createEditEmployee(employeeData) {
    await this.setValue(this.firstNameInput, employeeData.firstName);
    await this.setValue(this.lastNameInput, employeeData.lastName);
    await this.setValue(this.emailInput, employeeData.email);
    await this.setValue(this.passwordInput, employeeData.password);
    await this.selectByVisibleText(this.permissionProfileSelect, employeeData.permissionProfile);
    await this.selectByVisibleText(this.languageSelect, employeeData.language);
    await this.selectByVisibleText(this.defaultPageSelect, employeeData.defaultPage);
    // replace %ID by 1 in the selector if active = YES / 0 if active = NO
    if (employeeData.active) await this.page.click(this.activeSwitchlabel.replace('%ID', '1'));
    else await this.page.click(this.activeSwitchlabel.replace('%ID', '0'));
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Cancel page
   * @return {Promise<void>}
   */
  async cancel() {
    await this.clickAndWaitForNavigation(this.cancelButton);
  }
};
