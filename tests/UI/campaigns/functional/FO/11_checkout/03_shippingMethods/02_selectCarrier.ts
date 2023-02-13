// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/account';

// Import FO pages
import cartPage from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import homePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import productPage from '@pages/FO/product';

// Import data
import Carriers from '@data/demo/carriers';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import foHomePage from "@pages/FO/home";
import foProductPage from "@pages/FO/product";
import Products from "@data/demo/products";
import foCartPage from "@pages/FO/cart";

const baseContext: string = 'functional_FO_checkout_shippingMethods_selectCarrier';

/*
Scenario:
- Go to FO and login by default customer
- Add a product to cart and checkout
- In shipping methods, choose My carrier
 */

describe('FO - Checkout - Shipping methods : Select carrier', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const customerData: CustomerData = new CustomerData();
  const addressData: AddressData = new AddressData({
    email: customerData.email,
    country: 'France',
  });
  const addressDataInUnitedStates: AddressData = new AddressData({
    email: customerData.email,
    country: 'United States',
    state: 'Alabama',
  });

  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Select carrier', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(Products.demo_1.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foProductPage.addProductToTheCart(page);

      const pageTitle = await foCartPage.getPageTitle(page);
      await expect(pageTitle).to.equal(foCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should sign in by created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isCustomerConnected = await checkoutPage.customerLogin(page, customerData);
      await expect(isCustomerConnected, 'Customer is not connected!').to.be.true;
    });

    it('should create address in Europe then continue to shipping methods', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should check the carriers list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersList', baseContext);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      await expect(carriers).to.deep.equal([Carriers.default.name, Carriers.myCarrier.name]);
    });

    it('should check the first carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCarrierData', baseContext);

      const carrierData = await checkoutPage.getCarrierData(page, 1);
      await Promise.all([
        expect(carrierData.name).to.equal(Carriers.default.name),
        expect(carrierData.delay).to.equal(Carriers.default.delay),
        expect(carrierData.price).to.equal(Carriers.default.price),
      ]);
    });

    it('should check the second carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondCarrierData', baseContext);

      const carrierData = await checkoutPage.getCarrierData(page, 2);
      await Promise.all([
        expect(carrierData.name).to.equal(Carriers.myCarrier.name),
        expect(carrierData.delay).to.equal(Carriers.myCarrier.delay),
        expect(carrierData.price).to.equal(Carriers.myCarrier.price),
      ]);
    });

    it('should click on edit addresses step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      await expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(1);
    });

    it('should edit the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedAddress', baseContext);

      await checkoutPage.clickOnEditAddress(page);

      await checkoutPage.setAddress(page, addressDataInUnitedStates);

      const isStepCompleted = await checkoutPage.clickOnContinueButtonFromAddressStep(page);
      await expect(isStepCompleted).to.be.true;
    });

    it('should check the carriers list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersList', baseContext);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      await expect(carriers).to.deep.equal([Carriers.myCarrier.name]);
    });

    it('should check the carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCarrierData', baseContext);

      const carrierData = await checkoutPage.getCarrierData(page, 1);
      await Promise.all([
        expect(carrierData.name).to.equal(Carriers.myCarrier.name),
        expect(carrierData.delay).to.equal(Carriers.myCarrier.delay),
        expect(carrierData.price).to.equal(Carriers.myCarrier.price),
      ]);
    });
  });
});
