// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import APIAccessData from '@data/faker/APIAccess';

import type {Page} from 'playwright';

/**
 * New API Access page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddNewAPIAccess extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: (apiAccessName: string) => string;

  public readonly apiAccessGeneratedMessage: string;

  public readonly apiAccessRegeneratedMessage: string;

  private readonly copyClientSecretLink: string;

  private readonly formAPIAccess: string;

  private readonly clientNameInput: string;

  private readonly clientIdInput: string;

  private readonly descriptionInput: string;

  private readonly tokenLifetimeInput: string;

  private readonly statusSpan: string;

  private readonly statusInput: string;

  private readonly scopeGroup: (group:string) => string;

  private readonly scopeStatus: (scope: string) => string;

  private readonly scopeStatusInput: (scope: string) => string;

  private readonly saveButton: string;

  private readonly generateClientSecret: string;

  private readonly modalDialogConfirmButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New API Access • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (apiAccessName: string) => `Editing API Access "${apiAccessName}" • ${global.INSTALL.SHOP_NAME}`;
    this.successfulCreationMessage = 'Client secret:';
    this.apiAccessGeneratedMessage = 'The API access and client secret have been generated successfully. '
      + 'This secret value will only be displayed once. Don\'t forget to make a copy in a secure location.';
    this.apiAccessRegeneratedMessage = 'Your new client secret has been generated successfully. '
      + 'Your former client secret is now obsolete. '
      + 'This secret value will only be displayed once. '
      + 'Don\'t forget to make a copy in a secure location.';

    // Selectors
    this.alertSuccessBlockParagraph = 'div.alert-success div.alert-text';
    this.copyClientSecretLink = `${this.alertSuccessBlockParagraph} a.copy-secret-to-clipboard`;
    this.formAPIAccess = 'form[name="api_access"]';
    this.clientNameInput = `${this.formAPIAccess} #api_access_client_name`;
    this.clientIdInput = `${this.formAPIAccess} #api_access_client_id`;
    this.descriptionInput = `${this.formAPIAccess} #api_access_description`;
    this.tokenLifetimeInput = `${this.formAPIAccess} #api_access_lifetime`;
    this.statusSpan = `${this.formAPIAccess} span#api_access_enabled`;
    this.statusInput = `${this.statusSpan} input`;
    this.scopeGroup = (group:string) => `#api_access_scopes_${group}_accordion div.switch-scope`;
    this.scopeStatus = (scope: string) => `${this.formAPIAccess} div[data-scope="${scope}"] div.switch-widget span.ps-switch`;
    this.scopeStatusInput = (scope: string) => `${this.scopeStatus(scope)} input`;
    this.saveButton = `${this.formAPIAccess} .card-footer button`;
    this.generateClientSecret = `${this.formAPIAccess} .card-footer .generate-client-secret`;
    this.modalDialogConfirmButton = '#generate-secret-modal .modal-footer .btn-confirm-submit';
  }

  /*
  Methods
   */

  /**
   * Add API Access
   * @param page {Page} Browser tab
   * @param apiAccessData {APIAccessData}}
   * @return {Promise<string>}
   */
  async addAPIAccess(page: Page, apiAccessData: APIAccessData): Promise<string> {
    await this.setValue(page, this.clientNameInput, apiAccessData.clientName);
    await this.setValue(page, this.clientIdInput, apiAccessData.clientId);
    await this.setValue(page, this.descriptionInput, apiAccessData.description);
    await this.setValue(page, this.tokenLifetimeInput, apiAccessData.tokenLifetime);
    await this.setEnabled(page, apiAccessData.enabled);

    // eslint-disable-next-line no-restricted-syntax
    for (const scope of apiAccessData.scopes) {
      await this.setAPIScopeChecked(page, scope, true);
    }

    // Save
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Returns if the API Access is enabled
   * @param page {Page} Browser tab
   * @return {Promise<boolean>}
   */
  async isEnabled(page: Page): Promise<boolean> {
    if (await page.locator(`${this.statusInput}:checked`).count() === 0) {
      return false;
    }
    // Get value of the check input
    const inputValue = await this.getAttributeContent(page, `${this.statusInput}:checked`, 'value');

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Check/Uncheck an API Access
   * @param page {Page} Browser tab
   * @param valueWanted {boolean} True if we need to enable status, false if not
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setEnabled(page: Page, valueWanted: boolean = true): Promise<boolean> {
    if (await this.isEnabled(page) !== valueWanted) {
      await page.locator(this.statusSpan).click();

      await this.waitForVisibleSelector(
        page,
        `${this.statusInput}[value='${valueWanted ? 1 : 0}']:checked`,
      );

      return true;
    }

    return false;
  }

  /**
   * Returns the list of scopes from a group
   * @param page {Page} Browser tab
   * @param group {string} Scopes Group
   */
  async getApiScopes(page: Page, group: string): Promise<string[]> {
    return page
      .locator(this.scopeGroup(group))
      .evaluateAll(
        (all: HTMLElement[]) => all
          .map((el) => el.getAttribute('data-scope'))
          .filter((attr): attr is string => attr !== null),
      );
  }

  /**
   * Returns if a specific API Scope is disabled
   * @param page {Page} Browser tab
   * @param scope {string} Scope
   * @return {Promise<boolean>}
   */
  async isAPIScopeDisabled(page: Page, scope: string): Promise<boolean> {
    return this.isDisabled(page, `${this.scopeStatusInput(scope)}[value='0']`);
  }

  /**
   * Returns if a specific API Scope is checked
   * @param page {Page} Browser tab
   * @param scope {string} Scope
   * @return {Promise<boolean>}
   */
  async isAPIScopeChecked(page: Page, scope: string): Promise<boolean> {
    // Get value of the check input
    const inputValue = await this.getAttributeContent(page, `${this.scopeStatusInput(scope)}:checked`, 'value');

    // Return status=false if value='0' and true otherwise
    return (inputValue !== '0');
  }

  /**
   * Check/Uncheck a specific API Scope
   * @param page {Page} Browser tab
   * @param scope {string} Scope
   * @param valueWanted {boolean} True if we need to enable status, false if not
   * @return {Promise<boolean>} return true if action is done, false otherwise
   */
  async setAPIScopeChecked(page: Page, scope: string, valueWanted: boolean = true): Promise<boolean> {
    if (await this.isAPIScopeChecked(page, scope) !== valueWanted) {
      await page.locator(this.scopeStatus(scope)).click();

      await this.waitForVisibleSelector(
        page,
        `${this.scopeStatusInput(scope)}[value='${valueWanted ? 1 : 0}']:checked`,
      );

      return true;
    }

    return false;
  }

  /**
   * Return input value
   * @param page {Page}
   * @param inputName {string}
   */
  async getValue(page: Page, inputName: string): Promise<string> {
    switch (inputName) {
      case 'tokenLifetime':
        return this.getAttributeContent(page, this.tokenLifetimeInput, 'value');
      default:
        throw new Error(`Input ${inputName} was not found`);
    }
  }

  /**
   * Regenerate the client secret
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async regenerateClientSecret(page: Page): Promise<string> {
    await page.locator(this.generateClientSecret).click();

    await this.waitForSelectorAndClick(page, this.modalDialogConfirmButton, 2000);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Click on the Copy link
   * @param page {Page} Browser tab
   * @return {Promise<void>}
   */
  async copyClientSecret(page: Page): Promise<void> {
    await page.locator(this.copyClientSecretLink).click();
  }

  /**
   * Returns the client secret
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getClientSecret(page: Page): Promise<string> {
    const messageText = await this.getAlertSuccessBlockParagraphContent(page);

    const regexResult: RegExpMatchArray|null = /Client secret: ([a-z0-9]+)/.exec(messageText);

    if (regexResult === null) {
      return '';
    }

    return regexResult[1];
  }
}

export default new AddNewAPIAccess();
