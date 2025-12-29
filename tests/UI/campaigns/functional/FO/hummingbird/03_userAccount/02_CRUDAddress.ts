import testContext from '@utils/testContext';
import {expect} from 'chai';

import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {createAccountTest} from '@commonTests/FO/classic/account';
import {enableTheme, disableTheme} from '@commonTests/BO/design/hummingbird';

import {
  type BrowserContext,
  dataProducts,
  FakerAddress,
  FakerCustomer,
  foHummingbirdCartPage,
  foHummingbirdCheckoutPage,
  foHummingbirdHomePage,
  foHummingbirdLoginPage,
  foHummingbirdMyAccountPage,
  foHummingbirdMyAddressesPage,
  foHummingbirdMyAddressesCreatePage,
  foHummingbirdProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_hummingbird_userAccount_CRUDAddress';

/*
Pre-condition:
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

  const newCustomerData: FakerCustomer = new FakerCustomer();
  const createAddressData: FakerAddress = new FakerAddress({country: 'France'});
  const editAddressData: FakerAddress = new FakerAddress({country: 'France'});
  const secondAddressData: FakerAddress = new FakerAddress({country: 'France'});

  // Pre-condition
  createAccountTest(newCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition : Install Hummingbird
  enableTheme('hummingbird', `${baseContext}_preTest_2`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Go to \'Add first address\' page and create address', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoHomePage', baseContext);

      await foHummingbirdHomePage.goToFo(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

      await foHummingbirdHomePage.goToLoginPage(page);

      const pageHeaderTitle = await foHummingbirdLoginPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdLoginPage.pageTitle);
    });

    it('Should sign in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

      await foHummingbirdLoginPage.customerLogin(page, newCustomerData);

      const isCustomerConnected = await foHummingbirdMyAccountPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPage', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageHeaderTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to \'Add first address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddFirstAddressPage', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.addressPageTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, createAddressData);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Update the created address on FO', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      const addressPosition = await foHummingbirdMyAddressesPage.getAddressPosition(page, createAddressData.alias);
      await foHummingbirdMyAddressesPage.goToEditAddressPage(page, addressPosition);

      const pageHeaderTitle = await foHummingbirdMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesCreatePage.updateFormTitle);
    });

    it('should update the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, editAddressData);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.updateAddressSuccessfulMessage);
    });

    it('should go back to \'Your account page\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackYourAccountPage', baseContext);

      await foHummingbirdMyAddressesCreatePage.clickOnBreadCrumbLink(page, 'my-account');

      const pageHeaderTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should check that \'Add first address\' is changed to \'Addresses\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddFirstAddress', baseContext);

      const isAddFirstAddressLinkVisible = await foHummingbirdMyAccountPage.isAddFirstAddressLinkVisible(page);
      expect(isAddFirstAddressLinkVisible, 'Add first address link is still visible!').to.eq(false);
    });
  });

  describe('Create a second address', async () => {
    it('should go to \'Addresses\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPage', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.pageTitle);
    });

    it('should go to \'Create new address\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAddressPage', baseContext);

      await foHummingbirdMyAddressesPage.openNewAddressForm(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesCreatePage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesCreatePage.creationFormTitle);
    });

    it('should create new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress2', baseContext);

      const textResult = await foHummingbirdMyAddressesCreatePage.setAddress(page, secondAddressData);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.addAddressSuccessfulMessage);
    });
  });

  describe('Add a product to cart and check the created addresses', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePage', baseContext);

      await foHummingbirdHomePage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foHummingbirdHomePage.goToProductPage(page, 1);

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(dataProducts.demo_1.name);
    });

    it('should add product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foHummingbirdProductPage.addProductToTheCart(page);

      const notificationsNumber = await foHummingbirdCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should check that the two created addresses are displayed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedAddresses1', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      const addressesNumber = await foHummingbirdCheckoutPage.getNumberOfAddresses(page);
      expect(addressesNumber, 'The addresses number is not equal to 2!').to.equal(2);
    });
  });

  describe('Delete the address on FO', async () => {
    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePageToDeleteAddress', baseContext);

      await foHummingbirdHomePage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPageToDeleteAddress', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageHeaderTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDeleteAddress', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.pageTitle);
    });

    it('should try to delete the first address and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress', baseContext);

      firstAddressPosition = await foHummingbirdMyAddressesPage.getAddressPosition(page, editAddressData.alias);
      secondAddressPosition = await foHummingbirdMyAddressesPage.getAddressPosition(page, secondAddressData.alias);

      const textResult = await foHummingbirdMyAddressesPage.deleteAddress(page, firstAddressPosition);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.deleteAddressErrorMessage);
    });

    it('should go to cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToShoppingCartPage', baseContext);

      await foHummingbirdMyAddressesPage.goToCartPage(page);

      const pageTitle = await foHummingbirdCartPage.getPageTitle(page);
      expect(pageTitle).to.equal(foHummingbirdCartPage.pageTitle);
    });

    it('should select the second address and continue', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSecondAddress', baseContext);

      // Proceed to checkout the shopping cart
      await foHummingbirdCartPage.clickOnProceedToCheckout(page);

      await foHummingbirdCheckoutPage.chooseDeliveryAddress(page, secondAddressPosition);

      // Address step - Go to delivery step
      const isStepAddressComplete = await foHummingbirdCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToHomePageToDeleteAddress2', baseContext);

      await foHummingbirdHomePage.goToHomePage(page);

      const isHomePage = await foHummingbirdHomePage.isHomePage(page);
      expect(isHomePage, 'Home page is not displayed').to.eq(true);
    });

    it('should go to \'My Account\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMyAccountPageToDeleteAddress2', baseContext);

      await foHummingbirdHomePage.goToMyAccountPage(page);

      const pageHeaderTitle = await foHummingbirdMyAccountPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAccountPage.pageTitle);
    });

    it('should go to addresses page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddressesPageToDeleteAddress2', baseContext);

      await foHummingbirdMyAccountPage.goToAddressesPage(page);

      const pageHeaderTitle = await foHummingbirdMyAddressesPage.getPageTitle(page);
      expect(pageHeaderTitle).to.equal(foHummingbirdMyAddressesPage.pageTitle);
    });

    it('should delete the first address and check the success message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddress2', baseContext);

      const addressPosition = await foHummingbirdMyAddressesPage.getAddressPosition(page, editAddressData.alias);

      const textResult = await foHummingbirdMyAddressesPage.deleteAddress(page, addressPosition);
      expect(textResult).to.equal(foHummingbirdMyAddressesPage.deleteAddressSuccessfulMessage);
    });
  });

  // Post-condition : Uninstall Hummingbird
  disableTheme('hummingbird', `${baseContext}_postTest_1`);

  // Post-condition: Delete created customer
  deleteCustomerTest(newCustomerData, `${baseContext}_postTest_2`);
});
