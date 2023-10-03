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

  private readonly formAPIAccess: string;

  private readonly clientNameInput: string;

  private readonly clientIdInput: string;

  private readonly descriptionInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New API Access • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (apiAccessName: string) => `Editing API Access "${apiAccessName}" • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.formAPIAccess = 'form[name="api_access"]';
    this.clientNameInput = `${this.formAPIAccess} #api_access_client_name`;
    this.clientIdInput = `${this.formAPIAccess} #api_access_client_id`;
    this.descriptionInput = `${this.formAPIAccess} #api_access_description`;
    this.saveButton = `${this.formAPIAccess} .card-footer button`;
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
    // Save
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddNewAPIAccess();
