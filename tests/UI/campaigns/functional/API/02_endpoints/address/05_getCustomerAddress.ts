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
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_getCustomerAddress';

describe('API : GET /addresses/customers/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let numberOfAddresses: number = 0;
  let addressId: number;
  const customerData: FakerCustomer = new FakerCustomer();

  const clientScope: string = 'address_read';
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    firstName: customerData.firstName,
    lastName: customerData.lastName,
    alias: 'test',
    country: 'United States',
    state: 'California',
  });

  // Pre-condition: Create customer
  createCustomerTest(customerData, `${baseContext}_preTest_1`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Create an address', async () => {
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

    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await boAddressesPage.goToAddNewAddressPage(page);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleCreate);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAddress', baseContext);

      const textResult = await boAddressesCreatePage.createEditAddress(page, addressData);
      expect(textResult).to.contains(boAddressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterCreation).to.equal(numberOfAddresses + 1);
    });

    it('should filter list of addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAddress', baseContext);

      await boAddressesPage.filterAddresses(page, 'input', 'firstname', addressData.firstName);

      const textColumn = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(textColumn).to.contains(addressData.firstName);

      addressId = parseInt(
        await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
        10,
      );
      expect(addressId).to.be.gt(0);
    });
  });

  describe('API : Fetch the Customer Address', async () => {
    it('should request the endpoint /addresses/customers/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`addresses/customers/${addressId}`, {
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
        'addressId',
        'customerId',
        'addressAlias',
        'firstName',
        'lastName',
        'address',
        'address2',
        'city',
        'postCode',
        'countryId',
        'dni',
        'company',
        'vatNumber',
        'stateId',
        'homePhone',
        'mobilePhone',
        'other',
      );
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'id_address', addressId.toString());

      await boAddressesPage.goToEditAddressPage(page, 1);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);
    });

    it('should check the JSON Response : `dni`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDni', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'dni');
      expect(jsonResponse.dni).to.be.equal(value);
    });

    it('should check the JSON Response : `alias`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAlias', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'alias');
      expect(jsonResponse.addressAlias).to.be.equal(value);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseFirstName', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'firstName');
      expect(jsonResponse.firstName).to.be.equal(value);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLastName', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'lastName');
      expect(jsonResponse.lastName).to.be.equal(value);
    });

    it('should check the JSON Response : `company`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCompany', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'company');
      expect(jsonResponse.company).to.be.equal(value);
    });

    it('should check the JSON Response : `vatNumber`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseVatNumber', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'vatNumber');
      expect(jsonResponse.vatNumber).to.be.equal(value);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'address');
      expect(jsonResponse.address).to.be.equal(value);
    });

    it('should check the JSON Response : `address2`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress2', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'address2');
      expect(jsonResponse.address2).to.be.equal(value);
    });

    it('should check the JSON Response : `zipCode`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseZipCode', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'postCode');
      expect(jsonResponse.postCode).to.be.equal(value);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCity', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'city');
      expect(jsonResponse.city).to.be.equal(value);
    });

    it('should check the JSON Response : `phone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePhone', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'phone');
      expect(jsonResponse.homePhone).to.be.equal(value);
    });

    it('should check the JSON Response : `mobilePhone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseMobilePhone', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'mobilePhone');
      expect(jsonResponse.mobilePhone).to.be.equal(value);
    });

    it('should check the JSON Response : `other`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseOther', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'other');
      expect(jsonResponse.other).to.be.equal(value);
    });
  });

  describe('Post-Condition : Delete address', async () => {
    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );
      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should filter list by address ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'id_address', addressId.toString());

      const textColumn = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address');
      expect(textColumn).to.be.equal(addressId.toString());
    });

    it('should delete address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const textResult = await boAddressesPage.deleteAddress(page, 1);
      expect(textResult).to.equal(boAddressesPage.successfulDeleteMessage);

      const numberOfAddressesAfterDelete = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
