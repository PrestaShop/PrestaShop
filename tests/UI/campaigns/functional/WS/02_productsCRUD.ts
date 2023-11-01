// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import xml from '@utils/xml';

// Import webservices
import productXml from '@webservices/product/productXml';
import ProductWS from '@webservices/product/productWs';

// Import commonTests
import {addWebserviceKey, removeWebserviceKey, setWebserviceStatus} from '@commonTests/BO/advancedParameters/ws';
import {enableEcoTaxTest, disableEcoTaxTest} from '@commonTests/BO/international/ecoTax';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';
import detailsTab from '@pages/BO/catalog/products/add/detailsTab';
import optionsTab from '@pages/BO/catalog/products/add/optionsTab';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import seoTab from '@pages/BO/catalog/products/add/seoTab';
import shippingTab from '@pages/BO/catalog/products/add/shippingTab';
import stocksTab from '@pages/BO/catalog/products/add/stocksTab';

// Import data
import {WebservicePermission} from '@data/types/webservice';
import getProductXml from '@data/xml/product';

import {expect} from 'chai';
import type {
  APIResponse, APIRequestContext, BrowserContext, Page,
} from 'playwright';

const baseContext: string = 'functional_WS_productsCRUD';

describe('WS - Products : CRUD', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let wsKey: string = '';
  let authorization: string = '';

  const wsKeyDescription: string = 'Webservice Key - Products';
  const wsKeyPermissions: WebservicePermission[] = [
    {
      resource: 'products',
      methods: ['all'],
    },
  ];
  const xmlCreate: string = getProductXml();
  let xmlUpdate: string;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.FO.URL);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Enable webservice
  setWebserviceStatus(true, `${baseContext}_preTest_1`);

  // Create a new webservice key
  addWebserviceKey(wsKeyDescription, wsKeyPermissions, `${baseContext}_preTest_2`);

  // Enable ecotax
  enableEcoTaxTest(`${baseContext}_preTest_3`);

  describe('Products : CRUD', () => {
    let productNodeID: number;
    describe('Fetch the Webservice Key', () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Advanced Parameters > Webservice\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.webserviceLink,
        );
        await webservicePage.closeSfToolBar(page);

        const pageTitle = await webservicePage.getPageTitle(page);
        expect(pageTitle).to.contains(webservicePage.pageTitle);
      });

      it('should filter list by key description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterBeforeDelete', baseContext);

        await webservicePage.resetAndGetNumberOfLines(page);
        await webservicePage.filterWebserviceTable(
          page,
          'input',
          'description',
          wsKeyDescription,
        );

        const description = await webservicePage.getTextColumnFromTable(page, 1, 'description');
        expect(description).to.contains(wsKeyDescription);

        wsKey = await webservicePage.getTextColumnFromTable(page, 1, 'key');
        authorization = `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`;
        expect(wsKey).to.not.have.length(0);
      });
    });

    describe(`Endpoint : ${ProductWS.endpoint} - Schema : Blank `, () => {
      let apiResponse: APIResponse;
      let xmlResponse : string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankStatus', baseContext);

        apiResponse = await ProductWS.getBlank(
          apiContext,
          authorization,
        );
        expect(apiResponse.status()).to.eq(200);
      });

      it('should check that the blank XML can be parsed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankValid', baseContext);

        xmlResponse = await apiResponse.text();

        const isValidXML = xml.isValid(xmlResponse);
        expect(isValidXML).to.eq(true);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankRootNode', baseContext);

        expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNode', baseContext);

        const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.eq(1);
        expect(rootNodes[0].nodeName).to.eq('product');
      });

      it('should check each node name, attributes and has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNodes', baseContext);

        const nodes = productXml.getProductNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;
          expect(nodeAttributes.length).to.eq(0);

          // Empty value
          const isEmptyNode: boolean = xml.isEmpty(node);
          expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.eq(true);
        }
      });
    });

    describe(`Endpoint : ${ProductWS.endpoint} - Schema : Synopsis `, () => {
      let apiResponse: APIResponse;
      let xmlResponse : string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisStatus', baseContext);

        apiResponse = await ProductWS.getSynopsis(
          apiContext,
          authorization,
        );
        expect(apiResponse.status()).to.eq(200);
      });

      it('should check that the synopsis XML can be parsed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisValid', baseContext);

        xmlResponse = await apiResponse.text();

        const isValidXML = xml.isValid(xmlResponse);
        expect(isValidXML).to.eq(true);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisRootNode', baseContext);

        expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNode', baseContext);

        const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.eq(1);
        expect(rootNodes[0].nodeName).to.eq('product');
      });

      it('should check each node name, attributes and has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNodes', baseContext);

        const nodes = productXml.getProductNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;

          if ([
            'new',
            'cache_default_attribute',
            'id_default_image',
            'id_default_combination',
            'position_in_category',
            'manufacturer_name',
            'quantity',
            'type',
            'unit_price_ratio',
            'associations',
          ].includes(node.nodeName)) {
            expect(nodeAttributes.length).to.be.gte(0);
          } else {
            expect(nodeAttributes.length).to.be.gte(1);

            // Attribute : format
            expect(nodeAttributes[nodeAttributes.length - 1].nodeName).to.eq('format');
          }

          // Empty value
          const isEmptyNode = xml.isEmpty(node);
          expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.eq(true);
        }
      });
    });

    describe(`Endpoint : ${ProductWS.endpoint} - Method : GET `, () => {
      let apiResponse : APIResponse;
      let xmlResponse : string;
      let productsNode: Element[];

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetStatus1', baseContext);

        apiResponse = await ProductWS.getAll(
          apiContext,
          authorization,
        );

        expect(apiResponse.status()).to.eq(200);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetRootNode1', baseContext);

        xmlResponse = await apiResponse.text();
        expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNodeNumber1', baseContext);

        const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.eq(1);
        expect(rootNodes[0].nodeName).to.eq('products');
      });

      it('should check number of nodes under Products node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNumberOfNodes1', baseContext);

        productsNode = productXml.getAllProducts(xmlResponse);
        expect(productsNode.length).to.be.gt(0);
      });

      it('should check each node name, attributes and xlink:href', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetCheckAll1', baseContext);

        for (let c: number = 0; c < productsNode.length; c++) {
          const productNode: Element = productsNode[c];
          expect(productNode.nodeName).to.eq('product');

          // Attributes
          const productNodeAttributes: NamedNodeMap = productNode.attributes;
          expect(productNodeAttributes.length).to.eq(2);

          // Attribute : id
          expect(productNodeAttributes[0].nodeName).to.eq('id');
          const productNodeAttributeId = productNodeAttributes[0].nodeValue as string;
          expect(productNodeAttributeId).to.eq(parseInt(productNodeAttributeId, 10).toString());

          // Attribute : xlink:href
          expect(productNodeAttributes[1].nodeName).to.eq('xlink:href');
          expect(productNodeAttributes[1].nodeValue).to.be.a('string');
        }
      });
    });

    describe(`Endpoint : ${ProductWS.endpoint} - Method : POST `, () => {
      describe(`Endpoint : ${ProductWS.endpoint} - Method : POST - Add Product `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostStatus1', baseContext);

          apiResponse = await ProductWS.add(
            apiContext,
            authorization,
            xmlCreate,
          );
          expect(apiResponse.status()).to.eq(201);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostNodeNumber1', baseContext);

          const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.eq(1);
          expect(rootNodes[0].nodeName).to.eq('product');
        });

        it('should check id of the country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostCheckId1', baseContext);

          // Attribute : id
          const xmlID = productXml.getAttributeValue(xmlResponse, 'id');
          productNodeID = parseInt(xmlID, 10);
          expect(xmlID).to.eq(productNodeID.toString());
        });
      });

      describe(`Endpoint : ${ProductWS.endpoint}/{id} - Method : POST - Check with WS `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;
        let productsNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDStatus', baseContext);

          apiResponse = await ProductWS.getById(
            apiContext,
            authorization,
            productNodeID.toString(),
          );

          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNodeNumber1', baseContext);

          const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.eq(1);
          expect(rootNodes[0].nodeName).to.eq('product');
        });

        it('should check number of nodes under Products node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNumberOfNodes1', baseContext);

          productsNodes = productXml.getProductNodes(xmlResponse);
          expect(productsNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < productsNodes.length; o++) {
            const oNode: Element = productsNodes[o];

            if (['manufacturer_name', 'quantity', 'date_add', 'date_upd'].includes(oNode.nodeName)) {
              // It can be defined in POST/PUT
            } else if (oNode.nodeName === 'id') {
              expect(oNode.textContent).to.eq(productNodeID.toString());
            } else if ([
              'name',
              'delivery_in_stock',
              'delivery_out_stock',
              'meta_description',
              'meta_keywords',
              'meta_title',
              'link_rewrite',
              'description',
              'description_short',
              'available_now',
              'available_later',
            ].includes(oNode.nodeName)) {
              const objectNodeValueEN = productXml.getAttributeLangValue(
                xmlResponse,
                oNode.nodeName,
                '1',
              );

              const createNodeValueEN = productXml.getAttributeLangValue(
                xmlCreate,
                oNode.nodeName,
                '1',
              );
              expect(objectNodeValueEN).to.eq(createNodeValueEN);

              const objectNodeValueFR = productXml.getAttributeLangValue(
                xmlResponse,
                oNode.nodeName,
                '2',
              );
              const createNodeValueFR = productXml.getAttributeLangValue(
                xmlCreate,
                oNode.nodeName,
                '2',
              );
              expect(objectNodeValueFR).to.eq(createNodeValueFR);
            } else if (oNode.nodeName === 'new') {
              // @todo : https://github.com/PrestaShop/PrestaShop/issues/33429
            } else if (oNode.nodeName === 'position_in_category') {
              // @todo : https://github.com/PrestaShop/PrestaShop/issues/14903
            } else if (oNode.nodeName === 'associations') {
              // Don't check all associations at the moment
            } else {
              const objectNodeValue: string = productXml.getAttributeValue(
                xmlCreate,
                oNode.nodeName,
              );
              expect(oNode.textContent).to.eq(objectNodeValue);
            }
          }
        });
      });

      describe(`Endpoint : ${ProductWS.endpoint} - Method : POST - Check On BO `, () => {
        describe('Product Page : Filter & Edit', () => {
          it('should go to \'Catalog > Products\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageAfterPost', baseContext);

            await dashboardPage.goToSubMenu(
              page,
              dashboardPage.catalogParentLink,
              dashboardPage.productsLink,
            );
            await productsPage.closeSfToolBar(page);

            const pageTitle = await productsPage.getPageTitle(page);
            expect(pageTitle).to.contains(productsPage.pageTitle);
          });

          it('should filter product by ID', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost', baseContext);

            // Filter
            await productsPage.resetFilter(page);
            await productsPage.filterProducts(
              page,
              'id_product',
              {min: productNodeID, max: productNodeID},
              'input',
            );

            // Check number of Products
            const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
            expect(numberOfProductsAfterFilter).to.eq(1);

            const textColumn = await productsPage.getTextColumn(page, 'id_product', 1);
            expect(textColumn).to.eq(productNodeID);
          });

          it('should go to edit product page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

            await productsPage.goToProductPage(page, 1);

            const pageTitle: string = await createProductsPage.getPageTitle(page);
            expect(pageTitle).to.contains(createProductsPage.pageTitle);
          });
        });

        describe('Main Tab', () => {
          it('should check active', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckProductActive', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'active');
            const value = (await createProductsPage.getProductStatus(page)) ? '1' : '0';
            expect(value).to.eq(xmlValue);
          });

          it('should check type', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckProductType', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'product_type');
            const value = (await createProductsPage.getProductType(page));
            expect(value).to.eq(xmlValue);
          });

          it('should check name', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckName', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'name', '1');
            const valueEn = (await createProductsPage.getProductName(page, 'en'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'name', '2');
            const valueFr = (await createProductsPage.getProductName(page, 'fr'));
            expect(valueFr).to.eq(xmlValueFr);
          });
        });

        describe('Description Tab', () => {
          it('should check short description', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckShortDescription', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'description_short', '1');
            const valueEn = (await descriptionTab.getValue(page, 'summary', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'description_short', '2');
            const valueFr = (await descriptionTab.getValue(page, 'summary', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check description', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckDescription', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'description', '1');
            const valueEn = (await descriptionTab.getValue(page, 'description', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'description', '2');
            const valueFr = (await descriptionTab.getValue(page, 'description', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check default category', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckDefaultCategory', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'id_category_default');
            const value = (await descriptionTab.getValue(page, 'id_category_default', '1'));
            expect(value).to.eq(xmlValue);
          });

          it('should check manufacturer', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckManufacturer', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'id_manufacturer');
            const value = (await descriptionTab.getValue(page, 'manufacturer', '1'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Details Tab', () => {
          it('should go to details tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postGoToDetailsTab', baseContext);

            await createProductsPage.goToTab(page, 'details');

            const isTabActive = await createProductsPage.isTabActive(page, 'details');
            expect(isTabActive).to.eq(true);
          });

          it('should check reference', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckReference', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'reference');
            const value = (await detailsTab.getValue(page, 'reference'));
            expect(value).to.eq(xmlValue);
          });

          it('should check MPN', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckMPN', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'mpn');
            const value = (await detailsTab.getValue(page, 'mpn'));
            expect(value).to.eq(xmlValue);
          });

          it('should check UPC', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckUPC', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'upc');
            const value = (await detailsTab.getValue(page, 'upc'));
            expect(value).to.eq(xmlValue);
          });

          it('should check EAN13', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckEAN13', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'ean13');
            const value = (await detailsTab.getValue(page, 'ean13'));
            expect(value).to.eq(xmlValue);
          });

          it('should check ISBN', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckISBN', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'isbn');
            const value = (await detailsTab.getValue(page, 'isbn'));
            expect(value).to.eq(xmlValue);
          });

          it('should check "Display condition on product page"', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckShowCondition', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'show_condition');
            const value = (await detailsTab.getValue(page, 'show_condition'));
            expect(value).to.eq(xmlValue);
          });

          it('should check condition', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckCondition', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'condition');
            const value = (await detailsTab.getValue(page, 'condition'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Stocks Tab', () => {
          it('should go to Stocks tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postGoToStocksTab', baseContext);

            await createProductsPage.goToTab(page, 'stock');

            const isTabActive = await createProductsPage.isTabActive(page, 'stock');
            expect(isTabActive).to.eq(true);
          });

          it('should check Minimum quantity for sale', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckMinimalQuantity', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'minimal_quantity');
            const value = (await stocksTab.getValue(page, 'minimal_quantity'));
            expect(value).to.eq(xmlValue);
          });

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/33455
          it('should check Receive a low stock alert by email', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckLowStockAlert', baseContext);

            this.skip();

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'low_stock_alert');
            const value = (await stocksTab.getValue(page, 'low_stock_threshold_enabled'));
            expect(value).to.eq(xmlValue);
          });

          it('should check the low stock alert by email', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckLowStockThreshold', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'low_stock_threshold');
            const value = (await stocksTab.getValue(page, 'low_stock_threshold'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Label when in stock', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckLabelAvailableNow', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'available_now', '1');
            const valueEn = (await stocksTab.getValue(page, 'available_now', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'available_now', '2');
            const valueFr = (await stocksTab.getValue(page, 'available_now', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Label when out of stock', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckLabelAvailableLater', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'available_later', '1');
            const valueEn = (await stocksTab.getValue(page, 'available_later', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'available_later', '2');
            const valueFr = (await stocksTab.getValue(page, 'available_later', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Availability date', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckAvailableDate', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'available_date');
            const value = (await stocksTab.getValue(page, 'available_date'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Shipping Tab', () => {
          it('should go to Shipping tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postGoToShippingTab', baseContext);

            await createProductsPage.goToTab(page, 'shipping');

            const isTabActive = await createProductsPage.isTabActive(page, 'shipping');
            expect(isTabActive).to.eq(true);
          });

          it('should check Width', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckWidth', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'width');
            const value = (await shippingTab.getValue(page, 'width'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Height', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckHeight', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'height');
            const value = (await shippingTab.getValue(page, 'height'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Depth', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckDepth', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'depth');
            const value = (await shippingTab.getValue(page, 'depth'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Weight', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckWeight', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'weight');
            const value = (await shippingTab.getValue(page, 'weight'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Delivery time', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckDeliveryTime', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'additional_delivery_times');
            const value = (await shippingTab.getValue(page, 'additional_delivery_times'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Delivery time of in-stock products', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckDeliveryInStock', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'delivery_in_stock', '1');
            const valueEn = (await shippingTab.getValue(page, 'delivery_in_stock', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'delivery_in_stock', '2');
            const valueFr = (await shippingTab.getValue(page, 'delivery_in_stock', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Delivery time of out-of-stock products with allowed orders', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckDeliveryOutStock', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'delivery_out_stock', '1');
            const valueEn = (await shippingTab.getValue(page, 'delivery_out_stock', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'delivery_out_stock', '2');
            const valueFr = (await shippingTab.getValue(page, 'delivery_out_stock', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Additional shipping costs', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckShippingCost', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'additional_shipping_cost');
            const value = (await shippingTab.getValue(page, 'additional_shipping_cost'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Pricing Tab', () => {
          it('should go to Pricing tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postGoToPricingTab', baseContext);

            await createProductsPage.goToTab(page, 'pricing');

            const isTabActive = await createProductsPage.isTabActive(page, 'pricing');
            expect(isTabActive).to.eq(true);
          });

          it('should check Retail Price (tax excl.)', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckPrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'price');
            const value = (await pricingTab.getValue(page, 'price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Tax Rule', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckTaxRule', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'id_tax_rules_group');
            const value = (await pricingTab.getValue(page, 'id_tax_rules_group'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Ecotax', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckEcotax', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'ecotax');
            const value = (await pricingTab.getValue(page, 'ecotax'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Cost Price', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckWholesalePrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'wholesale_price');
            const value = (await pricingTab.getValue(page, 'wholesale_price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Unit Price', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckUnitPrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'unit_price');
            const value = (await pricingTab.getValue(page, 'unit_price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Unity', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckUnity', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'unity');
            const value = (await pricingTab.getValue(page, 'unity'));
            expect(value).to.eq(xmlValue);
          });

          it('should check "Display the "On sale!" flag on the product page"', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckOnSale', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'on_sale');
            const value = (await pricingTab.getValue(page, 'on_sale'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('SEO Tab', () => {
          it('should go to SEO tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postGoToSEOTab', baseContext);

            await createProductsPage.goToTab(page, 'seo');

            const isTabActive = await createProductsPage.isTabActive(page, 'seo');
            expect(isTabActive).to.eq(true);
          });

          it('should check Meta title', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckMetaTitle', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'meta_title', '1');
            const valueEn = (await seoTab.getValue(page, 'meta_title', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'meta_title', '2');
            const valueFr = (await seoTab.getValue(page, 'meta_title', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Meta description', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckMetaDescription', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'meta_description', '1');
            const valueEn = (await seoTab.getValue(page, 'meta_description', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'meta_description', '2');
            const valueFr = (await seoTab.getValue(page, 'meta_description', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Friendly URL', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckLinkRewrite', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlCreate, 'link_rewrite', '1');
            const valueEn = (await seoTab.getValue(page, 'link_rewrite', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlCreate, 'link_rewrite', '2');
            const valueFr = (await seoTab.getValue(page, 'link_rewrite', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Redirection page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckRedirectType', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'redirect_type');
            const value = (await seoTab.getValue(page, 'redirect_type', '1'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Redirection product', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckIdTypeRedirected', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'id_type_redirected');
            const value = (await seoTab.getValue(page, 'id_type_redirected', '1'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Options Tab', () => {
          it('should go to Options tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postGoToOptionsTab', baseContext);

            await createProductsPage.goToTab(page, 'options');

            const isTabActive = await createProductsPage.isTabActive(page, 'options');
            expect(isTabActive).to.eq(true);
          });

          it('should check Visibility', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckVisibility', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'visibility');
            const value = (await optionsTab.getValue(page, 'visibility'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Available for order', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckAvailableForOrder', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'available_for_order');
            const value = (await optionsTab.getValue(page, 'available_for_order'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Show prices', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckShowPrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'show_price');
            const value = (await optionsTab.getValue(page, 'show_price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Web only (not sold in your retail store)', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'postCheckOnlineOnly', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlCreate, 'online_only');
            const value = (await optionsTab.getValue(page, 'online_only'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Product Page : List & Reset', () => {
          it('should go to \'Catalog > Products\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageBeforePut', baseContext);

            await dashboardPage.goToSubMenu(
              page,
              dashboardPage.catalogParentLink,
              dashboardPage.productsLink,
            );
            await productsPage.closeSfToolBar(page);

            const pageTitle = await productsPage.getPageTitle(page);
            expect(pageTitle).to.contains(productsPage.pageTitle);
          });

          it('should reset all filters', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPost', baseContext);

            const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
            expect(numberOfProducts).to.be.above(0);
          });
        });
      });
    });

    describe(`Endpoint : ${ProductWS.endpoint} - Method : PUT `, () => {
      describe(`Endpoint : ${ProductWS.endpoint} - Method : PUT - Update Product `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;

        it(`should check response status of ${ProductWS.endpoint}/{id}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutStatus1', baseContext);

          xmlUpdate = getProductXml(productNodeID.toString());
          apiResponse = await ProductWS.update(
            apiContext,
            authorization,
            productNodeID.toString(),
            xmlUpdate,
          );

          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutNodeNumber1', baseContext);

          const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.eq(1);
          expect(rootNodes[0].nodeName).to.eq('product');
        });

        it('should check id of the product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutCheckId1', baseContext);

          // Attribute : id
          const xmlID = productXml.getAttributeValue(xmlResponse, 'id');
          productNodeID = parseInt(xmlID, 10);
          expect(xmlID).to.eq(productNodeID.toString());
        });
      });

      describe(`Endpoint : ${ProductWS.endpoint}/{id} - Method : PUT - Check with WS `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;
        let productsNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutStatus2', baseContext);

          apiResponse = await ProductWS.getById(
            apiContext,
            authorization,
            productNodeID.toString(),
          );
          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutRootNode2', baseContext);

          xmlResponse = await apiResponse.text();
          expect(productXml.getRootNodeName(xmlResponse)).to.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNodeNumber1', baseContext);

          const rootNodes = productXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.eq(1);
          expect(rootNodes[0].nodeName).to.eq('product');
        });

        it('should check number of nodes under products node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNumberOfNodes1', baseContext);

          productsNodes = productXml.getProductNodes(xmlResponse);
          expect(productsNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointProductsIdMethodGetAfterPut', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < productsNodes.length; o++) {
            const oNode: Element = productsNodes[o];

            if (['manufacturer_name', 'quantity', 'date_add', 'date_upd'].includes(oNode.nodeName)) {
              // It can be defined in POST/PUT
            } else if (oNode.nodeName === 'id') {
              expect(oNode.textContent).to.eq(productNodeID.toString());
            } else if ([
              'name',
              'delivery_in_stock',
              'delivery_out_stock',
              'meta_description',
              'meta_keywords',
              'meta_title',
              'link_rewrite',
              'description',
              'description_short',
              'available_now',
              'available_later',
            ].includes(oNode.nodeName)) {
              const objectNodeValueEN = productXml.getAttributeLangValue(
                xmlResponse,
                oNode.nodeName,
                '1',
              );

              const updateNodeValueEN = productXml.getAttributeLangValue(
                xmlUpdate,
                oNode.nodeName,
                '1',
              );
              expect(objectNodeValueEN).to.eq(updateNodeValueEN);

              const objectNodeValueFR = productXml.getAttributeLangValue(
                xmlResponse,
                oNode.nodeName,
                '2',
              );
              const updateNodeValueFR = productXml.getAttributeLangValue(
                xmlUpdate,
                oNode.nodeName,
                '2',
              );
              expect(objectNodeValueFR).to.eq(updateNodeValueFR);
            } else if (oNode.nodeName === 'new') {
              // @todo : https://github.com/PrestaShop/PrestaShop/issues/33429
            } else if (oNode.nodeName === 'position_in_category') {
              // @todo : https://github.com/PrestaShop/PrestaShop/issues/14903
            } else if (oNode.nodeName === 'associations') {
              // Don't check all associations at the moment
            } else {
              const objectNodeValue: string = productXml.getAttributeValue(
                xmlUpdate,
                oNode.nodeName,
              );
              expect(oNode.textContent).to.eq(objectNodeValue);
            }
          }
        });
      });

      describe(`Endpoint : ${ProductWS.endpoint} - Method : PUT - Check On BO `, () => {
        describe('Product Page : Filter & Edit', () => {
          it('should filter product by ID', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPut', baseContext);

            // Filter
            await productsPage.filterProducts(
              page,
              'id_product',
              {min: productNodeID, max: productNodeID},
              'input',
            );

            // Check number of Products
            const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
            expect(numberOfProductsAfterFilter).to.eq(1);

            const textColumn = await productsPage.getTextColumn(page, 'id_product', 1);
            expect(textColumn).to.eq(productNodeID);
          });

          it('should go to edit product page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPut', baseContext);

            await productsPage.goToProductPage(page, 1);

            const pageTitle: string = await createProductsPage.getPageTitle(page);
            expect(pageTitle).to.contains(createProductsPage.pageTitle);
          });
        });

        describe('Main Tab', () => {
          it('should check active', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckProductActive', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'active');
            const value = (await createProductsPage.getProductStatus(page)) ? '1' : '0';
            expect(value).to.eq(xmlValue);
          });

          it('should check type', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckProductType', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'product_type');
            const value = (await createProductsPage.getProductType(page));
            expect(value).to.eq(xmlValue);
          });

          it('should check name', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckName', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'name', '1');
            const valueEn = (await createProductsPage.getProductName(page, 'en'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'name', '2');
            const valueFr = (await createProductsPage.getProductName(page, 'fr'));
            expect(valueFr).to.eq(xmlValueFr);
          });
        });

        describe('Description Tab', () => {
          it('should check short description', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckShortDescription', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'description_short', '1');
            const valueEn = (await descriptionTab.getValue(page, 'summary', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'description_short', '2');
            const valueFr = (await descriptionTab.getValue(page, 'summary', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check description', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckDescription', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'description', '1');
            const valueEn = (await descriptionTab.getValue(page, 'description', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'description', '2');
            const valueFr = (await descriptionTab.getValue(page, 'description', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check default category', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckDefaultCategory', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'id_category_default');
            const value = (await descriptionTab.getValue(page, 'id_category_default', '1'));
            expect(value).to.eq(xmlValue);
          });

          it('should check manufacturer', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckManufacturer', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'id_manufacturer');
            const value = (await descriptionTab.getValue(page, 'manufacturer', '1'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Details Tab', () => {
          it('should go to details tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putGoToDetailsTab', baseContext);

            await createProductsPage.goToTab(page, 'details');

            const isTabActive = await createProductsPage.isTabActive(page, 'details');
            expect(isTabActive).to.eq(true);
          });

          it('should check reference', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckReference', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'reference');
            const value = (await detailsTab.getValue(page, 'reference'));
            expect(value).to.eq(xmlValue);
          });

          it('should check MPN', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckMPN', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'mpn');
            const value = (await detailsTab.getValue(page, 'mpn'));
            expect(value).to.eq(xmlValue);
          });

          it('should check UPC', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckUPC', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'upc');
            const value = (await detailsTab.getValue(page, 'upc'));
            expect(value).to.eq(xmlValue);
          });

          it('should check EAN13', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckEAN13', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'ean13');
            const value = (await detailsTab.getValue(page, 'ean13'));
            expect(value).to.eq(xmlValue);
          });

          it('should check ISBN', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckISBN', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'isbn');
            const value = (await detailsTab.getValue(page, 'isbn'));
            expect(value).to.eq(xmlValue);
          });

          it('should check "Display condition on product page"', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckShowCondition', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'show_condition');
            const value = (await detailsTab.getValue(page, 'show_condition'));
            expect(value).to.eq(xmlValue);
          });

          it('should check condition', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckCondition', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'condition');
            const value = (await detailsTab.getValue(page, 'condition'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Stocks Tab', () => {
          it('should go to Stocks tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putGoToStocksTab', baseContext);

            await createProductsPage.goToTab(page, 'stock');

            const isTabActive = await createProductsPage.isTabActive(page, 'stock');
            expect(isTabActive).to.eq(true);
          });

          it('should check Minimum quantity for sale', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckMinimalQuantity', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'minimal_quantity');
            const value = (await stocksTab.getValue(page, 'minimal_quantity'));
            expect(value).to.eq(xmlValue);
          });

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/33455
          it('should check Receive a low stock alert by email', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckLowStockAlert', baseContext);

            this.skip();

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'low_stock_alert');
            const value = (await stocksTab.getValue(page, 'low_stock_threshold_enabled'));
            expect(value).to.eq(xmlValue);
          });

          it('should check the low stock alert by email', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckLowStockThreshold', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'low_stock_threshold');
            const value = (await stocksTab.getValue(page, 'low_stock_threshold'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Label when in stock', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckLabelAvailableNow', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'available_now', '1');
            const valueEn = (await stocksTab.getValue(page, 'available_now', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'available_now', '2');
            const valueFr = (await stocksTab.getValue(page, 'available_now', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Label when out of stock', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckLabelAvailableLater', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'available_later', '1');
            const valueEn = (await stocksTab.getValue(page, 'available_later', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'available_later', '2');
            const valueFr = (await stocksTab.getValue(page, 'available_later', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Availability date', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckAvailableDate', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'available_date');
            const value = (await stocksTab.getValue(page, 'available_date'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Shipping Tab', () => {
          it('should go to Shipping tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putGoToShippingTab', baseContext);

            await createProductsPage.goToTab(page, 'shipping');

            const isTabActive = await createProductsPage.isTabActive(page, 'shipping');
            expect(isTabActive).to.eq(true);
          });

          it('should check Width', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckWidth', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'width');
            const value = (await shippingTab.getValue(page, 'width'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Height', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckHeight', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'height');
            const value = (await shippingTab.getValue(page, 'height'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Depth', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckDepth', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'depth');
            const value = (await shippingTab.getValue(page, 'depth'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Weight', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckWeight', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'weight');
            const value = (await shippingTab.getValue(page, 'weight'));
            expect(value).to.eq(parseInt(xmlValue, 10).toString());
          });

          it('should check Delivery time', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckDeliveryTime', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'additional_delivery_times');
            const value = (await shippingTab.getValue(page, 'additional_delivery_times'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Delivery time of in-stock products', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckDeliveryInStock', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'delivery_in_stock', '1');
            const valueEn = (await shippingTab.getValue(page, 'delivery_in_stock', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'delivery_in_stock', '2');
            const valueFr = (await shippingTab.getValue(page, 'delivery_in_stock', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Delivery time of out-of-stock products with allowed orders', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckDeliveryOutStock', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'delivery_out_stock', '1');
            const valueEn = (await shippingTab.getValue(page, 'delivery_out_stock', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'delivery_out_stock', '2');
            const valueFr = (await shippingTab.getValue(page, 'delivery_out_stock', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Additional shipping costs', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckShippingCost', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'additional_shipping_cost');
            const value = (await shippingTab.getValue(page, 'additional_shipping_cost'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Pricing Tab', () => {
          it('should go to Pricing tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putGoToPricingTab', baseContext);

            await createProductsPage.goToTab(page, 'pricing');

            const isTabActive = await createProductsPage.isTabActive(page, 'pricing');
            expect(isTabActive).to.eq(true);
          });

          it('should check Retail Price (tax excl.)', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckPrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'price');
            const value = (await pricingTab.getValue(page, 'price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Tax Rule', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckTaxRule', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'id_tax_rules_group');
            const value = (await pricingTab.getValue(page, 'id_tax_rules_group'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Ecotax', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckEcotax', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'ecotax');
            const value = (await pricingTab.getValue(page, 'ecotax'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Cost Price', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckWholesalePrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'wholesale_price');
            const value = (await pricingTab.getValue(page, 'wholesale_price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Unit Price', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckUnitPrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'unit_price');
            const value = (await pricingTab.getValue(page, 'unit_price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Unity', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckUnity', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'unity');
            const value = (await pricingTab.getValue(page, 'unity'));
            expect(value).to.eq(xmlValue);
          });

          it('should check "Display the "On sale!" flag on the product page"', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckOnSale', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'on_sale');
            const value = (await pricingTab.getValue(page, 'on_sale'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('SEO Tab', () => {
          it('should go to SEO tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putGoToSEOTab', baseContext);

            await createProductsPage.goToTab(page, 'seo');

            const isTabActive = await createProductsPage.isTabActive(page, 'seo');
            expect(isTabActive).to.eq(true);
          });

          it('should check Meta title', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckMetaTitle', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'meta_title', '1');
            const valueEn = (await seoTab.getValue(page, 'meta_title', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'meta_title', '2');
            const valueFr = (await seoTab.getValue(page, 'meta_title', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Meta description', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckMetaDescription', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'meta_description', '1');
            const valueEn = (await seoTab.getValue(page, 'meta_description', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'meta_description', '2');
            const valueFr = (await seoTab.getValue(page, 'meta_description', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Friendly URL', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckLinkRewrite', baseContext);

            const xmlValueEn = productXml.getAttributeLangValue(xmlUpdate, 'link_rewrite', '1');
            const valueEn = (await seoTab.getValue(page, 'link_rewrite', '1'));
            expect(valueEn).to.eq(xmlValueEn);

            const xmlValueFr = productXml.getAttributeLangValue(xmlUpdate, 'link_rewrite', '2');
            const valueFr = (await seoTab.getValue(page, 'link_rewrite', '2'));
            expect(valueFr).to.eq(xmlValueFr);
          });

          it('should check Redirection page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckRedirectType', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'redirect_type');
            const value = (await seoTab.getValue(page, 'redirect_type', '1'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Redirection product', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckIdTypeRedirected', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'id_type_redirected');
            const value = (await seoTab.getValue(page, 'id_type_redirected', '1'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Options Tab', () => {
          it('should go to Options tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putGoToOptionsTab', baseContext);

            await createProductsPage.goToTab(page, 'options');

            const isTabActive = await createProductsPage.isTabActive(page, 'options');
            expect(isTabActive).to.eq(true);
          });

          it('should check Visibility', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckVisibility', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'visibility');
            const value = (await optionsTab.getValue(page, 'visibility'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Available for order', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckAvailableForOrder', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'available_for_order');
            const value = (await optionsTab.getValue(page, 'available_for_order'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Show prices', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckShowPrice', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'show_price');
            const value = (await optionsTab.getValue(page, 'show_price'));
            expect(value).to.eq(xmlValue);
          });

          it('should check Web only (not sold in your retail store)', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'putCheckOnlineOnly', baseContext);

            const xmlValue = productXml.getAttributeValue(xmlUpdate, 'online_only');
            const value = (await optionsTab.getValue(page, 'online_only'));
            expect(value).to.eq(xmlValue);
          });
        });

        describe('Product Page : List & Reset', () => {
          it('should go to \'Catalog > Products\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageAfterPut', baseContext);

            await dashboardPage.goToSubMenu(
              page,
              dashboardPage.catalogParentLink,
              dashboardPage.productsLink,
            );
            await productsPage.closeSfToolBar(page);

            const pageTitle = await productsPage.getPageTitle(page);
            expect(pageTitle).to.contains(productsPage.pageTitle);
          });

          it('should reset all filters', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPut', baseContext);

            const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
            expect(numberOfProducts).to.be.above(0);
          });
        });
      });
    });

    describe(`Endpoint : ${ProductWS.endpoint} - Method : DELETE `, () => {
      it(`should request the endpoint ${ProductWS.endpoint}/{id} with method DELETE`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointProductsMethodDelete', baseContext);

        const apiResponse = await ProductWS.delete(
          apiContext,
          authorization,
          productNodeID.toString(),
        );

        expect(apiResponse.status()).to.eq(200);
      });

      it(`should request the endpoint ${ProductWS.endpoint}/{id} with method GET`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointProductsIdMethodGetAfterDelete', baseContext);

        const apiResponse = await ProductWS.getById(
          apiContext,
          authorization,
          productNodeID.toString(),
        );

        expect(apiResponse.status()).to.eq(404);
      });

      it('should filter product by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterDelete', baseContext);

        // Filter
        await productsPage.filterProducts(
          page,
          'id_product',
          {min: productNodeID, max: productNodeID},
          'input',
        );

        // Check number of Products
        const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.eq(0);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });
    });
  });

  // Remove a new webservice key
  removeWebserviceKey(wsKeyDescription, `${baseContext}_postTest_1`);

  // Disable webservice
  setWebserviceStatus(false, `${baseContext}_postTest_2`);

  // Disable ecotax
  disableEcoTaxTest(`${baseContext}_postTest_3`);
});
