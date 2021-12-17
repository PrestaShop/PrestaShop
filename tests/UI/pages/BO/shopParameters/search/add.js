require('module-alias/register');
const BOBasePage = require('@pages/BO/BObasePage');

/**
 * Add alias page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddAlias extends BOBasePage {
  /**
   * @constructs
   * Setting up titles and selectors to use on add alias page
   */
  constructor() {
    super();

    this.pageTitleCreate = 'Search > Add new •';
    this.pageTitleEdit = 'Search > Edit:';

    // selectors
    this.aliasInput = '#alias';
    this.resultInput = '#search';
    this.saveButton = '#alias_form_submit_btn';
  }

  /* Methods */
  /**
   * Create/Edit alias
   * @param page {Page} Browser tab
   * @param aliasData {SearchAliasData} Data to set on alias form
   * @returns {Promise<void>}
   */
  async setAlias(page, aliasData) {
    await this.setValue(page, this.aliasInput, aliasData.alias);
    await this.setValue(page, this.resultInput, aliasData.result);

    await this.clickAndWaitForNavigation(page, this.saveButton);
    return this.getAlertSuccessBlockContent(page);
  }
}

module.exports = new AddAlias();
