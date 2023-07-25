// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import xml from '@utils/xml';

// Import webservices
import StoreWS from '@webservices/store/storeWS';
import storeXml from '@webservices/store/storeXml';

// Import commonTests
import {addWebserviceKey, removeWebserviceKey, setWebserviceStatus} from '@commonTests/BO/advancedParameters/ws';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import dashboardPage from '@pages/BO/dashboard';
import contactPage from '@pages/BO/shopParameters/contact';
import storesPage from '@pages/BO/shopParameters/stores';
import addStorePage from '@pages/BO/shopParameters/stores/add';

// Import data
import {WebservicePermission} from '@data/types/webservice';
import {getStoreXml, getUpdateStoreXml} from '@data/xml/store';

import {expect} from 'chai';
import type {
  APIResponse, APIRequestContext, BrowserContext, Page,
} from 'playwright';

const baseContext: string = 'functional_WS_storesCRUD';

describe('WS - Stores : CRUD', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let wsKey: string = '';
  let authorization: string = '';

  const wsKeyDescription: string = 'Webservice Key - Stores';
  const wsKeyPermissions: WebservicePermission[] = [
    {
      resource: 'stores',
      methods: ['all'],
    },
  ];
  const xmlCreate: string = getStoreXml();
  const xmlValueHoursLang1 = storeXml.getLangEltTextContent(xmlCreate, 'hours', '1');
  const hoursArrLang1: string[] = xmlValueHoursLang1.split(',');
  const xmlValueHoursLang2 = storeXml.getLangEltTextContent(xmlCreate, 'hours', '2');
  const hoursArrLang2: string[] = xmlValueHoursLang2.split(',');
  let xmlUpdate: string;

  const week = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
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

  describe('Stores : CRUD', () => {
    let storeNodeID: string = '';
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
        await expect(pageTitle).to.contains(webservicePage.pageTitle);
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
        await expect(description).to.contains(wsKeyDescription);

        wsKey = await webservicePage.getTextColumnFromTable(page, 1, 'key');
        authorization = `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`;
        await expect(wsKey).to.be.not.empty;
      });
    });

    describe(`Endpoint : ${StoreWS.endpoint} - Schema : Blank `, () => {
      let apiResponse: APIResponse;
      let xmlResponse : string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestStoreBlankStatus', baseContext);

        apiResponse = await StoreWS.getBlank(
          apiContext,
          authorization,
        );
        await expect(apiResponse.status()).to.eq(200);
      });

      it('should check that the blank XML can be parsed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankValid', baseContext);

        xmlResponse = await apiResponse.text();

        const isValidXML = xml.isValid(xmlResponse);
        await expect(isValidXML).to.be.true;
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestStoreBlankRootNode', baseContext);

        expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNode', baseContext);

        const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('store');
      });

      it('should check each node name, attributes and has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestBlankChildNodes', baseContext);

        const nodes = storeXml.getStoreNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;
          expect(nodeAttributes.length).to.be.eq(0);

          // Empty value
          const isEmptyNode: boolean = xml.isEmpty(node);
          await expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.be.true;
        }
      });
    });

    describe(`Endpoint : ${StoreWS.endpoint} - Schema : Synopsis `, () => {
      let apiResponse: APIResponse;
      let xmlResponse : string;

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisStatus', baseContext);

        apiResponse = await StoreWS.getSynopsis(
          apiContext,
          authorization,
        );
        await expect(apiResponse.status()).to.eq(200);
      });

      it('should check that the synopsis XML can be parsed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisValid', baseContext);

        xmlResponse = await apiResponse.text();

        const isValidXML = xml.isValid(xmlResponse);
        await expect(isValidXML).to.be.true;
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisRootNode', baseContext);

        expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNode', baseContext);

        const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('store');
      });

      it('should check each node name, attributes and has empty values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestSynopsisChildNodes', baseContext);

        const nodes = storeXml.getStoreNodes(xmlResponse);
        expect(nodes.length).to.be.gt(0);

        for (let c: number = 0; c < nodes.length; c++) {
          const node: Element = nodes[c];

          // Attributes
          const nodeAttributes: NamedNodeMap = node.attributes;
          expect(nodeAttributes.length).to.be.gte(1);

          // Attribute : format
          if (node.nodeName !== 'postcode') {
            expect(nodeAttributes[nodeAttributes.length - 1].nodeName).to.be.eq('format');
          }

          // Empty value
          const isEmptyNode = xml.isEmpty(node);
          await expect(isEmptyNode, `The node ${node.nodeName} is not empty`).to.be.true;
        }
      });
    });

    describe(`Endpoint : ${StoreWS.endpoint} - Method : GET `, () => {
      let apiResponse : APIResponse;
      let xmlResponse : string;
      let storesNode: Element[];

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetStatus1', baseContext);

        apiResponse = await StoreWS.getAll(
          apiContext,
          authorization,
        );

        await expect(apiResponse.status()).to.eq(200);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetRootNode1', baseContext);

        xmlResponse = await apiResponse.text();
        expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNodeNumber1', baseContext);

        const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('stores');
      });

      it('should check number of nodes under stores node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNumberOfNodes1', baseContext);

        storesNode = storeXml.getAllStores(xmlResponse);
        expect(storesNode.length).to.gt(0);
      });

      it('should check each node name, attributes and xlink:href', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetCheckAll1', baseContext);

        for (let i: number = 0; i < storesNode.length; i++) {
          const storeNode: Element = storesNode[i];
          expect(storeNode.nodeName).to.be.eq('store');

          // Attributes
          const storeNodeAttributes: NamedNodeMap = storeNode.attributes;
          expect(storeNodeAttributes.length).to.be.eq(2);

          // Attribute : id
          expect(storeNodeAttributes[0].nodeName).to.be.eq('id');
          const storeNodeAttributeId = storeNodeAttributes[0].nodeValue as string;
          expect(storeNodeAttributeId).to.be.eq(parseInt(storeNodeAttributeId, 10).toString());

          // Attribute : xlink:href
          expect(storeNodeAttributes[1].nodeName).to.be.eq('xlink:href');
          expect(storeNodeAttributes[1].nodeValue).to.be.a('string');
        }
      });
    });

    describe(`Endpoint : ${StoreWS.endpoint} - Method : POST `, () => {
      describe(`Endpoint : ${StoreWS.endpoint} - Method : POST - Add Store `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostStatus1', baseContext);

          apiResponse = await StoreWS.add(
            apiContext,
            authorization,
            xmlCreate,
          );

          await expect(apiResponse.status()).to.eq(201);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostNodeNumber1', baseContext);

          const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('store');
        });

        it('should check id of the store', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostCheckId1', baseContext);

          // Attribute : id
          storeNodeID = storeXml.getEltTextContent(xmlResponse, 'id');
          expect(storeNodeID).to.be.eq(parseInt(storeNodeID, 10).toString());
        });
      });

      describe(`Endpoint : ${StoreWS.endpoint}/{id} - Method : POST - Check with WS `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;
        let storesNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDStatus', baseContext);

          apiResponse = await StoreWS.getById(
            apiContext,
            authorization,
            storeNodeID,
          );
          await expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNodeNumber1', baseContext);

          const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('store');
        });

        it('should check number of nodes under stores node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNumberOfNodes1', baseContext);

          storesNodes = storeXml.getStoreNodes(xmlResponse);
          expect(storesNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < storesNodes.length; o++) {
            const oNode: Element = storesNodes[o];

            if (oNode.nodeName === 'id') {
              expect(oNode.textContent).to.be.eq(storeNodeID);
            } else if (oNode.nodeName === 'name' || oNode.nodeName === 'hours' || oNode.nodeName === 'address1'
              || oNode.nodeName === 'address2' || oNode.nodeName === 'note') {
              const objectNodeValueEN = storeXml.getLangEltTextContent(
                xmlResponse,
                oNode.nodeName,
                '1',
              );

              const createNodeValueEN = storeXml.getLangEltTextContent(
                xmlCreate,
                oNode.nodeName,
                '1',
              );

              expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

              const objectNodeValueFR = storeXml.getLangEltTextContent(
                xmlResponse,
                oNode.nodeName,
                '2',
              );
              const createNodeValueFR = storeXml.getLangEltTextContent(
                xmlCreate,
                oNode.nodeName,
                '2',
              );
              expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
            } else if (oNode.nodeName !== 'date_add' && oNode.nodeName !== 'date_upd') {
              const objectNodeValue: string = storeXml.getEltTextContent(
                xmlCreate,
                oNode.nodeName,
              );
              expect(oNode.textContent).to.be.eq(objectNodeValue);
            }
          }
        });
      });

      describe(`Endpoint : ${StoreWS.endpoint} - Method : POST - Check On BO `, () => {
        it('should go to \'Shop Parameters > Contact\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.shopParametersParentLink,
            dashboardPage.contactLink,
          );
          await contactPage.closeSfToolBar(page);

          const pageTitle = await contactPage.getPageTitle(page);
          await expect(pageTitle).to.contains(contactPage.pageTitle);
        });

        it('should go to \'Stores\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPagePost', baseContext);

          await contactPage.goToStoresPage(page);

          const pageTitle = await storesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(storesPage.pageTitle);
        });

        it('should filter store by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost', baseContext);

          // Filter
          await storesPage.resetFilter(page);
          await storesPage.filterTable(page, 'input', 'id_store', storeNodeID);

          // Check number of stores
          const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
          await expect(numberOfStoresAfterFilter).to.be.eq(1);

          const textColumn = await storesPage.getTextColumn(page, 1, 'id_store');
          await expect(textColumn).to.contains(storeNodeID);
        });

        it('should go to edit store page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditStorePageAfterPost', baseContext);

          await storesPage.gotoEditStorePage(page, 1);

          const pageTitle = await addStorePage.getPageTitle(page);
          await expect(pageTitle).to.contains(addStorePage.pageTitleEdit);
        });

        it('should check store\'s name language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreNameLang1', baseContext);

          const xmlValueName1 = storeXml.getLangEltTextContent(xmlCreate, 'name', '1');
          const valueName1 = await addStorePage.getInputValue(page, 'name', '1');
          expect(valueName1).to.be.eq(xmlValueName1);
        });

        it('should check store\'s name language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreNameLang2', baseContext);

          const xmlValueName2 = storeXml.getLangEltTextContent(xmlCreate, 'name', '2');
          const valueName2 = await addStorePage.getInputValue(page, 'name', '2');
          expect(valueName2).to.be.eq(xmlValueName2);
        });

        it('should check store\'s address 1 language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress1Lang1', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlCreate, 'address1', '1');
          const valueAddress = await addStorePage.getInputValue(page, 'address1', '1');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s address 1 language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress1Lang2', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlCreate, 'address1', '2');
          const valueAddress = await addStorePage.getInputValue(page, 'address1', '2');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s address 2 language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress2Lang1', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlCreate, 'address2', '1');
          const valueAddress = await addStorePage.getInputValue(page, 'address2', '1');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s address 2 language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress2Lang2', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlCreate, 'address2', '2');
          const valueAddress = await addStorePage.getInputValue(page, 'address2', '2');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s postcode', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStorePostcode', baseContext);

          const xmlValuePostcode = storeXml.getEltTextContent(xmlCreate, 'postcode');
          const valuePostcode = await addStorePage.getInputValue(page, 'postcode');
          expect(valuePostcode).to.be.eq(xmlValuePostcode);
        });

        it('should check store\'s city', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreCity', baseContext);

          const xmlValueCity = storeXml.getEltTextContent(xmlCreate, 'city');
          const valueCity = await addStorePage.getInputValue(page, 'city');

          expect(valueCity).to.be.eq(xmlValueCity);
        });

        it('should check store\'s country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreCountry', baseContext);

          const xmlValueIDCountry = storeXml.getEltTextContent(xmlCreate, 'id_country');
          const valueIDCountry = await addStorePage.getSelectValue(page, 'id_country');

          expect(valueIDCountry).to.be.eq(xmlValueIDCountry);
        });

        it('should check store\'s state', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreState', baseContext);

          const xmlValueIDState  = storeXml.getEltTextContent(xmlCreate, 'id_state');
          const valueIDState = await addStorePage.getSelectValue(page, 'id_state');
          expect(valueIDState).to.be.eq(xmlValueIDState);
        });

        it('should check store\'s latitude', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreLatitude', baseContext);

          const xmlValueLatitude = storeXml.getEltTextContent(xmlCreate, 'latitude');
          const valueLatitude = await addStorePage.getInputValue(page, 'latitude');
          expect(valueLatitude).to.be.eq(xmlValueLatitude);
        });

        it('should check store\'s longitude', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreLongitude', baseContext);

          const xmlValueLongitude = storeXml.getEltTextContent(xmlCreate, 'longitude');
          const valueLongitude = await addStorePage.getInputValue(page, 'longitude');
          expect(valueLongitude).to.be.eq(xmlValueLongitude);
        });

        it('should check store\'s phone', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStorePhone', baseContext);

          const xmlValuePhone = storeXml.getEltTextContent(xmlCreate, 'phone');
          const valuePhone = await addStorePage.getInputValue(page, 'phone');
          expect(valuePhone).to.be.eq(xmlValuePhone);
        });

        it('should check store\'s fax', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreFax', baseContext);

          const xmlValueFax = storeXml.getEltTextContent(xmlCreate, 'fax');
          const valueFax = await addStorePage.getInputValue(page, 'fax');
          expect(valueFax).to.be.eq(xmlValueFax);
        });

        it('should check store\'s email', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreEmail', baseContext);

          const xmlValueFax = storeXml.getEltTextContent(xmlCreate, 'email');
          const valueFax = await addStorePage.getInputValue(page, 'email');
          expect(valueFax).to.be.eq(xmlValueFax);
        });

        it('should check store is active', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreActive', baseContext);

          const xmlValueActive = storeXml.getEltTextContent(xmlCreate, 'active');
          const active = await addStorePage.isActive(page, 'on');
          expect(active).to.be.eq(xmlValueActive !== '0');
        });

        week.forEach((day, index) => {
          it(`should check store's ${day} hours language 1`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkStoreHours${day}Lang1`, baseContext);

            const dayHours = hoursArrLang1[index];
            let expectedDayHours = '';

            if (index === 0) {
              expectedDayHours = dayHours.substring(3, dayHours.length - 2);
            } else if (index === (week.length - 1)) {
              expectedDayHours = dayHours.substring(2, dayHours.length - 3);
            } else {
              expectedDayHours = dayHours.substring(2, dayHours.length - 2);
            }

            const dayValue = await addStorePage.getInputValue(page, `${day}`, '1');
            expect(dayValue).to.be.eq(expectedDayHours);
          });

          it(`should check store's ${day} hours language 2`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkStoreHours${day}Lang2`, baseContext);

            const dayHours = hoursArrLang2[index];
            let expectedDayHours = '';

            if (index === 0) {
              expectedDayHours = dayHours.substring(3, dayHours.length - 2);
            } else if (index === (week.length - 1)) {
              expectedDayHours = dayHours.substring(2, dayHours.length - 3);
            } else {
              expectedDayHours = dayHours.substring(2, dayHours.length - 2);
            }
            const dayValue = await addStorePage.getInputValue(page, `${day}`, '2');
            expect(dayValue).to.be.eq(expectedDayHours);
          });
        });

        it('should go to \'Stores\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPagePostReset', baseContext);

          await contactPage.goToStoresPage(page);

          const pageTitle = await storesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(storesPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPost', baseContext);

          const numberOfStores = await storesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfStores).to.be.above(0);
        });
      });
    });

    describe(`Endpoint : ${StoreWS.endpoint} - Method : PUT `, () => {
      describe(`Endpoint : ${StoreWS.endpoint} - Method : PUT - Update Store `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;

        it(`should check response status of ${StoreWS.endpoint}/{id}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutStatus1', baseContext);
          xmlUpdate = getUpdateStoreXml(storeNodeID);
          apiResponse = await StoreWS.update(
            apiContext,
            authorization,
            storeNodeID,
            xmlUpdate,
          );

          await expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutNodeNumber1', baseContext);

          const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('store');
        });

        it('should check id of the store', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutCheckId1', baseContext);

          // Attribute : id
          storeNodeID = storeXml.getEltTextContent(xmlResponse, 'id');
          expect(storeNodeID).to.be.eq(parseInt(storeNodeID, 10).toString());
        });
      });

      describe(`Endpoint : ${StoreWS.endpoint}/{id} - Method : PUT - Check with WS `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;
        let storesNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutStatus2', baseContext);

          apiResponse = await StoreWS.getById(
            apiContext,
            authorization,
            storeNodeID,
          );

          await expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutRootNode2', baseContext);

          xmlResponse = await apiResponse.text();
          expect(storeXml.getRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNodeNumber1', baseContext);

          const rootNodes = storeXml.getPrestaShopNodes(xmlResponse);
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('store');
        });

        it('should check number of nodes under stores node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNumberOfNodes1', baseContext);

          storesNodes = storeXml.getStoreNodes(xmlResponse);
          expect(storesNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointStoresIdMethodGetAfterPut', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < storesNodes.length; o++) {
            const oNode: Element = storesNodes[o];

            if (oNode.nodeName === 'id') {
              expect(oNode.textContent).to.be.eq(storeNodeID);
            } else if (oNode.nodeName === 'name' || oNode.nodeName === 'hours' || oNode.nodeName === 'address1'
              || oNode.nodeName === 'address2' || oNode.nodeName === 'note') {
              const objectNodeValueEN = storeXml.getLangEltTextContent(
                xmlResponse,
                oNode.nodeName,
                '1',
              );

              const createNodeValueEN = storeXml.getLangEltTextContent(
                xmlUpdate,
                oNode.nodeName,
                '1',
              );

              expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

              const objectNodeValueFR = storeXml.getLangEltTextContent(
                xmlResponse,
                oNode.nodeName,
                '2',
              );
              const createNodeValueFR = storeXml.getLangEltTextContent(
                xmlUpdate,
                oNode.nodeName,
                '2',
              );
              expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
            } else if (oNode.nodeName !== 'date_add' && oNode.nodeName !== 'date_upd') {
              const objectNodeValue: string = storeXml.getEltTextContent(
                xmlUpdate,
                oNode.nodeName,
              );
              expect(oNode.textContent).to.be.eq(objectNodeValue);
            }
          }
        });
      });

      describe(`Endpoint : ${StoreWS.endpoint} - Method : PUT - Check On BO `, () => {
        it('should filter store by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost2', baseContext);

          // Filter
          await storesPage.resetFilter(page);
          await storesPage.filterTable(page, 'input', 'id_store', storeNodeID);

          // Check number of stores
          const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
          await expect(numberOfStoresAfterFilter).to.be.eq(1);

          const textColumn = await storesPage.getTextColumn(page, 1, 'id_store');
          await expect(textColumn).to.contains(storeNodeID);
        });

        it('should go to edit store page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditStorePageAfterPost2', baseContext);

          await storesPage.gotoEditStorePage(page, 1);

          const pageTitle = await addStorePage.getPageTitle(page);
          await expect(pageTitle).to.contains(addStorePage.pageTitleEdit);
        });

        it('should check store\'s name language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreNameLang12', baseContext);

          const xmlValueName1 = storeXml.getLangEltTextContent(xmlUpdate, 'name', '1');
          const valueName1 = await addStorePage.getInputValue(page, 'name', '1');
          expect(valueName1).to.be.eq(xmlValueName1);
        });

        it('should check store\'s name language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreNameLang22', baseContext);

          const xmlValueName2 = storeXml.getLangEltTextContent(xmlUpdate, 'name', '2');
          const valueName2 = await addStorePage.getInputValue(page, 'name', '2');
          expect(valueName2).to.be.eq(xmlValueName2);
        });

        it('should check store\'s address 1 language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress1Lang12', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlUpdate, 'address1', '1');
          const valueAddress = await addStorePage.getInputValue(page, 'address1', '1');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s address 1 language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress1Lang22', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlUpdate, 'address1', '2');
          const valueAddress = await addStorePage.getInputValue(page, 'address1', '2');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s address 2 language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress1Lang13', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlUpdate, 'address2', '1');
          const valueAddress = await addStorePage.getInputValue(page, 'address2', '1');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s address 2 language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreAddress2Lang22', baseContext);

          const xmlValueAddress = storeXml.getLangEltTextContent(xmlUpdate, 'address2', '2');
          const valueAddress = await addStorePage.getInputValue(page, 'address2', '2');
          expect(valueAddress).to.be.eq(xmlValueAddress);
        });

        it('should check store\'s postcode', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStorePostcode2', baseContext);

          const xmlValuePostcode = storeXml.getEltTextContent(xmlUpdate, 'postcode');
          const valuePostcode = await addStorePage.getInputValue(page, 'postcode');
          expect(valuePostcode).to.be.eq(xmlValuePostcode);
        });

        it('should check store\'s city', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreCity2', baseContext);

          const xmlValueCity = storeXml.getEltTextContent(xmlUpdate, 'city');
          const valueCity = await addStorePage.getInputValue(page, 'city');
          expect(valueCity).to.be.eq(xmlValueCity);
        });

        it('should check store\'s country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreCountry2', baseContext);

          const xmlValueIDCountry = storeXml.getEltTextContent(xmlUpdate, 'id_country');
          const valueIDCountry = await addStorePage.getSelectValue(page, 'id_country');
          expect(valueIDCountry).to.be.eq(xmlValueIDCountry);
        });

        it('should check store\'s state', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreState2', baseContext);

          const xmlValueIDState = storeXml.getEltTextContent(xmlUpdate, 'id_state');
          const valueIDState = await addStorePage.getSelectValue(page, 'id_state');
          expect(valueIDState).to.be.eq(xmlValueIDState);
        });

        it('should check store\'s latitude', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreLatitude2', baseContext);

          const xmlValueLatitude = storeXml.getEltTextContent(xmlUpdate, 'latitude');
          const valueLatitude = await addStorePage.getInputValue(page, 'latitude');
          expect(valueLatitude).to.be.eq(xmlValueLatitude);
        });

        it('should check store\'s longitude', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreLongitude2', baseContext);

          const xmlValueLongitude = storeXml.getEltTextContent(xmlUpdate, 'longitude');
          const valueLongitude = await addStorePage.getInputValue(page, 'longitude');
          expect(valueLongitude).to.be.eq(xmlValueLongitude);
        });

        it('should check store\'s phone', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStorePhone2', baseContext);

          const xmlValuePhone = storeXml.getEltTextContent(xmlUpdate, 'phone');
          const valuePhone = await addStorePage.getInputValue(page, 'phone');
          expect(valuePhone).to.be.eq(xmlValuePhone);
        });

        it('should check store\'s fax', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreFax2', baseContext);

          const xmlValueFax = storeXml.getEltTextContent(xmlUpdate, 'fax');
          const valueFax = await addStorePage.getInputValue(page, 'fax');
          expect(valueFax).to.be.eq(xmlValueFax);
        });

        it('should check store\'s email', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreEmail2', baseContext);

          const xmlValueFax = storeXml.getEltTextContent(xmlUpdate, 'email');
          const valueFax = await addStorePage.getInputValue(page, 'email');
          expect(valueFax).to.be.eq(xmlValueFax);
        });

        it('should check store is active', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkStoreActive2', baseContext);

          const xmlValueActive = storeXml.getEltTextContent(xmlUpdate, 'active');
          const active = await addStorePage.isActive(page, 'on');
          expect(active).to.be.eq((xmlValueActive !== '0'));
        });

        week.forEach((day, index) => {
          it(`should check store's ${day} hours language 1`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkStoreHours${day}Lang1${index}`, baseContext);

            const xmlValueUpdatedHoursLang1 = storeXml.getLangEltTextContent(xmlUpdate, 'hours', '1');
            const hoursArrUpdatedLang1: string[] = xmlValueUpdatedHoursLang1.split(',');
            const dayHours = hoursArrUpdatedLang1[index];

            let expectedDayHours = '';

            if (index === 0) {
              expectedDayHours = dayHours.substring(3, dayHours.length - 2);
            } else if (index === (week.length - 1)) {
              expectedDayHours = dayHours.substring(2, dayHours.length - 3);
            } else {
              expectedDayHours = dayHours.substring(2, dayHours.length - 2);
            }

            const dayValue = await addStorePage.getInputValue(page, `${day}`, '1');
            expect(dayValue).to.be.eq(expectedDayHours);
          });

          it(`should check store's ${day} hours language 2`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkStoreHours${day}Lang2${index}`, baseContext);

            const xmlValueUpdatedHoursLang2 = storeXml.getLangEltTextContent(xmlUpdate, 'hours', '2');
            const hoursArrUpdatedLang2: string[] = xmlValueUpdatedHoursLang2.split(',');
            const dayHours = hoursArrUpdatedLang2[index];

            let expectedDayHours = '';

            if (index === 0) {
              expectedDayHours = dayHours.substring(3, dayHours.length - 2);
            } else if (index === (week.length - 1)) {
              expectedDayHours = dayHours.substring(2, dayHours.length - 3);
            } else {
              expectedDayHours = dayHours.substring(2, dayHours.length - 2);
            }

            const dayValue = await addStorePage.getInputValue(page, `${day}`, '2');
            expect(dayValue).to.be.eq(expectedDayHours);
          });
        });

        it('should go to \'Stores\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToStoresPagePostReset2', baseContext);

          await contactPage.goToStoresPage(page);

          const pageTitle = await storesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(storesPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPost2', baseContext);

          const numberOfStores = await storesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfStores).to.be.above(0);
        });
      });
    });

    describe(`Endpoint : ${StoreWS.endpoint} - Method : DELETE `, () => {
      it(`should request the endpoint ${StoreWS.endpoint}/{id} with method DELETE`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointStoresMethodDelete', baseContext);

        const apiResponse = await StoreWS.delete(
          apiContext,
          authorization,
          storeNodeID,
        );

        await expect(apiResponse.status()).to.eq(200);
      });

      it(`should request the endpoint ${StoreWS.endpoint}/{id} with method GET`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointStoresIdMethodGetAfterDelete', baseContext);

        const apiResponse = await StoreWS.getById(
          apiContext,
          authorization,
          storeNodeID,
        );

        await expect(apiResponse.status()).to.eq(404);
      });

      it('should filter store by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterDelete', baseContext);

        // Filter
        await storesPage.resetFilter(page);
        await storesPage.filterTable(page, 'input', 'id_store', storeNodeID);

        // Check number of stores
        const numberOfStoresAfterFilter = await storesPage.getNumberOfElementInGrid(page);
        await expect(numberOfStoresAfterFilter).to.be.eq(0);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfStores = await storesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfStores).to.be.above(0);
      });
    });
  });

  // Remove a new webservice key
  removeWebserviceKey(wsKeyDescription, `${baseContext}_postTest_1`);

  // Disable webservice
  setWebserviceStatus(false, `${baseContext}_postTest_2`);
});
