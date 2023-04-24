import {APIRequestContext, APIResponse, Page} from 'playwright';

/**
 * @description Helper to use Keycloak
 */
export default {

  async login(page: Page, username: string, password: string): Promise<void> {
    await page.goto('http://127.0.0.1:8003/admin/');

    await page.fill('#username', username);
    await page.fill('#password', password);
    await page.click('#kc-login');
  },

  async getPageTitle(page: Page): Promise<string> {
    const textContent = await page.textContent('h1');

    return (textContent ?? '').replace(/\s+/g, ' ').trim();
  },

  async goToManageClientsPage(page: Page): Promise<void> {
    await page.click('#nav-item-clients');
  },

  async goToCreateClientPage(page: Page): Promise<void> {
    await page.click('#kc-main-content-page-container .pf-c-tab-content .pf-c-toolbar__content a');
  },

  async createClient(
    page: Page,
    clientId: string,
    name: string,
    withClientAuth: boolean,
    withAuthorization: boolean,
  ): Promise<void> {
    await page.fill('#kc-client-id', clientId);
    await page.fill('#kc-name', name);
    await page.click('button[data-testid="next"]');

    if (withClientAuth !== (await page.isChecked('#kc-authentication-switch'))) {
      // The selector is not visible, that why '+ i' is required here
      await page.$eval('#kc-authentication-switch + span.pf-c-switch__toggle', (el: HTMLInputElement) => el.click());
    }
    if (withAuthorization !== (await page.isChecked('#kc-authorization-switch'))) {
      // The selector is not visible, that why '+ i' is required here
      await page.$eval('#kc-authorization-switch + span.pf-c-switch__toggle', (el: HTMLInputElement) => el.click());
    }
    await page.setChecked('#kc-flow-standard', false);
    await page.setChecked('#kc-flow-direct', false);
    await page.click('button[data-testid="save"]');
    await page.waitForNavigation({timeout: 3000});
  },

  async getClientSecret(apiContext: APIRequestContext): Promise<string> {
    const apiResponseAuth: APIResponse = await apiContext.post(
      '/realms/master/protocol/openid-connect/token',
      {
        form: {
          username: 'admin',
          password: 'admin',
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
          clientId: 'prestashop_client_id',
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
