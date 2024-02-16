// Import utils
import testContext from '@utils/testContext';
import helper from '@utils/helpers';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import createAccountTest from '@commonTests/FO/hummingbird/account';
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import pages
import foHomePage from '@pages/FO/hummingbird/home';
import foProductPage from '@pages/FO/hummingbird/product';
import cartPage from '@pages/FO/hummingbird/cart';
import checkoutPage from '@pages/FO/hummingbird/checkout';

// Import data
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_checkout_addresses_CRUDAddress';

/*
Pre-condition:
- Create account in FO
Scenario:
- Create new address in checkout page
- Edit created address in checkout page
- Create second new address in checkout page
- Create invoice address in checkout page
- Choose same addresses for invoice address and shipping address
- Delete all addresses
Post_condition:
- Delete customer account
 */
describe('FO - Checkout - Addresses : CRUD address', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const customerData: CustomerData = new CustomerData();
  const addressData: AddressData = new AddressData({
    email: customerData.email,
    country: 'France',
  });
  const editAddressData: AddressData = new AddressData({
    alias: 'First address',
    email: customerData.email,
    country: 'France',
  });
  const newAddressData: AddressData = new AddressData({
    alias: 'Second address',
    email: customerData.email,
    country: 'France',
  });
  const newInvoiceAddressData: AddressData = new AddressData({
    alias: 'Third address',
    email: customerData.email,
    country: 'France',
  });

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_0`);

  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create new address in checkout page', async () => {
    it('should open the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foHomePage.goToFo(page);
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page);

      const pageTitle = await cartPage.getPageTitle(page);
      expect(pageTitle).to.equal(cartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should sign in by created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, customerData);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it('should create address then continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });
  });

  describe('Edit created address in checkout page', async () => {
    it('should click on edit addresses step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(1);
    });

    it('should edit the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedAddress', baseContext);

      await checkoutPage.clickOnEditAddress(page);

      const isStepAddressComplete = await checkoutPage.setAddress(page, editAddressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });
  });

  describe('Create second new address in checkout page', async () => {
    it('should click on edit addresses step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep2', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(1);
    });

    it('should add new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewAddress', baseContext);

      await checkoutPage.clickOnAddNewAddressButton(page);

      const isStepAddressComplete = await checkoutPage.setAddress(page, newAddressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });
  });

  describe('Create invoice address in checkout page', async () => {
    it('should click on edit addresses step and check the number of addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep3', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(2);
    });

    it('should click on \'Billing address differs from shipping address\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBillingAddressDifferent', baseContext);

      await checkoutPage.clickOnDifferentInvoiceAddressLink(page);

      const isInvoiceAddressBlockVisible = await checkoutPage.isInvoiceAddressBlockVisible(page);
      expect(isInvoiceAddressBlockVisible).to.eq(true);
    });

    it('should create new invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createInvoiceAddress', baseContext);

      await checkoutPage.clickOnAddNewInvoiceAddressButton(page);

      const isStepAddressComplete = await checkoutPage.setInvoiceAddress(page, newInvoiceAddressData);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should check the number of delivered addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses1', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 3!').to.equal(3);
    });

    it('should check the number of invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses2', baseContext);

      const addressesNumber = await checkoutPage.getNumberOfInvoiceAddresses(page);
      expect(addressesNumber).to.equal(3);
    });
  });

  describe('Delete address in checkout page', async () => {
    it('should delete the first address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstAddress', baseContext);

      const textMessage = await checkoutPage.deleteAddress(page, 3);
      expect(textMessage).to.equal(checkoutPage.deleteAddressSuccessMessage);
    });

    it('should check the number of delivered addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses3', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 2!').to.equal(2);
    });

    it('should check the number of invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses4', baseContext);

      const addressesNumber = await checkoutPage.getNumberOfInvoiceAddresses(page);
      expect(addressesNumber).to.equal(2);
    });
  });

  describe('Choose the invoice address different than shipping address', async () => {
    it('should choose the invoice address different than shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'invoiceAddressDiffShippingAddress', baseContext);

      await checkoutPage.selectDeliveryAddress(page, 1);
      await checkoutPage.selectInvoiceAddress(page, 2);

      const isStepCompleted = await checkoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.eq(true);
    });

    it('should check the number of delivered addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses5', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 2!').to.equal(2);
    });

    it('should check the number of invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses6', baseContext);

      const addressesNumber = await checkoutPage.getNumberOfInvoiceAddresses(page);
      expect(addressesNumber).to.equal(2);
    });
  });

  describe('Choose same address for invoice address and shipping address', async () => {
    it('should choose the same address for invoice address and shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sameInvoiceDeliveryAddress', baseContext);

      await checkoutPage.selectDeliveryAddress(page, 1);
      await checkoutPage.selectInvoiceAddress(page, 1);

      const isStepCompleted = await checkoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.eq(true);
    });

    it('should click on edit address step and check that there is no invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoiceAddress', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const isInvoiceAddressBlockVisible = await checkoutPage.isInvoiceAddressBlockVisible(page);
      expect(isInvoiceAddressBlockVisible).to.eq(false);
    });
  });

  describe('Delete all addresses', async () => {
    it('should delete the 2 addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTwoAddresses', baseContext);

      let textMessage = await checkoutPage.deleteAddress(page);
      expect(textMessage).to.equal(checkoutPage.deleteAddressSuccessMessage);

      textMessage = await checkoutPage.deleteAddress(page);
      expect(textMessage).to.equal(checkoutPage.deleteAddressSuccessMessage);
    });

    it('should check that the form for create address is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreateAddressForm', baseContext);

      const isFormVisible = await checkoutPage.isAddressFormVisible(page);
      expect(isFormVisible).to.eq(true);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(customerData, `${baseContext}_postTest_0`);

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);
});
