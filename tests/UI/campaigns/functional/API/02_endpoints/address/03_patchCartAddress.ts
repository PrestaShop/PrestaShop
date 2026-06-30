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
  boAddressesCreatePage,
  foHummingbirdHomePage,
  foHummingbirdProductPage,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  type BrowserContext,
  dataCountries,
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
    firstName: customerData.firstName,
    lastName: customerData.lastName,
    email: customerData.email,
    country: 'France',
  });
  const editAddressData: FakerAddress = new FakerAddress({
    country: 'France',
  });

  const currentAddress: Record<string, string | number> = {};

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

  // FO : Create shopping cart
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

      const isStepDeliveryComplete = await foHummingbirdCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.equal(true);
    });
  });

  // API : Fetch access token
  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  // BO : Fetch cart ID
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

  describe('BackOffice : Go to edit address page', async () => {
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

    it('should filter Addresses table by the firstName of the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAddressesTable', baseContext);

      await boAddressesPage.filterAddresses(page, 'input', 'firstname', addressData.firstName);

      const numberOfAddresses = await boAddressesPage.getNumberOfElementInGrid(page);
      expect(numberOfAddresses).to.be.gte(1);

      idAddress = parseInt(
        (await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address')).toString(),
        10,
      );
      expect(idAddress).to.be.gt(0);
    });

    it('should go to Edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await boAddressesPage.goToEditAddressPage(page, 1);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);
    });
  });

  // cartId, stateId are read-only → not patched
  [
    {
      propertyName: 'firstName',
      propertyValue: editAddressData.firstName,
      propertyType: 'string',
      boField: 'firstName',
    },
    {
      propertyName: 'lastName',
      propertyValue: editAddressData.lastName,
      propertyType: 'string',
      boField: 'lastName',
    },
    {
      propertyName: 'address',
      propertyValue: editAddressData.address,
      propertyType: 'string',
      boField: 'address',
    },
    {
      propertyName: 'address2',
      propertyValue: editAddressData.secondAddress,
      propertyType: 'string',
      boField: 'address2',
    },
    {
      propertyName: 'city',
      propertyValue: editAddressData.city,
      propertyType: 'string',
      boField: 'city',
    },
    {
      propertyName: 'postCode',
      propertyValue: editAddressData.postalCode,
      propertyType: 'string',
      boField: 'postCode',
    },
    {
      propertyName: 'countryId',
      propertyValue: dataCountries.france.id,
      propertyType: 'number',
      boField: 'country',
    },
    {
      propertyName: 'homePhone',
      propertyValue: editAddressData.phone,
      propertyType: 'string',
      boField: 'phone',
    },
    {
      propertyName: 'mobilePhone',
      propertyValue: editAddressData.mobilePhone,
      propertyType: 'string',
      boField: 'mobilePhone',
    },
    {
      propertyName: 'company',
      propertyValue: editAddressData.company,
      propertyType: 'string',
      boField: 'company',
    },
    {
      propertyName: 'vatNumber',
      propertyValue: editAddressData.vatNumber,
      propertyType: 'string',
      boField: 'vatNumber',
    },
    {
      propertyName: 'other',
      propertyValue: editAddressData.other,
      propertyType: 'string',
      boField: 'other',
    },
    {
      propertyName: 'dni',
      propertyValue: editAddressData.dni,
      propertyType: 'string',
      boField: 'dni',
    },
  ].forEach((data: {
    propertyName: string;
    propertyValue: string | number;
    propertyType: string;
    boField: string;
  }) => {
    describe(`Update the property \`${data.propertyName}\` with API and check in BO`, async () => {
      it(`should request the endpoint /addresses/carts/{cartAddressId} for property "${data.propertyName}"`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `requestEndpoint${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        const dataPatch: any = {
          addressType: 'invoice_address',
        };
        dataPatch[data.propertyName] = data.propertyValue;

        const apiResponse = await apiContext.patch(`addresses/carts/${idCart}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
            'Content-Type': 'application/json',
          },
          data: dataPatch,
        });

        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();

        expect(jsonResponse).to.have.all.keys(
          'cartId',
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
          'stateId',
          'homePhone',
          'mobilePhone',
          'company',
          'vatNumber',
          'other',
          'dni',
        );

        // Check read-only properties
        expect(jsonResponse.cartId).to.be.a('number').and.equal(idCart);

        // Check the patched property
        expect(jsonResponse[data.propertyName]).to.be.a(data.propertyType).and.equal(data.propertyValue);

        // Lazy initialization from the first PATCH response
        // The cart may use a different address than addressData
        // so we read the real state from the first API response
        if (Object.keys(currentAddress).length === 0) {
          Object.assign(currentAddress, {
            firstName: jsonResponse.firstName,
            lastName: jsonResponse.lastName,
            address: jsonResponse.address,
            address2: jsonResponse.address2 ?? '',
            city: jsonResponse.city,
            postCode: jsonResponse.postCode,
            countryId: jsonResponse.countryId,
            homePhone: jsonResponse.homePhone ?? '',
            mobilePhone: jsonResponse.mobilePhone ?? '',
            company: jsonResponse.company ?? '',
            vatNumber: jsonResponse.vatNumber ?? '',
            other: jsonResponse.other ?? '',
            dni: jsonResponse.dni ?? '',
          });
        }

        // Check that other properties have not been modified
        Object.entries(currentAddress)
          .filter(([key]) => key !== data.propertyName && key !== 'countryId')
          .forEach(([key, expectedValue]) => {
            expect(
              jsonResponse[key],
              `Property "${key}" should not have changed after patching "${data.propertyName}"`,
            ).to.equal(expectedValue);
          });

        // Update current state with the new value
        currentAddress[data.propertyName] = data.propertyValue;
      });

      it(`should check the property "${data.propertyName}" is updated in BO`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkBO${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        await boAddressesCreatePage.reloadPage(page);

        const value = await boAddressesCreatePage.getValue(page, data.boField);

        if (data.propertyName !== 'countryId') {
          expect(value).to.equal(data.propertyValue);
        } else {
          expect(value).to.equal(dataCountries.france.name);
        }
      });

      it('should check that other properties are not modified in BO', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkBOOtherProperties${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        const boFieldMap: Record<string, string> = {
          firstName: 'firstName',
          lastName: 'lastName',
          address: 'address',
          address2: 'address2',
          city: 'city',
          postCode: 'postCode',
          homePhone: 'phone',
          mobilePhone: 'mobilePhone',
          company: 'company',
          vatNumber: 'vatNumber',
          other: 'other',
          dni: 'dni',
        };

        await Promise.all(
          Object.entries(currentAddress)
            .filter(([key]) => key !== data.propertyName && key !== 'countryId' && boFieldMap[key])
            .map(async ([key, expectedValue]) => {
              const value = await boAddressesCreatePage.getValue(page, boFieldMap[key]);
              expect(
                value,
                `Property "${key}" should not have changed in BO after patching "${data.propertyName}"`,
              ).to.equal(String(expectedValue));
            }),
        );
      });
    });
  });

  // API : Delete the address
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

  // Post-condition: Delete created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
