require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import common tests
const {createAccountTest} = require('@commonTests/FO/createAccount');
const {deleteCustomerTest} = require('@commonTests/BO/customers/createDeleteCustomer');

// import FO pages
const homePage = require('@pages/FO/home');
const loginPage = require('@pages/FO/login');
const myAccountPage = require('@pages/FO/myAccount');
const addressesPage = require('@pages/FO/myAccount/addresses');
const addAddressPage = require('@pages/FO/myAccount/addAddress');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');

// Import Faker data
const CustomerFaker = require('@data/faker/customer');
const FakerAddress = require('@data/faker/address');

// Import demo data
const {Products} = require('@data/demo/products');

const newCustomerData = new CustomerFaker();
const createAddressData = new FakerAddress({country: 'France'});
const secondAddressData = new FakerAddress({country: 'France'});
const editAddressData = new FakerAddress({country: 'France'});
const checkoutPage = require('@pages/FO/checkout');

const baseContext = 'functional_FO_userAccount_CRUDAddress';

let browserContext;
let page;

/*
Create address in FO
Update the created address in FO
Delete the address in FO
Check that the address is deleted
 */
describe('FO - Account : CRUD address', async () => {
  // Pre-condition
  createAccountTest(newCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Go to \'Add first address\' page and create address', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoHomePage', baseContext);

      await homePage.goToFo(page);
      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await homePage.goToLoginPage(page);

      const pageHeaderTitle = await loginPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(loginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await loginPage.customerLogin(page, newCustomerData);

      const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await homePage.goToMyAccountPage(page);

      const pageHeaderTitle = await myAccountPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should go to \'Add first address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(addressesPage.pageTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.setAddress(page, createAddressData);
      await expect(textResult).to.equal(addressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Update the created address on FO', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      const addressPosition = await addressesPage.getAddressPosition(page, createAddressData.alias);
      await addressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await addAddressPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(addAddressPage.updateFormTitle);
    });

    it('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await addAddressPage.setAddress(page, editAddressData);
      await expect(textResult).to.equal(addressesPage.updateAddressSuccessfulMessage);
    });

    it('should go back to \'Your account page\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackYourAccountPage', baseContext);

      await addAddressPage.clickOnBreadCrumbLink(page, 'my-account');

      const pageHeaderTitle = await myAccountPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(myAccountPage.pageTitle);
    });

    it('should check that \'Add first address\' is changed to \'Addresses\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddFirstAddress', baseContext);

      const isAddFirstAddressLinkVisible = await myAccountPage.isAddFirstAddressLinkVisible(page);
      await expect(isAddFirstAddressLinkVisible, 'Add first address link is still visible!').to.be.false;
    });
  });

  describe('Create a second address', async () => {
    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await myAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await addressesPage.getPageTitle(page);
      await expect(pageHeaderTitle).to.equal(addressesPage.pageTitle);
    });

    it('should go to \'Create new address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

      await addressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await addAddressPage.getHeaderTitle(page);
      await expect(pageHeaderTitle).to.equal(addAddressPage.creationFormTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await addAddressPage.setAddress(page, secondAddressData);
      await expect(textResult).to.equal(addressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Add a product to cart and check the created addresses', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await homePage.goToHomePage(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Home page is not displayed').to.be.true;
    });

    it('should go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should add product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      await expect(notificationsNumber).to.be.equal(1);
    });

    it('should check that the two created addresses are displayed', async function(){
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedAddresses', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const number = await checkoutPage.getNumberOfAddresses(page);
      console.log(number);
    });
  });

  describe('Delete the address on FO', async () => {
    it('should delete the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      const addressPosition = await addressesPage.getAddressPosition(page, editAddressData.alias);
      const textResult = await addressesPage.deleteAddress(page, addressPosition);
      await expect(textResult).to.equal(addressesPage.deleteAddressSuccessfulMessage);
    });
  });

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomerData, baseContext);
});
