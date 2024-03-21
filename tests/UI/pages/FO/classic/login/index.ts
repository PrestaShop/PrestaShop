// Import pages
import FOBasePage from '@pages/FO/FObasePage';

import {
  // Import data
  type FakerCustomer,
} from '@prestashop-core/ui-testing';

import type {Page} from 'playwright';

/**
 * Login page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class LoginPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly loginErrorText: string;

  public readonly disabledAccountErrorText: string;

  private readonly loginForm: string;

  private readonly emailInput: string;

  private readonly passwordInput: string;

  private readonly signInButton: string;

  protected displayRegisterFormLink: string;

  protected passwordReminderLink: string;

  private readonly showPasswordButton: string;

  protected alertDangerTextBlock: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on login page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Login';
    this.loginErrorText = 'Authentication failed.';
    this.disabledAccountErrorText = 'Your account isn\'t available at this time, please contact us';

    // Selectors
    this.loginForm = '#login-form';
    this.emailInput = `${this.loginForm} input[name='email']`;
    this.passwordInput = `${this.loginForm} input[name='password']`;
    this.signInButton = `${this.loginForm} button#submit-login`;
    this.displayRegisterFormLink = 'div.no-account a[data-link-action=\'display-register-form\']';
    this.passwordReminderLink = '.forgot-password a';
    this.showPasswordButton = '#login-form button[data-action=show-password]';
    this.alertDangerTextBlock = '#content section.login-form div.help-block li.alert-danger';
  }

  /*
  Methods
   */

  /**
   * Login in FO
   * @param page {Page} Browser tab
   * @param customer {FakerCustomer} Customer's information (email and password)
   * @param waitForNavigation {boolean} true to wait for navigation after the click on button
   * @return {Promise<void>}
   */
  async customerLogin(page: Page, customer: FakerCustomer, waitForNavigation: boolean = true): Promise<void> {
    await this.setValue(page, this.emailInput, customer.email);
    await this.setValue(page, this.passwordInput, customer.password);
    if (waitForNavigation) {
      await this.clickAndWaitForLoadState(page, this.signInButton);
      await this.elementNotVisible(page, this.signInButton, 2000);
    } else {
      await page.locator(this.signInButton).click();
    }
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getLoginError(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertDangerTextBlock);
  }

  /**
   * Get password type
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getPasswordType(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.passwordInput, 'type');
  }

  /**
   * Show password
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async showPassword(page: Page): Promise<string> {
    await this.waitForSelectorAndClick(page, this.showPasswordButton);

    return this.getAttributeContent(page, this.passwordInput, 'type');
  }

  /**
   * Go to create account page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToCreateAccountPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.displayRegisterFormLink);
  }

  /**
   * Go to the password reminder page
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToPasswordReminderPage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.passwordReminderLink);
  }
}

const loginPage = new LoginPage();
export {loginPage, LoginPage};
