import type {Page} from 'playwright';
import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';
import {
  dataLanguages,
} from '@prestashop-core/ui-testing';

/**
 * Module configuration page for module : psgdpr, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class PsGdprTabDataConsentPage extends ModuleConfiguration {
  public readonly saveFormMessage: string;

  private readonly checkboxCreationForm: (status: boolean) => string;

  private readonly messageCreationForm: (idLang: number) => string;

  private readonly checkboxCustomerForm: (status: boolean) => string;

  private readonly messageCustomerForm: (idLang: number) => string;

  private readonly btnDropdownLangModuleForm: string;

  private readonly btnDropdownItemLangModuleForm: (idLang: number) => string;

  private readonly checkboxModuleForm: (status: boolean) => string;

  private readonly messageModuleForm: (idLang: number) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   */
  constructor() {
    super();

    this.saveFormMessage = 'Saved with success!';

    this.checkboxCreationForm = (status: boolean) => `#switch_account_creation_${status ? 'on' : 'off'}`;
    this.messageCreationForm = (idLang: number) => `#psgdpr_creation_form_${idLang}_ifr`;
    this.checkboxCustomerForm = (status: boolean) => `#switch_account_customer_${status ? 'on' : 'off'}`;
    this.messageCustomerForm = (idLang: number) => `#psgdpr_customer_form_${idLang}_ifr`;
    this.btnDropdownLangModuleForm = 'div[id^="registered_module_message_"] div.translatable-field:not([style])'
      + ' button.dropdown-toggle';
    this.btnDropdownItemLangModuleForm = (idLang: number) => `div.open ul.dropdown-menu a[data-id="${idLang}"]`;
    this.checkboxModuleForm = (status: boolean) => `input[id^="switch_registered_module_"]${status ? '.yes' : '.no'}`;
    this.messageModuleForm = (idLang: number) => `iframe[id^="psgdpr_registered_module_"][id$="_${idLang}_ifr"]`;
    this.saveButton = '#submitDataConsent';
  }

  /**
   * Enable/Disable the "Account Creation" RGPD Label
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @returns {Promise<void>}
   */
  async setAccountCreationStatus(page: Page, status: boolean): Promise<void> {
    await page.locator(this.checkboxCreationForm(status)).setChecked(true, {
      force: true,
    });
  }

  /**
   * Define the "Account Creation" RGPD Label
   * @param page {Page} Browser tab
   * @param message {string} Message
   * @returns {Promise<void>}
   */
  async setAccountCreationMessage(page: Page, message: string): Promise<void> {
    await this.setValueOnTinymceInput(page, this.messageCreationForm(dataLanguages.english.id), message, false);
  }

  /**
   * Enable/Disable the "Customer Account" RGPD Label
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @returns {Promise<void>}
   */
  async setCustomerAccountStatus(page: Page, status: boolean): Promise<void> {
    await page.locator(this.checkboxCustomerForm(status)).setChecked(true, {
      force: true,
    });
  }

  /**
   * Define the "Customer Account" RGPD Label
   * @param page {Page} Browser tab
   * @param message {string} Message
   * @returns {Promise<void>}
   */
  async setCustomerAccountMessage(page: Page, message: string): Promise<void> {
    await this.setValueOnTinymceInput(page, this.messageCustomerForm(dataLanguages.english.id), message, false);
  }

  /**
   * Enable/Disable the "Newsletter" RGPD Label
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @returns {Promise<void>}
   */
  async setNewsletterStatus(page: Page, status: boolean): Promise<void> {
    await page.locator(this.checkboxModuleForm(status)).nth(0).setChecked(true, {
      force: true,
    });
  }

  /**
   * Define the "Newsletter" RGPD Label
   * @param page {Page} Browser tab
   * @param message {string} Message
   * @returns {Promise<void>}
   */
  async setNewsletterMessage(page: Page, message: string): Promise<void> {
    await this.setTinyMCEInputValue(page.frameLocator(this.messageModuleForm(dataLanguages.english.id)).nth(0), message);
  }

  /**
   * Enable/Disable the "Contact Form" RGPD Label
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @returns {Promise<void>}
   */
  async setContactFormStatus(page: Page, status: boolean): Promise<void> {
    await page.locator(this.checkboxModuleForm(status)).nth(2).setChecked(true, {
      force: true,
    });
  }

  /**
   * Define the "Contact" RGPD Label
   * @param page {Page} Browser tab
   * @param message {string} Message
   * @returns {Promise<void>}
   */
  async setContactFormMessage(page: Page, message: string): Promise<void> {
    await this.setTinyMCEInputValue(page.frameLocator(this.messageModuleForm(dataLanguages.english.id)).nth(2), message);
  }

  /**
   * Enable/Disable the "Product Comments" RGPD Label
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @returns {Promise<void>}
   */
  async setProductCommentsStatus(page: Page, status: boolean): Promise<void> {
    await page.locator(this.checkboxModuleForm(status)).nth(1).setChecked(true, {
      force: true,
    });
  }

  /**
   * Define the "Product Comments" RGPD Label
   * @param page {Page} Browser tab
   * @param message {string} Message
   * @returns {Promise<void>}
   */
  async setProductCommentsMessage(page: Page, message: string): Promise<void> {
    await this.setTinyMCEInputValue(page.frameLocator(this.messageModuleForm(dataLanguages.english.id)).nth(1), message);
  }

  /**
   * Enable/Disable the "Mail Alerts" RGPD Label
   * @param page {Page} Browser tab
   * @param status {boolean} Enable/Disable
   * @returns {Promise<void>}
   */
  async setMailAlertsStatus(page: Page, status: boolean): Promise<void> {
    await page.locator(this.checkboxModuleForm(status)).nth(3).setChecked(true, {
      force: true,
    });
  }

  /**
   * Define the "Mail Alerts" RGPD Label
   * @param page {Page} Browser tab
   * @param message {string} Message
   * @param idLang {number} Lang ID
   * @returns {Promise<void>}
   */
  async setMailAlertsMessage(page: Page, message: string, idLang: number = dataLanguages.english.id): Promise<void> {
    await page.locator(this.btnDropdownLangModuleForm).nth(3).click();
    await page.locator(this.btnDropdownItemLangModuleForm(idLang)).click();
    await this.setTinyMCEInputValue(page.frameLocator(this.messageModuleForm(idLang)).nth(3), message);
  }

  /**
   * Save the form
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async saveForm(page: Page): Promise<string> {
    await page.locator(this.saveButton).click();

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new PsGdprTabDataConsentPage();
