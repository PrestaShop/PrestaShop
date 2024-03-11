// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import APIClientData from '@data/faker/APIClient';

import type {Page} from 'playwright';

/**
 * New API Client page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddNewAPIClient extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: (apiClientName: string) => string;

  public readonly apiClientGeneratedMessage: string;

  public readonly apiClientRegeneratedMessage: string;

  private readonly copyClientSecretLink: string;

  private readonly formAPIClient: string;

  private readonly clientNameInput: string;

  private readonly clientIdInput: string;

  private readonly descriptionInput: string;

  private readonly tokenLifetimeInput: string;

  private readonly statusSpan: string;

  private readonly statusInput: string;

  private readonly noScopes: string;

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

    this.pageTitleCreate = `New API Client • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (apiClientName: string) => `Editing API Client "${apiClientName}" • ${global.INSTALL.SHOP_NAME}`;
    this.successfulCreationMessage = 'Client secret:';
    this.apiClientGeneratedMessage = 'The API Client and client secret have been generated successfully. '
      + 'This secret value will only be displayed once. Don\'t forget to make a copy in a secure location.';
    this.apiClientRegeneratedMessage = 'Your new client secret has been generated successfully. '
      + 'Your former client secret is now obsolete. '
      + 'This secret value will only be displayed once. '
      + 'Don\'t forget to make a copy in a secure location.';

    // Selectors
    this.alertSuccessBlockParagraph = 'div.alert-success div.alert-text';
    this.copyClientSecretLink = `${this.alertSuccessBlockParagraph} a.copy-secret-to-clipboard`;
    this.formAPIClient = 'form[name="api_client"]';
    this.clientNameInput = `${this.formAPIClient} #api_client_client_name`;
    this.clientIdInput = `${this.formAPIClient} #api_client_client_id`;
    this.descriptionInput = `${this.formAPIClient} #api_client_description`;
    this.tokenLifetimeInput = `${this.formAPIClient} #api_client_lifetime`;
    this.statusSpan = `${this.formAPIClient} span#api_client_enabled`;
    this.statusInput = `${this.statusSpan} input`;
    this.noScopes = `${this.formAPIClient} p.resource-scopes-not-available`;
    this.scopeGroup = (group:string) => `#api_client_scopes_${group}_accordion div.switch-scope`;
    this.scopeStatus = (scope: string) => `${this.formAPIClient} div[data-scope="${scope}"] div.switch-widget span.ps-switch`;
    this.scopeStatusInput = (scope: string) => `${this.scopeStatus(scope)} input`;
    this.saveButton = `${this.formAPIClient} .card-footer button`;
    this.generateClientSecret = `${this.formAPIClient} .card-footer .generate-client-secret`;
    this.modalDialogConfirmButton = '#generate-secret-modal .modal-footer .btn-confirm-submit';
  }

  /*
  Methods
   */
  /**
   * Save the form
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveForm(page: Page): Promise<string> {
    // Save
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }

  /**
   * Add API Client
   * @param page {Page} Browser tab
   * @param apiClientData {APIClientData}}
   * @return {Promise<string>}
   */
  async addAPIClient(page: Page, apiClientData: APIClientData): Promise<string> {
    await this.setValue(page, this.clientNameInput, apiClientData.clientName);
    await this.setValue(page, this.clientIdInput, apiClientData.clientId);
    await this.setValue(page, this.descriptionInput, apiClientData.description);
    await this.setValue(page, this.tokenLifetimeInput, apiClientData.tokenLifetime);
    await this.setEnabled(page, apiClientData.enabled);

    // eslint-disable-next-line no-restricted-syntax
    for (const scope of apiClientData.scopes) {
      await this.setAPIScopeChecked(page, scope, true);
    }

    return this.saveForm(page);
  }

  /**
   * Returns if the API Client is enabled
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
   * Check/Uncheck an API Client
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
   * Returns if scopes are visible
   * @param page {Page} Browser tab
   * @return Promise<boolean>
   */
  async hasScopes(page: Page): Promise<boolean> {
    return this.elementNotVisible(page, this.noScopes, 1000);
  }

  /**
   * Returns the list of scopes from a group
   * @param page {Page} Browser tab
   * @param group {string} Scopes Group
   * @param isChecked {boolean|undefined} Scopes Group
   */
  async getApiScopes(page: Page, group: string, isChecked?: boolean): Promise<string[]> {
    if ((await page.locator(this.scopeGroup(group)).count()) === 0) {
      return [];
    }
    const scopes = await page
      .locator(this.scopeGroup(group))
      .evaluateAll(
        (all: HTMLElement[]) => all
          .map((el) => el.getAttribute('data-scope'))
          .filter((attr): attr is string => attr !== null),
      );

    if (typeof isChecked === 'undefined') {
      return scopes;
    }
    const scopesStatusChecked = [];

    // eslint-disable-next-line no-restricted-syntax
    for (const scope of scopes) {
      const isAPIChecked = await this.isAPIScopeChecked(page, scope);

      if ((isChecked && isAPIChecked) || (!isChecked && !isAPIChecked)) {
        scopesStatusChecked.push(scope);
      }
    }

    return scopesStatusChecked;
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
      case 'clientId':
        return page.inputValue(this.clientIdInput);
      case 'clientName':
        return page.inputValue(this.clientNameInput);
      case 'description':
        return page.inputValue(this.descriptionInput);
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

export default new AddNewAPIClient();
