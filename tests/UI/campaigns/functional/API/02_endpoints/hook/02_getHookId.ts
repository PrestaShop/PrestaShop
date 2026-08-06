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

const baseContext: string = 'functional_API_endpoints_hook_getHookId';

describe('API : GET /hooks/{hookId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let hookId: number;
  let statusHook: boolean;
  let nameHook: string;
  let titleHook: string;
  let descriptionHook: string;

  const clientScope: string = 'hook_read';

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

      statusHook = await boDesignPositionsPage.getHookStatus(page, 0);
      expect(statusHook).to.be.equal(true);

      nameHook = await boDesignPositionsPage.getHookName(page, 0);
      expect(nameHook.length).to.be.gt(0);

      titleHook = await boDesignPositionsPage.getHookTitle(page, 0);
      expect(titleHook.length).to.be.gte(0);

      descriptionHook = await boDesignPositionsPage.getHookDescription(page, 0);
      expect(descriptionHook.length).to.be.gte(0);
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /hooks/{hookId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`hooks/${hookId}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'hookId',
        'enabled',
        'name',
        'title',
        'description',
      );
    });

    it('should check the JSON Response : `hookId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseId', baseContext);

      expect(jsonResponse).to.have.property('hookId');
      expect(jsonResponse.hookId).to.be.a('number');
      expect(jsonResponse.hookId).to.be.equal(hookId);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.be.equal(statusHook);
    });

    it('should check the JSON Response : `name`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseName', baseContext);

      expect(jsonResponse).to.have.property('name');
      expect(jsonResponse.name).to.be.a('string');
      expect(jsonResponse.name).to.be.equal(nameHook);
    });

    it('should check the JSON Response : `title`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseTitle', baseContext);

      expect(jsonResponse).to.have.property('title');
      expect(jsonResponse.title).to.be.a('string');
      expect(jsonResponse.title).to.be.equal(titleHook);
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      expect(jsonResponse).to.have.property('description');
      expect(jsonResponse.description).to.be.a('string');
      expect(jsonResponse.description).to.be.equal(descriptionHook);
    });
  });
});
