import {ModuleConfiguration} from '@pages/BO/modules/moduleConfiguration';

import type {Page} from 'playwright';

/**
 * Module configuration page for module : ps_email_subscription, contains selectors and functions for the page
 * @class
 * @extends ModuleConfiguration
 */
class KeycloakConnectorDemo extends ModuleConfiguration {
  public readonly pageTitle: string;

  private readonly formKeycloakEndpoint: string;

  private readonly formKeycloakAllowedIssuers: string;

  private readonly formKeycloakButtonSubmit: string;

  /**
   * @constructs
   * Setting up titles and selectors to use on ps email subscription page
   */
  constructor() {
    super();

    this.pageTitle = `Keycloak connector • ${global.INSTALL.SHOP_NAME}`;

    // Form selectors
    this.formKeycloakEndpoint = '#form_KEYCLOAK_REALM_ENDPOINT';
    this.formKeycloakAllowedIssuers = '#form_KEYCLOAK_ALLOWED_ISSUERS';
    this.formKeycloakButtonSubmit = 'form.form-horizontal .card-footer button';
  }

  /* Methods */

  /**
   * Set the Keycloak Realm Endpoint
   * @param page {Page} Browser tab
   * @param keycloakRealmUrl {string}
   * @param allowedIssuers {string[]}
   * @returns {Promise<number>}
   */
  async setKeycloakEndpoint(page: Page, keycloakRealmUrl: string, allowedIssuers: string[]): Promise<string> {
    await page.locator(this.formKeycloakEndpoint).fill(keycloakRealmUrl);
    await page.locator(this.formKeycloakAllowedIssuers).fill(allowedIssuers.join(' '));

    await this.clickAndWaitForLoadState(page, this.formKeycloakButtonSubmit);

    return this.getAlertSuccessBlockParagraphContent(page);
  }
}

export default new KeycloakConnectorDemo();
