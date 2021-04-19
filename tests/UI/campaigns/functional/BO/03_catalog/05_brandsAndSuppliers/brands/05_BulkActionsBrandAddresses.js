require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const BrandAddressFaker = require('@data/faker/brandAddress');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const brandsPage = require('@pages/BO/catalog/brands');
const addBrandAddressPage = require('@pages/BO/catalog/brands/addresses/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_bulkActionsBrandAddresses';


let browserContext;
let page;
let numberOfBrandAddresses = 0;
const firstAddressData = new BrandAddressFaker({firstName: 'AddressToDelete'});
const secondAddressData = new BrandAddressFaker({firstName: 'AddressToDeleteTwo'});


// Create 2 brands, Enable, disable and delete with bulk actions
describe('Create 2 brand Addresses and delete with bulk actions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // GO to Brands Page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.brandsAndSuppliersLink,
    );

    await brandsPage.closeSfToolBar(page);

    const pageTitle = await brandsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(brandsPage.pageTitle);
  });

  it('should reset all Addresses filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfBrandAddresses = await brandsPage.resetAndGetNumberOfLines(page, 'manufacturer_address');
    await expect(numberOfBrandAddresses).to.be.above(0);
  });

  // 1: Create 2 Addresses
  describe('Create 2 Addresses', async () => {
    const addressesToCreate = [firstAddressData, secondAddressData];

    addressesToCreate.forEach((addressToCreate, index) => {
      it('should go to new brand Address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAddressPage${index + 1}`, baseContext);

        await brandsPage.goToAddNewBrandAddressPage(page);
        const pageTitle = await addBrandAddressPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addBrandAddressPage.pageTitle);
      });

      it('should create new brand Address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index + 1}`, baseContext);

        const result = await addBrandAddressPage.createEditBrandAddress(page, addressToCreate);
        await expect(result).to.equal(brandsPage.successfulCreationMessage);

        const numberOfBrandAddressesAfterCreation = await brandsPage.getNumberOfElementInGrid(
          page,
          'manufacturer_address',
        );

        await expect(numberOfBrandAddressesAfterCreation).to.be.equal(numberOfBrandAddresses + index + 1);
      });
    });
  });

  // 2 : Delete Brand Addresses created with bulk actions
  describe('Delete Addresses with Bulk Actions', async () => {
    it('should filter Addresses list by firstName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await brandsPage.filterAddresses(page, 'input', 'firstname', 'AddressToDelete');

      const numberOfBrandAddressesAfterFilter = await brandsPage.getNumberOfElementInGrid(
        page,
        'manufacturer_address',
      );

      await expect(numberOfBrandAddressesAfterFilter).to.be.at.most(2);

      for (let i = 1; i <= numberOfBrandAddressesAfterFilter; i++) {
        const textColumn = await brandsPage.getTextColumnFromTableAddresses(page, i, 'firstname');
        await expect(textColumn).to.contains('AddressToDelete');
      }
    });

    it('should delete Addresses with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await brandsPage.deleteWithBulkActions(page, 'manufacturer_address');
      await expect(deleteTextResult).to.be.equal(brandsPage.successfulDeleteMessage);
    });

    it('should reset Addresses filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfBrandAddressesAfterReset = await brandsPage.resetAndGetNumberOfLines(
        page,
        'manufacturer_address',
      );

      await expect(numberOfBrandAddressesAfterReset).to.be.equal(numberOfBrandAddresses);
    });
  });
});
