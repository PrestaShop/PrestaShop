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
  boAddressesCreatePage,
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

describe('API : PATCH /addresses/orders/{orderId}', async () => {
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

  // Pre-condition : Create an order
  createOrderByCustomerTest(orderData, `${baseContext}_preTest_0`);

  // API : Fetch access token
  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  // BO : Fetch order ID & init currentAddress
  describe('BackOffice : Fetch the order ID', async () => {
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

      // Initialize current address state from IHM creation data
      Object.assign(currentAddress, {
        addressAlias: addressData.alias ?? '',
        firstName: addressData.firstName,
        lastName: addressData.lastName,
        address: addressData.address,
        address2: addressData.secondAddress ?? '',
        city: addressData.city,
        postCode: addressData.postalCode,
        countryId: dataCountries.france.id,
        homePhone: addressData.phone ?? '',
        mobilePhone: addressData.mobilePhone ?? '',
        company: addressData.company ?? '',
        vatNumber: addressData.vatNumber ?? '',
        other: addressData.other ?? '',
        dni: addressData.dni ?? '',
      });
    });
  });

  // API : Patch each property individually
  // orderId, addressId, customerId, stateId are read-only → not patched

  [
    {
      propertyName: 'firstName',
      propertyValue: updateAddress.firstName,
      propertyType: 'string',
      boField: 'firstName',
    },
    {
      propertyName: 'addressAlias',
      propertyValue: updateAddress.alias,
      propertyType: 'string',
      boField: 'alias',
    },
    {
      propertyName: 'lastName',
      propertyValue: updateAddress.lastName,
      propertyType: 'string',
      boField: 'lastName',
    },
    {
      propertyName: 'address',
      propertyValue: updateAddress.address,
      propertyType: 'string',
      boField: 'address',
    },
    {
      propertyName: 'address2',
      propertyValue: updateAddress.secondAddress,
      propertyType: 'string',
      boField: 'address2',
    },
    {
      propertyName: 'city',
      propertyValue: updateAddress.city,
      propertyType: 'string',
      boField: 'city',
    },
    {
      propertyName: 'postCode',
      propertyValue: updateAddress.postalCode,
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
      propertyValue: updateAddress.phone,
      propertyType: 'string',
      boField: 'phone',
    },
    {
      propertyName: 'mobilePhone',
      propertyValue: updateAddress.mobilePhone,
      propertyType: 'string',
      boField: 'mobilePhone',
    },
    {
      propertyName: 'company',
      propertyValue: updateAddress.company,
      propertyType: 'string',
      boField: 'company',
    },
    {
      propertyName: 'vatNumber',
      propertyValue: updateAddress.vatNumber,
      propertyType: 'string',
      boField: 'vatNumber',
    },
    {
      propertyName: 'other',
      propertyValue: updateAddress.other,
      propertyType: 'string',
      boField: 'other',
    },
    {
      propertyName: 'dni',
      propertyValue: updateAddress.dni,
      propertyType: 'string',
      boField: 'dni',
    },
  ].forEach((data: {
    propertyName: string;
    propertyValue: string | number;
    propertyType: string;
    boField: string;
  }) => {
    describe(`API : Update the property \`${data.propertyName}\` and check in BO`, async () => {
      it(`should request the endpoint /addresses/orders/{idOrder} for property "${data.propertyName}"`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `requestEndpoint${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        const dataPatch: any = {
          addressType: 'delivery_address',
          countryId: dataCountries.france.id,
        };
        dataPatch[data.propertyName] = data.propertyValue;

        const apiResponse = await apiContext.patch(`addresses/orders/${idOrder}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: dataPatch,
        });

        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();

        expect(jsonResponse).to.have.all.keys(
          'orderId',
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
        expect(jsonResponse.orderId).to.be.a('number').and.equal(idOrder);

        // Check the patched property
        expect(jsonResponse[data.propertyName]).to.be.a(data.propertyType).and.equal(data.propertyValue);

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

      it('should go to \'Customer > Addresses\' page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToAddressesPage${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customersParentLink,
          boDashboardPage.addressesLink,
        );

        const pageTitle = await boAddressesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boAddressesPage.pageTitle);
      });

      it('should filter list by first name', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `filterListByName${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        await boAddressesPage.resetFilter(page);
        await boAddressesPage.filterAddresses(page, 'input', 'firstname', jsonResponse.firstName);

        const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
        expect(firstName).to.contains(jsonResponse.firstName);
      });

      it(`should check the property "${data.propertyName}" is updated in BO`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkBO${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        await boAddressesPage.goToEditAddressPage(page, 1);

        const editPageTitle = await boAddressesCreatePage.getPageTitle(page);
        expect(editPageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);

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
          addressAlias: 'alias',
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

        // Check that other properties have not been modified in BO
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

        // Go back to the addresses list for the next iteration
        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customersParentLink,
          boDashboardPage.addressesLink,
        );
      });
    });
  });

  // BO : Delete address
  describe('Backoffice : Delete address', async () => {
    it('should filter list by first name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boAddressesPage.resetFilter(page);
      await boAddressesPage.filterAddresses(page, 'input', 'firstname', jsonResponse.firstName);

      const firstName = await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(firstName).to.contains(jsonResponse.firstName);
    });

    it('should delete address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const textResult = await boAddressesPage.deleteAddress(page, 1);
      expect(textResult).to.equal(boAddressesPage.successfulDeleteMessage);

      const numberOfAddressesAfterDelete = await boAddressesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAddressesAfterDelete).to.be.gt(1);
    });
  });

  // Post-condition: Delete customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);
});
