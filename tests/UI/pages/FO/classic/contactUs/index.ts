// Import FO pages
import FOBasePage from '@pages/FO/classic/FObasePage';

// Import data
import type MessageData from '@data/faker/message';

import type {Page} from 'playwright';

/**
 * Contact us page, contains functions that can be used on the page
 * @class
 * @extends FOBasePage
 */
class ContactUsPage extends FOBasePage {
  public readonly pageTitle: string;

  public readonly validationMessage: string;

  public readonly invalidEmail: string;

  public readonly invalidContent: string;

  public readonly badFileExtensionErrorMessage: string;

  private readonly emailUsLink: string;

  private readonly subjectSelect: string;

  private readonly emailAddressInput: string;

  private readonly attachmentLabel: string;

  private readonly orderReferenceSelect: string;

  private readonly messageTextarea: string;

  private readonly sendButton: string;

  private readonly alertSuccessDiv: string;

  private readonly alertDangerTextBlock: string;

  /**
   * @constructs
   * Setting up texts and selectors to use on contact us page
   */
  constructor(theme: string = 'classic') {
    super(theme);

    this.pageTitle = 'Contact us';
    this.validationMessage = 'Your message has been successfully sent to our team.';
    this.invalidEmail = 'Invalid email address.';
    this.invalidContent = 'The message cannot be blank.';
    this.badFileExtensionErrorMessage = 'Bad file extension';

    // Left column selectors
    this.emailUsLink = '#left-column a';

    // Form selectors
    this.subjectSelect = '#content select[name=\'id_contact\']';
    this.emailAddressInput = '#content input[name=\'from\']';
    this.attachmentLabel = '#file-upload';
    this.orderReferenceSelect = 'select[name=id_order]';
    this.messageTextarea = '#content textarea[name=\'message\']';
    this.sendButton = '#content input[name=\'submitMessage\']';
    this.alertSuccessDiv = '#content div.alert-success';
    this.alertDangerTextBlock = '#content div.alert-danger';
  }

  /*
  Methods
   */
  /**
   * Get email us link href
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  getEmailUsLink(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.emailUsLink, 'href');
  }

  /**
   * Send message
   * @param page {Page} Browser tab
   * @param contactUsData {MessageData} The data for fill the form
   * @param file {string|null} The path of the file to upload
   * @returns {Promise<void>}
   */
  async sendMessage(page: Page, contactUsData: MessageData, file: string|null = null): Promise<void> {
    await this.selectByVisibleText(page, this.subjectSelect, contactUsData.subject);
    await this.setValue(page, this.emailAddressInput, contactUsData.emailAddress);

    if (file) {
      await this.uploadFile(page, this.attachmentLabel, file);
    }

    if (contactUsData.reference) {
      await this.selectByVisibleText(page, this.orderReferenceSelect, contactUsData.reference);
    }

    await this.setValue(page, this.messageTextarea, contactUsData.message);
    await page.locator(this.sendButton).click();
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertSuccess(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertSuccessDiv);
  }

  /**
   * Get login error
   * @param page {Page} Browser tab
   * @return {Promise<string>}
   */
  async getAlertError(page: Page): Promise<string> {
    return this.getTextContent(page, this.alertDangerTextBlock);
  }

  /**
   * Get and return the content of the email input
   * @param page {Page} Browser tab
   * @returns {Promise<string>}
   */
  async getEmailFieldValue(page: Page): Promise<string> {
    return this.getAttributeContent(page, this.emailAddressInput, 'value');
  }

  /**
   * Check if attachment input is visible
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  isAttachmentInputVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.attachmentLabel, 1000);
  }
}

const contactUsPage = new ContactUsPage();
export {contactUsPage, ContactUsPage};
