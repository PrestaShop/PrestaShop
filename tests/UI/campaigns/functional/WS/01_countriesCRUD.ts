// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import ws from '@utils/ws';

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

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

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
  const xmlCreate: string = `<?xml version="1.0" encoding="UTF-8"?>
       <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
       <country>
           <id_zone><![CDATA[1]]></id_zone>
           <id_currency><![CDATA[1]]></id_currency>
           <call_prefix><![CDATA[123]]></call_prefix>
           <iso_code><![CDATA[tst]]></iso_code>
           <active><![CDATA[1]]></active>
           <contains_states><![CDATA[0]]></contains_states>
           <need_identification_number><![CDATA[0]]></need_identification_number>
           <need_zip_code><![CDATA[0]]></need_zip_code>
           <zip_code_format><![CDATA[NNLLNN]]></zip_code_format>
           <display_tax_label><![CDATA[0]]></display_tax_label>
           <name>
               <language id="1"><![CDATA[Test in English]]></language>
               <language id="2"><![CDATA[Test en Français]]></language>
           </name>
       </country>
    </prestashop>`;
  const xmlUpdate: string = `<?xml version="1.0" encoding="UTF-8"?>
       <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
       <country>
           <id><![CDATA[{fetchedId}]]></id>
           <id_zone><![CDATA[2]]></id_zone>
           <id_currency><![CDATA[1]]></id_currency>
           <call_prefix><![CDATA[456]]></call_prefix>
           <iso_code><![CDATA[upd]]></iso_code>
           <active><![CDATA[0]]></active>
           <contains_states><![CDATA[0]]></contains_states>
           <need_identification_number><![CDATA[1]]></need_identification_number>
           <need_zip_code><![CDATA[1]]></need_zip_code>
           <zip_code_format><![CDATA[LLNNLL]]></zip_code_format>
           <display_tax_label><![CDATA[1]]></display_tax_label>
           <name>
               <language id="1"><![CDATA[Updated in English]]></language>
               <language id="2"><![CDATA[Mis à jour en Français]]></language>
           </name>
       </country>
       </prestashop>`;

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
      it('should request the endpoint /api/countries with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodGet', baseContext);

        const apiResponse = await apiContext.get('api/countries/', {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);

        const xml: string = await apiResponse.text();
        expect(ws.getWSRootNodeName(xml)).to.be.eq('prestashop');

        const rootNodes = ws.getWSNodes(xml, '/prestashop/*');
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('countries');

        const countriesNode = ws.getWSNodes(xml, '/prestashop/countries/*');
        expect(countriesNode.length).to.be.gt(0);

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
      it('should request the endpoint /api/countries with method POST', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodPost', baseContext);

        const apiResponse = await apiContext.post('api/countries/', {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
          data: xmlCreate,
        });
        await expect(apiResponse.status()).to.eq(201);

        const xml: string = await apiResponse.text();
        expect(ws.getWSRootNodeName(xml)).to.be.eq('prestashop');

        const rootNodes = ws.getWSNodes(xml, '/prestashop/*');
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        // Attribute : id
        countryNodeID = ws.getWSNodeValue(xml, '/prestashop/country/id');
        expect(countryNodeID).to.be.eq(parseInt(countryNodeID, 10).toString());
      });

      it('should request the endpoint /api/countries/{id} with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterPost', baseContext);

        const apiResponse = await apiContext.get(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);

        const xml: string = await apiResponse.text();
        expect(ws.getWSRootNodeName(xml)).to.be.eq('prestashop');

        const rootNodes = ws.getWSNodes(xml, '/prestashop/*');
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        const objectNodes = ws.getWSNodes(xml, '/prestashop/country/*');
        expect(objectNodes.length).to.be.gt(0);

        // Check nodes are equal to them done in Create
        for (let o: number = 0; o < objectNodes.length; o++) {
          const oNode: Element = objectNodes[o];

          if (oNode.nodeName === 'id') {
            expect(oNode.textContent).to.be.eq(countryNodeID);
          } else if (oNode.nodeName === 'name') {
            const objectNodeValueEN = ws.getWSNodeValue(xmlCreate, `/prestashop/country/${oNode.nodeName}/language[@id="1"]`);
            const createNodeValueEN = ws.getWSNodeValue(xmlCreate, `/prestashop/country/${oNode.nodeName}/language[@id="1"]`);
            expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

            const objectNodeValueFR = ws.getWSNodeValue(xmlCreate, `/prestashop/country/${oNode.nodeName}/language[@id="2"]`);
            const createNodeValueFR = ws.getWSNodeValue(xmlCreate, `/prestashop/country/${oNode.nodeName}/language[@id="2"]`);
            expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
          } else {
            const objectNodeValue: string = ws.getWSNodeValue(xmlCreate, `/prestashop/country/${oNode.nodeName}`);
            expect(oNode.textContent).to.be.eq(objectNodeValue);
          }
        }
      });

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

      it('should check all values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEditCountryValuesAfterPost', baseContext);

        const xmlValueIDZone = ws.getWSNodeValue(xmlCreate, '/prestashop/country/id_zone');
        const valueIDZone = await addCountryPage.getSelectValue(page, 'id_zone');
        expect(valueIDZone).to.be.eq(xmlValueIDZone);

        const xmlValueIDCurrency = ws.getWSNodeValue(xmlCreate, '/prestashop/country/id_currency');
        const valueIDCurrency = await addCountryPage.getSelectValue(page, 'id_currency');
        expect(valueIDCurrency).to.be.eq(xmlValueIDCurrency);

        const xmlValueCallPrefix = ws.getWSNodeValue(xmlCreate, '/prestashop/country/call_prefix');
        const valueCallPrefix = await addCountryPage.getInputValue(page, 'call_prefix');
        expect(valueCallPrefix).to.be.eq(xmlValueCallPrefix);

        const xmlValueIsoCode = ws.getWSNodeValue(xmlCreate, '/prestashop/country/iso_code');
        const valueIsoCode = await addCountryPage.getInputValue(page, 'iso_code');
        expect(valueIsoCode).to.be.eq(xmlValueIsoCode);

        const xmlValueActive = ws.getWSNodeValue(xmlCreate, '/prestashop/country/active');
        const valueActive = (await addCountryPage.isCheckboxChecked(page, 'active')) ? '1' : '0';
        expect(valueActive).to.be.eq(xmlValueActive);

        const xmlValueContainsStates = ws.getWSNodeValue(xmlCreate, '/prestashop/country/contains_states');
        const valueContainsStates = (await addCountryPage.isCheckboxChecked(page, 'contains_states')) ? '1' : '0';
        expect(valueContainsStates).to.be.eq(xmlValueContainsStates);

        const xmlValueNeedIDNumber = ws.getWSNodeValue(xmlCreate, '/prestashop/country/need_identification_number');
        const valueNeedIDNumber = (await addCountryPage.isCheckboxChecked(page, 'need_identification_number')) ? '1' : '0';
        expect(valueNeedIDNumber).to.be.eq(xmlValueNeedIDNumber);

        const xmlValueNeedZipCode = ws.getWSNodeValue(xmlCreate, '/prestashop/country/need_zip_code');
        const valueNeedZipCode = (await addCountryPage.isCheckboxChecked(page, 'need_zip_code')) ? '1' : '0';
        expect(valueNeedZipCode).to.be.eq(xmlValueNeedZipCode);

        const xmlValueZipCode = ws.getWSNodeValue(xmlCreate, '/prestashop/country/zip_code_format');
        const valueZipCode = await addCountryPage.getInputValue(page, 'zipCodeFormat');
        expect(valueZipCode).to.be.eq(xmlValueZipCode);

        const xmlValueNameEn = ws.getWSNodeValue(xmlCreate, '/prestashop/country/name/language[@id="1"]');
        const valueNameEn = (await addCountryPage.getInputValue(page, 'nameEn'));
        expect(valueNameEn).to.be.eq(xmlValueNameEn);

        const xmlValueNameFr = ws.getWSNodeValue(xmlCreate, '/prestashop/country/name/language[@id="2"]');
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

    describe('Endpoint : /api/countries - Method : PUT ', () => {
      it('should request the endpoint /api/countries/{id} with method PUT', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodPut', baseContext);

        const apiResponse = await apiContext.put(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
          data: xmlUpdate.replace('{fetchedId}', countryNodeID),
        });
        await expect(apiResponse.status()).to.eq(200);

        const xml: string = await apiResponse.text();
        expect(ws.getWSRootNodeName(xml)).to.be.eq('prestashop');

        const rootNodes = ws.getWSNodes(xml, '/prestashop/*');
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        // Attribute : id
        const countryUpdateNodeID = ws.getWSNodeValue(xml, '/prestashop/country/id');
        expect(countryUpdateNodeID).to.be.eq(countryNodeID);
      });

      it('should request the endpoint /api/countries/{id} with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterPut', baseContext);

        const apiResponse = await apiContext.get(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);

        const xml: string = await apiResponse.text();
        const xmlUId = xmlUpdate.replace('{fetchedId}', countryNodeID);

        expect(ws.getWSRootNodeName(xml)).to.be.eq('prestashop');

        const rootNodes = ws.getWSNodes(xml, '/prestashop/*');
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        const objectNodes = ws.getWSNodes(xml, '/prestashop/country/*');
        expect(objectNodes.length).to.be.gt(0);

        // Check nodes are equal to them done in Create
        for (let o: number = 0; o < objectNodes.length; o++) {
          const oNode: Element = objectNodes[o];

          if (oNode.nodeName === 'id') {
            expect(oNode.textContent).to.be.eq(countryNodeID);
          } else if (oNode.nodeName === 'name') {
            const objectNodeValueEN = ws.getWSNodeValue(xmlUId, `/prestashop/country/${oNode.nodeName}/language[@id="1"]`);
            const createNodeValueEN = ws.getWSNodeValue(xml, `/prestashop/country/${oNode.nodeName}/language[@id="1"]`);
            expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

            const objectNodeValueFR = ws.getWSNodeValue(xmlUId, `/prestashop/country/${oNode.nodeName}/language[@id="2"]`);
            const createNodeValueFR = ws.getWSNodeValue(xml, `/prestashop/country/${oNode.nodeName}/language[@id="2"]`);
            expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
          } else {
            const objectNodeValue = ws.getWSNodeValue(xmlUId, `/prestashop/country/${oNode.nodeName}`);
            expect(oNode.textContent).to.be.eq(objectNodeValue);
          }
        }
      });

      it('should filter country by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateAfterPut', baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCountryPage', baseContext);

        await countriesPage.goToEditCountryPage(page, 1);

        const pageTitle = await addCountryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCountryPage.pageTitleEdit);
      });

      it('should check all values', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkEditCountryValues', baseContext);

        const xmlUId = xmlUpdate.replace('{fetchedId}', countryNodeID);

        const xmlValueIDZone = ws.getWSNodeValue(xmlUId, '/prestashop/country/id_zone');
        const valueIDZone = await addCountryPage.getSelectValue(page, 'id_zone');
        expect(valueIDZone).to.be.eq(xmlValueIDZone);

        const xmlValueIDCurrency = ws.getWSNodeValue(xmlUId, '/prestashop/country/id_currency');
        const valueIDCurrency = await addCountryPage.getSelectValue(page, 'id_currency');
        expect(valueIDCurrency).to.be.eq(xmlValueIDCurrency);

        const xmlValueCallPrefix = ws.getWSNodeValue(xmlUId, '/prestashop/country/call_prefix');
        const valueCallPrefix = await addCountryPage.getInputValue(page, 'call_prefix');
        expect(valueCallPrefix).to.be.eq(xmlValueCallPrefix);

        const xmlValueIsoCode = ws.getWSNodeValue(xmlUId, '/prestashop/country/iso_code');
        const valueIsoCode = await addCountryPage.getInputValue(page, 'iso_code');
        expect(valueIsoCode).to.be.eq(xmlValueIsoCode);

        const xmlValueActive = ws.getWSNodeValue(xmlUId, '/prestashop/country/active');
        const valueActive = (await addCountryPage.isCheckboxChecked(page, 'active')) ? '1' : '0';
        expect(valueActive).to.be.eq(xmlValueActive);

        const xmlValueContainsStates = ws.getWSNodeValue(xmlUId, '/prestashop/country/contains_states');
        const valueContainsStates = (await addCountryPage.isCheckboxChecked(page, 'contains_states')) ? '1' : '0';
        expect(valueContainsStates).to.be.eq(xmlValueContainsStates);

        const xmlValueNeedIDNumber = ws.getWSNodeValue(xmlUId, '/prestashop/country/need_identification_number');
        const valueNeedIDNumber = (await addCountryPage.isCheckboxChecked(page, 'need_identification_number')) ? '1' : '0';
        expect(valueNeedIDNumber).to.be.eq(xmlValueNeedIDNumber);

        const xmlValueNeedZipCode = ws.getWSNodeValue(xmlUId, '/prestashop/country/need_zip_code');
        const valueNeedZipCode = (await addCountryPage.isCheckboxChecked(page, 'need_zip_code')) ? '1' : '0';
        expect(valueNeedZipCode).to.be.eq(xmlValueNeedZipCode);

        const xmlValueZipCode = ws.getWSNodeValue(xmlUId, '/prestashop/country/zip_code_format');
        const valueZipCode = await addCountryPage.getInputValue(page, 'zipCodeFormat');
        expect(valueZipCode).to.be.eq(xmlValueZipCode);

        const xmlValueNameEn = ws.getWSNodeValue(xmlUId, '/prestashop/country/name/language[@id="1"]');
        const valueNameEn = (await addCountryPage.getInputValue(page, 'nameEn'));
        expect(valueNameEn).to.be.eq(xmlValueNameEn);

        const xmlValueNameFr = ws.getWSNodeValue(xmlUId, '/prestashop/country/name/language[@id="2"]');
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

    describe('Endpoint : /api/countries - Method : DELETE ', () => {
      it('should request the endpoint /api/countries/{id} with method DELETE', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodDelete', baseContext);

        const apiResponse = await apiContext.delete(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);
      });

      it('should request the endpoint /api/countries/{id} with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGetAfterDelete', baseContext);

        const apiResponse = await apiContext.get(`api/countries/${countryNodeID}`, {
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
