// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

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
import {DOMParser} from '@xmldom/xmldom';
import * as xpath from 'xpath-ts';

const baseContext: string = 'functional_WS_countriesCRUD';

describe('WS - Countries : CRUD', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let wsKey: string = '';

  const domParser: DOMParser = new DOMParser();
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

        const txtResponse: string = await apiResponse.text();
        const xmlDocument: Document = domParser.parseFromString(txtResponse);
        const docElement: HTMLElement = xmlDocument.documentElement;
        expect(docElement.nodeName).to.be.eq('prestashop');

        const rootNodes: Node[] = xpath.select('/prestashop/*', xmlDocument) as Node[];
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('countries');

        const countriesNode: Element[] = xpath.select('/prestashop/countries/*', xmlDocument) as Element[];
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

        const txtResponse: string = await apiResponse.text();
        const xmlDocument: Document = domParser.parseFromString(txtResponse);
        const docElement: HTMLElement = xmlDocument.documentElement;
        expect(docElement.nodeName).to.be.eq('prestashop');

        const rootNodes: Node[] = xpath.select('/prestashop/*', xmlDocument) as Node[];
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        // Attribute : id
        countryNodeID = xpath.select1('string(/prestashop/country/id)', xmlDocument) as string;
        expect(countryNodeID).to.be.eq(parseInt(countryNodeID, 10).toString());
      });

      it('should request the endpoint /api/countries/{id} with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGet', baseContext);

        const apiResponse = await apiContext.get(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);

        const txtResponse: string = await apiResponse.text();
        const xmlDocument: Document = domParser.parseFromString(txtResponse);
        const docElement: HTMLElement = xmlDocument.documentElement;
        expect(docElement.nodeName).to.be.eq('prestashop');

        const rootNodes: Node[] = xpath.select('/prestashop/*', xmlDocument) as Node[];
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        const objectNodes: Element[] = xpath.select('/prestashop/country/*', xmlDocument) as Element[];
        expect(objectNodes.length).to.be.gt(0);

        const createDocument: Document = domParser.parseFromString(xmlCreate);

        // Check nodes are equal to them done in Create
        for (let o: number = 0; o < objectNodes.length; o++) {
          const objectNode: Element = objectNodes[o];

          if (objectNode.nodeName === 'id') {
            expect(objectNode.textContent).to.be.eq(countryNodeID);
          } else if (objectNode.nodeName === 'name') {
            const objectNodeValueEN: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="1"])`,
              createDocument,
            ) as string;
            const createNodeValueEN: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="1"])`,
              xmlDocument,
            ) as string;
            expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

            const objectNodeValueFR: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="2"])`,
              createDocument,
            ) as string;
            const createNodeValueFR: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="2"])`,
              xmlDocument,
            ) as string;
            expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
          } else {
            const objectNodeValue: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName})`,
              createDocument,
            ) as string;
            expect(objectNode.textContent).to.be.eq(objectNodeValue);
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
        await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

        await zonesPage.goToSubTabCountries(page);

        const pageTitle = await countriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(countriesPage.pageTitle);
      });

      it('should filter country by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

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

        const createDocument: Document = domParser.parseFromString(xmlCreate);

        const xmlValueIDZone = xpath.select1('string(/prestashop/country/id_zone)', createDocument) as string;
        const valueIDZone = await addCountryPage.getSelectValue(page, 'id_zone');
        expect(valueIDZone).to.be.eq(xmlValueIDZone);

        const xmlValueIDCurrency = xpath.select1('string(/prestashop/country/id_currency)', createDocument) as string;
        const valueIDCurrency = await addCountryPage.getSelectValue(page, 'id_currency');
        expect(valueIDCurrency).to.be.eq(xmlValueIDCurrency);

        const xmlValueCallPrefix = xpath.select1('string(/prestashop/country/call_prefix)', createDocument) as string;
        const valueCallPrefix = await addCountryPage.getInputValue(page, 'call_prefix');
        expect(valueCallPrefix).to.be.eq(xmlValueCallPrefix);

        const xmlValueIsoCode = xpath.select1('string(/prestashop/country/iso_code)', createDocument) as string;
        const valueIsoCode = await addCountryPage.getInputValue(page, 'iso_code');
        expect(valueIsoCode).to.be.eq(xmlValueIsoCode);

        const xmlValueActive = xpath.select1('string(/prestashop/country/active)', createDocument) as string;
        const valueActive = (await addCountryPage.isCheckboxChecked(page, 'active')) ? '1' : '0';
        expect(valueActive).to.be.eq(xmlValueActive);

        const xmlValueContainsStates = xpath.select1('string(/prestashop/country/contains_states)', createDocument) as string;
        const valueContainsStates = (await addCountryPage.isCheckboxChecked(page, 'contains_states')) ? '1' : '0';
        expect(valueContainsStates).to.be.eq(xmlValueContainsStates);

        const xmlValueNeedIDNumber = xpath.select1(
          'string(/prestashop/country/need_identification_number)',
          createDocument,
        ) as string;
        const valueNeedIDNumber = (await addCountryPage.isCheckboxChecked(page, 'need_identification_number')) ? '1' : '0';
        expect(valueNeedIDNumber).to.be.eq(xmlValueNeedIDNumber);

        const xmlValueNeedZipCode = xpath.select1('string(/prestashop/country/need_zip_code)', createDocument) as string;
        const valueNeedZipCode = (await addCountryPage.isCheckboxChecked(page, 'need_zip_code')) ? '1' : '0';
        expect(valueNeedZipCode).to.be.eq(xmlValueNeedZipCode);

        const xmlValueZipCode = xpath.select1('string(/prestashop/country/zip_code_format)', createDocument) as string;
        const valueZipCode = await addCountryPage.getInputValue(page, 'zipCodeFormat');
        expect(valueZipCode).to.be.eq(xmlValueZipCode);

        const xmlValueNameEn = xpath.select1('string(/prestashop/country/name/language[@id="1"])', createDocument) as string;
        const valueNameEn = (await addCountryPage.getInputValue(page, 'nameEn'));
        expect(valueNameEn).to.be.eq(xmlValueNameEn);

        const xmlValueNameFr = xpath.select1('string(/prestashop/country/name/language[@id="2"])', createDocument) as string;
        const valueNameFr = (await addCountryPage.getInputValue(page, 'nameFr'));
        expect(valueNameFr).to.be.eq(xmlValueNameFr);
      });

      it('should go to \'Countries\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

        await zonesPage.goToSubTabCountries(page);

        const pageTitle = await countriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(countriesPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCountries).to.be.above(0);
      });
    });

    describe('Endpoint : /api/countries - Method : PUT ', () => {
      it('should request the endpoint /api/countries/{id} with method PUT', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodPost', baseContext);

        const apiResponse = await apiContext.put(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
          data: xmlUpdate.replace('{fetchedId}', countryNodeID),
        });
        await expect(apiResponse.status()).to.eq(200);

        const txtResponse: string = await apiResponse.text();
        const xmlDocument: Document = domParser.parseFromString(txtResponse);
        const docElement: HTMLElement = xmlDocument.documentElement;
        expect(docElement.nodeName).to.be.eq('prestashop');

        const rootNodes: Node[] = xpath.select('/prestashop/*', xmlDocument) as Node[];
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        // Attribute : id
        const countryUpdateNodeID = xpath.select1('string(/prestashop/country/id)', xmlDocument) as string;
        expect(countryUpdateNodeID).to.be.eq(countryNodeID);
      });

      it('should request the endpoint /api/countries/{id} with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGet', baseContext);

        const apiResponse = await apiContext.get(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);

        const txtResponse: string = await apiResponse.text();
        const xmlDocument: Document = domParser.parseFromString(txtResponse);
        const docElement: HTMLElement = xmlDocument.documentElement;
        expect(docElement.nodeName).to.be.eq('prestashop');

        const rootNodes: Node[] = xpath.select('/prestashop/*', xmlDocument) as Node[];
        expect(rootNodes.length).to.be.eq(1);
        expect(rootNodes[0].nodeName).to.be.eq('country');

        const objectNodes: Element[] = xpath.select('/prestashop/country/*', xmlDocument) as Element[];
        expect(objectNodes.length).to.be.gt(0);

        const updateDocument: Document = domParser.parseFromString(xmlUpdate.replace('{fetchedId}', countryNodeID));

        // Check nodes are equal to them done in Create
        for (let o: number = 0; o < objectNodes.length; o++) {
          const objectNode: Element = objectNodes[o];

          if (objectNode.nodeName === 'id') {
            expect(objectNode.textContent).to.be.eq(countryNodeID);
          } else if (objectNode.nodeName === 'name') {
            const objectNodeValueEN: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="1"])`,
              updateDocument,
            ) as string;
            const createNodeValueEN: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="1"])`,
              xmlDocument,
            ) as string;
            expect(objectNodeValueEN).to.be.eq(createNodeValueEN);

            const objectNodeValueFR: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="2"])`,
              updateDocument,
            ) as string;
            const createNodeValueFR: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName}/language[@id="2"])`,
              xmlDocument,
            ) as string;
            expect(objectNodeValueFR).to.be.eq(createNodeValueFR);
          } else {
            const objectNodeValue: string = xpath.select1(
              `string(/prestashop/country/${objectNode.nodeName})`,
              updateDocument,
            ) as string;
            expect(objectNode.textContent).to.be.eq(objectNodeValue);
          }
        }
      });

      it('should filter country by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

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

        const updateDocument: Document = domParser.parseFromString(xmlUpdate.replace('{fetchedId}', countryNodeID));

        const xmlValueIDZone = xpath.select1('string(/prestashop/country/id_zone)', updateDocument) as string;
        const valueIDZone = await addCountryPage.getSelectValue(page, 'id_zone');
        expect(valueIDZone).to.be.eq(xmlValueIDZone);

        const xmlValueIDCurrency = xpath.select1('string(/prestashop/country/id_currency)', updateDocument) as string;
        const valueIDCurrency = await addCountryPage.getSelectValue(page, 'id_currency');
        expect(valueIDCurrency).to.be.eq(xmlValueIDCurrency);

        const xmlValueCallPrefix = xpath.select1('string(/prestashop/country/call_prefix)', updateDocument) as string;
        const valueCallPrefix = await addCountryPage.getInputValue(page, 'call_prefix');
        expect(valueCallPrefix).to.be.eq(xmlValueCallPrefix);

        const xmlValueIsoCode = xpath.select1('string(/prestashop/country/iso_code)', updateDocument) as string;
        const valueIsoCode = await addCountryPage.getInputValue(page, 'iso_code');
        expect(valueIsoCode).to.be.eq(xmlValueIsoCode);

        const xmlValueActive = xpath.select1('string(/prestashop/country/active)', updateDocument) as string;
        const valueActive = (await addCountryPage.isCheckboxChecked(page, 'active')) ? '1' : '0';
        expect(valueActive).to.be.eq(xmlValueActive);

        const xmlValueContainsStates = xpath.select1('string(/prestashop/country/contains_states)', updateDocument) as string;
        const valueContainsStates = (await addCountryPage.isCheckboxChecked(page, 'contains_states')) ? '1' : '0';
        expect(valueContainsStates).to.be.eq(xmlValueContainsStates);

        const xmlValueNeedIDNumber = xpath.select1(
          'string(/prestashop/country/need_identification_number)',
          updateDocument,
        ) as string;
        const valueNeedIDNumber = (await addCountryPage.isCheckboxChecked(page, 'need_identification_number')) ? '1' : '0';
        expect(valueNeedIDNumber).to.be.eq(xmlValueNeedIDNumber);

        const xmlValueNeedZipCode = xpath.select1('string(/prestashop/country/need_zip_code)', updateDocument) as string;
        const valueNeedZipCode = (await addCountryPage.isCheckboxChecked(page, 'need_zip_code')) ? '1' : '0';
        expect(valueNeedZipCode).to.be.eq(xmlValueNeedZipCode);

        const xmlValueZipCode = xpath.select1('string(/prestashop/country/zip_code_format)', updateDocument) as string;
        const valueZipCode = await addCountryPage.getInputValue(page, 'zipCodeFormat');
        expect(valueZipCode).to.be.eq(xmlValueZipCode);

        const xmlValueNameEn = xpath.select1('string(/prestashop/country/name/language[@id="1"])', updateDocument) as string;
        const valueNameEn = (await addCountryPage.getInputValue(page, 'nameEn'));
        expect(valueNameEn).to.be.eq(xmlValueNameEn);

        const xmlValueNameFr = xpath.select1('string(/prestashop/country/name/language[@id="2"])', updateDocument) as string;
        const valueNameFr = (await addCountryPage.getInputValue(page, 'nameFr'));
        expect(valueNameFr).to.be.eq(xmlValueNameFr);
      });

      it('should go to \'Countries\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCountriesPage', baseContext);

        await zonesPage.goToSubTabCountries(page);

        const pageTitle = await countriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(countriesPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

        const numberOfCountries = await countriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCountries).to.be.above(0);
      });
    });

    describe('Endpoint : /api/countries - Method : DELETE ', () => {
      it('should request the endpoint /api/countries/{id} with method DELETE', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesMethodPost', baseContext);

        const apiResponse = await apiContext.delete(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(200);
      });

      it('should request the endpoint /api/countries/{id} with method GET', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointCountriesIdMethodGet', baseContext);

        const apiResponse = await apiContext.get(`api/countries/${countryNodeID}`, {
          headers: {
            Authorization: `Basic ${Buffer.from(`${wsKey}:`).toString('base64')}`,
          },
        });
        await expect(apiResponse.status()).to.eq(404);
      });

      it('should filter country by ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

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
