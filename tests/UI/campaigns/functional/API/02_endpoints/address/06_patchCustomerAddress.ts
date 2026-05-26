// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAddressesPage,
  boAddressesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCountries,
  FakerAddress,
  FakerCustomer,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_patchCustomerAddress';

describe('API : PATCH /addresses/customers/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let idAddress: number;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'address_write';

  const customerData: FakerCustomer = new FakerCustomer();

  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });

  const editAddressData: FakerAddress = new FakerAddress({
    country: 'France',
  });

  // Pre-condition: Create customer
  createCustomerTest(customerData, `${baseContext}_preTest_1`);

  // Pre-condition: Create address
  createAddressTest(addressData, `${baseContext}_preTest_2`);

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

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );
      await boAddressesPage.closeSfToolBar(page);

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should filter list by firstname', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAddress', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', addressData.firstName);

      const numberOfAddressesAfterFilter = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterFilter).to.equal(1);

      const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.equal(addressData.firstName);

      idAddress = parseInt(
        (await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address')).toString(),
        10,
      );
      expect(idAddress).to.be.gt(0);
    });
  });

  describe('API : Patch the Customer Address', async () => {
    it('should request the endpoint /addresses/customers/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.patch(`addresses/customers/${idAddress}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
          'Content-Type': 'application/json',
        },
        data: {
          firstName: editAddressData.firstName,
          lastName: editAddressData.lastName,
          address: editAddressData.address,
          city: editAddressData.city,
          countryId: dataCountries.france.id,
          phone: editAddressData.phone,
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

    it('should check the JSON Response : `addressId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddressId', baseContext);

      expect(jsonResponse).to.have.property('addressId');
      expect(jsonResponse.addressId).to.be.a('number');
      expect(jsonResponse.addressId).to.be.equal(idAddress);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseFirstName', baseContext);

      expect(jsonResponse).to.have.property('firstName');
      expect(jsonResponse.firstName).to.be.a('string');
      expect(jsonResponse.firstName).to.be.equal(editAddressData.firstName);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLastName', baseContext);

      expect(jsonResponse).to.have.property('lastName');
      expect(jsonResponse.lastName).to.be.a('string');
      expect(jsonResponse.lastName).to.be.equal(editAddressData.lastName);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress', baseContext);

      expect(jsonResponse).to.have.property('address');
      expect(jsonResponse.address).to.be.a('string');
      expect(jsonResponse.address).to.be.equal(editAddressData.address);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCity', baseContext);

      expect(jsonResponse).to.have.property('city');
      expect(jsonResponse.city).to.be.a('string');
      expect(jsonResponse.city).to.be.equal(editAddressData.city);
    });

    it('should check the JSON Response : `phone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePhone', baseContext);

      expect(jsonResponse).to.have.property('homePhone');
      expect(jsonResponse.homePhone).to.be.a('string');
      expect(jsonResponse.homePhone).to.be.equal(editAddressData.phone);
    });
  });

  describe('BackOffice : Check that the Address is updated', async () => {
    it('should filter list by firstname', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterUpdate', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', editAddressData.firstName);

      const numberOfAddressesAfterFilter = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterFilter).to.equal(1);

      const lastName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.equal(editAddressData.lastName);

      const address = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      expect(address).to.equal(editAddressData.address);

      const city = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'city');
      expect(city).to.equal(editAddressData.city);
    });

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await boAddressesPage.goToEditAddressPage(page, 1);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOFirstName', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'firstName');
      expect(jsonResponse.firstName).to.be.equal(value);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOLastName', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'lastName');
      expect(jsonResponse.lastName).to.be.equal(value);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOAddress', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'address');
      expect(jsonResponse.address).to.be.equal(value);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOCity', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'city');
      expect(jsonResponse.city).to.be.equal(value);
    });

    it('should check the JSON Response : `phone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOPhone', baseContext);

      const value = await boAddressesCreatePage.getValue(page, 'phone');
      expect(jsonResponse.homePhone).to.be.equal(value);
    });
  });

  describe('API : Delete the Address', async () => {
    it('should request the endpoint /addresses/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const apiResponse = await apiContext.delete(`addresses/${idAddress}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  // Post-condition: Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
