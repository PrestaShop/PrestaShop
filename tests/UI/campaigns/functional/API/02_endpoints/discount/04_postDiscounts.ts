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
  dataCurrencies,
  dataLanguages,
  FakerDiscount,
  type Page,
  utilsAPI,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

const baseContext: string = 'functional_API_endpoints_discount_postDiscounts';

describe('API : POST /admin-api/discounts', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idDiscount: number;

  const clientScope: string = 'discount_write';
  const dataDiscount: FakerDiscount = new FakerDiscount({
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
    discountCompatibilityTypes: [2, 3],
    priority: 5,
  });
  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');

  // Pre-condition: Enable discount
  setFeatureFlag(boFeatureFlagPage.featureFlagDiscount, true, `${baseContext}_preTest`);

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

  describe('API : Create the Discount', async () => {
    it('should request the endpoint /discounts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const dataRequest: any = {
        names: {
          [dataLanguages.english.locale]: dataDiscount.name,
          [dataLanguages.french.locale]: `${dataDiscount.name} (FRENCH)`,
        },
        description: dataDiscount.description,
        type: dataDiscount.discountType,
        priority: dataDiscount.priority,
        code: dataDiscount.discountCode,
        compatibleDiscountTypeIds: dataDiscount.discountCompatibilityTypes,
      };

      if (dataDiscount.discountReductionType === '%') {
        dataRequest.reductionPercent = dataDiscount.discountValue;
      }
      if (dataDiscount.discountReductionType === '€') {
        dataRequest.reductionAmount = {
          amount: dataDiscount.discountValue,
          currencyId: dataCurrencies.euro.id,
          taxIncluded: dataDiscount.discountTax === 'Tax included',
        };
      }
      if (dataDiscount.minimumPurchaseAmount) {
        dataRequest.minimumAmount = {
          amount: dataDiscount.minimumAmountValue,
          currencyId: dataCurrencies.euro.id,
          taxIncluded: dataDiscount.minimumAmountTax === 'Tax included',
        };
      }
      const apiResponse = await apiContext.post('discounts', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: dataRequest,
      });
      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41198
        'allowPartialUse',
        'carrierIds',
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41196
        'cheapestProduct',
        'code',
        'compatibleDiscountTypeIds',
        'countryIds',
        'customerGroupIds',
        'customerId',
        'description',
        'discountId',
        'enabled',
        'giftCombinationId',
        'giftProductId',
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41197
        'highlightInCart',
        'minimumAmount',
        'minimumProductQuantity',
        'names',
        'priority',
        'productConditions',
        'quantityPerUser',
        'reductionAmount',
        'reductionPercent',
        'totalQuantity',
        'type',
        'validFrom',
        'validTo',
      );
    });

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.discountId).to.be.gt(0);
      expect(jsonResponse.type).to.equal(dataDiscount.discountType);
      expect(jsonResponse.names).to.deep.equal({
        [dataLanguages.english.locale]: dataDiscount.name,
        [dataLanguages.french.locale]: `${dataDiscount.name} (FRENCH)`,
      });
      expect(jsonResponse.description).to.equal(dataDiscount.description);
      expect(jsonResponse.code).to.equal(dataDiscount.discountCode);
      expect(jsonResponse.enabled).to.equals(false);
      expect(jsonResponse.totalQuantity).to.equals(null);
      expect(jsonResponse.quantityPerUser).to.equals(null);
      expect(jsonResponse.reductionPercent).to.equals(null);
      expect(jsonResponse.reductionAmount).to.deep.equals({
        amount: dataDiscount.discountValue,
        currencyId: dataCurrencies.euro.id,
        taxIncluded: dataDiscount.discountTax === 'Tax included',
      });
      expect(jsonResponse.giftProductId).to.equals(null);
      expect(jsonResponse.giftCombinationId).to.equals(null);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/41196
      expect(jsonResponse.cheapestProduct).to.equals(false);
      expect(jsonResponse.productConditions).to.deep.equal([]);
      expect(jsonResponse.minimumProductQuantity).to.equals(0);
      expect(jsonResponse.minimumAmount).to.deep.equals({
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41199
        shippingIncluded: true,
        amount: dataDiscount.minimumAmountValue,
        currencyId: dataCurrencies.euro.id,
        taxIncluded: dataDiscount.minimumAmountTax === 'Tax included',
      });
      expect(jsonResponse.customerId).to.equals(null);
      expect(jsonResponse.customerGroupIds).to.deep.equal([]);
      expect(jsonResponse.carrierIds).to.deep.equal([]);
      expect(jsonResponse.countryIds).to.deep.equal([]);
      expect(jsonResponse.compatibleDiscountTypeIds).to.deep.equal(dataDiscount.discountCompatibilityTypes);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/41197
      expect(jsonResponse.highlightInCart).to.equals(false);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/41198
      expect(jsonResponse.allowPartialUse).to.equals(true);
      expect(jsonResponse.priority).to.equals(dataDiscount.priority);
      expect(jsonResponse.validFrom).to.contains(today);
      expect(jsonResponse.validTo).to.equals(null);
    });
  });

  describe('BackOffice : Check the Discount is created', async () => {
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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numDiscounts = await boDiscountsPage.resetAndGetNumberOfLines(page);
      expect(numDiscounts).to.equals(1);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterById', baseContext);

      await boDiscountsPage.filterDiscount(page, 'input', 'id_discount', jsonResponse.discountId.toString());

      const numDiscounts = await boDiscountsPage.getNumberOfElementInGrid(page);
      expect(numDiscounts).to.be.equal(1);

      idDiscount = parseInt(await boDiscountsPage.getTextColumn(page, 'id_discount', 1), 10);
    });

    it('should edit the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editDiscount', baseContext);

      await boDiscountsPage.goToEditDiscountPage(page, 1);

      const pageTitle = await boDiscountsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boDiscountsCreatePage.pageTitleEdit);
    });

    it('should check the JSON Response : `discountId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDiscountId', baseContext);

      expect(idDiscount).to.equals(jsonResponse.discountId);
    });

    it('should check the JSON Response : `type`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDiscountType', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'type');
      expect(jsonResponse.type).to.equals(value);
    });

    it('should check the JSON Response : `names` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesEN', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'names', dataLanguages.english.id);
      expect(jsonResponse.names[dataLanguages.english.locale]).to.be.equal(value);
    });

    it('should check the JSON Response : `names` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesFR', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'names', dataLanguages.french.id);
      expect(jsonResponse.names[dataLanguages.french.locale]).to.be.equal(value);
    });

    it('should check the JSON Response : `description`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescription', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'description');
      expect(jsonResponse.description).to.be.equal(value);
    });

    it('should check the JSON Response : `code`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCode', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'code');
      expect(jsonResponse.code).to.be.equal(value);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'enabled');
      expect(jsonResponse.enabled).to.be.equal(value === '1');
    });

    it('should check the JSON Response : `totalQuantity`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseTotalQuantity', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'totalQuantity');
      expect(jsonResponse.totalQuantity ?? '').to.be.equal(value);
    });

    it('should check the JSON Response : `quantityPerUser`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseQuantityPerUser', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'quantityPerUser');
      expect(jsonResponse.quantityPerUser ?? '').to.be.equal(value);
    });

    it('should check the JSON Response : `reductionPercent` && `reductionAmount`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseReductionPercent', baseContext);

      const reductionAmount = parseFloat(await boDiscountsCreatePage.getValue(page, 'reductionAmount'));
      const reductionType = await boDiscountsCreatePage.getValue(page, 'reductionType');

      if (reductionType === 'percentage') {
        expect(jsonResponse.reductionPercent).to.be.equals(reductionAmount);
        expect(jsonResponse.reductionAmount).to.be.equals(null);
      } else {
        const reductionAmountCurrency = parseInt(await boDiscountsCreatePage.getValue(page, 'reductionCurrency'), 10);
        const reductionIncludeTax = await boDiscountsCreatePage.getValue(page, 'reductionIncludeTax');

        expect(jsonResponse.reductionPercent).to.be.equals(null);
        expect(jsonResponse.reductionAmount).to.deep.equals({
          amount: reductionAmount,
          currencyId: reductionAmountCurrency,
          taxIncluded: reductionIncludeTax === '1',
        });
      }
    });

    it('should check the JSON Response : `giftProductId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseGiftProductId', baseContext);

      // No gift on cart_level
      expect(jsonResponse.giftProductId).to.be.equal(null);
    });

    it('should check the JSON Response : `giftCombinationId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseGiftCombinationId', baseContext);

      // No gift on cart_level
      expect(jsonResponse.giftCombinationId).to.be.equal(null);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41196
    it.skip('should check the JSON Response : `cheapestProduct`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCheapestProduct', baseContext);

      expect(jsonResponse.cheapestProduct).to.be.equal(false);
    });

    it('should check the JSON Response : `productConditions`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseProductConditions', baseContext);

      expect(jsonResponse.productConditions).to.deep.equal([]);
    });

    it('should check the JSON Response : `minimumAmount` && `minimumProductQuantity`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCartConditions', baseContext);

      const cartConditionType = await boDiscountsCreatePage.getValue(page, 'cartConditionType');
      const minimalAmount = parseFloat(await boDiscountsCreatePage.getValue(page, 'minimalAmount'));
      const minimalAmountCurrency = parseInt(await boDiscountsCreatePage.getValue(page, 'minimalAmountCurrency'), 10);
      const minimalAmountIncludeTax = await boDiscountsCreatePage.getValue(page, 'minimalAmountIncludeTax');
      const minimalProductQuantity = parseInt(await boDiscountsCreatePage.getValue(page, 'minimalProductQuantity'), 10);

      if (cartConditionType === 'none') {
        expect(jsonResponse.minimumProductQuantity).to.equals(0);
        expect(jsonResponse.minimumAmount).to.equals(null);
      } else if (cartConditionType === 'minimum_product_quantity') {
        expect(jsonResponse.minimumProductQuantity).to.equals(minimalProductQuantity);
        expect(jsonResponse.minimumAmount).to.equals(null);
      } else if (cartConditionType === 'minimum_amount') {
        expect(jsonResponse.minimumProductQuantity).to.equals(0);
        expect(jsonResponse.minimumAmount).to.deep.equals({
          // @todo : https://github.com/PrestaShop/PrestaShop/issues/41199
          shippingIncluded: true,
          amount: minimalAmount,
          currencyId: minimalAmountCurrency,
          taxIncluded: minimalAmountIncludeTax === '1',
        });
      } else {
        expect(['minimum_amount', 'minimum_product_quantity', 'none']).includes(cartConditionType);
      }
    });

    it('should check the JSON Response : `customerId` && `customerGroupIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCustomerEligibility', baseContext);

      const customerEligibilityType = await boDiscountsCreatePage.getValue(page, 'customerEligibilityType');

      if (customerEligibilityType === 'all_customers') {
        expect(jsonResponse.customerId).to.equals(null);
        expect(jsonResponse.customerGroupIds).to.deep.equal([]);
      } else if (customerEligibilityType === 'customer_groups') {
        // Not implemented
      } else if (customerEligibilityType === 'single_customer') {
        // Not implemented
      } else {
        expect(['all_customers', 'customer_groups', 'single_customer']).includes(customerEligibilityType);
      }
    });

    it('should check the JSON Response : `carrierIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCarrierIds', baseContext);

      // No filter Carrier on cart_level
      expect(jsonResponse.carrierIds).to.deep.equal([]);
    });

    it('should check the JSON Response : `countryIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCountryIds', baseContext);

      // No filter Country on cart_level
      expect(jsonResponse.countryIds).to.deep.equal([]);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41201
    it.skip('should check the JSON Response : `compatibleDiscountTypeIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCompatibleDiscountTypeIds', baseContext);

      const compatibleDiscountTypeIds = await boDiscountsCreatePage.getValues(page, 'compatibleDiscountTypeIds');
      expect(jsonResponse.compatibleDiscountTypeIds).to.deep.equals(
        compatibleDiscountTypeIds.map((value: string) => parseInt(value, 10)),
      );
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41197
    it.skip('should check the JSON Response : `highlightInCart`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseHighlightInCart', baseContext);

      expect(jsonResponse.highlightInCart).to.equals(false);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/41198
    it.skip('should check the JSON Response : `allowPartialUse`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAllowPartialUse', baseContext);

      expect(jsonResponse.allowPartialUse).to.equals(true);
    });

    it('should check the JSON Response : `priority`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePriority', baseContext);

      const value = parseInt(await boDiscountsCreatePage.getValue(page, 'priority'), 10);
      expect(jsonResponse.priority).to.be.equal(value);
    });

    it('should check the JSON Response : `validFrom`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseValidFrom', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'validFrom');
      expect(jsonResponse.validFrom).to.be.equal(value);
    });

    it('should check the JSON Response : `validTo`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseValidTo', baseContext);

      const value = await boDiscountsCreatePage.getValue(page, 'validTo');
      expect(jsonResponse.validTo ?? '').to.be.equal(value);
    });
  });

  describe('BackOffice : Delete the Discount', async () => {
    it('should return to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToDiscountsPage', baseContext);

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
