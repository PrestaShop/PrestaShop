import BOBasePage from '@pages/BO/BObasePage';
import {Page} from 'playwright';
import ContactData from '@data/faker/contact';

/**
 * Add contact page, contains functions that can be used on the page
 * @class
 * @extends BOBasePage
 */
class AddContact extends BOBasePage {
  public readonly pageTitleCreate: string;

  public readonly pageTitleEdit: string;

  private readonly pageTitleLangButton: string;

  private readonly pageTitleLangSpan: (lang: string) => string;

  private readonly titleInputEN: string;

  private readonly titleInputFR: string;

  private readonly emailAddressInput: string;

  private readonly enableSaveMessagesToggleInput: (toggle: number) => string;

  private readonly descriptionTextareaEN: string;

  private readonly descriptionTextareaFR: string;

  private readonly saveContactButton: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on add contact page
   */
  constructor() {
    super();

    this.pageTitleCreate = `New contact • ${global.INSTALL.SHOP_NAME}`;
    this.pageTitleEdit = 'Editing';

    // Selectors
    this.pageTitleLangButton = '#contact_title_dropdown';
    this.pageTitleLangSpan = (lang: string) => 'div.dropdown-menu[aria-labelledby=\'contact_title_dropdown\']'
      + ` span[data-locale='${lang}']`;
    this.titleInputEN = '#contact_title_1';
    this.titleInputFR = '#contact_title_2';
    this.emailAddressInput = '#contact_email';
    this.enableSaveMessagesToggleInput = (toggle: number) => `#contact_is_messages_saving_enabled_${toggle}`;
    this.descriptionTextareaEN = '#contact_description_1';
    this.descriptionTextareaFR = '#contact_description_2';
    this.saveContactButton = '#save-button';
  }

  /*
  Methods
   */

  /**
   * Change language for selectors
   * @param page {Page} Browser tab
   * @param lang {string} Language to choose
   * @return {Promise<void>}
   */
  async changeLanguageForSelectors(page: Page, lang: string = 'en'): Promise<void> {
    await Promise.all([
      page.locator(this.pageTitleLangButton).click(),
      this.waitForVisibleSelector(page, `${this.pageTitleLangButton}[aria-expanded='true']`),
    ]);
    await Promise.all([
      page.locator(this.pageTitleLangSpan(lang)).click(),
      this.waitForVisibleSelector(page, `${this.pageTitleLangButton}[aria-expanded='false']`),
    ]);
  }

  /**
   * Fill form for add/edit contact
   * @param page {Page} Browser tab
   * @param contactData {ContactData} Data to set on contact form
   * @returns {Promise<string>}
   */
  async createEditContact(page: Page, contactData: ContactData): Promise<string> {
    await this.setValue(page, this.titleInputEN, contactData.title);
    await this.setValue(page, this.emailAddressInput, contactData.email);
    await this.setValue(page, this.descriptionTextareaEN, contactData.description);
    await this.changeLanguageForSelectors(page, 'fr');
    await this.setValue(page, this.titleInputFR, contactData.title);
    await this.setValue(page, this.descriptionTextareaFR, contactData.description);
    await this.setChecked(page, this.enableSaveMessagesToggleInput(contactData.saveMessage ? 1 : 0));
    // Save Contact
    await this.clickAndWaitForURL(page, this.saveContactButton);
    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new AddContact();
