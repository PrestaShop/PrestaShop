// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIAccessTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiAccessPage from 'pages/BO/advancedParameters/APIAccess';
import addNewApiAccessPage from '@pages/BO/advancedParameters/APIAccess/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import dashboardPage from '@pages/BO/dashboard';
import positionsPage from '@pages/BO/design/positions';

// Import data
import APIAccessData from '@data/faker/APIAccess';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_hook_getAPIHookStatusId';

describe('API : GET /api/hook-status/{id}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idHook: number;
  let statusHook: boolean;
  let clientSecret: string;

  const clientAccess: APIAccessData = new APIAccessData({
    enabled: true,
    scopes: [
      'hook_read',
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.BO.URL);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  describe('BackOffice : Fetch the access token', async () => {
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

      const textResult = await addNewApiAccessPage.addAPIAccess(page, clientAccess);
      expect(textResult).to.contains(addNewApiAccessPage.successfulCreationMessage);

      const textMessage = await addNewApiAccessPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiAccessPage.apiAccessGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await addNewApiAccessPage.copyClientSecret(page);

      clientSecret = await addNewApiAccessPage.getClipboardText(page);
      expect(clientSecret.length).to.be.gt(0);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: clientAccess.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: 'hook_read',
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');

      accessToken = jsonResponse.access_token;
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Design > Positions\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.designParentLink,
        dashboardPage.positionsLink,
      );
      await positionsPage.closeSfToolBar(page);

      const pageTitle = await positionsPage.getPageTitle(page);
      expect(pageTitle).to.contains(positionsPage.pageTitle);
    });

    it('should get the hook informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getHookInformations', baseContext);

      idHook = await positionsPage.getHookId(page, 0);
      expect(idHook).to.be.gt(0);

      statusHook = await positionsPage.getHookStatus(page, 0);
      expect(statusHook).to.be.equal(true);
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /admin-dev/api/hook-status/{id}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`api/hook-status/${idHook}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response : `id`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseId', baseContext);

      expect(jsonResponse).to.have.property('id');
      expect(jsonResponse.id).to.be.a('number');
      expect(jsonResponse.id).to.be.equal(idHook);
    });

    it('should check the JSON Response : `active`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseActive', baseContext);

      expect(jsonResponse).to.have.property('active');
      expect(jsonResponse.active).to.be.a('boolean');
      expect(jsonResponse.active).to.be.equal(statusHook);
    });
  });

  // Pre-condition: Create an API Access
  deleteAPIAccessTest(`${baseContext}_postTest`);

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
