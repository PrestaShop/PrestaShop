// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import positionsPage from '@pages/BO/design/positions';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_hook_getAPIHookStatusId';

describe('API : GET /api/hook-status/{id}', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let apiContext: APIRequestContext;
  let accessToken: string;
  let jsonResponse: any;
  let idHook: number;
  let statusHook: boolean;

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

  describe('BackOffice : Expected data', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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
});
