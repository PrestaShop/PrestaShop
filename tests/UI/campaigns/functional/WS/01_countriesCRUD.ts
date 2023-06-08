// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import xmlHelper from '@utils/wsHelpers/xml';
import Endpoints from '@utils/wsHelpers/endpoints.enum';

// Import commonTests
import {addWebserviceKey, removeWebserviceKey, setWebserviceStatus} from '@commonTests/BO/advancedParameters/ws';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import webservicePage from '@pages/BO/advancedParameters/webservice';
import dashboardPage from '@pages/BO/dashboard';
import zonesPage from '@pages/BO/international/locations';
import countriesPage from '@pages/BO/international/locations/countries';
import addCountryPage from '@pages/BO/international/locations/countries/add';

// Import data
import {WebservicePermission} from '@data/types/webservice';
import getCountryXml from '@data/xml/country';

import {expect} from 'chai';
import type {
  APIResponse, APIRequestContext, BrowserContext, Page,
} from 'playwright';

const baseContext: string = 'functional_WS_countriesCRUD';

describe('WS - Countries : CRUD', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let wsKey: string = '';

  const wsKeyDescription: string = 'Webservice Key - Countries';
  const wsKeyPermissions: WebservicePermission[] = [
    {
      resource: 'countries',
      methods: ['all'],
    },
  ];
  const xmlCreate: string = getCountryXml();
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

  describe('Countries : CRUD', () => {
    let countryNodeID: string = '';
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
        await expect(wsKey).to.be.not.empty;
      });
    });

    describe('Endpoint : /api/countries - Method : GET ', () => {
      let apiResponse : APIResponse;
      let xmlResponse : string;
      let countriesNode: Element[];

      it('should check response status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetStatus1', baseContext);

        apiResponse = await apiContext.get(Endpoints.COUNTRIES, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });

        await expect(apiResponse.status()).to.eq(200);
      });

      it('should check response root node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetRootNode1', baseContext);

        xmlResponse = await apiResponse.text();
        expect(xmlHelper.getWSRootNodeName(xmlResponse)).to.be.eq('prestashop');
      });

      it('should check number of node under prestashop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNodeNumber1', baseContext);

        const rootNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/*');
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('countries');
      });

      it('should check number of nodes under countries node', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetNumberOfNodes1', baseContext);

        countriesNode = xmlHelper.getWSNodes(xmlResponse, '/prestashop/countries/*');
        expect(countriesNode.length).to.be.gt(0);
      });

      it('should check each node name, attributes and xlink:href', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestGetCheckAll1', baseContext);

        for (let c: number = 0; c < countriesNode.length; c++) {
          const countryNode: Element = countriesNode[c];
          expect(countryNode.nodeName).to.be.eq('country');

          // Attributes
          const countryNodeAttributes: NamedNodeMap = countryNode.attributes;
          expect(countryNodeAttributes.length).to.be.eq(2);

          // Attribute : id
          expect(countryNodeAttributes[0].nodeName).to.be.eq('id');
          const countryNodeAttributeId = countryNodeAttributes[0].nodeValue as string;
          expect(countryNodeAttributeId).to.be.eq(parseInt(countryNodeAttributeId, 10).toString());

          // Attribute : xlink:href
          expect(countryNodeAttributes[1].nodeName).to.be.eq('xlink:href');
          expect(countryNodeAttributes[1].nodeValue).to.be.a('string');
        }
      });
    });

    describe('Endpoint : /api/countries - Method : POST ', () => {
      describe('Endpoint : /api/countries - Method : POST - Add Country ', () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostStatus1', baseContext);

          apiResponse = await apiContext.post(Endpoints.COUNTRIES, {
            headers: {
              Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
            },
            data: xmlCreate,
          });
          await expect(apiResponse.status()).to.eq(201);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(xmlHelper.getWSRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostNodeNumber1', baseContext);

          const rootNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/*');
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('country');
        });

        it('should check id of the country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPostCheckId1', baseContext);

          // Attribute : id
          countryNodeID = xmlHelper.getWSNodeValue(xmlResponse, '/prestashop/country/id');
          expect(countryNodeID).to.be.eq(parseInt(countryNodeID, 10).toString());
        });
      });

      describe(`Endpoint : /${Endpoints.COUNTRIES}{id} - Method : POST - Check with WS `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;
        let countriesNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDStatus', baseContext);

          apiResponse = await apiContext.get(Endpoints.COUNTRIES + countryNodeID, {
            headers: {
              Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
            },
          });
          await expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(xmlHelper.getWSRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNodeNumber1', baseContext);

          const rootNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/*');
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('country');
        });

        it('should check number of nodes under countries node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDNumberOfNodes1', baseContext);

          countriesNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/country/*');
          expect(countriesNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < countriesNodes.length; o++) {
            const oNode: Element = countriesNodes[o];

            if (oNode.nodeName === 'id') {
              expect(oNode.textContent).to.be.eq(countryNodeID);
            } else if (oNode.nodeName === 'name') {
              const objectNodeValueEN = xmlHelper.getWSNodeValue(
                xmlCreate,
                `/prestashop/country/${oNode.nodeName}/language[@id="1"]`,
              );

              const createNodeValueEN = xmlHelper.getWSNodeValue(
                xmlCreate,
                `/prestashop/country/${oNode.nodeName}/language[@id="1"]`,
              );
              expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

              const objectNodeValueFR = xmlHelper.getWSNodeValue(
                xmlCreate,
                `/prestashop/country/${oNode.nodeName}/language[@id="2"]`,
              );
              const createNodeValueFR = xmlHelper.getWSNodeValue(
                xmlCreate,
                `/prestashop/country/${oNode.nodeName}/language[@id="2"]`,
              );
              expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
            } else {
              const objectNodeValue: string = xmlHelper.getWSNodeValue(
                xmlCreate,
                `/prestashop/country/${oNode.nodeName}`,
              );
              expect(oNode.textContent).to.be.eq(objectNodeValue);
            }
          }
        });
      });

      describe('Endpoint : /api/countries - Method : POST - Check On BO ', () => {
        it('should go to \'International > Locations\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToLocationsPage', baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.internationalParentLink,
            dashboardPage.locationsLink,
          );
          await zonesPage.closeSfToolBar(page);

          const pageTitle = await zonesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(zonesPage.pageTitle);
        });

        it('should go to \'Countries\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPagePost', baseContext);

          await zonesPage.goToSubTabCountries(page);

          const pageTitle = await countriesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(countriesPage.pageTitle);
        });

        it('should filter country by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost', baseContext);

          // Filter
          await countriesPage.resetFilter(page);
          await countriesPage.filterTable(page, 'input', 'id_country', countryNodeID);

          // Check number of countries
          const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
          await expect(numberOfCountriesAfterFilter).to.be.eq(1);

          const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'id_country');
          await expect(textColumn).to.contains(countryNodeID);
        });

        it('should go to edit country page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountryPageAfterPost', baseContext);

          await countriesPage.goToEditCountryPage(page, 1);

          const pageTitle = await addCountryPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addCountryPage.pageTitleEdit);
        });

        it('should check country\'s zone', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryZone1', baseContext);

          const xmlValueIDZone = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/id_zone');
          const valueIDZone = await addCountryPage.getSelectValue(page, 'id_zone');
          expect(valueIDZone).to.be.eq(xmlValueIDZone);
        });

        it('should check country\'s currency', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryCurrency1', baseContext);

          const xmlValueIDCurrency = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/id_currency');
          const valueIDCurrency = await addCountryPage.getSelectValue(page, 'id_currency');
          expect(valueIDCurrency).to.be.eq(xmlValueIDCurrency);
        });

        it('should check country\'s call_prefix', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryCallPrefix1', baseContext);

          const xmlValueCallPrefix = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/call_prefix');
          const valueCallPrefix = await addCountryPage.getInputValue(page, 'call_prefix');
          expect(valueCallPrefix).to.be.eq(xmlValueCallPrefix);
        });

        it('should check country\'s iso_code', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryIsoCode1', baseContext);

          const xmlValueIsoCode = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/iso_code');
          const valueIsoCode = await addCountryPage.getInputValue(page, 'iso_code');
          expect(valueIsoCode).to.be.eq(xmlValueIsoCode);
        });

        it('should check country\'s active', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryActive1', baseContext);

          const xmlValueActive = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/active');
          const valueActive = (await addCountryPage.isCheckboxChecked(page, 'active')) ? '1' : '0';
          expect(valueActive).to.be.eq(xmlValueActive);
        });

        it('should check country\'s states', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryStates1', baseContext);

          const xmlValueContainsStates = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/contains_states');
          const valueContainsStates = (await addCountryPage.isCheckboxChecked(page, 'contains_states')) ? '1' : '0';
          expect(valueContainsStates).to.be.eq(xmlValueContainsStates);
        });

        it('should check country\'s need_identification_number', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryIdentificationNumber1', baseContext);

          const xmlValueNeedIDNumber = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/need_identification_number');
          const valueNeedIDNumber = (await addCountryPage.isCheckboxChecked(page, 'need_identification_number')) ? '1' : '0';
          expect(valueNeedIDNumber).to.be.eq(xmlValueNeedIDNumber);
        });

        it('should check country\'s need_zip_code', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryZipCodeNumber1', baseContext);

          const xmlValueNeedZipCode = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/need_zip_code');
          const valueNeedZipCode = (await addCountryPage.isCheckboxChecked(page, 'need_zip_code')) ? '1' : '0';
          expect(valueNeedZipCode).to.be.eq(xmlValueNeedZipCode);
        });

        it('should check country\'s zip_code_format', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryZipCodeFormat1', baseContext);

          const xmlValueZipCode = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/zip_code_format');
          const valueZipCode = await addCountryPage.getInputValue(page, 'zipCodeFormat');
          expect(valueZipCode).to.be.eq(xmlValueZipCode);
        });

        it('should check country\'s name language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryNameLang11', baseContext);

          const xmlValueNameEn = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/name/language[@id="1"]');
          const valueNameEn = (await addCountryPage.getInputValue(page, 'nameEn'));
          expect(valueNameEn).to.be.eq(xmlValueNameEn);
        });

        it('should check country\'s name language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryNameLang21', baseContext);

          const xmlValueNameFr = xmlHelper.getWSNodeValue(xmlCreate, '/prestashop/country/name/language[@id="2"]');
          const valueNameFr = (await addCountryPage.getInputValue(page, 'nameFr'));
          expect(valueNameFr).to.be.eq(xmlValueNameFr);
        });

        it('should go to \'Countries\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPagePostReset', baseContext);

          await zonesPage.goToSubTabCountries(page);

          const pageTitle = await countriesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(countriesPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPost', baseContext);

          const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfCountries).to.be.above(0);
        });
      });
    });

    describe('Endpoint : /api/countries - Method : PUT ', () => {
      describe('Endpoint : /api/countries - Method : PUT - Update Country ', () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;

        it(`should check response status of /${Endpoints.COUNTRIES}{id}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutStatus1', baseContext);

          xmlUpdate = getCountryXml(countryNodeID);
          apiResponse = await apiContext.put(Endpoints.COUNTRIES + countryNodeID, {
            headers: {
              Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
            },
            data: xmlUpdate,
          });
          await expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutRootNode1', baseContext);

          xmlResponse = await apiResponse.text();
          expect(xmlHelper.getWSRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutNodeNumber1', baseContext);

          const rootNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/*');
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('country');
        });

        it('should check id of the country', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestPutCheckId1', baseContext);

          // Attribute : id
          countryNodeID = xmlHelper.getWSNodeValue(xmlResponse, '/prestashop/country/id');
          expect(countryNodeID).to.be.eq(parseInt(countryNodeID, 10).toString());
        });
      });

      describe(`Endpoint : /${Endpoints.COUNTRIES}{id} - Method : PUT - Check with WS `, () => {
        let apiResponse: APIResponse;
        let xmlResponse : string;
        let countriesNodes: Element[];

        it('should check response status', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutStatus2', baseContext);

          apiResponse = await apiContext.get(Endpoints.COUNTRIES + countryNodeID, {
            headers: {
              Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
            },
          });
          await expect(apiResponse.status()).to.eq(200);
        });

        it('should check response root node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutRootNode2', baseContext);

          xmlResponse = await apiResponse.text();
          expect(xmlHelper.getWSRootNodeName(xmlResponse)).to.be.eq('prestashop');
        });

        it('should check number of node under prestashop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNodeNumber1', baseContext);

          const rootNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/*');
          expect(rootNodes.length).to.be.eq(1);
          expect(rootNodes[0].nodeName).to.be.eq('country');
        });

        it('should check number of nodes under countries node', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetPutNumberOfNodes1', baseContext);

          countriesNodes = xmlHelper.getWSNodes(xmlResponse, '/prestashop/country/*');
          expect(countriesNodes.length).to.be.gt(0);
        });

        it('should check each node id, name ...', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'requestGetIDCheckAll2', baseContext);

          await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterPut', baseContext);

          // Check nodes are equal to them done in Create
          for (let o: number = 0; o < countriesNodes.length; o++) {
            const oNode: Element = countriesNodes[o];

            if (oNode.nodeName === 'id') {
              expect(oNode.textContent).to.be.eq(countryNodeID);
            } else if (oNode.nodeName === 'name') {
              const objectNodeValueEN = xmlHelper.getWSNodeValue(
                xmlUpdate,
                `/prestashop/country/${oNode.nodeName}/language[@id="1"]`,
              );

              const createNodeValueEN = xmlHelper.getWSNodeValue(
                xmlResponse,
                `/prestashop/country/${oNode.nodeName}/language[@id="1"]`,
              );

              expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

              const objectNodeValueFR = xmlHelper.getWSNodeValue(
                xmlUpdate,
                `/prestashop/country/${oNode.nodeName}/language[@id="2"]`,
              );
              const createNodeValueFR = xmlHelper.getWSNodeValue(
                xmlResponse,
                `/prestashop/country/${oNode.nodeName}/language[@id="2"]`,
              );
              expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
            } else {
              const objectNodeValue = xmlHelper.getWSNodeValue(
                xmlUpdate,
                `/prestashop/country/${oNode.nodeName}`,
              );
              expect(oNode.textContent).to.be.eq(objectNodeValue);
            }
          }
        });
      });

      describe('Endpoint : /api/countries - Method : PUT - Check On BO ', () => {
        it('should filter country by ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPost2', baseContext);

          // Filter
          await countriesPage.resetFilter(page);
          await countriesPage.filterTable(page, 'input', 'id_country', countryNodeID);

          // Check number of countries
          const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
          await expect(numberOfCountriesAfterFilter).to.be.eq(1);

          const textColumn = await countriesPage.getTextColumnFromTable(page, 1, 'id_country');
          await expect(textColumn).to.contains(countryNodeID);
        });

        it('should go to edit country page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountryPageAfterPost2', baseContext);

          await countriesPage.goToEditCountryPage(page, 1);

          const pageTitle = await addCountryPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addCountryPage.pageTitleEdit);
        });

        it('should check country\'s zone', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryZone2', baseContext);

          const xmlValueIDZone = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/id_zone');
          const valueIDZone = await addCountryPage.getSelectValue(page, 'id_zone');
          expect(valueIDZone).to.be.eq(xmlValueIDZone);
        });

        it('should check country\'s currency', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryCurrency2', baseContext);

          const xmlValueIDCurrency = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/id_currency');
          const valueIDCurrency = await addCountryPage.getSelectValue(page, 'id_currency');
          expect(valueIDCurrency).to.be.eq(xmlValueIDCurrency);
        });

        it('should check country\'s call_prefix', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryCallPrefix2', baseContext);

          const xmlValueCallPrefix = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/call_prefix');
          const valueCallPrefix = await addCountryPage.getInputValue(page, 'call_prefix');
          expect(valueCallPrefix).to.be.eq(xmlValueCallPrefix);
        });

        it('should check country\'s iso_code', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryIsoCode2', baseContext);

          const xmlValueIsoCode = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/iso_code');
          const valueIsoCode = await addCountryPage.getInputValue(page, 'iso_code');
          expect(valueIsoCode).to.be.eq(xmlValueIsoCode);
        });

        it('should check country\'s active', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryActive2', baseContext);

          const xmlValueActive = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/active');
          const valueActive = (await addCountryPage.isCheckboxChecked(page, 'active')) ? '1' : '0';
          expect(valueActive).to.be.eq(xmlValueActive);
        });

        it('should check country\'s states', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryStates2', baseContext);

          const xmlValueContainsStates = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/contains_states');
          const valueContainsStates = (await addCountryPage.isCheckboxChecked(page, 'contains_states')) ? '1' : '0';
          expect(valueContainsStates).to.be.eq(xmlValueContainsStates);
        });

        it('should check country\'s need_identification_number', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryIdentificationNumber2', baseContext);

          const xmlValueNeedIDNumber = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/need_identification_number');
          const valueNeedIDNumber = (await addCountryPage.isCheckboxChecked(page, 'need_identification_number')) ? '1' : '0';
          expect(valueNeedIDNumber).to.be.eq(xmlValueNeedIDNumber);
        });

        it('should check country\'s need_zip_code', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryZipCodeNumber2', baseContext);

          const xmlValueNeedZipCode = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/need_zip_code');
          const valueNeedZipCode = (await addCountryPage.isCheckboxChecked(page, 'need_zip_code')) ? '1' : '0';
          expect(valueNeedZipCode).to.be.eq(xmlValueNeedZipCode);
        });

        it('should check country\'s zip_code_format', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryZipCodeFormat2', baseContext);

          const xmlValueZipCode = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/zip_code_format');
          const valueZipCode = await addCountryPage.getInputValue(page, 'zipCodeFormat');
          expect(valueZipCode).to.be.eq(xmlValueZipCode);
        });

        it('should check country\'s name language 1', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryNameLang12', baseContext);

          const xmlValueNameEn = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/name/language[@id="1"]');
          const valueNameEn = (await addCountryPage.getInputValue(page, 'nameEn'));
          expect(valueNameEn).to.be.eq(xmlValueNameEn);
        });

        it('should check country\'s name language 2', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCountryNameLang22', baseContext);

          const xmlValueNameFr = xmlHelper.getWSNodeValue(xmlUpdate, '/prestashop/country/name/language[@id="2"]');
          const valueNameFr = (await addCountryPage.getInputValue(page, 'nameFr'));
          expect(valueNameFr).to.be.eq(xmlValueNameFr);
        });

        it('should go to \'Countries\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPagePutReset', baseContext);

          await zonesPage.goToSubTabCountries(page);

          const pageTitle = await countriesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(countriesPage.pageTitle);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirstAfterPut', baseContext);

          const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfCountries).to.be.above(0);
        });
      });
    });

    describe('Endpoint : /api/countries - Method : DELETE ', () => {
      it(`should request the endpoint /${Endpoints.COUNTRIES}{id} with method DELETE`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodDelete', baseContext);

        const apiResponse = await apiContext.delete(Endpoints.COUNTRIES + countryNodeID, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);
      });

      it(`should request the endpoint /${Endpoints.COUNTRIES}{id} with method GET`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterDelete', baseContext);

        const apiResponse = await apiContext.get(Endpoints.COUNTRIES + countryNodeID, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(404);
      });

      it('should filter country by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterDelete', baseContext);

        // Filter
        await countriesPage.resetFilter(page);
        await countriesPage.filterTable(page, 'input', 'id_country', countryNodeID);

        // Check number of countries
        const numberOfCountriesAfterFilter = await countriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCountriesAfterFilter).to.be.eq(0);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCountries).to.be.above(0);
      });
    });
  });

  // Remove a new webservice key
  removeWebserviceKey(wsKeyDescription, `${baseContext}_postTest_1`);

  // Disable webservice
  setWebserviceStatus(false, `${baseContext}_postTest_2`);
});
