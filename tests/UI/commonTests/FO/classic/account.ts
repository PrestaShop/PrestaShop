// Import utils
import testContext from '@utils/testContext';

// Import FO pages
import {createAccountPage as foCreateAccountPage} from '@pages/FO/classic/myAccount/add';
import {addressesPage} from '@pages/FO/classic/myAccount/addresses';
import {addAddressPage} from '@pages/FO/classic/myAccount/addAddress';

import {
  type BrowserContext,
  FakerAddress,
  FakerCustomer,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to create account in FO
 * @param customerData {FakerCustomer} Data to set when creating the account
 * @param baseContext {string} String to identify the test
 */
function createAccountTest(customerData: FakerCustomer, baseContext: string = 'commonTests-createAccountTest'): void {
  describe('PRE-TEST: Create account on FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await foClassicHomePage.goToFo(page);

      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foCreateAccountPage.goToHomePage(page);
      await foClassicHomePage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });
}

function createAddressTest(
  customerLoginData: FakerCustomer,
  addressData: FakerAddress,
  baseContext: string = 'commonTests-createAddressTest',
): void {
  describe('PRE-TEST: Create address on FO', async () => {
  // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await foClassicHomePage.goToFo(page);

      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, customerLoginData);
      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to \'My account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
    });

    it('should go to \'Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToaddressesPage', baseContext);

      await foClassicMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.include(addressesPage.addressPageTitle);
    });

    it('should go to create address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

      await addressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await addAddressPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(addAddressPage.creationFormTitle);
    });

    it('should create address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.setAddress(page, addressData);
      expect(textResult).to.equal(addressesPage.addAddressSuccessfulMessage);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await addAddressPage.goToHomePage(page);
      await foClassicHomePage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });
  });
}

export {createAccountTest, createAddressTest};
