require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add search engine page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddSearchEngine extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on add search engine page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Search Engines > Add new •';
    this.pageTitleEdit = 'Search Engines > Edit:';

    // Form Selectors
    this.serverInput = '#server';
    this.getVarInput = '#getvar';
    this.saveButton = '#search_engine_form_submit_btn';
    this.alertSuccessBlockParagraph = '.alert-success';
  }

  /*
  Methods
   */

  /**
   * Fill create or edit search engine form and save it
   * @param page {Page} Browser tab
   * @param searchEngineData {SearchEngine} Data to set on search engine form
   * @return {Promise<string>}
   */
  async createEditSearchEngine(page, searchEngineData) {
    // Fill the form
    await this.setValue(page, this.serverInput, searchEngineData.server);
    await this.setValue(page, this.getVarInput, searchEngineData.getVar);

    // Save form
    await this.clickAndWaitForNavigation(page, this.saveButton);

    // Get successful message
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

module.exports = new AddSearchEngine();
