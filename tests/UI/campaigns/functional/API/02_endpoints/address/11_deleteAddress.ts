// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createAddressTest} from '@commonTests/BO/customers/address';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAddressesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAddress,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_deleteAddress';

describe('API : DELETE /addresses/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let idAddress: number;
  let accessToken: string;

  const clientScope: string = 'address_write';
  const addressData: FakerAddress = new FakerAddress({
    firstName: 'Test',
    lastName: 'Playwright',
    email: 'pub@prestashop.com',
    country: 'France',
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-Condition : Create an address
  createAddressTest(addressData, `${baseContext}_preTest_0`);

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Fetch the ID of the address', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.customersParentLink, boDashboardPage.addressesLink);
      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should filter list by address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForCreation', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'address1', addressData.address);

      const numAddresses = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numAddresses).to.be.equal(1);

      const address = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      expect(address).to.contains(addressData.address);

      idAddress = parseInt((await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address')).toString(), 10);
      expect(idAddress).to.be.gt(0);
    });
  });

  describe('API : Delete the Address', async () => {
    it('should request the endpoint /addresses/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`addresses/${idAddress}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Address is deleted', async () => {
    it('should filter list by address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDeletion', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'address1', addressData.address);

      const numAddresses = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numAddresses).to.be.equal(0);
    });
  });
});
