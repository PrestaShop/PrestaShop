import EmployeeBasePage from '@pages/BO/advancedParameters/team/employeeBasePage';

import type EmployeeData from '@data/faker/employee';

import type {Page} from 'playwright';

/**
 * Add employee page, contains functions that can be used on the page
 * @class
 * @extends EmployeeBasePage
 */
class MyProfile extends EmployeeBasePage {
  public readonly successfulUpdateMessageFR: string;

  public readonly errorInvalidFirstNameMessage: string;

  public readonly errorInvalidLastNameMessage: string;

  public readonly errorInvalidFormatImageMessage: string;

  private readonly passwordButton: string;

  private readonly currentPasswordInput: string;

  private readonly newPasswordInput: string;

  private readonly confirmPasswordInput: string;

  private readonly avatarFileInput: string;

  private readonly enableGravatarInput: (toggle: number) => string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add employee page
   */
  constructor() {
    super();

    this.successfulUpdateMessageFR = 'Mise à jour réussie';
    this.errorInvalidFirstNameMessage = 'The "First name" field is invalid.';
    this.errorInvalidLastNameMessage = 'The "Last name" field is invalid.';
    this.errorInvalidFormatImageMessage = 'Image format not recognized, allowed formats are: image/gif, image/jpg, '
      + 'image/jpeg, image/pjpeg, image/png, image/x-png, image/webp, image/svg+xml, image/svg';

    // Selectors
    this.passwordButton = '#employee_change_password_change_password_button';
    this.currentPasswordInput = '#employee_change_password_old_password';
    this.newPasswordInput = '#employee_change_password_new_password_first';
    this.confirmPasswordInput = '#employee_change_password_new_password_second';
    this.avatarFileInput = '#employee_avatarUrl';
    this.enableGravatarInput = (toggle: number) => `#employee_has_enabled_gravatar_${toggle}`;
  }

  /*
  Methods
   */

  /**
   * Fill form for update My Profile page
   * @param page {Page} Browser tab
   * @param currentPassword {string} Data to set on add/edit employee form
   * @param newEmployeeData {EmployeeData} Data to set on add/edit employee form
   * @returns {Promise<void>}
   */
  async updateEditEmployee(page: Page, currentPassword: string, newEmployeeData: EmployeeData): Promise<void> {
    await this.setValue(page, this.firstNameInput, newEmployeeData.firstName);
    await this.setValue(page, this.lastNameInput, newEmployeeData.lastName);
    if (newEmployeeData.avatarFile) {
      await this.uploadOnFileChooser(page, this.avatarFileInput, [newEmployeeData.avatarFile]);
    }
    await this.setChecked(page, this.enableGravatarInput(newEmployeeData.enableGravatar ? 1 : 0));
    await this.setValue(page, this.emailInput, newEmployeeData.email);
    await page.locator(this.passwordButton).click();
    await this.setValue(page, this.currentPasswordInput, currentPassword);
    await this.setValue(page, this.newPasswordInput, newEmployeeData.password);
    await this.setValue(page, this.confirmPasswordInput, newEmployeeData.password);
    await this.selectByVisibleText(page, this.languageSelect, newEmployeeData.language);
    await this.selectDefaultPage(page, newEmployeeData.defaultPage);
    await this.clickAndWaitForLoadState(page, this.saveButton);
  }

  /**
   * Get the value of an input
   * @override
   * @param page {Page} Browser tab
   * @param input {string} ID of the input
   * @returns {Promise<string>}
   */
  async getInputValue(page: Page, input: string): Promise<string> {
    let inputSelector: string;

    switch (input) {
      case 'firstname':
        inputSelector = this.firstNameInput;
        break;
      case 'lastname':
        inputSelector = this.lastNameInput;
        break;
      default:
        throw new Error(`Field ${input} was not found`);
    }

    return super.getInputValue(page, inputSelector);
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertSuccess(page: Page): Promise<string> {
    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertError(page: Page): Promise<string> {
    return this.getAlertDangerBlockParagraphContent(page);
  }

  /**
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isGravatarEnabled(page: Page): Promise<boolean> {
    return this.isChecked(page, this.enableGravatarInput(1));
  }
}

export default new MyProfile();
