// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import createProductsPage from '@pages/BO/catalog/products/add';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';
import {
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boProductsPage,
  boProductsCreateTabDescriptionPage,
  dataLanguages,
  FakerAPIClient,
  FakerProduct,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_patchProductId';

describe('API : PATCH /product/{productId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let idProduct: number;

  const clientScope: string = 'product_write';
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createProduct: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });
  const patchProduct: FakerProduct = new FakerProduct({
    type: 'virtual',
    status: false,
  });

  // Pre Condition : Create a product
  createProductTest(createProduct, `${baseContext}_preTest_0`);

  describe('API : PATCH /product/{productId}', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);

      apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    describe('BackOffice : Fetch the access token', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Advanced Parameters > API Client\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await boApiClientsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

        const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
        expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New API Client page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

        await boApiClientsPage.goToNewAPIClientPage(page);

        const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
      });

      it('should create API Client', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

        const textResult = await boApiClientsCreatePage.addAPIClient(page, clientData);
        expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

        const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
        expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
      });

      it('should copy client secret', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

        await boApiClientsCreatePage.copyClientSecret(page);

        clientSecret = await boApiClientsCreatePage.getClipboardText(page);
        expect(clientSecret.length).to.be.gt(0);
      });

      it('should request the endpoint /access_token', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

        const apiResponse = await apiContext.post('access_token', {
          form: {
            client_id: clientData.clientId,
            client_secret: clientSecret,
            grant_type: 'client_credentials',
            scope: clientScope,
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property('access_token');
        expect(jsonResponse.token_type).to.be.a('string');

        accessToken = jsonResponse.access_token;
      });
    });

    describe('BackOffice : Fetch the ID product', async () => {
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

        const pageTitle: string = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });
    });

    [
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35626
      /*{
        propertyName: 'type',
        propertyValue: patchProduct.type,
      },*/
      {
        propertyName: 'active',
        propertyValue: patchProduct.status,
      },
      {
        propertyName: 'names',
        propertyValue: {
          [dataLanguages.english.id]: patchProduct.name,
          [dataLanguages.french.id]: patchProduct.nameFR,
        },
      },
      {
        propertyName: 'descriptions',
        propertyValue: {
          [dataLanguages.english.id]: patchProduct.description,
          [dataLanguages.french.id]: patchProduct.descriptionFR,
        },
      },
    ].forEach((data: { propertyName: string, propertyValue: boolean|string|object}) => {
      describe(`Update the property \`${data.propertyName}\` with API and check in BO`, async () => {
        it('should request the endpoint /product/{productId}', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${data.propertyName}`, baseContext);

          const dataPatch: any = {};
          dataPatch[data.propertyName] = data.propertyValue;

          const apiResponse = await apiContext.patch(`product/${idProduct}`, {
            headers: {
              Authorization: `Bearer ${accessToken}`,
            },
            data: dataPatch,
          });
          expect(apiResponse.status()).to.eq(200);
          expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
          expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

          const jsonResponse = await apiResponse.json();
          expect(jsonResponse).to.have.property(data.propertyName);
          expect(jsonResponse[data.propertyName]).to.deep.equal(data.propertyValue);
        });

        it(`should check that the property "${data.propertyName}"`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProperty${data.propertyName}`, baseContext);

          await boApiClientsCreatePage.reloadPage(page);

          if (data.propertyName === 'type') {
            const valueProperty = await createProductsPage.getProductType(page);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'active') {
            const valueProperty = await createProductsPage.getProductStatus(page);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'names') {
            const valuePropertyEN = await createProductsPage.getProductName(page, dataLanguages.english.isoCode);
            const valuePropertyFR = await createProductsPage.getProductName(page, dataLanguages.french.isoCode);
            expect({
              [dataLanguages.english.id]: valuePropertyEN,
              [dataLanguages.french.id]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'descriptions') {
            const valuePropertyEN = await boProductsCreateTabDescriptionPage.getValue(page, 'description', dataLanguages.english.id.toString());
            const valuePropertyFR = await boProductsCreateTabDescriptionPage.getValue(page, 'description', dataLanguages.french.id.toString());
            expect({
              [dataLanguages.english.id]: valuePropertyEN,
              [dataLanguages.french.id]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          }
        });
      });
    });
  });

  // Pre-condition: Delete a product
  deleteProductTest(patchProduct, `${baseContext}_postTest_0`);

  // Pre-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_1`);
});
