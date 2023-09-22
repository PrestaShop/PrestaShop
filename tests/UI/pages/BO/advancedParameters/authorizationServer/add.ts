// Import pages
import BOBasePage from '@pages/BO/BObasePage';

// Import data
import AuthorizedApplicationData from '@data/faker/authorizedApplication';

import type {Page} from 'playwright';

/**
 * New authorized application page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddNewAuthorizedApp extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: (appName: string) => string;

  private readonly formApplication: string;

  private readonly appNameInput: string;

  private readonly descriptionInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on Performance page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New authorized application • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (appName: string) => `Editing application ${appName} • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.formApplication = 'form[name="application"]';
    this.appNameInput = `${this.formApplication} #application_name`;
    this.descriptionInput = `${this.formApplication} #application_description`;
    this.saveButton = `${this.formApplication} .card-footer button`;
  }

  /*
  Methods
   */

  /**
   * Add authorized Application
   * @param page {Page} Browser tab
   * @param authorizedApplicationData {AuthorizedApplicationData}}
   * @return {Promise<string>}
   */
  async addAuthorizedApplication(page: Page, authorizedApplicationData: AuthorizedApplicationData): Promise<string> {
    await this.setValue(page, this.appNameInput, authorizedApplicationData.appName);
    await this.setValue(page, this.descriptionInput, authorizedApplicationData.description);
    // Save
    await this.clickAndWaitForURL(page, this.saveButton);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddNewAuthorizedApp();
