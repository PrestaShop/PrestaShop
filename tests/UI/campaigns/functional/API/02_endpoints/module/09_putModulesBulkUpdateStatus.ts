// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boDashboardPage,
  boModuleManagerPage,
  boLoginPage,
  type BrowserContext,
  FakerModule,
  type ModuleInfo,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_module_putModulesBulkUpdateStatus';

describe('API : PUT /modules/bulk-update-status', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let moduleInfo1: ModuleInfo;
  let moduleInfo2: ModuleInfo;

  const clientScope: string = 'module_write';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Fetch two modules', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModulesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
      await boModuleManagerPage.closeSfToolBar(page);
      await boModuleManagerPage.filterByStatus(page, 'installed');

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should fetch modules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchModules', baseContext);

      const isModule1Visible = await boModuleManagerPage.searchModule(page, {tag: 'statscarrier'} as FakerModule);
      expect(isModule1Visible).to.be.equal(true);
      moduleInfo1 = await boModuleManagerPage.getModuleInformationNth(page, 1);

      const isModule2Visible = await boModuleManagerPage.searchModule(page, {tag: 'pagesnotfound'} as FakerModule);
      expect(isModule2Visible).to.be.equal(true);
      moduleInfo2 = await boModuleManagerPage.getModuleInformationNth(page, 1);
    });
  });

  [
    {
      status: false,
      verb: 'disable',
    },
    {
      status: true,
      verb: 'enable',
    },
  ].forEach((arg: {status: boolean, verb: string}, index: number) => {
    describe(`API : Update modules (${utilsCore.capitalize(arg.verb)})`, async () => {
      it('should request the endpoint /modules/bulk-update-status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${index}`, baseContext);

        const apiResponse = await apiContext.put('modules/bulk-update-status', {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: {
            modules: [
              moduleInfo1.technicalName,
              moduleInfo2.technicalName,
            ],
            enabled: arg.status,
          },
        });

        expect(apiResponse.status()).to.eq(204);

        const response = (await apiResponse.body()).toString();
        expect(response).to.be.equal('');
      });
    });

    describe(`BackOffice : Check modules are ${arg.verb}d`, async () => {
      it('should check module status by technical name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkModules${index}`, baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
        await boModuleManagerPage.closeSfToolBar(page);

        const pageTitle = await boModuleManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);

        const modules: ModuleInfo[] = [
          moduleInfo1,
          moduleInfo2,
        ];

        for (let idxModule = 0; idxModule < modules.length; idxModule++) {
          const isModuleEnabled = await boModuleManagerPage.isModuleStatus(page, modules[idxModule].technicalName, 'enable');
          expect(isModuleEnabled).to.eq(arg.status);
        }
      });
    });
  });
});
