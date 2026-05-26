// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAddressesPage,
  boAddressesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerAddress,
  FakerCustomer,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_bulkDeleteAddresses';

describe('API : DELETE /addresses/bulk-delete', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let numberOfAddresses: number = 0;
  const addressIds: number[] = [];
  const clientScope: string = 'address_write';
  const firstCustomerData: FakerCustomer = new FakerCustomer();
  const secondCustomerData: FakerCustomer = new FakerCustomer();
  const addressData1: FakerAddress = new FakerAddress({
    email: firstCustomerData.email,
    firstName: firstCustomerData.firstName,
    lastName: firstCustomerData.lastName,
    country: 'France',
  });
  const addressData2: FakerAddress = new FakerAddress({
    email: secondCustomerData.email,
    firstName: secondCustomerData.firstName,
    lastName: secondCustomerData.lastName,
    country: 'France',
  });

  // Pre-condition: Create customer
  createCustomerTest(firstCustomerData, `${baseContext}_preTest_1`);
  createCustomerTest(secondCustomerData, `${baseContext}_preTest_2`);

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

  describe('BackOffice : Go to Customers > Addresses', async () => {
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
      expect(numberOfAddresses).to.be.above(0);
    });
  });

  [
    addressData1,
    addressData2,
  ].forEach((data: FakerAddress, index: number) => {
    describe(`BackOffice : Create address #${index + 1}`, async () => {
      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await boAddressesPage.goToAddNewAddressPage(page);

        const pageTitle = await boAddressesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);
      });

      it('should create new address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewAddress${index}`, baseContext);

        const textResult = await boAddressesCreatePage.createEditAddress(page, data);
        expect(textResult).to.contains(boAddressesPage.successfulCreationMessage);
      });

      it('should filter list of addresses', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToViewCreatedAddress${index}`, baseContext);

        await boAddressesPage.filterAddresses(page, 'input', 'firstname', data.firstName);

        const textColumn = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
        expect(textColumn).to.contains(data.firstName);

        const addressId = parseInt(
          await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
          10,
        );
        expect(addressId).to.be.gt(0);

        addressIds.push(addressId);
      });
    });
  });

  describe('API : Bulk delete the Addresses', async () => {
    it('should request the endpoint /addresses/bulk-delete', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete('addresses/bulk-delete', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          addressIds,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Addresses are deleted', async () => {
    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

      const numberAddresses = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberAddresses).to.be.equal(numberOfAddresses);
    });

    it('should check addresses are deleted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddresses', baseContext);

      expect(addressIds.length).to.equal(2);

      for (let i: number = 0; i < addressIds.length; i++) {
        await boAddressesPage.filterAddresses(page, 'input', 'id_address', addressIds[i].toString());

        const numberAddresses = await boAddressesPage.getNumberOfElementInGrid(page);
        expect(numberAddresses).to.be.equal(0);
      }
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(firstCustomerData, `${baseContext}_postTest_1`);
  deleteCustomerTest(secondCustomerData, `${baseContext}_postTest_2`);
});
