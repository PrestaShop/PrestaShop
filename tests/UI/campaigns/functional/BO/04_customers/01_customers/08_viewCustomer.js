require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const addCustomerPage = require('@pages/BO/customers/add');
const viewCustomerPage = require('@pages/BO/customers/view');
const addAddressPage = require('@pages/BO/customers/addresses/add');
const viewOrderPage = require('@pages/BO/orders/view');
const viewCartPage = require('@pages/BO/orders/shoppingCarts/view');
const foHomePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');
const cartPage = require('@pages/FO/cart');
const checkoutPage = require('@pages/FO/checkout');
const orderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const CustomerFaker = require('@data/faker/customer');
const {Products} = require('@data/demo/products');
const {Languages} = require('@data/demo/languages');
const {Statuses} = require('@data/demo/orderStatuses');
const AddressFaker = require('@data/faker/address');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_viewCustomer';


let browserContext;
let page;

let numberOfCustomers = 0;

// Init data
const createCustomerData = new CustomerFaker({defaultCustomerGroup: 'Customer'});
const editCustomerData = new CustomerFaker({defaultCustomerGroup: 'Visitor'});
const address = new AddressFaker({city: 'Paris', country: 'France'});
const createAddressData = new AddressFaker({country: 'France'});

// Get today date format 'mm/dd/yyyy'
const today = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
const todayDate = `${mm}/${dd}/${yyyy}`;

// Get customer birth date format 'mm/dd/yyyy'
const mmBirth = `0${createCustomerData.monthOfBirth}`.slice(-2);
const ddBirth = `0${createCustomerData.dayOfBirth}`.slice(-2);
const yyyyBirth = createCustomerData.yearOfBirth;
const customerBirthDate = `${mmBirth}/${ddBirth}/${yyyyBirth}`;

const mmEditBirth = `0${editCustomerData.monthOfBirth}`.slice(-2);
const ddEditBirth = `0${editCustomerData.dayOfBirth}`.slice(-2);
const yyyyEditBirth = editCustomerData.yearOfBirth;
const editCustomerBirthDate = `${mmEditBirth}/${ddEditBirth}/${yyyyEditBirth}`;

// View customer
describe('View information about customer', async () => {
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
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter', baseContext);

    numberOfCustomers = await customersPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCustomers).to.be.above(0);
  });

  // 1 : Create customer
  describe('Create Customer in BO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);

      await customersPage.goToAddNewCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, createCustomerData);
      await expect(textResult).to.equal(customersPage.successfulCreationMessage);

      const numberOfCustomersAfterCreation = await customersPage.getNumberOfElementInGrid(page);
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
  });

  // 2 : View customer
  describe('View customer created', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        createCustomerData.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateCustomer', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);
      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformationTitle1', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);

      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo1', baseContext);

      const cardHeaderText = await viewCustomerPage.getTextFromElement(page, 'Personal information');

      await expect(cardHeaderText).to.contains(createCustomerData.socialTitle);
      await expect(cardHeaderText).to.contains(`birth date: ${customerBirthDate}`);
      await expect(cardHeaderText).to.contains('Never');
      await expect(cardHeaderText).to.contains(Languages.english.name);
      await expect(cardHeaderText).to.contains('Newsletter');
      await expect(cardHeaderText).to.contains('Partner offers');
      await expect(cardHeaderText).to.contains('Active');
    });

    const tests = [
      {args: {blockName: 'Orders', number: 0}},
      {args: {blockName: 'Carts', number: 0}},
      {args: {blockName: 'Messages', number: 0}},
      {args: {blockName: 'Vouchers', number: 0}},
      {args: {blockName: 'Groups', number: 3}},
    ];

    tests.forEach((test) => {
      it(`should check ${test.args.blockName} number`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `check${test.args.blockName}Number`, baseContext);

        const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, test.args.blockName);
        await expect(cardHeaderText).to.contains(test.args.number);
      });
    });
  });

  // 3 : Create order
  describe('Create order in FO', async () => {
    it('should go to FO and add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);

      // Click on view my shop
      page = await viewCustomerPage.viewMyShop(page);

      // Change language
      await foHomePage.changeLanguage(page, 'en');

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      // Add the product to the cart
      await productPage.addProductToTheCart(page);

      // Proceed to checkout the shopping cart
      await cartPage.clickOnProceedToCheckout(page);
      const isCheckoutPage = await checkoutPage.isCheckoutPage(page);
      await expect(isCheckoutPage).to.be.true;
    });

    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToFO', baseContext);

      await checkoutPage.clickOnSignIn(page);
      const isStepLoginComplete = await checkoutPage.customerLogin(page, createCustomerData);
      await expect(isStepLoginComplete, 'Step Personal information is not complete').to.be.true;
    });

    it('should create address then continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);

      const isStepAddressComplete = await checkoutPage.setAddress(page, address);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should add a comment then continue to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await checkoutPage.chooseShippingMethodAndAddComment(
        page,
        1,
        'test message',
      );

      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose the payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'choosePaymentMethod', baseContext);

      await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

      // Check the confirmation message
      const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
      await expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

      page = await orderConfirmationPage.closePage(browserContext, page, 0);

      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
    });
  });

  // 4 : View customer after creating the order
  describe('View customer after creating the order', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomersPage', baseContext);

      await viewCustomerPage.goToSubMenu(
        page,
        viewCustomerPage.customersParentLink,
        viewCustomerPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomer', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        createCustomerData.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateOrder', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);
      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformationTitle2', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);

      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo2', baseContext);

      const cardHeaderText = await viewCustomerPage.getTextFromElement(page, 'Personal information');

      await expect(cardHeaderText).to.contains(createCustomerData.socialTitle);
      await expect(cardHeaderText).to.contains(`birth date: ${customerBirthDate}`);
      await expect(cardHeaderText).to.contains(todayDate);
      await expect(cardHeaderText).to.contains(Languages.english.name);
      await expect(cardHeaderText).to.contains('Newsletter');
      await expect(cardHeaderText).to.contains('Partner offers');
      await expect(cardHeaderText).to.contains('Active');
    });

    const tests = [
      {args: {blockName: 'Orders', number: 1}},
      {args: {blockName: 'Carts', number: 1}},
      {args: {blockName: 'Viewed products', number: 1}},
      {args: {blockName: 'Messages', number: 1}},
      {args: {blockName: 'Vouchers', number: 0}},
      {args: {blockName: 'Last emails', number: 2}},
      {args: {blockName: 'Last connections', number: 1}},
      {args: {blockName: 'Groups', number: 3}},
    ];

    tests.forEach((test) => {
      it(`should check ${test.args.blockName} number`, async function () {
        await testContext.addContextItem(
          this, 'testIdentifier',
          `check${test.args.blockName}NumberAfterEdit`,
          baseContext,
        );

        const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, test.args.blockName);
        await expect(cardHeaderText).to.contains(test.args.number);
      });
    });

    it('should check orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrders', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Orders');

      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Bank transfer');
      expect(carts).to.contains(Statuses.awaitingBankWire.status);
      expect(carts).to.contains('€0.00');
    });

    it('should check carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarts', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Carts');
      expect(carts).to.contains(todayDate);
      expect(carts).to.contains(Products.demo_1.finalPrice);
    });

    it('should check viewed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewedProduct', baseContext);

      const viewedProduct = await viewCustomerPage.getTextFromElement(page, 'Viewed products');
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

      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Open');
      expect(carts).to.contains('test message');
    });

    it('should check last connections', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLAstConnections', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Last connections');

      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Direct link');
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
  describe('Edit customer created then view it', async () => {
    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCustomerPage', baseContext);

      await viewCustomerPage.goToEditCustomerPage(page);
      const pageTitle = await addCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCustomerPage.pageTitleEdit);
    });

    it('should edit customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);

      const textResult = await addCustomerPage.createEditCustomer(page, editCustomerData);
      await expect(textResult).to.equal(viewCustomerPage.successfulUpdateMessage);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerTitle', baseContext);

      const cardHeaderText = await viewCustomerPage.getPersonalInformationTitle(page);

      await expect(cardHeaderText).to.contains(editCustomerData.firstName);
      await expect(cardHeaderText).to.contains(editCustomerData.lastName);
      await expect(cardHeaderText).to.contains(editCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);

      const cardHeaderText = await viewCustomerPage.getTextFromElement(page, 'Personal information');

      expect(cardHeaderText).to.contains(editCustomerData.socialTitle);
      expect(cardHeaderText).to.contains(`birth date: ${editCustomerBirthDate}`);
      expect(cardHeaderText).to.contains(todayDate);
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
  describe('Edit order then view customer', async () => {
    it('should go to view order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewOrderPage', baseContext);

      await viewCustomerPage.goToPage(page, 'Orders');
      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });

    it('should modify order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'modifyOrderStatus', baseContext);

      const result = await viewOrderPage.modifyOrderStatus(page, Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageAfterEditOrder', baseContext);

      await viewOrderPage.goToSubMenu(
        page,
        viewOrderPage.customersParentLink,
        viewOrderPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomerAfterEditOrder', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        editCustomerData.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterEditOrder', baseContext);

      await customersPage.goToViewCustomerPage(page, 1);
      const pageTitle = await viewCustomerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCustomerPage.pageTitle);
    });

    it('should check order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckOrderStatusAfterEdit', baseContext);

      const carts = await viewCustomerPage.getTextFromElement(page, 'Orders');

      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Bank transfer');
      expect(carts).to.contains(Statuses.shipped.status);
      expect(carts).to.contains(Products.demo_1.finalPrice);
    });

    it('should check purchased products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckPurchasedProductsNumber', baseContext);

      const cardHeaderText = await viewCustomerPage.getNumberOfElementFromTitle(page, 'Purchased products');
      await expect(cardHeaderText).to.contains(1);
    });

    it('should check purchased products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPurchasedProduct', baseContext);

      const purchasedProduct = await viewCustomerPage.getTextFromElement(page, 'Purchased products');

      expect(purchasedProduct).to.contains(todayDate);
      expect(purchasedProduct).to.contains(Products.demo_1.name);
    });
  });

  // 7 : Edit address then check customer information page
  describe('Edit address then view customer', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);

      await viewCustomerPage.goToPage(page, 'Addresses');
      const pageTitle = await addAddressPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addAddressPage.pageTitleEdit);
    });

    it('should modify the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);

      const textResult = await addAddressPage.createEditAddress(page, createAddressData);
      await expect(textResult).to.equal(viewCustomerPage.updateSuccessfulMessage);
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
  // 8 : View carts page
  describe('View cart page', async () => {
    it('should go to view cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCartPage', baseContext);

      await viewCustomerPage.goToPage(page, 'Carts');
      const pageTitle = await viewCartPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewCartPage.pageTitle);
    });
  });

  // 9 : Delete customer from BO
  describe('Delete Customer', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);

      await viewCartPage.goToSubMenu(
        page,
        viewCartPage.customersParentLink,
        viewCartPage.customersLink,
      );

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        editCustomerData.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);

      const textResult = await customersPage.deleteCustomer(page, 1);
      await expect(textResult).to.equal(customersPage.successfulDeleteMessage);

      const numberOfCustomersAfterDelete = await customersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });
  });
});
