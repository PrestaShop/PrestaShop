// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataModules,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_apiresources_installation_disableEnableModule';

describe('PrestaShop API Resources module - Disable/Enable module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BackOffice - Login', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });
  });

  [
    {
      state: false,
      action: 'disable',
    },
    {
      state: true,
      action: 'enable',
    },
  ].forEach((test: {state: boolean, action: string}, index: number) => {
    describe(`${utilsCore.capitalize(test.action)} the module`, async () => {
      it('should go to \'Modules > Module Manager\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToModuleManagerPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.modulesParentLink,
          boDashboardPage.moduleManagerLink,
        );
        await boModuleManagerPage.closeSfToolBar(page);

        const pageTitle = await boModuleManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
      });

      it(`should search the module ${dataModules.psApiResources.name}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModule${index}`, baseContext);

        const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psApiResources);
        expect(isModuleVisible).to.eq(true);
      });

      it(`should ${test.action} the module`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Module`, baseContext);

        const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psApiResources, test.action);

        if (test.state) {
          expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.psApiResources.tag));
        } else {
          expect(successMessage).to.eq(boModuleManagerPage.disableModuleSuccessMessage(dataModules.psApiResources.tag));
        }
      });

      it('should go to \'Advanced Parameters > API Client\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAdminAPIPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await apiClientPage.getPageTitle(page);
        expect(pageTitle).to.eq(apiClientPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkThatNoRecordFound${index}`, baseContext);

        const noRecordsFoundText = await apiClientPage.getTextForEmptyTable(page);
        expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New API Client page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewAPIClientPage${index}`, baseContext);

        await apiClientPage.goToNewAPIClientPage(page);

        const pageTitle = await addNewApiClientPage.getPageTitle(page);
        expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
      });

      it('should check that scopes from Core are present and enabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkScopesCore${index}`, baseContext);

        const scopes = await addNewApiClientPage.getApiScopes(page, '__core_scopes');
        expect(scopes.length).to.be.eq(0);

        // eslint-disable-next-line no-restricted-syntax
        for (const scope of scopes) {
          const isScopeDisabled = await addNewApiClientPage.isAPIScopeDisabled(page, scope);
          expect(isScopeDisabled).to.be.equal(false);
        }
      });

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34496
      it(`should check that scopes from Module are present and ${test.action}d`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkScopesModule${index}`, baseContext);

        this.skip();
        /*
        const scopes = await addNewApiClientPage.getApiScopes(page, dataModules.psApiResources.tag);

        // eslint-disable-next-line no-restricted-syntax
        for (const scope of scopes) {
          const isScopeDisabled = await addNewApiClientPage.isAPIScopeDisabled(page, scope);
          expect(isScopeDisabled).to.be.equal(!test.state);
        }
        */
      });
    });
  });
});
