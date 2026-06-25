import testContext from '@utils/testContext';

import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boDashboardPage,
  boDiscountsPage,
  boFeatureFlagPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_discount_getDiscountTypes';

describe('API : GET /admin-api/discounts/types', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  const clientScope: string = 'discount_read';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-condition: Enable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, true, `${baseContext}_preTest`);

  describe('API : Fetch the access token', async () => {
    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /discounts/types', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('discounts/types', {
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

      expect(jsonResponse.length).to.be.gt(0);
      for (let i:number = 0; i < jsonResponse.length; i++) {
        expect(jsonResponse[i]).to.have.all.keys(
          'core',
          'descriptions',
          'discountTypeId',
          'enabled',
          'names',
          'type',
        );
      }
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

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.closeSfToolBar(page);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    //@todo : https://github.com/PrestaShop/PrestaShop/issues/41110
    it.skip('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponse', baseContext);
    });
  });

  // Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
