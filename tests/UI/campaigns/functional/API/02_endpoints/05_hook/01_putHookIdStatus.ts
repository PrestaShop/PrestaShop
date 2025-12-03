// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boDashboardPage,
  boDesignPositionsPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_hook_putHookIdStatus';

describe('API : PUT /hooks/{hookId}/status', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let hookId: number;
  let hookStatus: boolean;

  const clientScope: string = 'hook_write';

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

    it('should go to \'Design > Positions\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPositionsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.positionsLink,
      );
      await boDesignPositionsPage.closeSfToolBar(page);

      const pageTitle = await boDesignPositionsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDesignPositionsPage.pageTitle);
    });

    it('should get the hook informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getHookInformations', baseContext);

      hookId = await boDesignPositionsPage.getHookId(page, 0);
      expect(hookId).to.be.gt(0);

      hookStatus = await boDesignPositionsPage.getHookStatus(page, 0);
      expect(hookStatus).to.be.equal(true);
    });
  });

  [
    false,
    true,
  ].forEach((argStatus: boolean, index: number) => {
    describe(`API : Check Data with status = ${argStatus}`, async () => {
      it('should request the endpoint /hooks/{technicalName}/status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${index}`, baseContext);

        const apiResponse = await apiContext.put(`hooks/${hookId}/status`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: {
            enabled: argStatus,
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it('should check the JSON Response keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseKeys${index}`, baseContext);

        expect(jsonResponse).to.have.all.keys(
          'hookId',
          'enabled',
          'name',
          'title',
          'description',
        );
      });

      it('should check the JSON Response : `hookId`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseId${index}`, baseContext);

        expect(jsonResponse).to.have.property('hookId');
        expect(jsonResponse.hookId).to.be.a('number');
        expect(jsonResponse.hookId).to.be.equal(hookId);
      });

      it('should check the JSON Response : `enabled`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseEnabled${index}`, baseContext);

        expect(jsonResponse).to.have.property('enabled');
        expect(jsonResponse.enabled).to.be.a('boolean');
        expect(jsonResponse.enabled).to.be.equal(argStatus);
      });

      it('should go to \'Design > Positions\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToPositionsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.designParentLink,
          boDashboardPage.positionsLink,
        );
        await boDesignPositionsPage.closeSfToolBar(page);

        const pageTitle = await boDesignPositionsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDesignPositionsPage.pageTitle);
      });

      it('should search the hook', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `getHook${index}`, baseContext);

        const hookReloadedId = await boDesignPositionsPage.getHookId(page, 0);
        expect(hookReloadedId).to.be.gt(0);
        expect(hookReloadedId).to.be.equal(hookId);
      });

      it(`should check the hook is ${argStatus ? 'enabled' : 'disabled'}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStatus${index}`, baseContext);

        const hookReloadedStatus = await boDesignPositionsPage.getHookStatus(page, 0);
        expect(hookReloadedStatus).to.be.equal(argStatus);
      });
    });
  });
});
