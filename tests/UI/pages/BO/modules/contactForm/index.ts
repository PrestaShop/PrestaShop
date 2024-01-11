import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : contactform, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class ContactFormPage extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly sendConfirmationEmailToggle: (toggle: string) => string;

  private readonly sendNotificationEmailToggle: (toggle: string) => string;

  private readonly saveButton: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on contact form page
   */
  constructor() {
    super();

    this.pageTitle = 'Contact form';
    this.successfulUpdateMessage = 'The settings have been successfully updated.';

    // Selectors
    this.sendConfirmationEmailToggle = (toggle: string) => `#CONTACTFORM_SEND_CONFIRMATION_EMAIL_${toggle}`;
    this.sendNotificationEmailToggle = (toggle: string) => `#CONTACTFORM_SEND_NOTIFICATION_EMAIL_${toggle}`;
    this.saveButton = '#module_form_submit_btn';
  }

  /* Methods */

  /**
   * Set send confirmation email
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable send confirmation email
   * @returns {Promise<number>}
   */
  async setSendConfirmationEmail(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.sendConfirmationEmailToggle(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForLoadState(page, this.saveButton);

    return this.getAlertSuccessBlockContent(page);
  }

  /**
   * Set send confirmation email
   * @param page {Page} Browser tab
   * @param toEnable {boolean} True if we need to enable receive customers message by email
   * @returns {Promise<number>}
   */
  async setReceiveCustomersMessageByEmail(page: Page, toEnable: boolean): Promise<string> {
    await this.setChecked(page, this.sendNotificationEmailToggle(toEnable ? 'on' : 'off'));
    await this.clickAndWaitForLoadState(page, this.saveButton);

    return this.getAlertSuccessBlockContent(page);
  }
}

export default new ContactFormPage();
