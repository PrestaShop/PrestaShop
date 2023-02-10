// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {createAccountPage as foCreateAccountPage} from '@pages/FO/myAccount/add';
import {myAccountPage} from '@pages/FO/myAccount';
import foAddressesPage from '@pages/FO/myAccount/addresses';
import foAddAddressesPage from '@pages/FO/myAccount/addAddress';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import type AddressData from '@data/faker/address';
import type CustomerData from '@data/faker/customer';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to create account in FO
 * @param customerData {CustomerData} Data to set when creating the account
 * @param baseContext {string} String to identify the test
 */
function createAccountTest(customerData: CustomerData, baseContext: string = 'commonTests-createAccountTest'): void {
  describe('PRE-TEST: Create account on FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await homePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      await expect(isCustomerConnected).to.be.true;
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foCreateAccountPage.goToHomePage(page);
      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });
}

function createAddressTest(
  customerLoginData: CustomerData,
  addressData: AddressData,
  baseContext: string = 'commonTests-createAddressTest',
): void {
  describe('PRE-TEST: Create address on FO', async () => {
  // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should open FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFO', baseContext);

      // Go to FO and change language
      await homePage.goToFo(page);

      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await homePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, customerLoginData);
      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to \'My account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageTitle = await myAccountPage.getPageTitle(page);
      await expect(pageTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to \'Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFOAddressesPage', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foAddressesPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.include(foAddressesPage.addressPageTitle);
    });

    it('should go to create address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

      await foAddressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await foAddAddressesPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(foAddAddressesPage.creationFormTitle);
    });

    it('should create address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await foAddAddressesPage.setAddress(page, addressData);
      await expect(textResult).to.equal(foAddressesPage.addAddressSuccessfulMessage);
    });

    it('should sign out from FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signOutFO', baseContext);

      await foAddAddressesPage.goToHomePage(page);
      await homePage.logout(page);

      const isCustomerConnected = await homePage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is connected').to.be.false;
    });
  });
}

export {createAccountTest, createAddressTest};
