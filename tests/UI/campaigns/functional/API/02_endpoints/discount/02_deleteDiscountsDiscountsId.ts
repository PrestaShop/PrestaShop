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
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'functional_API_endpoints_discount_deleteDiscountsDiscountsId';

describe('API : DELETE /admin-api/discounts/{discountId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let discountId: number;

  const clientScope: string = 'discount_write';
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

      discountId = parseInt(await boDiscountsPage.getTextColumn(page, 'id_discount', 1), 10);
    });
  });

  describe('API : Delete the Discount', async () => {
    it('should request the endpoint /discounts/{discountId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`discounts/${discountId}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Discount is deleted', async () => {
    it('should reload the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

      await boDiscountsPage.reloadPage(page);

      const pageTitle = await boDiscountsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsPage.pageTitle);

      const numDiscounts = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(numDiscounts).to.be.equal(0);
    });
  });

  // Post-condition: Disable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, false, `${baseContext}_postTest`);
});
