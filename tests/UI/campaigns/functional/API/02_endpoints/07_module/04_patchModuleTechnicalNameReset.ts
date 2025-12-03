// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boDashboardPage,
  boModuleManagerPage,
  boLoginPage,
  type BrowserContext,
  dataModules,
  modBlockwishlistBoMain,
  type ModuleInfo,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_module_patchModuleTechnicalNameReset';

describe('API : PATCH /modules/{technicalName}/reset', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let moduleInfo: ModuleInfo;

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

  describe('BackOffice : Expected data', async () => {
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

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
      expect(isModuleVisible).to.be.equal(true);

      moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
    });

    it(`should go to the configuration page of the module '${dataModules.blockwishlist.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.blockwishlist.tag);

      const pageTitle = await modBlockwishlistBoMain.getPageTitle(page);
      expect(pageTitle).to.eq(modBlockwishlistBoMain.pageTitle);

      const isConfigurationTabActive = await modBlockwishlistBoMain.isTabActive(page, 'Configuration');
      expect(isConfigurationTabActive).to.eq(true);

      const wishlistDefaultTitle = await modBlockwishlistBoMain.getInputValue(page, 'wishlistDefaultTitle');
      expect(wishlistDefaultTitle).to.equals(modBlockwishlistBoMain.defaultValueWishlistDefaultTitle);
    });

    it('should change the configuration in the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeConfiguration', baseContext);

      const textResult = await modBlockwishlistBoMain.setFormWording(page, 'Test');
      expect(textResult).to.contains(modBlockwishlistBoMain.successfulUpdateMessage);
    });
  });

  [
    true,
    false,
    null,
  ].forEach((argKeepData: boolean|null, index: number) => {
    describe(`API : Check Data with keepData = ${argKeepData}`, async () => {
      it('should request the endpoint /modules/{technicalName}/reset', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${index}`, baseContext);

        // keepData == true => trigger onReset method (if exists, else keepData == false)
        // keepData == false => trigger uninstall & install method
        // keepData === null => Provide no data (equivalent to keepData == true)
        let apiData;

        if (argKeepData !== null) {
          apiData = {
            headers: {
              Authorization: `Bearer ${accessToken}`,
            },
            data: {
              keepData: argKeepData,
            },
          };
        } else {
          apiData = {
            headers: {
              Authorization: `Bearer ${accessToken}`,
            },
          };
        }

        const apiResponse = await apiContext.patch(`modules/${moduleInfo.technicalName}/reset`, apiData);
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it('should check the JSON Response keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseKeys${index}`, baseContext);
        expect(jsonResponse).to.have.all.keys(
          'moduleId',
          'technicalName',
          'moduleVersion',
          'installedVersion',
          'enabled',
          'installed',
        );
      });

      it('should check the JSON Response : `moduleId`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseModuleId${index}`, baseContext);

        expect(jsonResponse).to.have.property('moduleId');
        expect(jsonResponse.moduleId).to.be.a('number');
        expect(jsonResponse.moduleId).to.be.gt(moduleInfo.moduleId);
      });

      it('should check the JSON Response : `technicalName`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseTechnicalName${index}`, baseContext);

        expect(jsonResponse).to.have.property('technicalName');
        expect(jsonResponse.technicalName).to.be.a('string');
        expect(jsonResponse.technicalName).to.be.equal(moduleInfo.technicalName);
      });

      it('should check the JSON Response : `moduleVersion`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseModuleVersion${index}`, baseContext);

        expect(jsonResponse).to.have.property('moduleVersion');
        expect(jsonResponse.moduleVersion).to.be.a('string');
        expect(jsonResponse.moduleVersion).to.be.equal(moduleInfo.version);
      });

      it('should check the JSON Response : `installedVersion`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseInstalledVersion${index}`, baseContext);

        expect(jsonResponse).to.have.property('installedVersion');
        expect(jsonResponse.installedVersion).to.be.a('string');
        expect(jsonResponse.installedVersion).to.be.equal(moduleInfo.version);
      });

      it('should check the JSON Response : `enabled`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseEnabled${index}`, baseContext);

        expect(jsonResponse).to.have.property('enabled');
        expect(jsonResponse.enabled).to.be.a('boolean');
        expect(jsonResponse.enabled).to.be.equal(moduleInfo.enabled);
      });

      it('should check the JSON Response : `installed`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseInstalled${index}`, baseContext);

        expect(jsonResponse).to.have.property('installed');
        expect(jsonResponse.installed).to.be.a('boolean');
        expect(jsonResponse.installed).to.be.equal(moduleInfo.installed);
      });

      it('should check the module is reset', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkConfiguration${index}`, baseContext);

        await modBlockwishlistBoMain.reloadPage(page);

        const value = await modBlockwishlistBoMain.getInputValue(page, 'wishlistDefaultTitle');
        expect(value).to.equals(modBlockwishlistBoMain.defaultValueWishlistDefaultTitle);
      });

      if (index === 0) {
        it('should change the configuration in the module', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `changeConfiguration${index}`, baseContext);

          const textResult = await modBlockwishlistBoMain.setFormWording(page, 'Test');
          expect(textResult).to.contains(modBlockwishlistBoMain.successfulUpdateMessage);
        });
      }
    });
  });
});
