// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAddressesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_getAddressesList';

describe('API : GET /addresses', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numberOfAddresses: number = 0;
  const clientScope: string = 'address_read';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('addresses', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

      expect(jsonResponse).to.have.all.keys(
        'totalItems',
        'orderBy',
        'sortOrder',
        'limit',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i: number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'addressId',
          'firstname',
          'lastname',
          'address1',
          'postcode',
          'city',
          'country_name',
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

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );
      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should reset all filters and get number of addresses in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfAddresses = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddresses).to.be.equal(jsonResponse.totalItems);
    });

    it('should check each address in the list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await boAddressesPage.resetFilter(page);
        await boAddressesPage.filterAddresses(
          page,
          'input',
          'id_address',
          jsonResponse.items[idxItem].addressId.toString(),
        );

        const numAddresses = await boAddressesPage.getNumberOfElementInGrid(page);
        expect(numAddresses).to.be.equal(1);

        const addressId = parseInt(
          await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
          10,
        );
        expect(addressId).to.equal(jsonResponse.items[idxItem].addressId);

        const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
        expect(firstName).to.equal(jsonResponse.items[idxItem].firstname);

        const lastName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
        expect(lastName).to.equal(jsonResponse.items[idxItem].lastname);

        const address = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
        expect(address).to.equal(jsonResponse.items[idxItem].address1);

        const zipCode = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'postcode');
        expect(zipCode).to.equal(jsonResponse.items[idxItem].postcode);

        const city = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'city');
        expect(city).to.equal(jsonResponse.items[idxItem].city);

        const country = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'country_name');
        expect(country).to.equal(jsonResponse.items[idxItem].country_name);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await boAddressesPage.resetFilter(page);

      const numAddresses = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numAddresses).to.be.equal(numberOfAddresses);
    });
  });
});
