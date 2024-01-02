import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTest
import loginCommon from '@commonTests/BO/loginBO';
import {deleteAPIAccessTest} from '@commonTests/BO/advancedParameters/authServer';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
// Import BO pages
import apiAccessPage from '@pages/BO/advancedParameters/APIAccess';
import addNewApiAccessPage from '@pages/BO/advancedParameters/APIAccess/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIAccessData from '@data/faker/APIAccess';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_internalAuthServer_authorizationEndpoint';

describe('API : Internal Auth Server - Authorization Endpoint', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let apiContext: APIRequestContext;
  let clientSecret: string;

  const clientAccess: APIAccessData = new APIAccessData({
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

  describe('API Access : Fetch the client secret', async () => {
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
  });

  describe('Authorization Endpoint', async () => {
    it('should request the endpoint /admin-dev/api/oauth2/token with method GET', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodGET', baseContext);

      const apiResponse = await apiContext.get('api/oauth2/token');
      expect(apiResponse.status()).to.eq(405);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOST', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token');
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTUnusefulData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with invalid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTInvalidData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: 'bad_client_id',
          client_secret: 'bad_client_secret',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(401);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with valid + unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidAndUnusefulData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: clientAccess.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(200);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with valid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: clientAccess.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('token_type');
      expect(jsonResponse.token_type).to.be.eq('Bearer');
      expect(jsonResponse).to.have.property('expires_in');
      expect(jsonResponse.expires_in).to.be.eq(clientAccess.tokenLifetime);
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');
    });
  });

  deleteAPIAccessTest(`${baseContext}_postTest_0`);

  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
