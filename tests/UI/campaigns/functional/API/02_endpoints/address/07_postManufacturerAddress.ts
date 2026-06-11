// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boBrandsPage,
  boBrandAdressesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCountries,
  dataStates,
  FakerBrandAddress,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_postManufacturerAddress';

describe('API : POST /addresses/manufacturers', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idAddress: number;
  let numberOfAddresses: number;

  const clientScope: string = 'address_write';
  const createAddress: FakerBrandAddress = new FakerBrandAddress({
    country: dataCountries.unitedStates.name,
    state: dataStates.california.name,
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
    });
  });

  describe('API : Create the Manufacturer Address', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should request the endpoint /addresses/manufacturers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('addresses/manufacturers', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          manufacturerId: 2,
          firstName: createAddress.firstName,
          lastName: createAddress.lastName,
          address: createAddress.address,
          address2: createAddress.secondaryAddress,
          city: createAddress.city,
          countryId: dataCountries.unitedStates.id,
          stateId: dataStates.california.id,
          postCode: createAddress.postalCode,
          homePhone: createAddress.homePhone,
          mobilePhone: createAddress.mobilePhone,
          other: createAddress.other,
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
        'manufacturerId',
        'lastName',
        'firstName',
        'address',
        'address2',
        'city',
        'postCode',
        'countryId',
        'stateId',
        'homePhone',
        'mobilePhone',
        'other',
        'dni',
      );
    });

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.addressId).to.be.gt(0);
      expect(jsonResponse.manufacturerId).to.be.eq(2);
      expect(jsonResponse.firstName).to.be.equal(createAddress.firstName);
      expect(jsonResponse.lastName).to.be.equal(createAddress.lastName);
      expect(jsonResponse.address).to.be.equal(createAddress.address);
      expect(jsonResponse.address2).to.be.equal(createAddress.secondaryAddress);
      expect(jsonResponse.city).to.be.equal(createAddress.city);
      expect(jsonResponse.postCode).to.be.equal(createAddress.postalCode);
      expect(jsonResponse.countryId).to.be.equal(dataCountries.unitedStates.id);
      expect(jsonResponse.homePhone).to.be.equal(createAddress.homePhone);
      expect(jsonResponse.mobilePhone).to.be.equal(createAddress.mobilePhone);
      expect(jsonResponse.other).to.be.equal(createAddress.other);
      expect(jsonResponse.dni).to.be.equal(createAddress.dni);
    });
  });

  describe('BackOffice : Check the Manufacturer Address is created', async () => {
    it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.brandsAndSuppliersLink,
      );
      await boBrandsPage.closeSfToolBar(page);

      const pageTitle = await boBrandsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boBrandsPage.pageTitle);
    });

    it('should reset all filters and get number of addresses in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfAddresses = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
      expect(numberOfAddresses).to.be.above(0);
    });

    it('should filter list by first name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByFirstName', baseContext);

      await boBrandsPage.filterAddresses(page, 'input', 'firstname', createAddress.firstName);

      const numberOfAddressesAfterCreation = await boBrandsPage.getNumberOfElementInGrid(page, 'manufacturer_address');
      expect(numberOfAddressesAfterCreation).to.be.equal(1);

      idAddress = parseInt(
        await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
        10,
      );
      expect(idAddress).to.be.gt(0);
    });

    it('should go to edit manufacturer address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await boBrandsPage.goToEditBrandAddressPage(page, 1);

      const pageTitle = await boBrandAdressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boBrandAdressesCreatePage.pageTitleEdit);
    });

    it('should check the JSON Response : `addressId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddressId', baseContext);

      expect(jsonResponse.addressId).to.be.equal(idAddress);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseFirstName', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'firstName');
      expect(jsonResponse.firstName).to.be.equal(value);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLastName', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'lastName');
      expect(jsonResponse.lastName).to.be.equal(value);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'address');
      expect(jsonResponse.address).to.be.equal(value);
    });

    it('should check the JSON Response : `address2`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress2', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'address2');
      expect(jsonResponse.address2).to.be.equal(value);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCity', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'city');
      expect(jsonResponse.city).to.be.equal(value);
    });

    it('should check the JSON Response : `postCode`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePostCode', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'postCode');
      expect(jsonResponse.postCode).to.be.equal(value);
    });

    it('should check the JSON Response : `countryId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCountryId', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'countryId');
      expect(value).to.be.equal(createAddress.country);
    });

    it('should check the JSON Response : `stateId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseStateId', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'state');
      expect(value).to.be.equal(createAddress.state);
    });

    it('should check the JSON Response : `homePhone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseHomePhone', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'phone');
      expect(jsonResponse.homePhone).to.be.equal(value);
    });

    it('should check the JSON Response : `mobilePhone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseMobilePhone', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'mobilePhone');
      expect(jsonResponse.mobilePhone).to.be.equal(value);
    });

    it('should check the JSON Response : `other`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseOther', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'other');
      expect(jsonResponse.other).to.be.equal(value);
    });

    it('should check the JSON Response : `dni`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDni', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'dni');
      expect(jsonResponse.dni).to.be.equal(value);
    });
  });

  describe('BackOffice : Delete the Manufacturer Address', async () => {
    it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPageToDelete', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.brandsAndSuppliersLink,
      );
      await boBrandsPage.closeSfToolBar(page);

      const pageTitle = await boBrandsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boBrandsPage.pageTitle);
    });

    it('should filter list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteAddresses', baseContext);

      await boBrandsPage.filterAddresses(page, 'input', 'firstname', createAddress.firstName);

      const textColumn = await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(textColumn).to.contains(createAddress.firstName);
    });

    it('should delete address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await boBrandsPage.deleteBrandAddress(page);
      expect(deleteTextResult).to.be.equal(boBrandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddresses', baseContext);

      const numberOfAddressesAfterDelete = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
      expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses - 1);
    });
  });
});
