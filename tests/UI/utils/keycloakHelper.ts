import {APIRequestContext, APIResponse, Page} from 'playwright';

const selLoginUsernameInput: string = '#username';
const selLoginPasswordInput: string = '#password';
const selLoginButton: string = '#kc-login';
const selPageTitle: string = 'h1';
const selNavbarClients: string = '#nav-item-clients';
const selClientsCreateClientButton: string = '#kc-main-content-page-container .pf-c-tab-content .pf-c-toolbar__content a';
const selCreateClientClientId: string = '#kc-client-id';
const selCreateClientName: string = '#kc-name';
const selCreateClientNextPageButton: string = 'button[data-testid="next"]';
const selCreateClientAuthentificationSwitch: string = '#kc-authentication-switch';
const selCreateClientAuthorizationSwitch: string = '#kc-authorization-switch';
const selCreateClientSwitchToggle = (selSwitch: string): string => `${selSwitch} + span.pf-c-switch__toggle`;
const selCreateClientFlowStandardCheckbox : string = '#kc-flow-standard';
const selCreateClientFlowDirectCheckbox : string = '#kc-flow-direct';
const selCreateClientSaveButton: string = 'button[data-testid="save"]';

/**
 * @description Helper to use Keycloak
 */
export default {
  /**
   * Login to Keycloak Server Admin Panel
   * @param {Page} page
   * @param {string} username
   * @param {string} password
   * @return {Promise<void>}
   */
  async login(page: Page, username: string, password: string): Promise<void> {
    await page.goto(`${global.keycloakConfig.keycloakServer}/admin/`);

    await page.fill(selLoginUsernameInput, username);
    await page.fill(selLoginPasswordInput, password);
    await page.click(selLoginButton);
  },

  /**
   * Returns the title of the page
   * @param {Page} page
   * @return {Promise<string>}
   */
  async getPageTitle(page: Page): Promise<string> {
    const textContent = await page.textContent(selPageTitle);

    return (textContent ?? '').replace(/\s+/g, ' ').trim();
  },

  /**
   * Go to the 'Manage > Clients' page
   * @param {Page} page
   * @return {Promise<void>}
   */
  async goToManageClientsPage(page: Page): Promise<void> {
    await page.click(selNavbarClients);
  },

  /**
   * Go to the 'Create client' page
   * @param {Page} page
   * @return {Promise<void>}
   */
  async goToCreateClientPage(page: Page): Promise<void> {
    await page.click(selClientsCreateClientButton);
  },

  /**
   * Fill the form for creating a client and check it
   * @param {Page} page
   * @param {string} clientId
   * @param {string} name
   * @param {boolean} withClientAuth
   * @param {boolean} withAuthorization
   * @return {Promise<void>}
   */
  async createClient(
    page: Page,
    clientId: string,
    name: string,
    withClientAuth: boolean,
    withAuthorization: boolean,
  ): Promise<void> {
    await page.fill(selCreateClientClientId, clientId);
    await page.fill(selCreateClientName, name);
    await page.click(selCreateClientNextPageButton);

    if (withClientAuth !== (await page.isChecked(selCreateClientAuthentificationSwitch))) {
      // The sel is not visible, that why '+ i' is required here
      await page.$eval(selCreateClientSwitchToggle(selCreateClientAuthentificationSwitch), (el: HTMLInputElement) => el.click());
    }
    if (withAuthorization !== (await page.isChecked(selCreateClientAuthorizationSwitch))) {
      // The sel is not visible, that why '+ i' is required here
      await page.$eval(selCreateClientSwitchToggle(selCreateClientAuthorizationSwitch), (el: HTMLInputElement) => el.click());
    }
    await page.setChecked(selCreateClientFlowStandardCheckbox, false);
    await page.setChecked(selCreateClientFlowDirectCheckbox, false);
    await page.click(selCreateClientSaveButton);
    await page.waitForNavigation({timeout: 3000});
  },

  /**
   * Use the Keycloak Admin API and fetch the client secret
   * @param {APIRequestContext} apiContext
   * @return {Promise<string>}
   */
  async getClientSecret(apiContext: APIRequestContext): Promise<string> {
    const apiResponseAuth: APIResponse = await apiContext.post(
      '/realms/master/protocol/openid-connect/token',
      {
        form: {
          username: global.keycloakConfig.keycloakAdminUser,
          password: global.keycloakConfig.keycloakAdminPass,
          grant_type: 'password',
        },
        headers: {
          Authorization: `Basic ${btoa('admin-cli:Psip5UvTO1EXUEwzb15nxLWnwdU1Nlcg')}`,
        },
      },
    );

    const jsonApiResponseAuth = await apiResponseAuth.json();
    const adminAccessToken = jsonApiResponseAuth.access_token;

    const apiResponseAccess: APIResponse = await apiContext.get(
      '/admin/realms/master/clients',
      {
        params: {
          clientId: global.keycloakConfig.keycloakClientId,
        },
        headers: {
          Authorization: `Bearer ${adminAccessToken}`,
        },
      },
    );
    const jsonApiResponseAccess = await apiResponseAccess.json();

    return jsonApiResponseAccess[0].secret;
  },
};
