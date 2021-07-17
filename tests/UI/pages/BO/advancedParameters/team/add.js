require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

class AddEmployee extends BOBasePage {
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';
    this.pageTitleEdit = 'Edit:';

    // Selectors
    this.firstNameInput = '#employee_firstname';
    this.lastNameInput = '#employee_lastname';
    this.emailInput = '#employee_email';
    this.passwordInput = '#employee_password';
    this.defaultPageSpan = '.select2-selection[aria-labelledby=\'select2-employee_default_page-container\']';
    this.languageSelect = '#employee_language';
    this.activeSwitchLabel = toggle => `label[for='employee_active_${toggle}']`;
    this.permissionProfileSelect = '#employee_profile';
    this.saveButton = 'div.card-footer button';
    this.cancelButton = 'div.card-footer a';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page Employee
   * @param page
   * @param employeeData
   * @returns {Promise<string>}
   */
  async createEditEmployee(page, employeeData) {
    await this.setValue(page, this.firstNameInput, employeeData.firstName);
    await this.setValue(page, this.lastNameInput, employeeData.lastName);
    await this.setValue(page, this.emailInput, employeeData.email);
    await this.setValue(page, this.passwordInput, employeeData.password);
    await this.selectByVisibleText(page, this.permissionProfileSelect, employeeData.permissionProfile);
    await this.selectByVisibleText(page, this.languageSelect, employeeData.language);
    await this.selectDefaultPage(page, employeeData.defaultPage);
    // replace toggle by 1 in the selector if active = YES / 0 if active = NO
    await page.click(this.activeSwitchLabel(employeeData.active ? 1 : 0));
    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Select default Page
   * @param page
   * @param defaultPage
   * @returns {Promise<void>}
   */
  async selectDefaultPage(page, defaultPage) {
    await Promise.all([
      page.click(this.defaultPageSpan),
      this.waitForVisibleSelector(page, `${this.defaultPageSpan}[aria-expanded='true']`),
    ]);
    await page.keyboard.type(defaultPage);
    await page.keyboard.press('Enter');
  }

  /**
   * Cancel page
   * @returns {Promise<void>}
   */
  async cancel(page) {
    await this.clickAndWaitForNavigation(page, this.cancelButton);
  }
}

module.exports = new AddEmployee();
