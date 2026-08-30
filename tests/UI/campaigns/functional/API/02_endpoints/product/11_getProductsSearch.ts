import testContext from '@utils/testContext';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {expect} from 'chai';

import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductsCreateTabDetailsPage,
  boProductsCreateTabStocksPage,
  boProductsPage,
  type BrowserContext,
  dataCurrencies,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_getProductsSearch';

describe('API : GET /products/search', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const productSearchPhrase: string = 'hummingbird';
  const clientScope: string = 'product_read';

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

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /products/search', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('products/search', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        params: {
          phrase: productSearchPhrase,
          resultsLimit: 10,
          isoCode: dataCurrencies.euro.isoCode,
          orderId: 1,
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
          'productId',
          'availableOutOfStock',
          'name',
          'taxRate',
          'formattedPrice',
          'priceTaxIncl',
          'priceTaxExcl',
          'stock',
          'location',
          'combinations',
          'customizationFields',
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

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);
      await boProductsPage.resetFilter(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItemsCount', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', productSearchPhrase);

      const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numProducts).to.eq(jsonResponse.length);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.length; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boProductsPage.filterProducts(page, 'id_product', {
          min: jsonResponse[idxItem].productId,
          max: jsonResponse[idxItem].productId,
        });

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productId = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
        expect(productId).to.equal(jsonResponse[idxItem].productId);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.equal(jsonResponse[idxItem].name);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
        //const productPriceFormattedTaxExcluded = await boProductsPage.getTextColumn(page, 'formattedPriceTaxExcluded', 1);
        //expect(productPriceFormattedTaxExcluded).to.equal(jsonResponse[idxItem].formattedPrice);

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
        //const productPriceTaxExcluded = await boProductsPage.getTextColumn(page, 'priceTaxExcluded', 1);
        //expect(productPriceTaxExcluded).to.equal(parseFloat(jsonResponse[idxItem].priceTaxExcl));

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
        //const productPriceTaxIncluded = await boProductsPage.getTextColumn(page, 'priceTaxIncluded', 1);
        //expect(productPriceTaxIncluded).to.equal(parseFloat(jsonResponse[idxItem].priceTaxIncl));

        // Go to Edit Page
        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);

        const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
        expect(productHeaderSummary.quantity).to.equal(`${jsonResponse[idxItem].stock} in stock`);

        // Go to Edit Page > Details Stock
        await boProductsCreatePage.goToTab(page, 'details');

        const numCustomizations = await boProductsCreateTabDetailsPage.countCustomizations(page);
        expect(numCustomizations).to.equals(Object.values(jsonResponse[idxItem].customizationFields).length);
        if (numCustomizations > 0) {
          const keyCustomization = Object.keys(jsonResponse[idxItem].customizationFields);

          for (let incCustomization = 1; incCustomization <= numCustomizations; incCustomization++) {
            const jsonCustomization = jsonResponse[idxItem].customizationFields[keyCustomization[incCustomization - 1]];

            const nthCustomization = await boProductsCreateTabDetailsPage.getCustomizationNth(page, incCustomization);
            expect(nthCustomization.id!).to.equal(jsonCustomization.customizationFieldId);
            expect(nthCustomization.label).to.equal(jsonCustomization.name);
            expect(nthCustomization.required).to.equal(jsonCustomization.required);
            expect(nthCustomization.type).to.equal(jsonCustomization.type.toString());

            expect(nthCustomization.id!.toString()).to.equal(keyCustomization[incCustomization - 1]);
          }
        }

        // Go to Edit Page > Combinations Stock
        if (await boProductsCreatePage.isTabVisible(page, 'combinations')) {
          await boProductsCreatePage.goToTab(page, 'combinations');
          await page.waitForTimeout(5000);

          const numCombinations = await boProductsCreateTabCombinationsPage.countCombinations(page);
          expect(numCombinations).to.equals(Object.values(jsonResponse[idxItem].combinations).length);
          if (numCombinations > 0) {
            const keyCombination = Object.keys(jsonResponse[idxItem].combinations);

            for (let incCombination = 1; incCombination <= numCombinations; incCombination++) {
              const jsonCombination = jsonResponse[idxItem].combinations[keyCombination[incCombination - 1]];
              const jsonCombinationAttributes = jsonCombination.attribute.split('-');

              const nthCombination = await boProductsCreateTabCombinationsPage.getCombinationNth(page, incCombination);
              expect(nthCombination.id).to.equal(jsonCombination.attributeCombinationId);
              for (let a = 0; a < jsonCombinationAttributes.length; a++) {
                expect(nthCombination.attribute).to.contains(`- ${jsonCombinationAttributes[a].trim()}`);
              }
              // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
              //expect(nthCombination.formattedPrice).to.equal(jsonCombination.formattedPrice);
              expect(nthCombination.reference).to.equal(jsonCombination.reference);
              expect(nthCombination.stock).to.equal(jsonCombination.stock.toString());

              expect(nthCombination.id!.toString()).to.equal(keyCombination[incCombination - 1]);

              const isModalVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, incCombination);
              expect(isModalVisible).to.eq(true);

              await page.waitForTimeout(5000);
              // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
              //const nthCombinationFinalPriceTaxExcluded = parseFloat(await boProductsCreateTabCombinationsPage.getCombinationModalValue(page, 'finalPriceTaxExcluded'));
              //expect(nthCombinationFinalPriceTaxExcluded).to.equals(jsonCombination.priceTaxExcluded);

              // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
              //const nthCombinationFinalPriceTaxIncluded = parseFloat(await boProductsCreateTabCombinationsPage.getCombinationModalValue(page, 'finalPriceTaxIncluded'));
              //expect(nthCombinationFinalPriceTaxIncluded).to.equals(jsonCombination.priceTaxIncluded);

              const nthCombinationLocation = await boProductsCreateTabCombinationsPage.getCombinationModalValue(page, 'location');
              expect(nthCombinationLocation).to.equals(jsonCombination.location);

              const isModalVisibleAfterClose = await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
              expect(isModalVisibleAfterClose).eq(false);
            }
          }
        } else {
          expect(0).to.equals(Object.values(jsonResponse[idxItem].combinations).length);
        }

        // Go to Edit Page > Tab Stock
        if (await boProductsCreatePage.isTabVisible(page, 'stock')) {
          await boProductsCreatePage.goToTab(page, 'stock');
          const valueStockLocation = await boProductsCreateTabStocksPage.getValue(page, 'location');
          expect(valueStockLocation).to.eq(jsonResponse[idxItem].location);

          const valueStockAvailableOutOfStock = await boProductsCreateTabStocksPage.getValue(page, 'availability_out_of_stock');
          expect(valueStockAvailableOutOfStock).to.eq(jsonResponse[idxItem].availableOutOfStock ? '1' : '0');
        } else {
          expect('').to.eq(jsonResponse[idxItem].location);
          expect(false).to.eq(jsonResponse[idxItem].availableOutOfStock);
        }

        // Go to Edit Page > Pricing Stock
        await boProductsCreatePage.goToTab(page, 'pricing');

        // @todo : https://github.com/PrestaShop/PrestaShop/issues/41452
        //const valueTaxRate = (await boProductsCreateTabPricingPage.getValue(page, 'id_tax_rules_group-rate'));
        //expect(valueTaxRate).to.eq(jsonResponse[idxItem].taxRate);

        // Returns to grid
        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

        const pageTitleListing = await boProductsPage.getPageTitle(page);
        expect(pageTitleListing).to.contains(boProductsPage.pageTitle);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boProductsPage.resetFilter(page);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });
});
