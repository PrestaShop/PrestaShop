// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setFeatureFlag} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import authorizationServerPage from '@pages/BO/advancedParameters/authorizationServer';
import addNewAuthorizedAppPage from '@pages/BO/advancedParameters/authorizationServer/add';
import viewAuthorizedAppPage from '@pages/BO/advancedParameters/authorizationServer/view';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import AuthorizedApplicationData from '@data/faker/authorizedApplication';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_authorizationServer_viewDetail';

describe('BO - Advanced Parameter - Authorization Server : View detail', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const application: AuthorizedApplicationData = new AuthorizedApplicationData({
    appName: 'Application XYZ',
    description: 'Description ABC',
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BO - Advanced Parameter - Authorization Server : CRUD', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    describe('Create an application', async () => {
      it('should go to \'Advanced Parameters > Authorization Server\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.authorizationServerLink,
        );

        const pageTitle = await authorizationServerPage.getPageTitle(page);
        await expect(pageTitle).to.eq(authorizationServerPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

        const noRecordsFoundText = await authorizationServerPage.getTextForEmptyTable(page);
        await expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New Authorized App page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewAuthorizedAppPage', baseContext);

        await authorizationServerPage.goToNewAuthorizedAppPage(page);

        const pageTitle = await addNewAuthorizedAppPage.getPageTitle(page);
        await expect(pageTitle).to.eq(addNewAuthorizedAppPage.pageTitleCreate);
      });

      it('should create authorized app', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAuthorizedApp', baseContext);

        const textResult = await addNewAuthorizedAppPage.addAuthorizedApplication(page, application);
        await expect(textResult).to.equal(addNewAuthorizedAppPage.successfulCreationMessage);

        const numElements = await authorizationServerPage.getNumberOfElementInGrid(page);
        await expect(numElements).to.equal(1);
      });
    });

    describe('View the application', async () => {
      it('should go to the details page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDetailsPage', baseContext);

        await authorizationServerPage.goToViewAuthorizedAppPage(page, 1);

        const pageTitle = await viewAuthorizedAppPage.getPageTitle(page);
        await expect(pageTitle).to.eq(viewAuthorizedAppPage.pageTitle(application.appName));
      });
      it('should check details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDetailsInformations', baseContext);

        const appInformation = await viewAuthorizedAppPage.getAppInformation(page);
        await expect(appInformation.appName).to.eq(application.appName);
        await expect(appInformation.description).to.eq(application.description);
      });
    });

    describe('Delete the application', async () => {
      it('should go to \'Advanced Parameters > Authorization Server\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnToAuthorizationServerPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.authorizationServerLink,
        );

        const pageTitle = await authorizationServerPage.getPageTitle(page);
        await expect(pageTitle).to.eq(authorizationServerPage.pageTitle);
      });
      it('should delete authorized app', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteAuthorizedApp', baseContext);

        const textResult = await authorizationServerPage.deleteAuthorizationApplication(page, 1);
        await expect(textResult).to.equal(addNewAuthorizedAppPage.successfulDeleteMessage);

        const numElements = await authorizationServerPage.getNumberOfElementInGrid(page);
        await expect(numElements).to.equal(0);
      });
    });
  });

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
