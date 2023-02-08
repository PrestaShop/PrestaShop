// Import BO pages
import BOBasePage from '@pages/BO/BObasePage';

import type {Page} from 'playwright';

/**
 * Add search engine page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddSearchEngine extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly serverInput: string;

  private readonly queryKeyInput: string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add search engine page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Search Engines •';
    this.pageTitleEdit = 'Edit:';

    // Form Selectors
    this.serverInput = '#search_engine_server';
    this.queryKeyInput = '#search_engine_query_key';
    this.saveButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Fill create or edit search engine form and save it
   * @param page {Page} Browser tab
   * @param searchEngineData {SearchEngineData} Data to set on search engine form
   * @return {Promise<string>}
   */
  async createEditSearchEngine(page: Page, searchEngineData: any): Promise<string> {
    // Fill the form
    await this.setValue(page, this.serverInput, searchEngineData.server);
    await this.setValue(page, this.queryKeyInput, searchEngineData.queryKey);

    // Save form
    await this.clickAndWaitForURL(page, this.saveButton);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddSearchEngine();
