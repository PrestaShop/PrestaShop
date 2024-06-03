import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';
import {
  type FakerEmployeeRole,
} from '@prestashop-core/ui-testing';

/**
 * Add profile page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddRole extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: (name: string) => string;

  private readonly nameInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add profile page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New role • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = (name: string) => `Editing ${name} role • `
      + `${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.nameInput = '#profile_name_1';
    this.saveButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill form for add/edit page profile
   * @param page {Page} Browser tab
   * @param roleData {FakerEmployeeRole} Data to set on add/edit profile form
   * @return {Promise<string>}
   */
  async createEditRole(page: Page, roleData: FakerEmployeeRole): Promise<string> {
    await this.setValue(page, this.nameInput, roleData.name);
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddRole();
