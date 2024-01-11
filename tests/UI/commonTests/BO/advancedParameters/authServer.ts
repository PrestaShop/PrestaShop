import helper from '@utils/helpers';
import testContext from '@utils/testContext';

import loginCommon from '@commonTests/BO/loginBO';

import dashboardPage from '@pages/BO/dashboard';
import apiAccessPage from '@pages/BO/advancedParameters/APIAccess';
import addNewApiAccessPage from '@pages/BO/advancedParameters/APIAccess/add';

import APIAccessData from '@data/faker/APIAccess';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

/**
 * Function to create API Access
 * @param apiAccess {APIAccessData} Data to set in API Access form
 * @param baseContext {string} String to identify the test
 */
function createAPIAccessTest(apiAccess: APIAccessData, baseContext: string = 'commonTests-createAPIAccessTest'): void {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAPIAccess: number = 0;

  describe('PRE-TEST: Create an API Access', async () => {
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

    it('should go to \'Advanced Parameters > API Access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiAccessPage.pageTitle);

      numberOfAPIAccess = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numberOfAPIAccess).to.gte(0);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiAccessPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIAccessPage', baseContext);

      await apiAccessPage.goToNewAPIAccessPage(page);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleCreate);
    });

    it('should create API Access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIAccess', baseContext);

      const textResult = await addNewApiAccessPage.addAPIAccess(page, apiAccess);
      expect(textResult).to.contains(addNewApiAccessPage.successfulCreationMessage);

      // Go back to list to get number of elements because creation form redirects to edition form
      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );
      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(numberOfAPIAccess + 1);
    });
  });
}

/**
 * Function to delete API Access
 * @param baseContext {string} String to identify the test
 */
function deleteAPIAccessTest(baseContext: string = 'commonTests-deleteAPIAccessTest'): void {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAPIAccess: number = 0;

  describe('PRE-TEST: Delete an API Access', async () => {
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

    it('should go to \'Advanced Parameters > API Access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiAccessPage.pageTitle);

      numberOfAPIAccess = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numberOfAPIAccess).to.gte(0);
    });

    it('should delete API Access', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAPIAccess', baseContext);

      const textResult = await apiAccessPage.deleteAPIAccess(page, 1);
      expect(textResult).to.equal(addNewApiAccessPage.successfulDeleteMessage);

      const numElements = await apiAccessPage.getNumberOfElementInGrid(page);
      expect(numElements).to.equal(0);
    });
  });
}

export {
  createAPIAccessTest,
  deleteAPIAccessTest,
};
