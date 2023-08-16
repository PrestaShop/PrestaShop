// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import customersPage from '@pages/BO/customers';
import addCustomerPage from '@pages/BO/customers/add';
import viewCustomerPage from '@pages/BO/customers/view';
import addAddressPage from '@pages/BO/customers/addresses/add';
import dashboardPage from '@pages/BO/dashboard';
import viewCartPage from '@pages/BO/orders/shoppingCarts/view';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';
// Import FO pages
import {cartPage} from '@pages/FO/cart';
import checkoutPage from '@pages/FO/checkout';
import orderConfirmationPage from '@pages/FO/checkout/orderConfirmation';
import {homePage as foHomePage} from '@pages/FO/home';
import productPage from '@pages/FO/product';

// Import data
import Languages from '@data/demo/languages';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';
import CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_viewCustomer';

/*
Create customer
View customer
Create order
View customer after creating the order
Edit customer then check customer information page
Edit order then check customer information page
Edit address then check customer information page
View carts page
Delete customer
 */
describe('BO - Customers - Customers : View information about customer', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCustomers: number = 0;

  const today: string = date.getDateFormat('mm/dd/yyyy');
  // Init data
  const createCustomerData: CustomerData = new CustomerData({defaultCustomerGroup: 'Customer'});
  const editCustomerData: CustomerData = new CustomerData({defaultCustomerGroup: 'Visitor'});
  const address: AddressData = new AddressData({city: 'Paris', country: 'France'});
  const createAddressData: AddressData = new AddressData({country: 'France'});

  // Get customer birthdate format 'mm/dd/yyyy'
  const mmBirth: string = `0${createCustomerData.monthOfBirth}`.slice(-2);
  const ddBirth: string = `0${createCustomerData.dayOfBirth}`.slice(-2);
  const yyyyBirth: string = createCustomerData.yearOfBirth;
  const customerBirthDate: string = `${mmBirth}/${ddBirth}/${yyyyBirth}`;

  const mmEditBirth: string = `0${editCustomerData.monthOfBirth}`.slice(-2);
  const ddEditBirth: string = `0${editCustomerData.dayOfBirth}`.slice(-2);
  const yyyyEditBirth: string = editCustomerData.yearOfBirth;
  const editCustomerBirthDate: string = `${mmEditBirth}/${ddEditBirth}/${yyyyEditBirth}`;

  const createCustomerName: string = `${createCustomerData.firstName[0]}. ${createCustomerData.lastName}`;
  const editCustomerName: string = `${editCustomerData.firstName[0]}. ${editCustomerData.lastName}`;

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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );
    await customersPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create customer
  describe('Create customer in BO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, createCustomerData);
      expect(textResult).to.equal(customersPage.successfulCreationMessage);

      const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
      expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
  });

  // 2 : View customer
  describe('View customer created', async () => {
    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        createCustomerData.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateCustomer', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(createCustomerName));
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformationTitle1', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(createCustomerData.firstName);
      expect(cardHeaderText).to.contains(createCustomerData.lastName);
      expect(cardHeaderText).to.contains(createCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo1', baseContext);

      const cardHeaderText = await viewCustomerPage.getTextFromElement(page, 'Personal information');
      expect(cardHeaderText).to.contains(createCustomerData.socialTitle);
      expect(cardHeaderText).to.contains(`birth date: ${customerBirthDate}`);
      expect(cardHeaderText).to.contains('Never');
      expect(cardHeaderText).to.contains(Languages.english.name);
      expect(cardHeaderText).to.contains('Newsletter');
      expect(cardHeaderText).to.contains('Partner offers');
      expect(cardHeaderText).to.contains('Active');
    });

    [
      {args: {blockName: 'Orders', number: 0}},
      {args: {blockName: 'Carts', number: 0}},
      {args: {blockName: 'Messages', number: 0}},
      {args: {blockName: 'Vouchers', number: 0}},
      {args: {blockName: 'Groups', number: 1}},
    ].forEach((test) => {
      it(`should check ${test.args.blockName} number`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.args.blockName}Number`, baseContext);

        const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, test.args.blockName);
        expect(cardHeaderText).to.contains(test.args.number);
      });
    });
  });

  // 3 : Create order
  describe('Create order in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

      // Click on view my shop
      page = await viewCustomerPage.viewMyShop(page);
      // Change language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);
      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      const notificationsNumber = await cartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(1);
    });

    it('should proceed to checkout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckout', baseContext);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);

      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      expect(isCheckoutPage).to.eq(true);
    });

    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToFO', baseContext);

      await checkoutPage.clickOnSignIn(page);

      const isStepLoginComplete = await checkoutPage.customerLogin(page, createCustomerData);
      expect(isStepLoginComplete, 'Step Personal information is not complete').to.eq(true);
    });

    it('should create address then continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, address);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should add a comment then continue to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await checkoutPage.chooseShippingMethodAndAddComment(
        page,
        1,
        'test message',
      );
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose the payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(createCustomerName));
    });
  });

  // 4 : View customer after creating the order
  describe('View customer after creating the order', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomersPage', baseContext);

      await viewCustomerPage.goToSubMenu(
        page,
        viewCustomerPage.customersParentLink,
        viewCustomerPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${createCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomer', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', createCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateOrder', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(createCustomerName));
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformationTitle2', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(createCustomerData.firstName);
      expect(cardHeaderText).to.contains(createCustomerData.lastName);
      expect(cardHeaderText).to.contains(createCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo2', baseContext);

      const cardHeaderText = await viewCustomerPage.getTextFromElement(page, 'Personal information');
      expect(cardHeaderText).to.contains(createCustomerData.socialTitle);
      expect(cardHeaderText).to.contains(`birth date: ${customerBirthDate}`);
      expect(cardHeaderText).to.contains(today);
      expect(cardHeaderText).to.contains(Languages.english.name);
      expect(cardHeaderText).to.contains('Newsletter');
      expect(cardHeaderText).to.contains('Partner offers');
      expect(cardHeaderText).to.contains('Active');
    });

    [
      {args: {blockName: 'Orders', number: 1}},
      {args: {blockName: 'Carts', number: 1}},
      {args: {blockName: 'Purchased products', number: 1}},
      {args: {blockName: 'Messages', number: 1}},
      {args: {blockName: 'Vouchers', number: 0}},
      {args: {blockName: 'Last emails', number: 2}},
      {args: {blockName: 'Last connections', number: 1}},
      {args: {blockName: 'Groups', number: 1}},
    ].forEach((test) => {
      it(`should check ${test.args.blockName} number`, async function () {
        await testContext.addContextItem(
          this, 'testIdentifier', `check${test.args.blockName}NumberAfterEdit`, baseContext,
        );

        const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, test.args.blockName);
        expect(cardHeaderText).to.contains(test.args.number);
      });
    });

    it('should check orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrders', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Orders');
      expect(carts).to.contains(today);
      expect(carts).to.contains('Bank transfer');
      expect(carts).to.contains(OrderStatuses.awaitingBankWire.name);
      expect(carts).to.contains('€0.00');
    });

    it('should check carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarts', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Carts');
      expect(carts).to.contains(today);
      expect(carts).to.contains(Products.demo_1.finalPrice);
    });

    it('should check purchased products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPurchasedProduct1', baseContext);

      const viewedProduct = await viewCustomerPage.getTextFromElement(page, 'Purchased products');
      expect(viewedProduct).to.contains(Products.demo_1.name);
    });

    it('should check address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddress', baseContext);

      const customerAddress = await viewCustomerPage.getTextFromElement(page, 'Addresses');
      expect(customerAddress).to.contains(address.company);
      expect(customerAddress).to.contains(`${createCustomerData.firstName} ${createCustomerData.lastName}`);
      expect(customerAddress).to.contains(address.address);
      expect(customerAddress).to.contains(address.country);
      expect(customerAddress).to.contains(address.phone);
    });

    it('should check messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessages', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Messages');
      expect(carts).to.contains(today);
      expect(carts).to.contains('Open');
      expect(carts).to.contains('test message');
    });

    it('should check last connections', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLAstConnections', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Last connections');
      expect(carts).to.contains(today);
    });

    it('should check groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroups', baseContext);

      const groups = await viewCustomerPage.getTextFromElement(page, 'Groups');
      expect(groups).to.contains(createCustomerData.defaultCustomerGroup);
    });

    it('should add a private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPrivateNote', baseContext);

      const result = await viewCustomerPage.setPrivateNote(page, 'Test note');
      expect(result).to.contains(viewCustomerPage.successfulUpdateMessage);
    });
  });

  // 5 : Edit customer then check customer information page
  describe('Edit customer then view it and check information', async () => {
    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCustomerPage', baseContext);

      await viewCustomerPage.goToEditCustomerPage(page);

      const pageTitle = await addCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCustomerPage.pageTitleEdit);
    });

    it('should edit customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, editCustomerData);
      expect(textResult).to.equal(viewCustomerPage.successfulUpdateMessage);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerTitle', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);
      expect(cardHeaderText).to.contains(editCustomerData.firstName);
      expect(cardHeaderText).to.contains(editCustomerData.lastName);
      expect(cardHeaderText).to.contains(editCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);

      const cardHeaderText = await viewCustomerPage.getTextFromElement(page, 'Personal information');
      expect(cardHeaderText).to.contains(editCustomerData.socialTitle);
      expect(cardHeaderText).to.contains(`birth date: ${editCustomerBirthDate}`);
      expect(cardHeaderText).to.contains(today);
      expect(cardHeaderText).to.contains(Languages.english.name);
      expect(cardHeaderText).to.contains('Newsletter');
      expect(cardHeaderText).to.contains('Partner offers');
      expect(cardHeaderText).to.contains('Active');
    });

    it('should check groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroupsAfterEdit', baseContext);

      const groups = await viewCustomerPage.getTextFromElement(page, 'Groups');
      expect(groups).to.contains(editCustomerData.defaultCustomerGroup);
    });
  });

  // 6 : Edit order then check customer information page
  describe('Edit order then view customer and check information', async () => {
    it('should go to view order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToorderPageCustomerBlock', baseContext);

      await viewCustomerPage.goToPage(page, 'Orders');

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should modify order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyOrderStatus', baseContext);

      const result = await orderPageCustomerBlock.modifyOrderStatus(page, OrderStatuses.shipped.name);
      expect(result).to.equal(OrderStatuses.shipped.name);
    });

    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageAfterEditOrder', baseContext);

      await orderPageCustomerBlock.goToSubMenu(
        page,
        orderPageCustomerBlock.customersParentLink,
        orderPageCustomerBlock.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomerAfterEditOrder', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterEditOrder', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCustomerPage.pageTitle(editCustomerName));
    });

    it('should check order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckOrderStatusAfterEdit', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Orders');
      expect(carts).to.contains(today);
      expect(carts).to.contains('Bank transfer');
      expect(carts).to.contains(OrderStatuses.shipped.name);
      expect(carts).to.contains(Products.demo_1.finalPrice);
    });

    it('should check purchased products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckPurchasedProductsNumber', baseContext);

      const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Purchased products');
      expect(cardHeaderText).to.contains(1);
    });

    it('should check purchased products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPurchasedProduct', baseContext);

      const purchasedProduct = await viewCustomerPage.getTextFromElement(page, 'Purchased products');
      expect(purchasedProduct).to.contains(today);
      expect(purchasedProduct).to.contains(Products.demo_1.name);
    });
  });

  // 7 : Edit address then check customer information page
  describe('Edit address then view customer and check address', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await viewCustomerPage.goToPage(page, 'Addresses');

      const pageTitle = await addAddressPage.getPageTitle(page);
      expect(pageTitle).to.contains(addAddressPage.pageTitleEdit);
    });

    it('should modify the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await addAddressPage.createEditAddress(page, createAddressData);
      expect(textResult).to.equal(viewCustomerPage.updateSuccessfulMessage);
    });

    it('should check the edited address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckEditedAddress', baseContext);

      const customerAddress = await viewCustomerPage.getTextFromElement(page, 'Addresses');
      expect(customerAddress).to.contains(createAddressData.company);
      expect(customerAddress).to.contains(`${createAddressData.firstName} ${createAddressData.lastName}`);
      expect(customerAddress).to.contains(createAddressData.address);
      expect(customerAddress).to.contains(createAddressData.country);
      expect(customerAddress).to.contains(createAddressData.phone);
    });
  });

  // 8 : View cart page
  describe('View cart page', async () => {
    it('should go to view cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCartPage', baseContext);

      const idCart = await viewCustomerPage.getTextColumnFromTableCarts(page, 'id_cart', 1);

      await viewCustomerPage.goToPage(page, 'Carts');

      const pageTitle = await viewCartPage.getPageTitle(page);
      expect(pageTitle).to.contains(viewCartPage.pageTitle(idCart));
    });
  });

  // 9 : Delete customer from BO
  describe('Delete customer', async () => {
    it('should go to \'Customers > Customers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await viewCartPage.goToSubMenu(
        page,
        viewCartPage.customersParentLink,
        viewCartPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it(`should filter list by email '${editCustomerData.email}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.resetFilter(page);
      await customersPage.filterCustomers(page, 'input', 'email', editCustomerData.email);

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      expect(textResult).to.equal(customersPage.successfulDeleteMessage);

      const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter2', baseContext);

      numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCustomers).to.be.above(0);
    });
  });
});
