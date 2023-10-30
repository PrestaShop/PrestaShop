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

const baseContext: string = 'functional_API_clientCredentialGrantFlow_internalAuthServer_resourceEndpoint';

describe('API : Internal Auth Server - Resource Endpoint', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let accessTokenExpired: string;
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

  describe('BO - Advanced Parameter - API Access : CRUD', async () => {
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

  describe('Resource Endpoint', async () => {
    it('should request the authorization endpoint and fetch valid token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthorizationEndpoint', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: clientAccess.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: 'hook_read',
        },
      });
      expect(apiResponse.status()).to.eq(200);

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.access_token).to.be.a('string');

      accessToken = jsonResponse.access_token;
      accessTokenExpired = api.setAccessTokenAsExpired(accessToken);
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 without access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithoutAccessToken', baseContext);

      const apiResponse = await apiContext.get('api/hook-status/1');
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with invalid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithInvalidAccessToken', baseContext);

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: 'Bearer INVALIDTOKEN',
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with expired access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithExpiredAccessToken', baseContext);

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessTokenExpired}`,
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithValidAccessToken', baseContext);

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });

      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('id');
      expect(jsonResponse.id).to.be.a('number');
      expect(jsonResponse).to.have.property('active');
      expect(jsonResponse.active).to.be.a('boolean');
    });
  });

  deleteAPIAccessTest(`${baseContext}_postTest_0`);

  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
