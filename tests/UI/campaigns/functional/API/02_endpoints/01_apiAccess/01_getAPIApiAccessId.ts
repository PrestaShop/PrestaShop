// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createAPIAccessTest, deleteAPIAccessTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiAccessPage from 'pages/BO/advancedParameters/APIAccess';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIAccessData from '@data/faker/APIAccess';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_apiAccess_getAPIApiAccessId';

describe('API : GET /api/api-access/{apiAccessId}', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let apiContext: APIRequestContext;
  let accessToken: string;
  let jsonResponse: any;
  let idApiAccess: number;

  const createAPIAccess: APIAccessData = new APIAccessData({
    scopes: [
      'hook_write',
      'api_access_read',
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.BO.URL);

    if (!global.GENERATE_FAILED_STEPS) {
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34297
      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(200);

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.access_token).to.be.a('string');

      accessToken = jsonResponse.access_token;
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  // Pre-condition: Create an API Access
  createAPIAccessTest(createAPIAccess, `${baseContext}_preTest`);

  describe('BackOffice : Expected data', async () => {
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

    it('should get informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getInformations', baseContext);

      idApiAccess = parseInt(await apiAccessPage.getTextColumn(page, 'id_api_access', 1), 10);
      expect(idApiAccess).to.be.gt(0);
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /admin-dev/api/api-access/{apiAccessId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`api/api-access/${idApiAccess}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response : `apiAccessId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseApiAccessId', baseContext);

      expect(jsonResponse).to.have.property('apiAccessId');
      expect(jsonResponse.apiAccessId).to.be.a('number');
      expect(jsonResponse.apiAccessId).to.be.equal(idApiAccess);
    });

    it('should check the JSON Response : `clientName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseClientName', baseContext);

      expect(jsonResponse).to.have.property('clientName');
      expect(jsonResponse.clientName).to.be.a('string');
      expect(jsonResponse.clientName).to.be.equal(createAPIAccess.clientName);
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      expect(jsonResponse).to.have.property('description');
      expect(jsonResponse.description).to.be.a('string');
      expect(jsonResponse.description).to.be.equal(createAPIAccess.description);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.be.equal(createAPIAccess.enabled);
    });
  });

  // Pre-condition: Create an API Access
  deleteAPIAccessTest(`${baseContext}_postTest`);

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
