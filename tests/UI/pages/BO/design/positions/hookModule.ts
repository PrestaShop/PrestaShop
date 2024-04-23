import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Positions page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class HookModulePage extends BOBasePage {
  public readonly pageTitle: string;

  private readonly formHookModule: string;

  private readonly formHookModuleSaveButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on positions page
   */
  constructor() {
    super();

    this.pageTitle = `Positions > Edit • ${global.INSTALL.SHOP_NAME}`;

    // Selectors
    this.formHookModule = '#hook_module_form';
    this.formHookModuleSaveButton = `${this.formHookModule} #hook_module_form_submit_btn`;
  }

  /* Methods */
  /**
   * Save the form on the Hook Module page
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async saveForm(page: Page): Promise<string> {
    await page.locator(this.formHookModuleSaveButton).click();

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new HookModulePage();
