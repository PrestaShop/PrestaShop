require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

module.exports = class AddPageEmployee extends BOBasePage {
  constructor(page) {
    super(page);

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.firstNameInput = '#employee_firstname';
    this.lastNameInput = '#employee_lastname';
    this.emailInput = '#employee_email';
    this.passwordInput = '#employee_password';
    this.defaultPageSelect = '#employee_default_page';
    this.defaultPageSpan = '.select2-selection[aria-labelledby=\'select2-employee_default_page-container\']';
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
    await this.selectDefaultPage(employeeData.defaultPage);
    // replace %ID by 1 in the selector if active = YES / 0 if active = NO
    if (employeeData.active) await this.page.click(this.activeSwitchlabel.replace('%ID', '1'));
    else await this.page.click(this.activeSwitchlabel.replace('%ID', '0'));
    await this.clickAndWaitForNavigation(this.saveButton);
    await this.page.waitForSelector(this.alertSuccessBlockParagraph, {visible: true});
    return this.getTextContent(this.alertSuccessBlockParagraph);
  }

  /**
   * Select default Page
   * @param defaultPage
   * @return {Promise<void>}
   */
  async selectDefaultPage(defaultPage) {
    await Promise.all([
      this.page.click(this.defaultPageSpan),
      this.page.waitForSelector(`${this.defaultPageSpan}[aria-expanded='true']`, {visible: true}),
    ]);
    await this.page.keyboard.type(defaultPage);
    await this.page.keyboard.press('Enter');
  }

  /**
   * Cancel page
   * @return {Promise<void>}
   */
  async cancel() {
    await this.clickAndWaitForNavigation(this.cancelButton);
  }
};
