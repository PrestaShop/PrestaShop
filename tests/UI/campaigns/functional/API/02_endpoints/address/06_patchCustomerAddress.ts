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

  // API : Fetch access token
  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
    });
  });

  // BO : Fetch address ID & init currentAddress
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

    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEditAddressPage', baseContext);

      await boAddressesPage.goToEditAddressPage(page, 1);

      const pageTitle = await boAddressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);
    });
  });

  // API : Patch each property individually
  // addressId, customerId, stateId are read-only → not patched
  [
    {
      propertyName: 'addressAlias',
      propertyValue: editAddressData.alias,
      propertyType: 'string',
      boField: 'alias',
    },
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
      it(`should request the endpoint /addresses/customers/{addressId} for property "${data.propertyName}"`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `requestEndpoint${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        const dataPatch: any = {
          countryId: dataCountries.france.id,
        };
        dataPatch[data.propertyName] = data.propertyValue;

        const apiResponse = await apiContext.patch(`addresses/customers/${idAddress}`, {
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

        // Check read-only properties
        expect(jsonResponse.addressId).to.be.a('number').and.equal(idAddress);

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
      });
    });
  });

  // API : Delete the address
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
