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
    // Save
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
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
