require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const addressesPage = require('@pages/BO/customers/addresses');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foMyAccountPage = require('@pages/FO/myAccount');
const foAddressesPage = require('@pages/FO/myAccount/addresses');
const foAddAddressesPage = require('@pages/FO/myAccount/addAddress');

const baseContext = 'functional_BO_customers_addresses_setRequiredFields';

// Import data
const {DefaultCustomer} = require('@data/demo/customer');
const FakerAddress = require('@data/faker/address');

let browserContext;
let page;
const addressDataWithVatNumber = new FakerAddress({country: 'France', vatNumber: '0102030405'});
const addressDataWithoutVatNumber = new FakerAddress({country: 'France'});

/*
Check 'Vat number' to be a required fields
Go to FO, new address page and verify that 'Vat number' is required
Uncheck 'Vat number'
Go to FO, new address page and verify that 'Vat number' is not required
 */
describe('BO - Customers - Addresses : Set required fields for addresses', async () => {
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

  [
    {args: {action: 'select', exist: true, addressData: addressDataWithVatNumber}},
    {args: {action: 'unselect', exist: false, addressData: addressDataWithoutVatNumber}},
  ].forEach((test, index) => {
    it(`should ${test.args.action} 'Vat number' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}VatNumber`, baseContext);

      const textResult = await addressesPage.setRequiredFields(page, 6, test.args.exist);
      await expect(textResult).to.equal(addressesPage.successfulUpdateMessage);
    });

    it('should view my shop and login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // View shop
      page = await addressesPage.viewMyShop(page);

      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should login in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `loginFO${index}`, baseContext);
      // Go to create account page
      await foHomePage.goToLoginPage(page);
      await foLoginPage.customerLogin(page, DefaultCustomer);

      const connected = await foHomePage.isCustomerConnected(page);
      await expect(connected, 'Customer is not connected in FO').to.be.true;
    });


    it('should go to \'Customers > Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFOAddressesPage${index}`, baseContext);

      await foHomePage.goToMyAccountPage(page);
      await foMyAccountPage.goToAddressesPage(page);
      const pageHeaderTitle = await foAddressesPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddressesPage.pageTitle);
    });

    it('should go to create address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToNewAddressPage${index}`, baseContext);

      await foAddressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await foAddAddressesPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddAddressesPage.creationFormTitle);
    });

    it('should check if \'Vat number\' is required', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkOptionalLabel${index}`, baseContext);

      const result = await foAddAddressesPage.isVatNumberRequired(page);
      await expect(result).to.equal(test.args.exist);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `signOutFO${index}`, baseContext);

      await foAddAddressesPage.logout(page);

      const isCustomerConnected = await foAddAddressesPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foAddAddressesPage.closePage(browserContext, page, 0);

      const pageTitle = await addressesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addressesPage.pageTitle);
    });
  });
});
