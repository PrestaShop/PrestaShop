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
  FakerBrandAddress,
  FakerAddress,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_getManufacturerAddressId';

describe('API : GET /addresses/manufacturers/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idAddress: number;
  let numberOfAddresses: number;

  const clientScope: string = 'address_read';
  const createAddress: FakerBrandAddress = new FakerBrandAddress({
    brandName : 'Graphic Corner',
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

  describe('BackOffice : Create a Manufacturer Address', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAddressesTable', baseContext);

      numberOfAddresses = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
      expect(numberOfAddresses).to.be.above(0);
    });

     it('should go to add new address page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

       await boBrandsPage.goToAddNewBrandAddressPage(page);

       const pageTitle = await boBrandAdressesCreatePage.getPageTitle(page);
       expect(pageTitle).to.contains(boBrandAdressesCreatePage.pageTitle);
     });

     it('should create address', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

       const result = await boBrandAdressesCreatePage.createEditBrandAddress(page, createAddress);
       expect(result).to.equal(boBrandsPage.successfulCreationMessage);

       const numberOfAddressesAfterCreation = await boBrandsPage.getNumberOfElementInGrid(page, 'manufacturer_address');
       expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses+1);
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
  });

  describe('API : Fetch the Manufacturer Address', async () => {
    it('should request the endpoint /addresses/manufacturers/{addressId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`addresses/manufacturers/${idAddress}`, {
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

    it('should check the JSON Response : `addressId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddressId', baseContext);

      expect(jsonResponse).to.have.property('addressId');
      expect(jsonResponse.addressId).to.be.a('number');
      expect(jsonResponse.addressId).to.be.equal(idAddress);
    });

    it('should check the JSON Response : `manufacturerId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseManufacturerId', baseContext);

      expect(jsonResponse).to.have.property('manufacturerId');
      expect(jsonResponse.manufacturerId).to.be.a('number');
      expect(jsonResponse.manufacturerId).to.be.equal(2);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseFirstName', baseContext);

      expect(jsonResponse).to.have.property('firstName');
      expect(jsonResponse.firstName).to.be.a('string');
      expect(jsonResponse.firstName).to.be.equal(createAddress.firstName);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLastName', baseContext);

      expect(jsonResponse).to.have.property('lastName');
      expect(jsonResponse.lastName).to.be.a('string');
      expect(jsonResponse.lastName).to.be.equal(createAddress.lastName);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseAddress', baseContext);

      expect(jsonResponse).to.have.property('address');
      expect(jsonResponse.address).to.be.a('string');
      expect(jsonResponse.address).to.be.equal(createAddress.address);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCity', baseContext);

      expect(jsonResponse).to.have.property('city');
      expect(jsonResponse.city).to.be.a('string');
      expect(jsonResponse.city).to.be.equal(createAddress.city);
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
      expect(jsonResponse.homePhone).to.be.equal(createAddress.homePhone);
    });
  });

  describe('BackOffice : Expected data', async () => {
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

      const numberOfAddressesAfterFilter = await boBrandsPage.getNumberOfElementInGrid(page, 'manufacturer_address');
      expect(numberOfAddressesAfterFilter).to.be.equal(1);

      const idAddressBO = parseInt(
        await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
        10,
      );
      expect(idAddressBO).to.be.equal(idAddress);
    });

    it('should go to edit manufacturer address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await boBrandsPage.goToEditBrandAddressPage(page, 1);

      const pageTitle = await boBrandAdressesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boBrandAdressesCreatePage.pageTitleEdit);
    });

    it('should check the JSON Response : `firstName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOFirstName', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'firstName');
      expect(jsonResponse.firstName).to.be.equal(value);
    });

    it('should check the JSON Response : `lastName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOLastName', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'lastName');
      expect(jsonResponse.lastName).to.be.equal(value);
    });

    it('should check the JSON Response : `address`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOAddress', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'address');
      expect(jsonResponse.address).to.be.equal(value);
    });

    it('should check the JSON Response : `city`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOCity', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'city');
      expect(jsonResponse.city).to.be.equal(value);
    });

    it('should check the JSON Response : `phone`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkBOPhone', baseContext);

      const value = await boBrandAdressesCreatePage.getValue(page, 'phone');
      expect(jsonResponse.homePhone).to.be.equal(value);
    });
  });
  
  describe('BackOffice : Delete the Manufacturer Address', async () => {
    it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPageForDeletion', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteAddress', baseContext);

      await boBrandsPage.filterAddresses(page, 'input', 'firstname', createAddress.firstName);

      const textColumn = await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(textColumn).to.contains(createAddress.firstName);
    });

    it('should delete address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const deleteTextResult = await boBrandsPage.deleteRowInTable(page, 'manufacturer_address');
      expect(deleteTextResult).to.be.equal(boBrandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddress', baseContext);

      const numberOfAddressesAfterDelete = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
      expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses - 1);
    });
  });
});
