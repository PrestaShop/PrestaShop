import EmployeeBasePage from '@pages/BO/advancedParameters/team/employeeBasePage';

import type EmployeeData from '@data/faker/employee';

import type {Page} from 'playwright';

/**
 * Add employee page, contains functions that can be used on the page
 * @class
 * @extends EmployeeBasePage
 */
class AddEmployee extends EmployeeBasePage {
  public readonly pageTitleCreate: string;

  private readonly passwordInput: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add employee page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Add new •';

    // Selectors
    this.passwordInput = '#employee_password';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page Employee
   * @param page {Page} Browser tab
   * @param employeeData {EmployeeData} Data to set on add/edit employee form
   * @returns {Promise<string>}
   */
  async createEditEmployee(page: Page, employeeData: EmployeeData): Promise<string> {
    await this.setValue(page, this.firstNameInput, employeeData.firstName);
    await this.setValue(page, this.lastNameInput, employeeData.lastName);
    await this.setValue(page, this.emailInput, employeeData.email);
    await this.setValue(page, this.passwordInput, employeeData.password);
    await this.selectByVisibleText(page, this.permissionProfileSelect, employeeData.permissionProfile);
    await this.selectByVisibleText(page, this.languageSelect, employeeData.language);
    if (employeeData.permissionProfile !== 'Translator') {
      await this.selectDefaultPage(page, employeeData.defaultPage);
    }
    // replace toggle by 1 in the selector if active = YES / 0 if active = NO
    await this.setChecked(page, this.statusToggleInput(employeeData.active ? 1 : 0));
    await this.clickAndWaitForLoadState(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddEmployee();
