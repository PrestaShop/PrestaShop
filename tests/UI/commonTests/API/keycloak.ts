import loginCommon from '@commonTests/BO/loginBO';

import dashboardPage from '@pages/BO/dashboard';
import keycloakConnectorDemo from '@pages/BO/modules/keycloakConnectorDemo';
import moduleManager from '@pages/BO/modules/moduleManager';
import {moduleConfigurationPage} from '@pages/BO/modules/moduleConfiguration';

import helper from '@utils/helpers';
import keycloakHelper from '@utils/keycloakHelper';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

function installKeycloak(baseContext: string = 'commonTests-installKeycloak'): void {
  describe('Install the module Keycloak Connector', async () => {
    let browserContext: BrowserContext;
    let page: Page;

    const versionModule: string = 'v1.0.2';
    const urlModule: string = `https://github.com/PrestaShop/keycloak_connector_demo/releases/download/${versionModule}`
      + '/keycloak_connector_demo.zip';

    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it('should upload the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadModule', baseContext);

      const uploadMessage = await moduleManager.uploadModuleFromURL(page, urlModule);
      await expect(uploadMessage).to.contains(moduleManager.uploadSuccessfulMessage);
    });

    it('should go to the Configure page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurePage', baseContext);

      await moduleManager.goToConfigurationPage(page, 'Keycloak OAuth2 connector demo');

      const pageSubtitle = await moduleConfigurationPage.getPageTitle(page);
      await expect(pageSubtitle).to.contains('Keycloak connector');
    });

    it('should set the Keycloak endpoint', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableThemeHummingbird', baseContext);

      const result = await keycloakConnectorDemo.setKeycloakEndpoint(page, 'http://127.0.0.1:8003/realms/master');
      await expect(result).to.eq(keycloakConnectorDemo.successfulUpdateMessage);
    });
  });
}

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

      await keycloakHelper.login(page, 'admin', 'admin');

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
        'prestashop_client_id',
        'PrestaShop Client ID',
        true,
        true,
      );

      const pageTitle = await keycloakHelper.getPageTitle(page);
      await expect(pageTitle).to.contains('prestashop_client_id');
    });
  });
}

function uninstallKeycloak(baseContext: string = 'commonTests-uninstallKeycloak'): void {
  describe('Uninstall the module Keycloak Connector', async () => {
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

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManager.closeSfToolBar(page);

      const pageTitle = await moduleManager.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManager.pageTitle);
    });

    it('should uninstall the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurePage', baseContext);

      const alertMessage = await moduleManager.uninstallModule(
        page,
        'Keycloak OAuth2 connector demo',
        'keycloak_connector_demo',
      );
      await expect(alertMessage).to.contains(moduleManager.uninstallModuleMessage('keycloak_connector_demo'));
    });
  });
}

export {
  createKeycloakClient,
  installKeycloak,
  uninstallKeycloak,
};
