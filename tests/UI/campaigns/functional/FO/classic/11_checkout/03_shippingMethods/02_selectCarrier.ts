// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/account';

// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {homePage} from '@pages/FO/classic/home';
import {foProductPage} from '@pages/FO/classic/product';

// Import data
import Carriers from '@data/demo/carriers';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_checkout_shippingMethods_selectCarrier';

/*
Pre-condition:
- Create new customer account in FO
Scenario:
- Add a product to cart and checkout
- Create an address in Europe and check the carriers
- Edit the address to US and check the carriers
Post-condition:
- Delete customer account
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

  describe('Add a product to the cart and checkout', async () => {
    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await homePage.goToFo(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await homePage.goToProductPage(page, 1);

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
  });

  describe('Select carrier in Europe address', async () => {
    it('should create address in Europe then continue to shipping methods', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should check the carriers list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersList', baseContext);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([Carriers.default.name, Carriers.myCarrier.name]);
    });

    it('should check the first carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCarrierData', baseContext);

      const carrierData = await checkoutPage.getCarrierData(page, 1);
      await Promise.all([
        expect(carrierData.name).to.equal(Carriers.default.name),
        expect(carrierData.delay).to.equal(Carriers.default.delay),
        expect(carrierData.priceText).to.equal('Free'),
      ]);
    });

    it('should check the second carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondCarrierData', baseContext);

      const carrierData = await checkoutPage.getCarrierData(page, 2);
      await Promise.all([
        expect(carrierData.name).to.equal(Carriers.myCarrier.name),
        expect(carrierData.delay).to.equal(Carriers.myCarrier.delay),
        expect(carrierData.priceText).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)} tax incl.`),
      ]);
    });

    it('should select the first carrier and check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice1', baseContext);

      await checkoutPage.chooseShippingMethod(page, Carriers.default.id);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.equal('Free');
    });

    it('should select the second carrier and check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice2', baseContext);

      await checkoutPage.chooseShippingMethod(page, Carriers.myCarrier.id);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)}`);
    });
  });

  describe('Select carrier in US address', async () => {
    it('should click on edit addresses step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep', baseContext);

      await checkoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await checkoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(1);
    });

    it('should edit the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedAddress', baseContext);

      await checkoutPage.clickOnEditAddress(page);
      await checkoutPage.setAddress(page, addressDataInUnitedStates);

      const isStepCompleted = await checkoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.eq(true);
    });

    it('should check the carriers list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersList2', baseContext);

      const carriers = await checkoutPage.getAllCarriersNames(page);
      expect(carriers).to.deep.equal([Carriers.myCarrier.name]);
    });

    it('should check the carrier data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCarrierData2', baseContext);

      const carrierData = await checkoutPage.getCarrierData(page, 2);
      await Promise.all([
        expect(carrierData.name).to.equal(Carriers.myCarrier.name),
        expect(carrierData.delay).to.equal(Carriers.myCarrier.delay),
        expect(carrierData.priceText).to.equal(`€${Carriers.myCarrier.price.toFixed(2)}`),
      ]);
    });

    it('should check the shipping price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShippingPrice3', baseContext);

      const shippingCost = await checkoutPage.getShippingCost(page);
      expect(shippingCost).to.equal(`€${Carriers.myCarrier.price.toFixed(2)}`);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
