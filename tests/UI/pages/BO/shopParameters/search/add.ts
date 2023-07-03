import BOBasePage from '@pages/BO/BObasePage';

import SearchAliasData from '@data/faker/search';

import type {Page} from 'playwright';

/**
 * Add alias page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddAlias extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly aliasInput: string;

  private readonly resultInput: string;

  private readonly saveButton: string;

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
   * @returns {Promise<string>}
   */
  async setAlias(page: Page, aliasData: SearchAliasData): Promise<string> {
    await this.setValue(page, this.aliasInput, aliasData.alias);
    await this.setValue(page, this.resultInput, aliasData.result);

    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockContent(page);
  }
}

export default new AddAlias();
