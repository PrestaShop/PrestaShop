import helper from '@utils/helpers';
import keycloakHelper from '@utils/keycloakHelper';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

function createKeycloakClient(baseContext: string = 'commonTests-setupKeycloak'): void {
  describe('Setup Keycloak', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in Keycloak', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginKeycloak', baseContext);

      await keycloakHelper.login(page, global.keycloakConfig.keycloakAdminUser, global.keycloakConfig.keycloakAdminPass);

      const pageTitle = await keycloakHelper.getPageTitle(page);
      await expect(pageTitle).to.contains('Master realm');
    });

    it('should go to \'Manage > Clients\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToManageClientsPage', baseContext);

      await keycloakHelper.goToManageClientsPage(page);

      const pageTitle = await keycloakHelper.getPageTitle(page);
      await expect(pageTitle).to.contains('Clients');
    });

    it('should go to \'Create client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateClientPage', baseContext);

      await keycloakHelper.goToCreateClientPage(page);

      const pageTitle = await keycloakHelper.getPageTitle(page);
      await expect(pageTitle).to.contains('Create client');
    });

    it('should create client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createClient', baseContext);

      await keycloakHelper.createClient(
        page,
        global.keycloakConfig.keycloakClientId,
        'PrestaShop Client ID',
        true,
        true,
      );

      const pageTitle = await keycloakHelper.getPageTitle(page);
      await expect(pageTitle).to.contains(global.keycloakConfig.keycloakClientId);
    });
  });
}

export default createKeycloakClient;
