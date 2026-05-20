// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boAddressesPage,
  boAddressesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCountries,
  dataCustomers,
  FakerAddress,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_postAddress';

describe('API : POST /addresses', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idAddress: number;
  let numberOfAddresses: number;

  const clientScope: string = 'address_write';
  const createAddress: FakerAddress = new FakerAddress({
    firstName: dataCustomers.johnDoe.firstName,
    lastName: dataCustomers.johnDoe.lastName,
    alias: 'test',
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

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      accessToken = await requestAccessToken(clientScope);
      expect(accessToken).to.not.be.empty;
    });
  });

  describe('API : Create the Address', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should request the endpoint /addresses/customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('addresses/customers', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          customerId: dataCustomers.johnDoe.id,
          addressAlias: createAddress.alias,
          firstName: createAddress.firstName,
          lastName: createAddress.lastName,
          address: createAddress.address,
          city: createAddress.city,
          countryId: dataCountries.france.id,
          postcode: '92400',
          mobilePhone: createAddress.phone,
        },
      });
      expect(apiResponse.status()).to.eq(201);
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

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.addressId).to.be.gt(0);
      expect(jsonResponse.customerId).to.be.equal(dataCustomers.johnDoe.id);
      expect(jsonResponse.firstName).to.be.equal(createAddress.firstName);
      expect(jsonResponse.lastName).to.be.equal(createAddress.lastName);
      expect(jsonResponse.address).to.be.equal(createAddress.address);
      expect(jsonResponse.city).to.be.equal(createAddress.city);
      expect(jsonResponse.countryId).to.be.equal(dataCountries.france.id);
      expect(jsonResponse.mobilePhone).to.be.equal(createAddress.phone);
    });
  });

   describe('BackOffice : Check the Address is created', async () => {
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
       await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);
 
       numberOfAddresses = await boAddressesPage.resetAndGetNumberOfLines(page);
       expect(numberOfAddresses).to.be.above(0);
     });
 
     it('should filter list by first name', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'filterByFirstName', baseContext);
 
       await boAddressesPage.filterAddresses(page, 'input', 'firstname', createAddress.firstName);
 
       const numberOfAddressesAfterCreation = await boAddressesPage.getNumberOfElementInGrid(page);
       expect(numberOfAddressesAfterCreation).to.be.equal(1);
 
       idAddress = parseInt(
         await boAddressesPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
         10,
       );
       expect(idAddress).to.be.gt(0);
     });
 
     it('should go to edit address page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);
 
       await boAddressesPage.goToEditAddressPage(page, 1);
 
       const pageTitle = await boAddressesCreatePage.getPageTitle(page);
       expect(pageTitle).to.contains(boAddressesCreatePage.pageTitleEdit);
     });
 
     it('should check the JSON Response : `addressId`', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddressId', baseContext);
 
       expect(jsonResponse.addressId).to.be.equal(idAddress);
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
 
     it('should check the JSON Response : `address`', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress', baseContext);
 
       const value = await boAddressesCreatePage.getValue(page, 'address');
       expect(jsonResponse.address).to.be.equal(value);
     });
 
     it('should check the JSON Response : `city`', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCity', baseContext);
 
       const value = await boAddressesCreatePage.getValue(page, 'city');
       expect(jsonResponse.city).to.be.equal(value);
     });
 
     it('should check the JSON Response : `phone`', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePhone', baseContext);
 
       const value = await boAddressesCreatePage.getValue(page, 'phone');
       expect(jsonResponse.phone).to.be.equal(value);
     });
   });
 
   describe('BackOffice : Delete the Address', async () => {
     it('should go to \'Customers > Addresses\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageForDeletion', baseContext);
 
       await boDashboardPage.goToSubMenu(
         page,
         boDashboardPage.customersParentLink,
         boDashboardPage.addressesLink,
       );
 
       const pageTitle = await boAddressesPage.getPageTitle(page);
       expect(pageTitle).to.contains(boAddressesPage.pageTitle);
     });
 
     it('should filter list by id', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'filterForDeletion', baseContext);
 
       await boAddressesPage.resetFilter(page);
       await boAddressesPage.filterAddresses(page, 'input', 'id_address', idAddress.toString());
 
       const numberOfAddressesAfterFilter = await boAddressesPage.getNumberOfElementInGrid(page);
       expect(numberOfAddressesAfterFilter).to.be.equal(1);
     });
 
     it('should delete address', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);
 
       const textResult = await boAddressesPage.deleteAddress(page, 1);
       expect(textResult).to.contains(boAddressesPage.successfulDeleteMessage);
     });
 
     it('should reset filter', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);
 
       const numberOfAddressesAfterDelete = await boAddressesPage.resetAndGetNumberOfLines(page);
       expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
     });
   });
});
