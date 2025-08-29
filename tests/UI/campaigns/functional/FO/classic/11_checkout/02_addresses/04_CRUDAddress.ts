// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';

import {
  type BrowserContext,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_classic_checkout_addresses_CRUDAddress';

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

  const customerData: FakerCustomer = new FakerCustomer();
  const addressData: FakerAddress = new FakerAddress({
    email: customerData.email,
    country: 'France',
  });
  const editAddressData: FakerAddress = new FakerAddress({
    alias: 'First address',
    email: customerData.email,
    country: 'France',
  });
  const newAddressData: FakerAddress = new FakerAddress({
    alias: 'Second address',
    email: customerData.email,
    country: 'France',
  });
  const newInvoiceAddressData: FakerAddress = new FakerAddress({
    alias: 'Third address',
    email: customerData.email,
    country: 'France',
  });

  // Pre-condition: Create new account on FO
  createAccountTest(customerData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create new address in checkout page', async () => {
    it('should open the FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to first product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should add product to cart and go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicProductPage.addProductToTheCart(page);

      const pageTitle = await foClassicCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
    });

    it('should validate shopping cart and go to checkout page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCheckoutPage', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await foClassicCheckoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should sign in by created customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foClassicCheckoutPage.clickOnSignIn(page);

      const isCustomerConnected = await foClassicCheckoutPage.customerLogin(page, customerData);
      expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
    });

    it('should create address then continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, addressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });
  });

  describe('Edit created address in checkout page', async () => {
    it('should click on edit addresses step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(1);
    });

    it('should edit the created address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editCreatedAddress', baseContext);

      await foClassicCheckoutPage.clickOnEditAddress(page);

      const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, editAddressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });
  });

  describe('Create second new address in checkout page', async () => {
    it('should click on edit addresses step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep2', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(1);
    });

    it('should add new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewAddress', baseContext);

      await foClassicCheckoutPage.clickOnAddNewAddressButton(page);

      const isStepAddressComplete = await foClassicCheckoutPage.setAddress(page, newAddressData);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });
  });

  describe('Create invoice address in checkout page', async () => {
    it('should click on edit addresses step and check the number of addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickEditAddressStep3', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 1!').to.equal(2);
    });

    it('should click on \'Billing address differs from shipping address\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBillingAddressDifferent', baseContext);

      await foClassicCheckoutPage.clickOnDifferentInvoiceAddressLink(page);

      const isInvoiceAddressBlockVisible = await foClassicCheckoutPage.isInvoiceAddressBlockVisible(page);
      expect(isInvoiceAddressBlockVisible).to.eq(true);
    });

    it('should create new invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createInvoiceAddress', baseContext);

      await foClassicCheckoutPage.clickOnAddNewInvoiceAddressButton(page);

      const isStepAddressComplete = await foClassicCheckoutPage.setInvoiceAddress(page, newInvoiceAddressData);
      expect(isStepAddressComplete).to.eq(true);
    });

    it('should check the number of delivered addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses1', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 3!').to.equal(3);
    });

    it('should check the number of invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses2', baseContext);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfInvoiceAddresses(page);
      expect(addressesNumber).to.equal(3);
    });
  });

  describe('Delete address in checkout page', async () => {
    it('should delete the first address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstAddress', baseContext);

      const textMessage = await foClassicCheckoutPage.deleteAddress(page, 3);
      expect(textMessage).to.equal(foClassicCheckoutPage.deleteAddressSuccessMessage);
    });

    it('should check the number of delivered addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses3', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 2!').to.equal(2);
    });

    it('should check the number of invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses4', baseContext);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfInvoiceAddresses(page);
      expect(addressesNumber).to.equal(2);
    });
  });

  describe('Choose the invoice address different than shipping address', async () => {
    it('should choose the invoice address different than shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'invoiceAddressDiffShippingAddress', baseContext);

      await foClassicCheckoutPage.selectDeliveryAddress(page, 1);
      await foClassicCheckoutPage.selectInvoiceAddress(page, 2);

      const isStepCompleted = await foClassicCheckoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.eq(true);
    });

    it('should check the number of delivered addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses5', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 2!').to.equal(2);
    });

    it('should check the number of invoice addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfAddresses6', baseContext);

      const addressesNumber = await foClassicCheckoutPage.getNumberOfInvoiceAddresses(page);
      expect(addressesNumber).to.equal(2);
    });
  });

  describe('Choose same address for invoice address and shipping address', async () => {
    it('should choose the same address for invoice address and shipping address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sameInvoiceDeliveryAddress', baseContext);

      await foClassicCheckoutPage.selectDeliveryAddress(page, 1);
      await foClassicCheckoutPage.selectInvoiceAddress(page, 1);

      const isStepCompleted = await foClassicCheckoutPage.clickOnContinueButtonFromAddressStep(page);
      expect(isStepCompleted).to.eq(true);
    });

    it('should click on edit address step and check that there is no invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoInvoiceAddress', baseContext);

      await foClassicCheckoutPage.clickOnEditAddressesStep(page);

      const isInvoiceAddressBlockVisible = await foClassicCheckoutPage.isInvoiceAddressBlockVisible(page);
      expect(isInvoiceAddressBlockVisible).to.eq(false);
    });
  });

  describe('Delete all addresses', async () => {
    it('should delete the 2 addresses', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTwoAddresses', baseContext);

      let textMessage = await foClassicCheckoutPage.deleteAddress(page);
      expect(textMessage).to.equal(foClassicCheckoutPage.deleteAddressSuccessMessage);

      textMessage = await foClassicCheckoutPage.deleteAddress(page);
      expect(textMessage).to.equal(foClassicCheckoutPage.deleteAddressSuccessMessage);
    });

    it('should check that the form for create address is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreateAddressForm', baseContext);

      const isFormVisible = await foClassicCheckoutPage.isAddressFormVisible(page);
      expect(isFormVisible).to.eq(true);
    });
  });

  // Post-condition: Delete the created customer account
  deleteCustomerTest(customerData, `${baseContext}_postTest`);
});
