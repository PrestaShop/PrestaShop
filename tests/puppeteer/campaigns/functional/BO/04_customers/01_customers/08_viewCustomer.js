require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
const AddCustomerPage = require('@pages/BO/customers/add');
const ViewCustomerPage = require('@pages/BO/customers/view');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/orderConfirmation');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const CustomerFaker = require('@data/faker/customer');
const AddressData = require('@data/faker/address');
const {Products} = require('@data/demo/products');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_viewCustomer';

let browser;
let page;
let numberOfCustomers = 0;
const createCustomerData = new CustomerFaker();
const editCustomerData = new CustomerFaker();
const address = new AddressData({city: 'Paris', country: 'France'});


// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    addCustomerPage: new AddCustomerPage(page),
    viewCustomerPage: new ViewCustomerPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

// View customer
describe('View customer', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);
    numberOfCustomers = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
    await expect(numberOfCustomers).to.be.above(0);
  });
  // 1 : Create customer
  describe('Create Customer in BO', async () => {
    it('should go to add new customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCustomerPage', baseContext);
      await this.pageObjects.customersPage.goToAddNewCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleCreate);
    });

    it('should create customer and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomer', baseContext);
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(createCustomerData);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulCreationMessage);
      const numberOfCustomersAfterCreation = await this.pageObjects.customersPage.getNumberOfElementInGrid();
      await expect(numberOfCustomersAfterCreation).to.be.equal(numberOfCustomers + 1);
    });
  });
  // 2 : View customer
  describe('View customer created', async () => {
    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage('1');
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextFromPersonnelInformationForm();
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
      await expect(cardHeaderText).to.contains(createCustomerData.yearOfBirth);
      await expect(cardHeaderText).to.contains('Never');
      await expect(cardHeaderText).to.contains('English (English)');
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
        const cardHeaderText = await this.pageObjects.viewCustomerPage.getNumberOfElementFromTitle(test.args.blockName);
        await expect(cardHeaderText).to.contains(test.args.number);
      });
    });
  });
  // 2 : Create order
  describe('Create order in FO', async () => {
    it('should go to FO and add the first product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstProductToCart', baseContext);
      // Click on view my shop
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      // Go to the first product page
      await this.pageObjects.homePage.goToProductPage(1);
      // Add the product to the cart
      await this.pageObjects.productPage.addProductToTheCart();
      // Proceed to checkout the shopping cart
      await this.pageObjects.cartPage.clickOnProceedToCheckout();
      const isCheckoutPage = await this.pageObjects.checkoutPage.isCheckoutPage();
      await expect(isCheckoutPage).to.be.true;
    });

    it('should login and go to address step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginToFO', baseContext);
      await this.pageObjects.checkoutPage.clickOnSignIn();
      const isStepLoginComplete = await this.pageObjects.checkoutPage.customerLogin(createCustomerData);
      await expect(isStepLoginComplete, 'Step Personal information is not complete').to.be.true;
    });

    it('should create address then continue to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAddress', baseContext);
      const isStepAddressComplete = await this.pageObjects.checkoutPage.setAddress(address);
      await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
    });

    it('should add a comment then continue to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);
      const isStepDeliveryComplete = await this.pageObjects.checkoutPage.chooseShippingMethodAndAddComment(
        1,
        'test message',
      );
      await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
    });

    it('should choose the payment method and confirm the order', async function () {
      await this.pageObjects.checkoutPage.choosePaymentAndOrder(PaymentMethods.wirePayment.moduleName);
      const cardTitle = await this.pageObjects.orderConfirmationPage
        .getTextContent(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitleH3);
      // Check the confirmation message
      await expect(cardTitle).to.contains(this.pageObjects.orderConfirmationPage.orderConfirmationCardTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });
  });
  // 3 : View customer
  describe('View customer created', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCustomer', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        createCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(createCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPage', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage('1');
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextFromPersonnelInformationForm();
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
      await expect(cardHeaderText).to.contains(createCustomerData.yearOfBirth);
      const today = await this.pageObjects.viewCustomerPage.getTodayDate();
      await expect(cardHeaderText).to.contains(today);
      await expect(cardHeaderText).to.contains('English (English)');
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
      // {args: {blockName: 'Last emails', number: 2}},
      {args: {blockName: 'Last connections', number: 1}},
      {args: {blockName: 'Groups', number: 3}},
    ];
    tests.forEach((test) => {
      it(`should check ${test.args.blockName} number`, async function () {
        const cardHeaderText = await this.pageObjects.viewCustomerPage.getNumberOfElementFromTitle(test.args.blockName);
        await expect(cardHeaderText).to.contains(test.args.number);
      });
    });

    it('should check orders', async function () {
      const today = await this.pageObjects.viewCustomerPage.getTodayDate();
      const carts = await this.pageObjects.viewCustomerPage.getOrders();
      expect(carts).to.contains(today);
      expect(carts).to.contains(PaymentMethods.wirePayment.name.toLowerCase());
      expect(carts).to.contains('€0.00');
    });

    it('should check carts', async function () {
      const today = await this.pageObjects.viewCustomerPage.getTodayDate();
      const carts = await this.pageObjects.viewCustomerPage.getCustomerCarts();
      expect(carts).to.contains(today);
      expect(carts).to.contains('€22.94');
    });

    it('should check viewed products', async function () {
      const viewedProduct = await this.pageObjects.viewCustomerPage.getViewedProduct();
      expect(viewedProduct).to.contains(Products.demo_1.name);
    });

    it('should check address', async function () {
      const customerAddress = await this.pageObjects.viewCustomerPage.getAddress();
      await expect(customerAddress).to.contains(address.company);
      await expect(customerAddress).to.contains(`${createCustomerData.firstName} ${createCustomerData.lastName}`);
      await expect(customerAddress).to.contains(address.address);
      await expect(customerAddress).to.contains(address.country);
      await expect(customerAddress).to.contains(address.phone);
    });

    it('should check messages', async function () {
      const today = await this.pageObjects.viewCustomerPage.getTodayDate();
      const carts = await this.pageObjects.viewCustomerPage.getMessages();
      expect(carts).to.contains(today);
      expect(carts).to.contains('Open');
      expect(carts).to.contains('test message');
    });

    it('should check last connections', async function () {
      const today = await this.pageObjects.viewCustomerPage.getTodayDate();
      const carts = await this.pageObjects.viewCustomerPage.getLastConnections();
      expect(carts).to.contains(today);
      expect(carts).to.contains('Direct link');
    });

    it('should add a private note', async function () {
      const result = await this.pageObjects.viewCustomerPage.setPrivateNote('Test note');
      expect(result).to.contains(this.pageObjects.viewCustomerPage.successfulUpdateMessage);
    });
  });

  // 4 : Edit customer
  describe('Edit customer created', async () => {
    it('should edit personnel information', async function () {
      await this.pageObjects.viewCustomerPage.goToEditCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleEdit);
    });

    it('should update customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(editCustomerData);
      await expect(textResult).to.equal(this.pageObjects.viewCustomerPage.successfulUpdateMessage);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextFromPersonnelInformationForm();
      await expect(cardHeaderText).to.contains(editCustomerData.firstName);
      await expect(cardHeaderText).to.contains(editCustomerData.lastName);
      await expect(cardHeaderText).to.contains(editCustomerData.email);
      await expect(cardHeaderText).to.contains(editCustomerData.yearOfBirth);
      const today = await this.pageObjects.viewCustomerPage.getTodayDate();
      await expect(cardHeaderText).to.contains(today);
      await expect(cardHeaderText).to.contains('English (English)');
      await expect(cardHeaderText).to.contains('Newsletter');
      await expect(cardHeaderText).to.contains('Partner offers');
      await expect(cardHeaderText).to.contains('Active');
    });
  });
  // 5 : Delete customer from BO
  describe('Delete Customer', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageToDelete', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        editCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should delete customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomer', baseContext);
      const textResult = await this.pageObjects.customersPage.deleteCustomer(1);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulDeleteMessage);
      const numberOfCustomersAfterDelete = await this.pageObjects.customersPage.resetAndGetNumberOfLines();
      await expect(numberOfCustomersAfterDelete).to.be.equal(numberOfCustomers);
    });
  });
});
