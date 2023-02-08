import BOBasePage from '@pages/BO/BObasePage';

import type OrderMessageData from '@data/faker/orderMessage';

import type {Page} from 'playwright';

/**
 * Add order message page, contains selectors and functions for the page
 * @class
 * @extends BOBasePage
 */
class AddOrderMessage extends BOBasePage {
  public readonly pageTitle: string;

  public readonly pageTitleEdit: string;

  public readonly pageTitleView: string;

  private readonly nameLangButton: string;

  private readonly langDropdownDiv: string;

  private readonly nameLangSpan: (lang: string) => string;

  private readonly nameInput: (id: number) => string;

  private readonly messageTextarea: (id: number) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on add order message page
   */
  constructor() {
    super();

    this.pageTitle = 'Add new';
    this.pageTitleEdit = 'Edit:';
    this.pageTitleView = 'View';

    // Selectors
    this.nameLangButton = '#order_message_name_dropdown';
    this.langDropdownDiv = 'div.locale-dropdown-menu';
    this.nameLangSpan = (lang: string) => `${this.langDropdownDiv} span[data-locale='${lang}']`;
    this.nameInput = (id: number) => `#order_message_name_${id}`;
    this.messageTextarea = (id: number) => `#order_message_message_${id}`;
    this.saveButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Change form language
   * @param page {Page} Browser tab
   * @param lang {string} Language to set on form
   * @return {Promise<void>}
   */
  async changeFormLang(page: Page, lang: string = 'en'): Promise<void> {
    await Promise.all([
      page.click(this.nameLangButton),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.click(this.nameLangSpan(lang)),
      this.waitForVisibleSelector(page, `${this.nameLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Add/Edit order message
   * @param page {Page} Browser tab
   * @param orderMessageData {OrderMessageData} Data to set order message form
   * @returns {Promise<string>}
   */
  async addEditOrderMessage(page: Page, orderMessageData: OrderMessageData): Promise<string> {
    // Change lang to 'en' than set inputs value
    await this.changeFormLang(page, 'en');
    await this.setValue(page, this.nameInput(1), orderMessageData.name);
    await this.setValue(page, this.messageTextarea(1), orderMessageData.message);
    // Change lang to 'fr' than set inputs value
    await this.changeFormLang(page, 'fr');
    await this.setValue(page, this.nameInput(2), orderMessageData.frName);
    await this.setValue(page, this.messageTextarea(2), orderMessageData.frMessage);
    // Save order message
    await this.clickAndWaitForURL(page, this.saveButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddOrderMessage();
