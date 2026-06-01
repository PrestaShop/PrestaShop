// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createOrderByCustomerTest} from '@commonTests/FO/hummingbird/order';
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boOrdersPage,
  boDashboardPage,
  boLoginPage,
  boAddressesPage,
  type BrowserContext,
  dataCountries,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerOrder,
  FakerCustomer,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_patchOrderAddress';

describe('API : PATCH /addresses/orders/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idOrder: number;

  const clientScope: string = 'address_write';
  const customerData: FakerCustomer = new FakerCustomer();

  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    firstName: customerData.firstName,
    lastName: customerData.lastName,
    country: 'France',
  });
  const updateAddress: FakerAddress = new FakerAddress({
    country: 'France',
  });
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
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

  // Pre-condition : Create an order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_0`);

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Fetch the address ID', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should filter order by customer name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterOrderByCustomer', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const numberOfOrdersAfterFilter = await boOrdersPage.getNumberOfElementInGrid(page);
      expect(numberOfOrdersAfterFilter).to.be.gt(0);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderAndAddressId', baseContext);

      idOrder = parseInt(await boOrdersPage.getTextColumn(page, 'id_order', 1), 10);
      expect(idOrder).to.be.gt(0);
    });
  });

  describe('API : Patch the Order Address', async () => {
    it('should request the endpoint /addresses/orders/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.patch(`addresses/orders/${idOrder}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          addressType: 'delivery_address',
          addressAlias: updateAddress.alias,
          firstName: updateAddress.firstName,
          lastName: updateAddress.lastName,
          address: updateAddress.address,
          city: updateAddress.city,
          countryId: dataCountries.france.id,
          postCode: '75000',
          homePhone: updateAddress.phone,
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
        'orderId',
        'addressAlias',
        'firstName',
        'lastName',
        'address',
        'address2',
        'city',
        'postCode',
        'countryId',
        'stateId',
        'homePhone',
        'mobilePhone',
        'company',
        'vatNumber',
        'other',
        'dni',
      );
    });

    it('should check the JSON Response : `addressId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddressId', baseContext);

      expect(jsonResponse).to.have.property('orderId');
      expect(jsonResponse.orderId).to.be.a('number');
      expect(jsonResponse.orderId).to.be.equal(idOrder);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseFirstName', baseContext);

      expect(jsonResponse).to.have.property('firstName');
      expect(jsonResponse.firstName).to.be.a('string');
      expect(jsonResponse.firstName).to.be.equal(updateAddress.firstName);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLastName', baseContext);

      expect(jsonResponse).to.have.property('lastName');
      expect(jsonResponse.lastName).to.be.a('string');
      expect(jsonResponse.lastName).to.be.equal(updateAddress.lastName);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress', baseContext);

      expect(jsonResponse).to.have.property('address');
      expect(jsonResponse.address).to.be.a('string');
      expect(jsonResponse.address).to.be.equal(updateAddress.address);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCity', baseContext);

      expect(jsonResponse).to.have.property('city');
      expect(jsonResponse.city).to.be.a('string');
      expect(jsonResponse.city).to.be.equal(updateAddress.city);
    });

    it('should check the JSON Response : `countryId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCountryId', baseContext);

      expect(jsonResponse).to.have.property('countryId');
      expect(jsonResponse.countryId).to.be.a('number');
      expect(jsonResponse.countryId).to.be.equal(dataCountries.france.id);
    });

    it('should check the JSON Response : `homePhone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseHomePhone', baseContext);

      expect(jsonResponse).to.have.property('homePhone');
      expect(jsonResponse.homePhone).to.be.a('string');
      expect(jsonResponse.homePhone).to.be.equal(updateAddress.phone);
    });
  });
  
  describe('BackOffice : Expected data', async () => {
    it('should go to \'Customer > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.addressesLink,
      );

      const pageTitle = await boAddressesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesPage.pageTitle);
    });

    it('should filter list by first name and last name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToGetID', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', updateAddress.firstName);
      await boAddressesPage.filterAddresses(page, 'input', 'lastname', updateAddress.lastName);

      const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.contains(updateAddress.firstName);

      const lastName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.contains(updateAddress.lastName);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfAddressesAfterReset = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterReset).to.be.gte(1);
    });
  });

  // Post-condition: Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
