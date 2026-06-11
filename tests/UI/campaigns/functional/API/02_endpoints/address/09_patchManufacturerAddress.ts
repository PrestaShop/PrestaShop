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
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_address_patchManufacturerAddress';

describe('API : PATCH /addresses/manufacturers/{addressId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idAddress: number;
  let numberOfAddresses: number;

  const clientScope: string = 'address_write';
  const createAddress: FakerBrandAddress = new FakerBrandAddress({
    country: 'France',
  });
  const updateAddress: FakerBrandAddress = new FakerBrandAddress({
    country: 'France',
  });

  const currentAddress: Record<string, string | number> = {};

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfAddresses = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
      expect(numberOfAddresses).to.be.above(0);
    });

    it('should create a manufacturer address via API', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createManufacturerAddress', baseContext);

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
          countryId: dataCountries.france.id,
          postCode: '75000',
          homePhone: createAddress.homePhone,
        },
      });
      expect(apiResponse.status()).to.eq(201);

      const createResponse = await apiResponse.json();
      idAddress = createResponse.addressId;
      expect(idAddress).to.be.gt(0);

      Object.assign(currentAddress, {
        firstName: createAddress.firstName,
        lastName: createAddress.lastName,
        address: createAddress.address,
        address2: createAddress.secondaryAddress ?? '',
        city: createAddress.city,
        postCode: '75000',
        countryId: dataCountries.france.id,
        homePhone: createAddress.homePhone,
        mobilePhone: createResponse.mobilePhone ?? '',
        other: createResponse.other ?? '',
        dni: createResponse.dni ?? '',
      });
    });
  });

  [
    {
      propertyName: 'firstName',
      propertyValue: updateAddress.firstName,
      propertyType: 'string',
      boField: 'firstName',
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
      propertyValue: updateAddress.secondaryAddress,
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
      boField: 'countryId',
    },
    {
      propertyName: 'homePhone',
      propertyValue: updateAddress.homePhone,
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
    describe(`API : Update the property \`${data.propertyName}\` with API`, async () => {
      it(`should request the endpoint /addresses/manufacturers/{addressId} for property "${data.propertyName}"`, async function () {
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

        const apiResponse = await apiContext.patch(`addresses/manufacturers/${idAddress}`, {
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

        expect(jsonResponse.addressId).to.be.a('number').and.equal(idAddress);
        expect(jsonResponse.manufacturerId).to.be.a('number').and.equal(2);

        expect(jsonResponse[data.propertyName]).to.be.a(data.propertyType).and.equal(data.propertyValue);

        for (const [key, expectedValue] of Object.entries(currentAddress)) {
          if (key === data.propertyName) continue;
          expect(jsonResponse[key],
            `Property "${key}" should not have changed after patching "${data.propertyName}"`,
          ).to.equal(expectedValue);
        }

        currentAddress[data.propertyName] = data.propertyValue;
      });

      it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToBrandsPageToCheck${data.propertyName}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.brandsAndSuppliersLink,
        );
        await boBrandsPage.closeSfToolBar(page);

        const pageTitle = await boBrandsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boBrandsPage.pageTitle);
      });

      it('should filter list by first name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByFirstName${data.propertyName}`, baseContext);

        await boBrandsPage.filterAddresses(page, 'input', 'firstname', jsonResponse.firstName);

        const numberOfAddressesAfterFilter = await boBrandsPage.getNumberOfElementInGrid(page, 'manufacturer_address');
        expect(numberOfAddressesAfterFilter).to.be.equal(1);

        const idAddressBO = parseInt(
          await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'id_address'),
          10,
        );
        expect(idAddressBO).to.be.equal(idAddress);
      });

      it('should go to edit manufacturer address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditAddressPage${data.propertyName}`, baseContext);

        await boBrandsPage.goToEditBrandAddressPage(page, 1);

        const pageTitle = await boBrandAdressesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boBrandAdressesCreatePage.pageTitleEdit);
      });

      it(`should check the property "${data.propertyName}" is updated in BO`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkBO${data.propertyName.charAt(0).toUpperCase() + data.propertyName.slice(1)}`,
          baseContext,
        );

        const value = await boBrandAdressesCreatePage.getValue(page, data.boField);
        if (data.boField !== 'countryId') {
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

        for (const [key, expectedValue] of Object.entries(currentAddress)) {
          if (key === data.propertyName) continue;
          if (key === 'countryId') continue;

          const boFieldMap: Record<string, string> = {
            firstName: 'firstName',
            lastName: 'lastName',
            address: 'address',
            address2: 'address2',
            city: 'city',
            postCode: 'postCode',
            homePhone: 'phone',
            mobilePhone: 'mobilePhone',
            other: 'other',
            dni: 'dni',
          };

          if (boFieldMap[key]) {
            const value = await boBrandAdressesCreatePage.getValue(page, boFieldMap[key]);
            expect(value, `Property "${key}" should not have changed in BO after patching "${data.propertyName}"`).to.equal(String(expectedValue));
          }
        }
      });
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

      await boBrandsPage.filterAddresses(page, 'input', 'firstname', jsonResponse.firstName);

      const textColumn = await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'firstname');
      expect(textColumn).to.contains(jsonResponse.firstName);
    });

    it('should delete address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const deleteTextResult = await boBrandsPage.deleteBrandAddress(page);
      expect(deleteTextResult).to.be.equal(boBrandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddress', baseContext);

      const numberOfAddressesAfterDelete = await boBrandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
      expect(numberOfAddressesAfterDelete).to.be.equal(numberOfAddresses);
    });
  });
});
