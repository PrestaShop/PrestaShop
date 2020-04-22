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
const AddAddressPage = require('@pages/BO/customers/addresses/add');
const ViewOrderPage = require('@pages/BO/orders/view');
const ViewCartPage = require('@pages/BO/orders/shoppingCarts/view');
const FOBasePage = require('@pages/FO/FObasePage');
const HomePage = require('@pages/FO/home');
const ProductPage = require('@pages/FO/product');
const CartPage = require('@pages/FO/cart');
const CheckoutPage = require('@pages/FO/checkout');
const OrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');
// Importing data
const {PaymentMethods} = require('@data/demo/paymentMethods');
const CustomerFaker = require('@data/faker/customer');
const AddressData = require('@data/faker/address');
const {Products} = require('@data/demo/products');
const {Languages} = require('@data/demo/languages');
const {Statuses} = require('@data/demo/orderStatuses');
const AddressFaker = require('@data/faker/address');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_viewCustomer';

let browser;
let page;
let numberOfCustomers = 0;
const createCustomerData = new CustomerFaker({defaultCustomerGroup: 'Customer'});
const editCustomerData = new CustomerFaker({defaultCustomerGroup: 'Visitor'});
const address = new AddressData({city: 'Paris', country: 'France'});
const today = new Date();
const mm = (`0${today.getMonth() + 1}`).slice(-2); // Current month
const dd = (`0${today.getDate()}`).slice(-2); // Current day
const yyyy = today.getFullYear(); // Current year
const todayDate = `${mm}/${dd}/${yyyy}`;
const mmBirth = `0${createCustomerData.monthOfBirth}`.slice(-2);
const ddBirth = `0${createCustomerData.dayOfBirth}`.slice(-2);
const yyyyBirth = createCustomerData.yearOfBirth;
const customerBirthDate = `${mmBirth}/${ddBirth}/${yyyyBirth}`;
const mmEditBirth = `0${editCustomerData.monthOfBirth}`.slice(-2);
const ddEditBirth = `0${editCustomerData.dayOfBirth}`.slice(-2);
const yyyyEditBirth = editCustomerData.yearOfBirth;
const editCustomerBirthDate = `${mmEditBirth}/${ddEditBirth}/${yyyyEditBirth}`;
const createAddressData = new AddressFaker({country: 'France'});


// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    addCustomerPage: new AddCustomerPage(page),
    addAddressPage: new AddAddressPage(page),
    viewCustomerPage: new ViewCustomerPage(page),
    viewOrderPage: new ViewOrderPage(page),
    viewCartPage: new ViewCartPage(page),
    foBasePage: new FOBasePage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
    checkoutPage: new CheckoutPage(page),
    orderConfirmationPage: new OrderConfirmationPage(page),
  };
};

// View customer
describe('View information about customer', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO
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
    await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilter', baseContext);
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateCustomer', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage(1);
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformationTitle1', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getPersonalInformationTitle();
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo1', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextFromElement('Personal information');
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
        const cardHeaderText = await this.pageObjects.viewCustomerPage.getNumberOfElementFromTitle(test.args.blockName);
        await expect(cardHeaderText).to.contains(test.args.number);
      });
    });
  });
  // 3 : Create order
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
  // 4 : View customer after creating the order
  describe('View customer after creating the order', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomersPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomer', baseContext);
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterCreateOrder', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage(1);
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPersonalInformationTitle2', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getPersonalInformationTitle();
      await expect(cardHeaderText).to.contains(createCustomerData.firstName);
      await expect(cardHeaderText).to.contains(createCustomerData.lastName);
      await expect(cardHeaderText).to.contains(createCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCustomerInfo2', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextFromElement('Personal information');
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
        const cardHeaderText = await this.pageObjects.viewCustomerPage.getNumberOfElementFromTitle(test.args.blockName);
        await expect(cardHeaderText).to.contains(test.args.number);
      });
    });

    it('should check orders', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkOrders', baseContext);
      const carts = await this.pageObjects.viewCustomerPage.getTextFromElement('Orders');
      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Bank transfer');
      expect(carts).to.contains(Statuses.awaitingBankWire.status);
      expect(carts).to.contains('€0.00');
    });

    it('should check carts', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarts', baseContext);
      const carts = await this.pageObjects.viewCustomerPage.getTextFromElement('Carts');
      expect(carts).to.contains(todayDate);
      expect(carts).to.contains(Products.demo_1.finalPrice);
    });

    it('should check viewed products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkViewedProduct', baseContext);
      const viewedProduct = await this.pageObjects.viewCustomerPage.getTextFromElement('Viewed products');
      expect(viewedProduct).to.contains(Products.demo_1.name);
    });

    it('should check address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAddress', baseContext);
      const customerAddress = await this.pageObjects.viewCustomerPage.getTextFromElement('Addresses');
      await expect(customerAddress).to.contains(address.company);
      await expect(customerAddress).to.contains(`${createCustomerData.firstName} ${createCustomerData.lastName}`);
      await expect(customerAddress).to.contains(address.address);
      await expect(customerAddress).to.contains(address.country);
      await expect(customerAddress).to.contains(address.phone);
    });

    it('should check messages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkMessages', baseContext);
      const carts = await this.pageObjects.viewCustomerPage.getTextFromElement('Messages');
      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Open');
      expect(carts).to.contains('test message');
    });

    it('should check last connections', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLAstConnections', baseContext);
      const carts = await this.pageObjects.viewCustomerPage.getTextFromElement('Last connections');
      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Direct link');
    });

    it('should check groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroups', baseContext);
      const groups = await this.pageObjects.viewCustomerPage.getTextFromElement('Groups');
      expect(groups).to.contains(createCustomerData.defaultCustomerGroup);
    });

    it('should add a private note', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addPrivateNote', baseContext);
      const result = await this.pageObjects.viewCustomerPage.setPrivateNote('Test note');
      expect(result).to.contains(this.pageObjects.viewCustomerPage.successfulUpdateMessage);
    });
  });
  // 5 : Edit customer then check customer information page
  describe('Edit customer created then view it', async () => {
    it('should go to edit customer page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCustomerPage', baseContext);
      await this.pageObjects.viewCustomerPage.goToEditCustomerPage();
      const pageTitle = await this.pageObjects.addCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCustomerPage.pageTitleEdit);
    });

    it('should edit customer information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);
      const textResult = await this.pageObjects.addCustomerPage.createEditCustomer(editCustomerData);
      await expect(textResult).to.equal(this.pageObjects.viewCustomerPage.successfulUpdateMessage);
    });

    it('should check personal information title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerTitle', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getPersonalInformationTitle();
      await expect(cardHeaderText).to.contains(editCustomerData.firstName);
      await expect(cardHeaderText).to.contains(editCustomerData.lastName);
      await expect(cardHeaderText).to.contains(editCustomerData.email);
    });

    it('should check customer personal information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCustomerInfo', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getTextFromElement('Personal information');
      await expect(cardHeaderText).to.contains(editCustomerData.socialTitle);
      await expect(cardHeaderText).to.contains(`birth date: ${editCustomerBirthDate}`);
      await expect(cardHeaderText).to.contains(todayDate);
      await expect(cardHeaderText).to.contains(Languages.english.name);
      await expect(cardHeaderText).to.contains('Newsletter');
      await expect(cardHeaderText).to.contains('Partner offers');
      await expect(cardHeaderText).to.contains('Active');
    });

    it('should check groups', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGroupsAfterEdit', baseContext);
      const groups = await this.pageObjects.viewCustomerPage.getTextFromElement('Groups');
      expect(groups).to.contains(editCustomerData.defaultCustomerGroup);
    });
  });
  // 6 : Edit order then check customer information page
  describe('Edit order then view customer', async () => {
    it('should go to view order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewOrderPage', baseContext);
      await this.pageObjects.viewCustomerPage.goToPage('Orders');
      const pageTitle = await this.pageObjects.viewOrderPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewOrderPage.pageTitle);
    });

    it('should modify order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCustomer', baseContext);
      const result = await this.pageObjects.viewOrderPage.modifyOrderStatus(Statuses.shipped.status);
      await expect(result).to.equal(Statuses.shipped.status);
    });

    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPageAfterEditOrder', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.customersParentLink,
        this.pageObjects.boBasePage.customersLink,
      );
      const pageTitle = await this.pageObjects.customersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCustomerAfterEditOrder', baseContext);
      await this.pageObjects.customersPage.resetFilter();
      await this.pageObjects.customersPage.filterCustomers(
        'input',
        'email',
        editCustomerData.email,
      );
      const textEmail = await this.pageObjects.customersPage.getTextColumnFromTableCustomers(1, 'email');
      await expect(textEmail).to.contains(editCustomerData.email);
    });

    it('should click on view customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCustomerPageAfterEditOrder', baseContext);
      await this.pageObjects.customersPage.goToViewCustomerPage(1);
      const pageTitle = await this.pageObjects.viewCustomerPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCustomerPage.pageTitle);
    });

    it('should check order status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckOrderStatusAfterEdit', baseContext);
      const carts = await this.pageObjects.viewCustomerPage.getTextFromElement('Orders');
      expect(carts).to.contains(todayDate);
      expect(carts).to.contains('Bank transfer');
      expect(carts).to.contains(Statuses.shipped.status);
      expect(carts).to.contains(Products.demo_1.finalPrice);
    });

    it('should check purchased products number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckPurchasedProductsNumber', baseContext);
      const cardHeaderText = await this.pageObjects.viewCustomerPage.getNumberOfElementFromTitle('Purchased products');
      await expect(cardHeaderText).to.contains(1);
    });

    it('should check purchased products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPurchasedProduct', baseContext);
      const purchasedProduct = await this.pageObjects.viewCustomerPage.getTextFromElement('Purchased products');
      expect(purchasedProduct).to.contains(todayDate);
      expect(purchasedProduct).to.contains(Products.demo_1.name);
    });
  });
  // 7 : Edit address then check customer information page
  describe('Edit address then view customer', async () => {
    it('should go to edit address page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditAddressPage', baseContext);
      await this.pageObjects.viewCustomerPage.goToPage('Addresses');
      const pageTitle = await this.pageObjects.addAddressPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addAddressPage.pageTitleEdit);
    });

    it('should modify the address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateAddress', baseContext);
      const textResult = await this.pageObjects.addAddressPage.createEditAddress(createAddressData);
      await expect(textResult).to.equal(this.pageObjects.viewCustomerPage.updateSuccessfulMessage);
    });

    it('should check the edited address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckEditedAddress', baseContext);
      const customerAddress = await this.pageObjects.viewCustomerPage.getTextFromElement('Addresses');
      await expect(customerAddress).to.contains(createAddressData.company);
      await expect(customerAddress).to.contains(`${createAddressData.firstName} ${createAddressData.lastName}`);
      await expect(customerAddress).to.contains(createAddressData.address);
      await expect(customerAddress).to.contains(createAddressData.country);
      await expect(customerAddress).to.contains(createAddressData.phone);
    });
  });
  // 8 : View carts page
  describe('View cart page', async () => {
    it('should go to view cart page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCartPage', baseContext);
      await this.pageObjects.viewCustomerPage.goToPage('Carts');
      const pageTitle = await this.pageObjects.viewCartPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.viewCartPage.pageTitle);
    });
  });
  // 9 : Delete customer from BO
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
