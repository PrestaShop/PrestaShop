// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import apiAccessPage from '@pages/BO/advancedParameters/APIAccess';
import addNewApiAccessPage from '@pages/BO/advancedParameters/APIAccess/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_apiresources_installation_disableEnableModule';

describe('PrestaShop API Resources module - Disable/Enable module', async () => {
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

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

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
    describe(`${basicHelper.capitalize(test.action)} the module`, async () => {
      it('should go to \'Modules > Module Manager\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToModuleManagerPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.modulesParentLink,
          dashboardPage.moduleManagerLink,
        );
        await moduleManagerPage.closeSfToolBar(page);

        const pageTitle = await moduleManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
      });

      it(`should search the module ${Modules.psApiResources.name}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModule${index}`, baseContext);

        const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psApiResources);
        expect(isModuleVisible).to.eq(true);
      });

      it(`should ${test.action} the module`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Module`, baseContext);

        const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psApiResources, test.action);

        if (test.state) {
          expect(successMessage).to.eq(moduleManagerPage.enableModuleSuccessMessage(Modules.psApiResources.tag));
        } else {
          expect(successMessage).to.eq(moduleManagerPage.disableModuleSuccessMessage(Modules.psApiResources.tag));
        }
      });

      it('should go to \'Advanced Parameters > API Access\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAuthorizationServerPage${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.authorizationServerLink,
        );

        const pageTitle = await apiAccessPage.getPageTitle(page);
        expect(pageTitle).to.eq(apiAccessPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkThatNoRecordFound${index}`, baseContext);

        const noRecordsFoundText = await apiAccessPage.getTextForEmptyTable(page);
        expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New API Access page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewAPIAccessPage${index}`, baseContext);

        await apiAccessPage.goToNewAPIAccessPage(page);

        const pageTitle = await addNewApiAccessPage.getPageTitle(page);
        expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleCreate);
      });

      it('should check that scopes from Core are present and enabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkScopesCore${index}`, baseContext);

        const scopes = await addNewApiAccessPage.getApiScopes(page, '__core_scopes');
        console.log(scopes);
        expect(scopes.length).to.be.gt(0);

        // eslint-disable-next-line no-restricted-syntax
        for (const scope of scopes) {
          const isScopeDisabled = await addNewApiAccessPage.isAPIScopeDisabled(page, scope);
          expect(isScopeDisabled).to.be.equal(false);
        }
      });

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34496
      it(`should check that scopes from Module are present and ${test.action}d`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkScopesModule${index}`, baseContext);

        this.skip();
        /*
        const scopes = await addNewApiAccessPage.getApiScopes(page, Modules.psApiResources.tag);

        // eslint-disable-next-line no-restricted-syntax
        for (const scope of scopes) {
          const isScopeDisabled = await addNewApiAccessPage.isAPIScopeDisabled(page, scope);
          expect(isScopeDisabled).to.be.equal(!test.state);
        }
        */
      });
    });
  });

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
