import FOBasePage from '@pages/FO/FObasePage';

import type {Page} from 'playwright';

/**
 * Password reminder page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class PasswordReminderPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly errorMessage: string;

  private readonly emailFormField: string;

  private readonly backToLoginLink: string;

  private readonly sendResetLinkButton: string;

  protected emailAddressText: string;

  private readonly newPasswordInput: string;

  private readonly confirmationPasswordInput: string;

  private readonly submitButton: string;

  protected sendResetLinkSuccessAlert: string;

  protected errorMessageAlert: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on password reminder page
   */
  constructor() {
    super();

    this.pageTitle = 'Forgot your password';
    this.errorMessage = 'You can regenerate your password only every 360 minute(s)';

    // Selectors
    this.emailFormField = '#email';
    this.backToLoginLink = '#back-to-login';
    this.sendResetLinkButton = '#send-reset-link';
    this.emailAddressText = 'section.renew-password .email';
    this.newPasswordInput = 'section.renew-password input[name=passwd]';
    this.confirmationPasswordInput = 'section.renew-password input[name=confirmation]';
    this.submitButton = 'section.renew-password button[name=submit]';

    // Success message
    this.sendResetLinkSuccessAlert = '.ps-alert-success';
    // Error message
    this.errorMessageAlert = '.ps-alert-error';
  }

  /*
  Methods
   */

  /**
   * Fill the reset password email form field and click "send reset link" button
   * @param page {Page} Browser tab
   * @param email {string} Account's email to fill on input
   * @returns {Promise<void>}
   */
  async sendResetPasswordLink(page: Page, email: string): Promise<void> {
    await this.setValue(page, this.emailFormField, email);
    await this.clickAndWaitForLoadState(page, this.sendResetLinkButton);
    await this.elementNotVisible(page, this.sendResetLinkButton, 2000);
  }

  /**
   * Check that the success alert message is visible
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async checkResetLinkSuccess(page: Page): Promise<string> {
    return this.getTextContent(page, this.sendResetLinkSuccessAlert);
  }

  /**
   * Open forgot password link
   * @param page {Page} Browser tab
   * @param emailBody {string} Text body in the mail
   * @returns {Promise<void>}
   */
  async openForgotPasswordPage(page: Page, emailBody: string): Promise<void> {
    // To get reset email password from received email
    const resetPasswordURL = emailBody.split(/(.*)(http.*password-recovery\\?.*)/)[2];
    await this.goTo(page, resetPasswordURL);
  }

  /**
   * Get email address to reset
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getEmailAddressToReset(page: Page): Promise<string> {
    return this.getTextContent(page, this.emailAddressText);
  }

  /**
   * Set new password
   * @param page {Page} Browser tab
   * @param password {string} New password to set
   * @returns {Promise<void>}
   */
  async setNewPassword(page: Page, password: string): Promise<void> {
    await this.setValue(page, this.newPasswordInput, password);
    await this.setValue(page, this.confirmationPasswordInput, password);
    await page.click(this.submitButton);
  }

  /**
   * Get error message
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getErrorMessage(page: Page): Promise<string> {
    return this.getTextContent(page, this.errorMessageAlert);
  }
}

const passwordReminderPage = new PasswordReminderPage();
export {passwordReminderPage, PasswordReminderPage};
