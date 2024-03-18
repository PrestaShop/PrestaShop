// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import xml from '@utils/xml';

// Import webservices
import orderXml from '@webservices/order/orderXml';
import OrderWS from '@webservices/order/orderWs';

// Import commonTests
import {addWebserviceKey, removeWebserviceKey, setWebserviceStatus} from '@commonTests/BO/advancedParameters/ws';
import loginCommon from '@commonTests/BO/loginBO';
import createShoppingCart from '@commonTests/FO/classic/shoppingCart';

// Import BO pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import shoppingCartsPage from '@pages/BO/orders/shoppingCarts';
import {viewOrderBasePage} from '@pages/BO/orders/view/viewOrderBasePage';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';
import orderPagePaymentBlock from '@pages/BO/orders/view/paymentBlock';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Address from '@data/demo/address';
import Products from '@data/demo/products';
import OrderData from '@data/faker/order';
import {WebservicePermission} from '@data/types/webservice';
import getOrderXml from '@data/xml/order';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';
import type {
  APIResponse, APIRequestContext, BrowserContext, Page,
} from 'playwright';

use(chaiString);

const baseContext: string = 'functional_WS_ordersCRUD';

describe('WS - Orders : CRUD', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let wsKey: string = '';
  let authorization: string = '';
  let idShoppingCart: number;

  const orderByCustomerData: OrderData = new OrderData({
    customer: dataCustomers.johnDoe,
    deliveryAddress: Address.second,
    invoiceAddress: Address.third,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
  });
  const wsKeyDescription: string = 'Webservice Key - Orders';
  const wsKeyPermissions: WebservicePermission[] = [
    {
      resource: 'orders',
      methods: ['all'],
    },
  ];
  let xmlCreate: string;
  let xmlUpdate: string;
  let xmlResponseCreate: string;
  let xmlResponseUpdate: string;

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

  // Create a shopping cart
  createShoppingCart(orderByCustomerData, `${baseContext}_preTest_3`);

  describe('Orders : CRUD', () => {
    let orderNodeID: string|null;

    describe('Fetch informations', () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      describe('Fetch the Webservice Key', () => {
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
          expect(wsKey).to.not.have.lengthOf(0);
        });
      });

      describe('Fetch the Shopping Cart ID', () => {
        it('should go to \'Orders > Shopping carts\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.shoppingCartsLink,
          );

          const pageTitle = await shoppingCartsPage.getPageTitle(page);
          expect(pageTitle).to.contains(shoppingCartsPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFirst', baseContext);

          const numberOfShoppingCarts = await shoppingCartsPage.resetAndGetNumberOfLines(page);
          expect(numberOfShoppingCarts).to.be.gt(0);
        });

        it('should search the non ordered shopping cart', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'searchNonOrderedShoppingCarts', baseContext);

          await shoppingCartsPage.filterTable(page, 'select', 'status', 'Non ordered');

          const numberOfShoppingCartsAfterFilter = await shoppingCartsPage.getNumberOfElementInGrid(page);
          expect(numberOfShoppingCartsAfterFilter).to.gte(1);

          const textColumn = await shoppingCartsPage.getTextColumn(page, 1, 'status');
          expect(textColumn).to.contains('Non ordered');

          idShoppingCart = parseInt(await shoppingCartsPage.getTextColumn(page, 1, 'id_cart'), 10);
          expect(idShoppingCart).to.be.gt(0);
        });
      });
    });

    describe(`Endpoint : ${OrderWS.endpoint} - Schema : Blank `, () => {
      let apiResponse: APIResponse;
      let xmlResponse : string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankStatus', baseContext);

        apiResponse = await OrderWS.getBlank(
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

        expect(orderXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNode', baseContext);

        const rootNodes = orderXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('order');
      });

      it('should check each node name, attributes and has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNodes', baseContext);

        const nodes = orderXml.getOrderNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;
          expect(nodeAttributes.length).to.be.eq(0);

          // Empty value
          const isEmptyNode: boolean = xml.isEmpty(node);
          expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.eq(true);
        }
      });
    });

    describe(`Endpoint : ${OrderWS.endpoint} - Schema : Synopsis `, () => {
      let apiResponse: APIResponse;
      let xmlResponse : string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisStatus', baseContext);

        apiResponse = await OrderWS.getSynopsis(
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

        expect(orderXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNode', baseContext);

        const rootNodes = orderXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('order');
      });

      it('should check each node name, attributes and has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNodes', baseContext);

        const nodes = orderXml.getOrderNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;
          expect(nodeAttributes.length).to.be.gte(0);

          if (!([
            'invoice_number',
            'invoice_date',
            'delivery_number',
            'delivery_date',
            'valid',
            'shipping_number',
            'note',
            'reference',
            'associations',
          ].includes(node.nodeName))) {
            // Attribute : format
            expect(nodeAttributes[nodeAttributes.length - 1].nodeName).to.be.eq('format');
          }

          // Empty value
          const isEmptyNode = xml.isEmpty(node);
          expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.eq(true);
        }
      });
    });

    describe(`Endpoint : ${OrderWS.endpoint} - Method : GET `, () => {
      let apiResponse : APIResponse;
      let xmlResponse : string;
      let ordersNode: Element[];

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetStatus1', baseContext);

        apiResponse = await OrderWS.getAll(
          apiContext,
          authorization,
        );

        expect(apiResponse.status()).to.eq(200);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetRootNode1', baseContext);

        xmlResponse = await apiResponse.text();
        expect(orderXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNodeNumber1', baseContext);

        const rootNodes = orderXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('orders');
      });

      it('should check number of nodes under orders node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNumberOfNodes1', baseContext);

        ordersNode = orderXml.getAllOrders(xmlResponse);
        expect(ordersNode.length).to.be.gt(0);
      });

      it('should check each node name, attributes and xlink:href', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetCheckAll1', baseContext);

        for (let c: number = 0; c < ordersNode.length; c++) {
          const orderNode: Element = ordersNode[c];
          expect(orderNode.nodeName).to.be.eq('order');

          // Attributes
          const orderNodeAttributes: NamedNodeMap = orderNode.attributes;
          expect(orderNodeAttributes.length).to.be.eq(2);

          // Attribute : id
          expect(orderNodeAttributes[0].nodeName).to.be.eq('id');
          const orderNodeAttributeId = orderNodeAttributes[0].nodeValue as string;
          expect(orderNodeAttributeId).to.be.eq(parseInt(orderNodeAttributeId, 10).toString());

          // Attribute : xlink:href
          expect(orderNodeAttributes[1].nodeName).to.be.eq('xlink:href');
          expect(orderNodeAttributes[1].nodeValue).to.be.a('string');
        }
      });
    });

    describe(`Endpoint : ${OrderWS.endpoint} - Method : POST `, () => {
      describe(`Endpoint : ${OrderWS.endpoint} - Method : POST - Add Order `, () => {
        let apiResponse: APIResponse;

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostStatus1', baseContext);

          xmlCreate = getOrderXml(idShoppingCart);
          apiResponse = await OrderWS.add(
            apiContext,
            authorization,
            xmlCreate,
          );
          expect(apiResponse.status()).to.eq(201);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostRootNode1', baseContext);

          xmlResponseCreate = await apiResponse.text();
          expect(orderXml.getRootNodeName(xmlResponseCreate)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostNodeNumber1', baseContext);

          const rootNodes = orderXml.getPrestaShopNodes(xmlResponseCreate);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('order');
        });

        it('should check id of the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostCheckId1', baseContext);

          // Attribute : id
          orderNodeID = orderXml.getAttributeValue(xmlResponseCreate, 'id');
          expect(orderNodeID).to.be.a('string');
          expect(orderNodeID).to.be.eq(parseInt(orderNodeID as string, 10).toString());
        });
      });

      describe(`Endpoint : ${OrderWS.endpoint}/{id} - Method : POST - Check with WS `, () => {
        let apiResponse: APIResponse;
        let ordersNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDStatus', baseContext);

          apiResponse = await OrderWS.getById(
            apiContext,
            authorization,
            orderNodeID as string,
          );

          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDRootNode1', baseContext);

          xmlResponseCreate = await apiResponse.text();
          expect(orderXml.getRootNodeName(xmlResponseCreate)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNodeNumber1', baseContext);

          const rootNodes = orderXml.getPrestaShopNodes(xmlResponseCreate);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('order');
        });

        it('should check number of nodes under orders node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNumberOfNodes1', baseContext);

          ordersNodes = orderXml.getOrderNodes(xmlResponseCreate);
          expect(ordersNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < ordersNodes.length; o++) {
            const oNode: Element = ordersNodes[o];

            const objectNodeValue: string|null = orderXml.getAttributeValue(
              xmlCreate,
              oNode.nodeName,
            );

            if (objectNodeValue !== null) {
              if (oNode.nodeName === 'id') {
                expect(oNode.textContent).to.be.eq(orderNodeID);
              } else if (oNode.nodeName === 'id_address_invoice') {
                // @todo : https://github.com/PrestaShop/PrestaShop/issues/34564
              } else if (oNode.nodeName === 'total_paid_real') {
                // @todo : https://github.com/PrestaShop/PrestaShop/issues/34564
              } else if (oNode.nodeName !== 'date_add' && oNode.nodeName !== 'date_upd') {
                expect(oNode.textContent).to.be.eq(objectNodeValue);
              }
            }
          }
        });
      });

      describe(`Endpoint : ${OrderWS.endpoint} - Method : POST - Check On BO `, () => {
        it('should go to \'Orders > Orders\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.ordersParentLink,
            dashboardPage.ordersLink,
          );
          await ordersPage.closeSfToolBar(page);

          const pageTitle = await ordersPage.getPageTitle(page);
          expect(pageTitle).to.contains(ordersPage.pageTitle);
        });

        it('should filter order by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost', baseContext);

          // Filter
          await ordersPage.resetFilter(page);
          await ordersPage.filterOrders(page, 'input', 'id_order', orderNodeID as string);

          // Check number of orders
          const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
          expect(numberOfOrdersAfterFilter).to.be.eq(1);

          const textColumn = await ordersPage.getTextColumn(page, 'id_order', 1);
          expect(textColumn).to.contains(orderNodeID);
        });

        it('should go to view order page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToViewOrderPageAfterPost', baseContext);

          await ordersPage.goToOrder(page, 1);

          const pageTitle = await viewOrderBasePage.getPageTitle(page);
          expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
        });

        describe('Block : Header', () => {
          it('should check current_state', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderCurrentState1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'current_state');
            const value = await viewOrderBasePage.getOrderStatusID(page);
            expect(value.toString()).to.be.eq(xmlValue);
          });

          it('should check reference', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReference1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'reference');
            const value = await viewOrderBasePage.getOrderReference(page);
            expect(value).to.be.eq(xmlValue);
          });
        });

        describe('Block : Customer', () => {
          it('should check id_customer', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderIdCustomer1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'id_customer');
            const value = await orderPageCustomerBlock.getCustomerID(page);
            expect(value.toString()).to.be.eq(xmlValue);
          });
        });

        describe('Block : Products', () => {
          it('should check total_discounts', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalDiscounts1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'total_discounts');
            const value = await orderPageProductsBlock.getOrderTotalDiscounts(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check total_shipping', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalShipping1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'total_shipping');
            const value = await orderPageProductsBlock.getOrderTotalShipping(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check total_products_wt', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalProductWT1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'total_products_wt');
            const value = await orderPageProductsBlock.getOrderTotalProducts(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check total_paid', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalPaid1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'total_paid');
            const value = await orderPageProductsBlock.getOrderTotalProducts(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check order_rows', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderRows1', baseContext);

            const value = await orderPageProductsBlock.getProductDetails(page, 1);

            const xmlValueID = orderXml.getAttributeValue(xmlResponseCreate, 'associations/order_rows/order_row/id');
            expect(value.orderDetailId).to.be.eq(xmlValueID);

            const xmlValueProductID = orderXml.getAttributeValue(
              xmlResponseCreate,
              'associations/order_rows/order_row/product_id',
            );
            expect(value.productId).to.be.eq(xmlValueProductID);
          });
        });

        describe('Block : Tabs', () => {
          it('should check note', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNote1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'note');
            const value = await orderPageTabListBlock.getOrderNoteContent(page);
            expect(value).to.be.eq(xmlValue);
          });

          it('should check recyclable', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderRecyclable1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'recyclable');
            const value = await orderPageTabListBlock.hasBadgeRecyclable(page) ? '1' : '0';
            expect(value).to.be.eq(xmlValue);
          });

          it('should check gift', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderGift1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'gift');
            const value = await orderPageTabListBlock.hasBadgeGift(page) ? '1' : '0';
            expect(value).to.be.eq(xmlValue);
          });

          it('should check gift_message', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderGiftMessage1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'gift_message');
            const value = await orderPageTabListBlock.getGiftMessage(page);
            expect(value).to.be.eq(xmlValue);
          });

          it('should display the Documents Tabs', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab1', baseContext);

            const isTabVisible = await orderPageTabListBlock.goToDocumentsTab(page);
            expect(isTabVisible).to.be.equal(true);
          });

          it('should check invoice_number', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInvoiceNumber1', baseContext);

            const documentType = await orderPageTabListBlock.getDocumentType(page, 1);
            expect(documentType).to.be.equal('Invoice');

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'invoice_number');
            const value = await orderPageTabListBlock.getFileName(page, 1);
            expect(value).to.endWith(xmlValue as string);
          });

          it('should check invoice_date', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInvoiceDate1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'invoice_date');
            const value = await orderPageTabListBlock.getDocumentDate(page, 1);
            expect(value).to.be.equal(date.setDateFormat('mm/dd/yyyy', xmlValue as string, false));
          });

          it('should display the Carriers Tabs', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'displayCarrierTab1', baseContext);

            const isTabVisible = await orderPageTabListBlock.goToCarriersTab(page);
            expect(isTabVisible).to.be.equal(true);
          });

          it('should check shipping_number', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderShippingNumber1', baseContext);

            const value = await orderPageTabListBlock.getCarrierDetails(page);
            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'shipping_number');
            expect(value.trackingNumber).to.be.eq(xmlValue);
          });

          it('should check id_carrier', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderIdCarrier1', baseContext);

            const isModalVisibleBefore = await orderPageTabListBlock.clickOnEditLink(page);
            expect(isModalVisibleBefore).to.be.equal(true);

            const value = await orderPageTabListBlock.getShippingCarrierID(page);

            const isModalHiddenAfter = await orderPageTabListBlock.closeOrderShippingModal(page);
            expect(isModalHiddenAfter).to.be.equal(true);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'id_carrier');
            expect(value.toString()).to.be.eq(xmlValue);
          });
        });

        describe('Block : Payments', () => {
          it('should check payment', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderPayment1', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseCreate, 'payment');
            const value = await orderPagePaymentBlock.getPaymentsDetails(page, 1);
            expect(value.paymentMethod).to.be.eq(xmlValue);
          });
        });

        describe('Reset filters', () => {
          it('should go to \'Orders > Orders\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPagePostReset', baseContext);

            await dashboardPage.goToSubMenu(
              page,
              dashboardPage.ordersParentLink,
              dashboardPage.ordersLink,
            );
            await ordersPage.closeSfToolBar(page);

            const pageTitle = await ordersPage.getPageTitle(page);
            expect(pageTitle).to.contains(ordersPage.pageTitle);
          });

          it('should reset all filters', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPost', baseContext);

            const numberOfCountries = await ordersPage.resetAndGetNumberOfLines(page);
            expect(numberOfCountries).to.be.above(0);
          });
        });
      });
    });

    describe(`Endpoint : ${OrderWS.endpoint} - Method : PUT `, () => {
      describe(`Endpoint : ${OrderWS.endpoint} - Method : PUT - Update Order `, () => {
        let apiResponse: APIResponse;

        it(`should check response status of ${OrderWS.endpoint}/{id}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutStatus2', baseContext);

          xmlUpdate = getOrderXml(idShoppingCart, {
            idOrder: orderNodeID as string,
            invoiceNumber: orderXml.getAttributeValue(xmlResponseCreate, 'invoice_number'),
            invoiceDate: orderXml.getAttributeValue(xmlResponseCreate, 'invoice_date'),
            secureKey: orderXml.getAttributeValue(xmlResponseCreate, 'secure_key'),
          });
          apiResponse = await OrderWS.update(
            apiContext,
            authorization,
            orderNodeID as string,
            xmlUpdate,
          );

          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutRootNode2', baseContext);

          xmlResponseUpdate = await apiResponse.text();
          expect(orderXml.getRootNodeName(xmlResponseUpdate)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutNodeNumber2', baseContext);

          const rootNodes = orderXml.getPrestaShopNodes(xmlResponseUpdate);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('order');
        });

        it('should check id of the order', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutCheckId2', baseContext);

          // Attribute : id
          orderNodeID = orderXml.getAttributeValue(xmlResponseUpdate, 'id');
          expect(orderNodeID).to.be.a('string');
          expect(orderNodeID).to.be.eq(parseInt(orderNodeID as string, 10).toString());
        });
      });

      describe(`Endpoint : ${OrderWS.endpoint}/{id} - Method : PUT - Check with WS `, () => {
        let apiResponse: APIResponse;
        let ordersNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutStatus2', baseContext);

          apiResponse = await OrderWS.getById(
            apiContext,
            authorization,
            orderNodeID as string,
          );

          expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutRootNode2', baseContext);

          xmlResponseUpdate = await apiResponse.text();
          expect(orderXml.getRootNodeName(xmlResponseUpdate)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNodeNumber2', baseContext);

          const rootNodes = orderXml.getPrestaShopNodes(xmlResponseUpdate);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('order');
        });

        it('should check number of nodes under orders node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNumberOfNodes2', baseContext);

          ordersNodes = orderXml.getOrderNodes(xmlResponseUpdate);
          expect(ordersNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterPut', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < ordersNodes.length; o++) {
            const oNode: Element = ordersNodes[o];

            const objectNodeValue: string|null = orderXml.getAttributeValue(
              xmlUpdate,
              oNode.nodeName,
            );

            if (objectNodeValue !== null) {
              if (oNode.nodeName === 'id') {
                expect(oNode.textContent).to.be.eq(orderNodeID);
              } else if (oNode.nodeName === 'id_address_invoice') {
                // @todo : https://github.com/PrestaShop/PrestaShop/issues/34564
              } else if (oNode.nodeName === 'total_paid_real') {
                // @todo : https://github.com/PrestaShop/PrestaShop/issues/34564
              } else if (oNode.nodeName !== 'date_add' && oNode.nodeName !== 'date_upd') {
                expect(oNode.textContent).to.be.eq(objectNodeValue);
              }
            }
          }
        });
      });

      describe(`Endpoint : ${OrderWS.endpoint} - Method : PUT - Check On BO `, () => {
        it('should filter order by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPut', baseContext);

          // Filter
          await ordersPage.resetFilter(page);
          await ordersPage.filterOrders(page, 'input', 'id_order', orderNodeID as string);

          // Check number of orders
          const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
          expect(numberOfOrdersAfterFilter).to.be.eq(1);

          const textColumn = await ordersPage.getTextColumn(page, 'id_order', 1);
          expect(textColumn).to.contains(orderNodeID);
        });

        it('should go to view order page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToViewOrderPageAfterPut', baseContext);

          await ordersPage.goToOrder(page, 1);

          const pageTitle = await viewOrderBasePage.getPageTitle(page);
          expect(pageTitle).to.contains(viewOrderBasePage.pageTitle);
        });

        describe('Block : Header', () => {
          it('should check current_state', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderCurrentState2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'current_state');
            const value = await viewOrderBasePage.getOrderStatusID(page);
            expect(value.toString()).to.be.eq(xmlValue);
          });

          it('should check reference', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReference2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'reference');
            const value = await viewOrderBasePage.getOrderReference(page);
            expect(value).to.be.eq(xmlValue);
          });
        });

        describe('Block : Customer', () => {
          it('should check id_customer', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderIdCustomer2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'id_customer');
            const value = await orderPageCustomerBlock.getCustomerID(page);
            expect(value.toString()).to.be.eq(xmlValue);
          });
        });

        describe('Block : Products', () => {
          it('should check total_discounts', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalDiscounts2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'total_discounts');
            const value = await orderPageProductsBlock.getOrderTotalDiscounts(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check total_shipping', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalShipping2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'total_shipping');
            const value = await orderPageProductsBlock.getOrderTotalShipping(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check total_products_wt', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalProductWT2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'total_products_wt');
            const value = await orderPageProductsBlock.getOrderTotalProducts(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check total_paid', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderTotalPaid2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'total_paid');
            const value = await orderPageProductsBlock.getOrderTotalProducts(page);
            expect(parseFloat(value.toString())).to.be.eq(parseFloat(xmlValue as string));
          });

          it('should check order_rows', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderRows2', baseContext);

            const value = await orderPageProductsBlock.getProductDetails(page, 1);

            const xmlValueID = orderXml.getAttributeValue(xmlResponseUpdate, 'associations/order_rows/order_row/id');
            expect(value.orderDetailId).to.be.eq(xmlValueID);

            const xmlValueProductID = orderXml.getAttributeValue(
              xmlResponseUpdate,
              'associations/order_rows/order_row/product_id',
            );
            expect(value.productId).to.be.eq(xmlValueProductID);
          });
        });

        describe('Block : Tabs', () => {
          it('should check note', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderNote2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'note');
            const value = await orderPageTabListBlock.getOrderNoteContent(page);
            expect(value).to.be.eq(xmlValue);
          });

          it('should check recyclable', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderRecyclable2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'recyclable');
            const value = await orderPageTabListBlock.hasBadgeRecyclable(page) ? '1' : '0';
            expect(value).to.be.eq(xmlValue);
          });

          it('should check gift', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderGift2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'gift');
            const value = await orderPageTabListBlock.hasBadgeGift(page) ? '1' : '0';
            expect(value).to.be.eq(xmlValue);
          });

          it('should check gift_message', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderGiftMessage2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'gift_message');
            const value = await orderPageTabListBlock.getGiftMessage(page);
            expect(value).to.be.eq(xmlValue);
          });

          it('should display the Documents Tabs', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab2', baseContext);

            const isTabVisible = await orderPageTabListBlock.goToDocumentsTab(page);
            expect(isTabVisible).to.be.equal(true);
          });

          it('should check invoice_number', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInvoiceNumber2', baseContext);

            const documentType = await orderPageTabListBlock.getDocumentType(page, 1);
            expect(documentType).to.be.equal('Invoice');

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'invoice_number');
            const value = await orderPageTabListBlock.getFileName(page, 1);
            expect(value).to.endWith(xmlValue as string);
          });

          it('should check invoice_date', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderInvoiceDate2', baseContext);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'invoice_date');
            const value = await orderPageTabListBlock.getDocumentDate(page, 1);
            expect(value).to.be.equal(date.setDateFormat('mm/dd/yyyy', xmlValue as string, false));
          });

          it('should display the Carriers Tabs', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'displayCarrierTab2', baseContext);

            const isTabVisible = await orderPageTabListBlock.goToCarriersTab(page);
            expect(isTabVisible).to.be.equal(true);
          });

          it('should check shipping_number', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderShippingNumber2', baseContext);

            const value = await orderPageTabListBlock.getCarrierDetails(page);
            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'shipping_number');
            expect(value.trackingNumber).to.be.eq(xmlValue);
          });

          it('should check id_carrier', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderIdCarrier2', baseContext);

            const isModalVisibleBefore = await orderPageTabListBlock.clickOnEditLink(page);
            expect(isModalVisibleBefore).to.be.equal(true);

            const value = await orderPageTabListBlock.getShippingCarrierID(page);

            const isModalHiddenAfter = await orderPageTabListBlock.closeOrderShippingModal(page);
            expect(isModalHiddenAfter).to.be.equal(true);

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'id_carrier');
            expect(value.toString()).to.be.eq(xmlValue);
          });
        });

        describe('Block : Payments', () => {
          // @todo : https://github.com/PrestaShop/PrestaShop/issues/34576
          it('should check payment', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkOrderPayment2', baseContext);

            this.skip();

            const xmlValue = orderXml.getAttributeValue(xmlResponseUpdate, 'payment');
            const value = await orderPagePaymentBlock.getPaymentsDetails(page, 1);
            expect(value.paymentMethod).to.be.eq(xmlValue);
          });
        });

        describe('Reset filters', () => {
          it('should go to \'Orders > Orders\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPagePutReset', baseContext);

            await dashboardPage.goToSubMenu(
              page,
              dashboardPage.ordersParentLink,
              dashboardPage.ordersLink,
            );
            await ordersPage.closeSfToolBar(page);

            const pageTitle = await ordersPage.getPageTitle(page);
            expect(pageTitle).to.contains(ordersPage.pageTitle);
          });

          it('should reset all filters', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPut', baseContext);

            const numberOfCountries = await ordersPage.resetAndGetNumberOfLines(page);
            expect(numberOfCountries).to.be.above(0);
          });
        });
      });
    });

    describe(`Endpoint : ${OrderWS.endpoint} - Method : DELETE `, () => {
      it(`should request the endpoint ${OrderWS.endpoint}/{id} with method DELETE`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodDelete', baseContext);

        const apiResponse = await OrderWS.delete(
          apiContext,
          authorization,
          orderNodeID as string,
        );

        expect(apiResponse.status()).to.eq(200);
      });

      it(`should request the endpoint ${OrderWS.endpoint}/{id} with method GET`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterDelete', baseContext);

        const apiResponse = await OrderWS.getById(
          apiContext,
          authorization,
          orderNodeID as string,
        );

        expect(apiResponse.status()).to.eq(404);
      });

      it('should filter order by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterDelete', baseContext);

        // Filter
        await ordersPage.resetFilter(page);
        await ordersPage.filterOrders(page, 'input', 'id_order', orderNodeID as string);

        // Check number of orders
        const numberOfCountriesAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
        expect(numberOfCountriesAfterFilter).to.be.eq(0);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfCountries = await ordersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCountries).to.be.above(0);
      });
    });
  });

  // Remove a new webservice key
  removeWebserviceKey(wsKeyDescription, `${baseContext}_postTest_1`);

  // Disable webservice
  setWebserviceStatus(false, `${baseContext}_postTest_2`);
});
