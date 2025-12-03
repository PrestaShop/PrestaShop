import testContext from '@utils/testContext';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {disableEcoTaxTest, enableEcoTaxTest} from '@commonTests/BO/international/ecoTax';
import {expect} from 'chai';

import {faker} from '@faker-js/faker';
import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  boProductsCreateTabDetailsPage,
  boProductsCreateTabOptionsPage,
  boProductsCreateTabPricingPage,
  boProductsCreateTabSEOPage,
  boProductsCreateTabShippingPage,
  boProductsCreateTabStocksPage,
  type BrowserContext,
  dataBrands,
  dataCategories,
  dataLanguages,
  dataTaxRules,
  FakerProduct,
  type Page,
  utilsAPI,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_patchProductId';

describe('API : PATCH /products/{productId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let idProduct: number;
  let jsonResponse: any;
  let hasAvailableDate: boolean = false;

  const clientScope: string = 'product_write';
  const createProduct: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });

  // Pre Condition : Enable ecotax
  enableEcoTaxTest(`${baseContext}_preTest_0`);

  // Pre Condition : Create a product
  createProductTest(createProduct, `${baseContext}_preTest_1`);

  describe('API : PATCH /products/{productId}', async () => {
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

    describe('BackOffice : Fetch the ID product', async () => {
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

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForCreation', baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', createProduct.name);

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(createProduct.name);

        idProduct = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
        expect(idProduct).to.be.gt(0);
      });

      it('should go to edit product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });
    });

    [
      {
        propertyName: 'enabled',
        propertyValue: false,
      },
      {
        propertyName: 'additionalShippingCost',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'availableDate',
        propertyValue: utilsDate.getDateFormat('yyyy-mm-dd', 'future'),
      },
      {
        propertyName: 'availableForOrder',
        propertyValue: false,
      },
      {
        propertyName: 'availableLaterLabels',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'availableNowLabels',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'condition',
        propertyValue: 'used',
      },
      {
        propertyName: 'deliveryTimeInStockNotes',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'deliveryTimeOutOfStockNotes',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'deliveryTimeNoteType',
        propertyValue: 2,
      },
      {
        propertyName: 'depth',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'descriptions',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'ecotaxTaxExcluded',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'gtin',
        propertyValue: '3234567890126',
      },
      {
        propertyName: 'height',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'isbn',
        propertyValue: '978-3-16-148410-0',
      },
      {
        propertyName: 'linkRewrites',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.slug(),
          [dataLanguages.french.locale]: faker.lorem.slug(),
        },
      },
      {
        propertyName: 'lowStockThreshold',
        propertyValue: faker.number.int({min: 0, max: 12345}),
      },
      {
        propertyName: 'manufacturerId',
        propertyValue: dataBrands.brand_2.id,
      },
      {
        propertyName: 'metaDescriptions',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'metaTitles',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'minimalQuantity',
        propertyValue: faker.number.int({min: 0, max: 12345}),
      },
      {
        propertyName: 'mpn',
        propertyValue: faker.string.alphanumeric({length: 15}).toString(),
      },
      {
        propertyName: 'names',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentence(),
          [dataLanguages.french.locale]: faker.lorem.sentence(),
        },
      },
      {
        propertyName: 'onlineOnly',
        propertyValue: true,
      },
      {
        propertyName: 'onSale',
        propertyValue: false,
      },
      {
        propertyName: 'packStockType',
        propertyValue: 3,
      },
      {
        propertyName: 'priceTaxExcluded',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'redirectOption',
        propertyValue: {
          redirectType: '301-category',
          redirectTarget: dataCategories.art.id,
        },
      },
      {
        propertyName: 'reference',
        propertyValue: faker.string.alphanumeric({length: 15}),
      },
      {
        propertyName: 'shortDescriptions',
        propertyValue: {
          [dataLanguages.english.locale]: faker.lorem.sentences(5),
          [dataLanguages.french.locale]: faker.lorem.sentence(5),
        },
      },
      {
        propertyName: 'showCondition',
        propertyValue: true,
      },
      {
        propertyName: 'showPrice',
        propertyValue: true,
      },
      {
        propertyName: 'taxRulesGroupId',
        propertyValue: dataTaxRules.at(-1)!.id,
      },
      {
        propertyName: 'unitPriceTaxExcluded',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'unity',
        propertyValue: 'per kilo',
      },
      {
        propertyName: 'upc',
        propertyValue: '72527273070',
      },
      {
        propertyName: 'visibility',
        propertyValue: 'search',
      },
      {
        propertyName: 'width',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'weight',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
      {
        propertyName: 'wholesalePrice',
        propertyValue: faker.number.float({fractionDigits: 2}),
      },
    ].forEach((data: { propertyName: string, propertyValue: boolean|string|number|object|any}) => {
      describe(`Update the property \`${data.propertyName}\` with API and check in BO`, async () => {
        it('should request the endpoint /products/{productId}', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${data.propertyName}`, baseContext);

          const dataPatch: any = {};
          dataPatch[data.propertyName] = data.propertyValue;

          const apiResponse = await apiContext.patch(`products/${idProduct}`, {
            headers: {
              Authorization: `Bearer ${accessToken}`,
            },
            data: dataPatch,
          });
          expect(apiResponse.status()).to.eq(200);
          expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
          expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

          jsonResponse = await apiResponse.json();
          if (data.propertyName === 'redirectOption') {
            expect(jsonResponse).to.have.property('redirectTarget');
            expect(jsonResponse.redirectTarget).to.deep.equal(data.propertyValue.redirectTarget);
            expect(jsonResponse).to.have.property('redirectType');
            expect(jsonResponse.redirectType).to.deep.equal(data.propertyValue.redirectType);
          } else {
            expect(jsonResponse).to.have.property(data.propertyName);
            expect(jsonResponse[data.propertyName]).to.deep.equal(data.propertyValue);
          }
        });

        it('should check the JSON Response keys', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkResponseKeys${data.propertyName}`, baseContext);

          const jsonResponseKeys: string[] = [
            'enabled',
            'additionalShippingCost',
            'availableForOrder',
            'availableLaterLabels',
            'availableNowLabels',
            'carrierReferenceIds',
            'categories',
            'condition',
            'coverThumbnailUrl',
            'defaultCategoryId',
            'deliveryTimeInStockNotes',
            'deliveryTimeNoteType',
            'deliveryTimeOutOfStockNotes',
            'depth',
            'descriptions',
            'ecotaxTaxExcluded',
            'ecotaxTaxIncluded',
            'gtin',
            'height',
            'isbn',
            'linkRewrites',
            'location',
            'lowStockAlertEnabled',
            'lowStockThreshold',
            'manufacturerId',
            'metaDescriptions',
            'metaTitles',
            'minimalQuantity',
            'mpn',
            'names',
            'onSale',
            'onlineOnly',
            'outOfStockType',
            'packStockType',
            'priceTaxExcluded',
            'priceTaxIncluded',
            'productId',
            'quantity',
            'redirectType',
            'reference',
            'shopIds',
            'shortDescriptions',
            'showCondition',
            'showPrice',
            'tags',
            'taxRulesGroupId',
            'type',
            'unitPriceRatio',
            'unitPriceTaxExcluded',
            'unitPriceTaxIncluded',
            'unity',
            'upc',
            'visibility',
            'weight',
            'wholesalePrice',
            'width',
          ];

          if (data.propertyName === 'availableDate') {
            hasAvailableDate = true;
          }
          if (hasAvailableDate) {
            jsonResponseKeys.push('availableDate');
          }
          if (jsonResponse.redirectType !== 'default') {
            jsonResponseKeys.push('redirectTarget');
          }
          expect(jsonResponse).to.have.all.keys(jsonResponseKeys);
        });

        it(`should check that the property "${data.propertyName}"`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProperty${data.propertyName}`, baseContext);

          await boProductsCreatePage.reloadPage(page);

          if (data.propertyName === 'enabled') {
            const valueProperty = await boProductsCreatePage.getProductStatus(page);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'additionalShippingCost') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valueProperty = parseFloat(
              await boProductsCreateTabShippingPage.getValue(page, 'additional_shipping_cost'),
            ).toFixed(2);
            expect(parseFloat(valueProperty)).to.equal(data.propertyValue);
          } else if (data.propertyName === 'availableDate') {
            await boProductsCreatePage.goToTab(page, 'stock');

            const valueProperty = await boProductsCreateTabStocksPage.getValue(page, 'available_date');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'availableForOrder') {
            await boProductsCreatePage.goToTab(page, 'options');

            const valueProperty = await boProductsCreateTabOptionsPage.getValue(page, 'available_for_order');
            expect(valueProperty).to.equal(data.propertyValue ? '1' : '0');
          } else if (data.propertyName === 'availableLaterLabels') {
            await boProductsCreatePage.goToTab(page, 'stock');

            const valuePropertyEN = await boProductsCreateTabStocksPage.getValue(
              page,
              'available_later',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabStocksPage.getValue(
              page,
              'available_later',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'availableNowLabels') {
            await boProductsCreatePage.goToTab(page, 'stock');

            const valuePropertyEN = await boProductsCreateTabStocksPage.getValue(
              page,
              'available_now',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabStocksPage.getValue(
              page,
              'available_now',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'condition') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'condition');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'deliveryTimeInStockNotes') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valuePropertyEN = await boProductsCreateTabShippingPage.getValue(
              page,
              'delivery_in_stock',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabShippingPage.getValue(
              page,
              'delivery_in_stock',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'deliveryTimeOutOfStockNotes') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valuePropertyEN = await boProductsCreateTabShippingPage.getValue(
              page,
              'delivery_out_stock',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabShippingPage.getValue(
              page,
              'delivery_out_stock',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'deliveryTimeNoteType') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valueProperty = parseInt(
              await boProductsCreateTabShippingPage.getValue(page, 'additional_delivery_times'),
              10,
            );
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'depth') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valueProperty = parseFloat(await boProductsCreateTabShippingPage.getValue(page, 'depth'));
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'descriptions') {
            const valuePropertyEN = await boProductsCreateTabDescriptionPage.getValue(
              page,
              'description',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabDescriptionPage.getValue(
              page,
              'description',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'ecotaxTaxExcluded') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = parseFloat(
              await boProductsCreateTabPricingPage.getValue(page, 'ecotax'),
            ).toFixed(2);
            expect(parseFloat(valueProperty)).to.equal(data.propertyValue);
          } else if (data.propertyName === 'gtin') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'ean13');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'height') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valueProperty = parseFloat(await boProductsCreateTabShippingPage.getValue(page, 'height'));
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'isbn') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'isbn');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'linkRewrites') {
            await boProductsCreatePage.goToTab(page, 'seo');
            const valuePropertyEN = await boProductsCreateTabSEOPage.getValue(
              page,
              'link_rewrite',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabSEOPage.getValue(
              page,
              'link_rewrite',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'lowStockThreshold') {
            await boProductsCreatePage.goToTab(page, 'stock');

            const valueProperty = parseInt(await boProductsCreateTabStocksPage.getValue(page, 'low_stock_threshold'), 10);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'manufacturerId') {
            const valueProperty = parseInt(await boProductsCreateTabDescriptionPage.getValue(page, 'manufacturer'), 10);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'metaDescriptions') {
            await boProductsCreatePage.goToTab(page, 'seo');
            const valuePropertyEN = await boProductsCreateTabSEOPage.getValue(
              page,
              'meta_description',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabSEOPage.getValue(
              page,
              'meta_description',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'metaTitles') {
            await boProductsCreatePage.goToTab(page, 'seo');
            const valuePropertyEN = await boProductsCreateTabSEOPage.getValue(
              page,
              'meta_title',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabSEOPage.getValue(
              page,
              'meta_title',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'minimalQuantity') {
            await boProductsCreatePage.goToTab(page, 'stock');

            const valueProperty = parseInt(await boProductsCreateTabStocksPage.getValue(page, 'minimal_quantity'), 10);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'mpn') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'mpn');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'names') {
            const valuePropertyEN = await boProductsCreatePage.getProductName(page, dataLanguages.english.isoCode);
            const valuePropertyFR = await boProductsCreatePage.getProductName(page, dataLanguages.french.isoCode);
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'onlineOnly') {
            await boProductsCreatePage.goToTab(page, 'options');

            const valueProperty = await boProductsCreateTabOptionsPage.getValue(page, 'online_only');
            expect(valueProperty).to.equal(data.propertyValue ? '1' : '0');
          } else if (data.propertyName === 'onSale') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = await boProductsCreateTabPricingPage.getValue(page, 'on_sale');
            expect(valueProperty).to.equal(data.propertyValue ? '1' : '0');
          } else if (data.propertyName === 'packStockType') {
            // Not tested
          } else if (data.propertyName === 'priceTaxExcluded') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = parseFloat(await boProductsCreateTabPricingPage.getValue(page, 'price'));
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'redirectOption') {
            await boProductsCreatePage.goToTab(page, 'seo');

            const valuePropertyType = await boProductsCreateTabSEOPage.getValue(page, 'redirect_type');
            const valuePropertyTarget = parseInt(await boProductsCreateTabSEOPage.getValue(page, 'id_type_redirected'), 10);
            expect({
              redirectType: valuePropertyType,
              redirectTarget: valuePropertyTarget,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'reference') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'reference');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'shortDescriptions') {
            const valuePropertyEN = await boProductsCreateTabDescriptionPage.getValue(
              page,
              'summary',
              dataLanguages.english.id.toString(),
            );
            const valuePropertyFR = await boProductsCreateTabDescriptionPage.getValue(
              page,
              'summary',
              dataLanguages.french.id.toString(),
            );
            expect({
              [dataLanguages.english.locale]: valuePropertyEN,
              [dataLanguages.french.locale]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'showCondition') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'show_condition');
            expect(valueProperty).to.equal(data.propertyValue ? '1' : '0');
          } else if (data.propertyName === 'showPrice') {
            await boProductsCreatePage.goToTab(page, 'options');

            const valueProperty = await boProductsCreateTabOptionsPage.getValue(page, 'show_price');
            expect(valueProperty).to.equal(data.propertyValue ? '1' : '0');
          } else if (data.propertyName === 'taxRulesGroupId') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = parseInt(await boProductsCreateTabPricingPage.getValue(page, 'id_tax_rules_group'), 10);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'unitPriceTaxExcluded') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = parseFloat(await boProductsCreateTabPricingPage.getValue(page, 'unit_price'));
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'unity') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = await boProductsCreateTabPricingPage.getValue(page, 'unity');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'upc') {
            await boProductsCreatePage.goToTab(page, 'details');

            const valueProperty = await boProductsCreateTabDetailsPage.getValue(page, 'upc');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'visibility') {
            await boProductsCreatePage.goToTab(page, 'options');

            const valueProperty = await boProductsCreateTabOptionsPage.getValue(page, 'visibility');
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'width') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valueProperty = parseFloat(await boProductsCreateTabShippingPage.getValue(page, 'width')).toFixed(2);
            expect(parseFloat(valueProperty)).to.equal(data.propertyValue);
          } else if (data.propertyName === 'weight') {
            await boProductsCreatePage.goToTab(page, 'shipping');

            const valueProperty = parseFloat(await boProductsCreateTabShippingPage.getValue(page, 'weight')).toFixed(2);
            expect(parseFloat(valueProperty)).to.equal(data.propertyValue);
          } else if (data.propertyName === 'wholesalePrice') {
            await boProductsCreatePage.goToTab(page, 'pricing');

            const valueProperty = parseFloat(await boProductsCreateTabPricingPage.getValue(page, 'wholesale_price')).toFixed(2);
            expect(parseFloat(valueProperty)).to.equal(data.propertyValue);
          } else {
            throw new Error(`The property ${data.propertyName} is not managed`);
          }
        });
      });
    });
  });

  // Pre-condition: Delete a product
  deleteProductTest(createProduct, `${baseContext}_postTest_0`);

  // Pre-condition: Delete a product
  disableEcoTaxTest(`${baseContext}_postTest_1`);
});
