// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/account';
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import FO pages
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';
import homePage from '@pages/FO/hummingbird/home';
import loginPage from '@pages/FO/hummingbird/login';
import myAccountPage from '@pages/FO/hummingbird/myAccount';
import addAddressPage from '@pages/FO/hummingbird/myAccount/addAddress';
import addressesPage from '@pages/FO/hummingbird/myAccount/addresses';
import productPage from '@pages/FO/hummingbird/product';

// Import data
import Products from '@data/demo/products';
import CustomerData from '@data/faker/customer';
import AddressData from '@data/faker/address';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_userAccount_CRUDAddress';

/*
Pre-condition:
- Clear cache
- Create account test
- Install hummingbird theme
Scenario:
- Create first address
- Edit address
- Create second address
- Add a product to cart
- Try to delete first address and check error message
- go to checkout page and choose the second address
- Delete the first address and check success message
Post-condition:
- Delete customer account
- Uninstall hummingbird theme
 */
describe('FO - Account : CRUD address', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let firstAddressPosition: number = 0;
  let secondAddressPosition: number = 0;

  const newCustomerData: CustomerData = new CustomerData();
  const createAddressData: AddressData = new AddressData({country: 'France'});
  const editAddressData: AddressData = new AddressData({country: 'France'});
  const secondAddressData: AddressData = new AddressData({country: 'France'});

  // Pre-condition
  createAccountTest(newCustomerData, `${baseContext}_preTest_2`);

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('../../admin-dev/hummingbird.zip');
  });

  describe('Go to \'Add first address\' page and create address', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoHomePage', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageHeaderTitle = await loginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(loginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await loginPage.customerLogin(page, newCustomerData);

      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageHeaderTitle = await myAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to \'Add first address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddFirstAddressPage', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(addressesPage.addressPageTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.setAddress(page, createAddressData);
      expect(textResult).to.equal(addressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Update the created address on FO', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      const addressPosition = await addressesPage.getAddressPosition(page, createAddressData.alias);
      await addressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await addAddressPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(addAddressPage.updateFormTitle);
    });

    it('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await addAddressPage.setAddress(page, editAddressData);
      expect(textResult).to.equal(addressesPage.updateAddressSuccessfulMessage);
    });

    it('should go back to \'Your account page\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackYourAccountPage', baseContext);

      await addAddressPage.clickOnBreadCrumbLink(page, 'my-account');

      const pageHeaderTitle = await myAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should check that \'Add first address\' is changed to \'Addresses\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddFirstAddress', baseContext);

      const isAddFirstAddressLinkVisible = await myAccountPage.isAddFirstAddressLinkVisible(page);
      expect(isAddFirstAddressLinkVisible, 'Add first address link is still visible!').to.eq(false);
    });
  });

  describe('Create a second address', async () => {
    it('should go to \'Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(addressesPage.pageTitle);
    });

    it('should go to \'Create new address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

      await addressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await addAddressPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(addAddressPage.creationFormTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress2', baseContext);

      const textResult = await addAddressPage.setAddress(page, secondAddressData);
      expect(textResult).to.equal(addressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Add a product to cart and check the created addresses', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should add product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should check that the two created addresses are displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedAddresses1', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 2!').to.equal(2);
    });
  });

  describe('Delete the address on FO', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePageToDeleteAddress', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPageToDeleteAddress', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageHeaderTitle = await myAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDeleteAddress', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(addressesPage.pageTitle);
    });

    it('should try to delete the first address and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      firstAddressPosition = await addressesPage.getAddressPosition(page, editAddressData.alias);
      secondAddressPosition = await addressesPage.getAddressPosition(page, secondAddressData.alias);

      const textResult = await addressesPage.deleteAddress(page, firstAddressPosition);
      expect(textResult).to.equal(addressesPage.deleteAddressErrorMessage);
    });

    it('should go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartPage', baseContext);

      await addressesPage.goToCartPage(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should select the second address and continue', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSecondAddress', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      await checkoutPage.chooseDeliveryAddress(page, secondAddressPosition);

      // Address step - Go to delivery step
      const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePageToDeleteAddress2', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPageToDeleteAddress2', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageHeaderTitle = await myAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDeleteAddress2', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(addressesPage.pageTitle);
    });

    it('should delete the first address and check the success message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress2', baseContext);

      const addressPosition = await addressesPage.getAddressPosition(page, editAddressData.alias);

      const textResult = await addressesPage.deleteAddress(page, addressPosition);
      expect(textResult).to.equal(addressesPage.deleteAddressSuccessfulMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomerData, `${baseContext}_postTest_2`);
});
