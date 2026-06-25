import {expect} from 'chai';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import testContext from '@utils/testContext';

import {
  type APIRequestContext,
  boDashboardPage,
  boDiscountsCreatePage,
  boDiscountsPage,
  boFeatureFlagPage,
  boLoginPage,
  type BrowserContext,
  FakerDiscount,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'functional_API_endpoints_discount_getDiscounts';

describe('API : GET /admin-api/discounts', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numberOfDiscounts: number;

  const clientScope: string = 'discount_read';
  const discountData: FakerDiscount = new FakerDiscount({
    discountType: 'cart_level',
    name: 'Test',
    noProductCondition: true,
    minimumPurchaseAmount: true,
    minimumAmountValue: 50,
    minimumAmountTax: 'Tax included',
    discountValue: 10,
    discountReductionType: '€',
    discountTax: 'Tax included',
    discountCode: 'test',
  });

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

  describe('BackOffice : Create a discount', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);

      const numDiscounts = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(numDiscounts).to.be.equal(0);
    });

    it(`should click on create discount button and choose the type '${discountData.discountType}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDiscountType', baseContext);

      await boDiscountsPage.clickOnCreateDiscountButton(page);
      await boDiscountsPage.selectDiscountType(page, discountData.discountType!);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitle);
    });

    it('should create a discount with code', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createDiscountWithCode', baseContext);

      const errorMessage = await boDiscountsCreatePage.createDiscount(page, discountData);
      expect(errorMessage).to.contains(boDiscountsCreatePage.successfulCreationMessage);
    });

    it('should return to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);

      const numDiscounts = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(numDiscounts).to.be.equal(1);
    });
  });

  describe('API : Get all discounts', async () => {
    it('should request the endpoint /discounts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('discounts', {
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
        'totalItems',
        'orderBy',
        'sortOrder',
        'limit',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'discountId',
          'type',
          'name',
          'enabled',
          'code',
        );
      }
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should reset all filters and get number of discounts in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfDiscounts = await boDiscountsPage.resetAndGetNumberOfLines(page);
      expect(numberOfDiscounts).to.be.equal(jsonResponse.totalItems);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boDiscountsPage.resetFilter(page);
        await boDiscountsPage.filterDiscount(page, 'input', 'id_discount', jsonResponse.items[idxItem].discountId.toString());

        const numAttributesAfterFilter = await boDiscountsPage.getNumberOfElementInGrid(page);
        expect(numAttributesAfterFilter).to.be.equal(1);

        const discountId = parseInt((await boDiscountsPage.getTextColumn(page, 'id_discount', 1)).toString(), 10);
        expect(discountId).to.equal(jsonResponse.items[idxItem].discountId);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41110
        //const type = await boDiscountsPage.getTextColumn(page, 'type', 1);
        //expect(type).to.equal(jsonResponse.items[idxItem].type);

        const name = await boDiscountsPage.getTextColumn(page, 'name', 1);
        expect(name).to.equal(jsonResponse.items[idxItem].name);

        const enabled = await boDiscountsPage.getDiscountStatus(page, 1);
        expect(enabled).to.equal(jsonResponse.items[idxItem].enabled);

        const code = await boDiscountsPage.getTextColumn(page, 'code', 1);
        expect(code).to.equal(jsonResponse.items[idxItem].code);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boDiscountsPage.resetFilter(page);

      const numDiscounts = await boDiscountsPage.resetAndGetNumberOfLines(page);
      expect(numDiscounts).to.be.equal(numberOfDiscounts);
    });
  });

  describe('BackOffice : Delete the Discount', async () => {
    it('should return to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToDiscountsPageForDeletion', baseContext);

      await boDashboardPage.closeSfToolBar(page);
      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);
    });

    it('should delete discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount', baseContext);

      const textResult = await boDiscountsPage.deleteDiscount(page, 1);
      expect(textResult).to.contains(boDiscountsPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numDiscounts = await boDiscountsPage.resetAndGetNumberOfLines(page);
      expect(numDiscounts).to.be.equal(0);
    });
  });

  // Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
