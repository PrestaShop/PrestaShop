/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const BrandAddressFaker = require('@data/faker/brandAddress');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const AddBrandAddressPage = require('@pages/BO/catalog/brands/addresses/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_bulkActionsBrandAddresses';


let browserContext;
let page;
let numberOfBrandAddresses = 0;
const firstAddressData = new BrandAddressFaker({firstName: 'AddressToDelete'});
const secondAddressData = new BrandAddressFaker({firstName: 'AddressToDeleteTwo'});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    addBrandAddressPage: new AddBrandAddressPage(page),
  };
};

// Create 2 brands, Enable, disable and delete with bulk actions
describe('Create 2 brand Addresses and delete with bulk actions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to brands page
  loginCommon.loginBO();

  // GO to Brands Page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );

    await this.pageObjects.brandsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all Addresses filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfBrandAddresses = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer_address');
    await expect(numberOfBrandAddresses).to.be.above(0);
  });

  // 1: Create 2 Addresses
  describe('Create 2 Addresses', async () => {
    const addressesToCreate = [firstAddressData, secondAddressData];

    addressesToCreate.forEach((addressToCreate, index) => {
      it('should go to new brand Address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAddressPage${index + 1}`, baseContext);

        await this.pageObjects.brandsPage.goToAddNewBrandAddressPage();
        const pageTitle = await this.pageObjects.addBrandAddressPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addBrandAddressPage.pageTitle);
      });

      it('should create new brand Address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index + 1}`, baseContext);

        const result = await this.pageObjects.addBrandAddressPage.createEditBrandAddress(addressToCreate);
        await expect(result).to.equal(this.pageObjects.brandsPage.successfulCreationMessage);

        const numberOfBrandAddressesAfterCreation = await this.pageObjects.brandsPage.getNumberOfElementInGrid(
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

      await this.pageObjects.brandsPage.filterAddresses('input', 'firstname', 'AddressToDelete');

      const numberOfBrandAddressesAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid(
        'manufacturer_address',
      );

      await expect(numberOfBrandAddressesAfterFilter).to.be.at.most(numberOfBrandAddresses);

      for (let i = 1; i <= numberOfBrandAddressesAfterFilter; i++) {
        const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableAddresses(i, 'firstname');
        await expect(textColumn).to.contains('AddressToDelete');
      }
    });

    it('should delete Addresses with Bulk Actions and check Result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await this.pageObjects.brandsPage.deleteWithBulkActions('manufacturer_address');
      await expect(deleteTextResult).to.be.equal(this.pageObjects.brandsPage.successfulDeleteMessage);
    });

    it('should reset Addresses filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfBrandAddressesAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines(
        'manufacturer_address',
      );

      await expect(numberOfBrandAddressesAfterReset).to.be.equal(numberOfBrandAddresses);
    });
  });
});
