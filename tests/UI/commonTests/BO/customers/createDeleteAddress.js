require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const addressesPage = require('@pages/BO/customers/addresses');
const addAddressPage = require('@pages/BO/customers/addresses/add');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;
let numberOfAddresses = 0;

/**
 * Function to create address
 * @param addressData {AddressData} Data to set to create customer
 * @param baseContext {string} String to identify the test
 */
function createAddressTest(addressData, baseContext = 'commonTests-createAddressTest') {
  describe('PRE-TEST: Create address', async () => {
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

    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.addressesLink,
      );

      await addressesPage.closeSfToolBar(page);

      const pageTitle = await addressesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addressesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfAddresses = await addressesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfAddresses).to.be.above(0);
    });

    it('should go to add new address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAddressPage', baseContext);

      await addressesPage.goToAddNewAddressPage(page);
      const pageTitle = await addAddressPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addAddressPage.pageTitleCreate);
    });

    it('should create address and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.createEditAddress(page, addressData);
      await expect(textResult).to.equal(addressesPage.successfulCreationMessage);

      const numberOfAddressesAfterCreation = await addressesPage.getNumberOfElementInGrid(page);
      await expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1);
    });
  });
}

module.exports = {createAddressTest};
