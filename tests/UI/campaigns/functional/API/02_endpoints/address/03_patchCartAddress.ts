// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createAddressTest} from '@commonTests/BO/customers/address';
import {createCustomerTest, deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boShoppingCartsPage,
  boDashboardPage,
  boLoginPage,
  boAddressesPage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  type BrowserContext,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  utilsAPI,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_patchCartAddress';

describe('API : PATCH /addresses/carts/{cartAddressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let idCart: number;
  let accessToken: string;
  let jsonResponse: any;
  let idAddress: number;

  const clientScope: string = 'address_write';

  const customerData: FakerCustomer = new FakerCustomer();

  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });
  const editAddressData: FakerAddress = new FakerAddress();

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

  describe('FO : Create shopping cart', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHummingbirdHomePage.goToFo(page);
      await foHummingbirdHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should go to the fourth product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 4);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_5.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page, 1);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foHummingbirdCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.equal(true);
    });

    it('should sign in with the created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillCustomerInformation', baseContext);

      await foHummingbirdCheckoutPage.clickOnSignIn(page);

      const isStepCompleted = await foHummingbirdCheckoutPage.customerLogin(page, customerData);
      expect(isStepCompleted).to.equal(true);
    });

    it('should choose the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdAddress', baseContext);

      const isStepCompleted = await foHummingbirdCheckoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.equal(true);
    });

    it('should continue to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'continueToPaymentStep', baseContext);

      // Delivery step - Go to payment step
      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);
    });
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Fetch the ID of the cart', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Shopping carts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.shoppingCartsLink,
      );

      const pageTitle = await boShoppingCartsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
    });

    it('should get the first cart ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getFirstCartAddressId', baseContext);

      const numberOfShoppingCarts = await boShoppingCartsPage.getNumberOfElementInGrid(page);
      expect(numberOfShoppingCarts).to.be.gt(0);

      idCart = parseInt(
        (await boShoppingCartsPage.getTextColumn(page, 1, 'id_cart')).toString(),
        10,
      );
      expect(idCart).to.be.gt(0);
    });
  });

  describe('API : Patch the Cart Address', async () => {
    it('should request the endpoint /addresses/carts/{cartAddressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.patch(`addresses/carts/${idCart}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
          'Content-Type': 'application/json',
        },
        data: {
          addressType: 'invoice_address',
          address: editAddressData.address,
          city: editAddressData.city,
          firstName: editAddressData.firstName,
          lastName: editAddressData.lastName,
        },
      });

      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('cartId');
      expect(jsonResponse.cartId).to.equal(idCart);
      expect(jsonResponse).to.have.property('address');
      expect(jsonResponse.address).to.equal(editAddressData.address);
      expect(jsonResponse).to.have.property('city');
      expect(jsonResponse.city).to.equal(editAddressData.city);
    });
  });

  describe('BackOffice : Check that the Address is updated', async () => {
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
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', editAddressData.firstName);

      const numberOfAddressesAfterFilter = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddressesAfterFilter).to.equal(1);

      const lastName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'lastname');
      expect(lastName).to.equal(editAddressData.lastName);

      const address = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'address1');
      expect(address).to.equal(editAddressData.address);

      const city = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'city');
      expect(city).to.equal(editAddressData.city);

      idAddress = parseInt((await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address')).toString(), 10);
      expect(idAddress).to.be.gt(0);
    });
  });

  describe('API : Delete the Address', async () => {
    it('should request the endpoint /addresses/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointToDelete', baseContext);

      const apiResponse = await apiContext.delete(`addresses/${idAddress}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
