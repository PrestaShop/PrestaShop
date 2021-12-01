require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');
const date = require('@utils/date');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const addCurrencyPage = require('@pages/BO/international/currencies/add');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foProductPage = require('@pages/FO/product');
const foCartPage = require('@pages/FO/cart');
const foCheckoutPage = require('@pages/FO/checkout');
const foOrderConfirmationPage = require('@pages/FO/checkout/orderConfirmation');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Currencies} = require('@data/demo/currencies');
const {Statuses} = require('@data/demo/orderStatuses');
const {Products} = require('@data/demo/products');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_paymentBlock';

let browserContext;
let page;
let filePath;
let todayToCheck;
let today;

const totalOrder = 22.94;

const paymentDataAmountInfTotal = {
  date: today,
  paymentMethod: 'Payment by check',
  transactionID: '12156',
  amount: 12.25,
  currency: '€',
};

const paymentDataAmountEqualTotal = {
  date: today,
  paymentMethod: 'Bank transfer',
  transactionID: '12190',
  amount: (totalOrder - paymentDataAmountInfTotal.amount).toFixed(2),
  currency: '€',
};

const paymentDataAmountSupTotal = {
  date: today,
  paymentMethod: 'Bank transfer',
  transactionID: '12639',
  amount: 30.56,
  currency: '€',
};

const paymentDataWithNewCurrency = {
  date: today,
  paymentMethod: 'Bank transfer',
  transactionID: '12640',
  amount: 5.25,
  currency: Currencies.mad.isoCode,
};

let invoiceID = 0;

const paymentDataAmountEqualRest = {
  date: today,
  paymentMethod: 'Bank transfer',
  transactionID: '12190',
  amount: Products.demo_5.priceTaxIncl,
  currency: '€',
};
/*
Pre-condition :
- Create 2 orders by default customer
Scenario :
- View order page
- Add payment inferior to the total
- Add payment equal to the total
- Add payment superior to the total
- Check payment details
- Add payment with new currency
- Click on payment accepted and check result
Post-condition
- Delete new currency
 */

describe('BO - Orders - View and edit order : Check payment Block', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    todayToCheck = await date.getDate('mm/dd/yyyy');
    today = await date.getDate('yyyy-mm-dd');
  });

  after(async () => {
    await files.deleteFile(filePath);
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition - Create order by default customer
  describe('Create order by default customer in FO', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      console.log(today);
      await foHomePage.goToFo(page);
      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO', baseContext);

      await foHomePage.goToHomePage(page);

      await foHomePage.goToLoginPage(page);

      const pageTitle = await foLoginPage.getPageTitle(page);
      await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

      await foLoginPage.customerLogin(page, DefaultCustomer);

      const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
      await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
    });

    ['first', 'second'].forEach((arg, index) => {
      it('should add product to cart and proceed to checkout', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

        await foHomePage.goToHomePage(page);

        // Go to the first product page
        await foHomePage.goToProductPage(page, 1);

        // Add the product to the cart
        await foProductPage.addProductToTheCart(page);

        const notificationsNumber = await foCartPage.getCartNotificationsNumber(page);
        await expect(notificationsNumber, 'Notifications number is not correct!').to.be.equal(1);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToDeliveryStep${index}`, baseContext);

        // Proceed to checkout the shopping cart
        await foCartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foCheckoutPage.goToDeliveryStep(page);
        await expect(isStepAddressComplete, 'Step Address is not complete').to.be.true;
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToPaymentStep${index}`, baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foCheckoutPage.goToPaymentStep(page);
        await expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.true;
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `confirmOrder${index}`, baseContext);

        // Payment step - Choose payment step
        await foCheckoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        await expect(cardTitle, 'Error in confirmation message')
          .to.contains(foOrderConfirmationPage.orderConfirmationCardTitle);
      });
    });
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn, 'Lastname is not correct').to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Add payment inferior to the total
  describe('Add payment inferior to the total', async () => {
    it('should check that payments number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber1', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(0);
    });

    it('should add payment when amount is inferior to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountInferiorTotal', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountInfTotal);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarning', baseContext);

      const warningMessage = await viewOrderPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${paymentDataAmountInfTotal.amount} paid instead of €${totalOrder}`);
    });

    it('should check that the payments number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber2', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountInfTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountInfTotal.transactionID),
        expect(result.amount).to.equal(`€${paymentDataAmountInfTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 3 - Add payment equal to the total
  describe('Add payment equal to the total', async () => {
    it('should add payment when amount is equal to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountEqualTotal', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountEqualTotal);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that the payments number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber3', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(2);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment2', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page, 3);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountEqualTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountEqualTotal.transactionID),
        expect(result.amount).to.equal(`€${paymentDataAmountEqualTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 4 - Add payment superior to the total
  describe('Add payment superior to the total', async () => {
    it('should add payment when amount is superior to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountSupTotal', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountSupTotal);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarning2', baseContext);

      const warningMessage = await viewOrderPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${(paymentDataAmountSupTotal.amount + totalOrder).toFixed(2)}`
        + ` paid instead of €${totalOrder}`);
    });

    it('should check that the payments number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber4', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(3);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment2', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page, 5);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountSupTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountSupTotal.transactionID),
        expect(result.amount).to.equal(`€${paymentDataAmountSupTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 5 - Check details button
  describe('Check details button', async () => {
    it('should click on \'Details\' button and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayPaymentDetail', baseContext);

      const result = await viewOrderPage.displayPaymentDetail(page);
      await expect(result)
        .to.contain('Card number Not defined')
        .and.to.contain('Card type Not defined')
        .and.to.contain('Expiration date Not defined')
        .and.to.contain('Cardholder name Not defined');
    });
  });

  // 6 - Add payment with new currency and check details
  describe('Add payment with new currency and check details', async () => {
    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.localizationLink,
      );

      await localizationPage.closeSfToolBar(page);

      const pageTitle = await localizationPage.getPageTitle(page);
      await expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await localizationPage.goToSubTabCurrencies(page);
      const pageTitle = await currenciesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);
      const pageTitle = await addCurrencyPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it('should create currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await addCurrencyPage.addOfficialCurrency(page, Currencies.mad);
      await expect(textResult).to.contains(currenciesPage.successfulCreationMessage);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage2', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(viewOrderPage.pageTitle);
    });

    it('should check that the new currency is visible on select options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSelectOption', baseContext);

      const listOfCurrencies = await viewOrderPage.getCurrencySelectOptions(page);
      await expect(listOfCurrencies).to.contain('€')
        .and.to.contain(Currencies.mad.isoCode);
    });

    it('should add payment with new currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'paymentWithNewCurrency', baseContext);

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataWithNewCurrency);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });
  });

  // 7 - Click on payment accepted and check result
  describe('Click on payment accepted and check result', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 2);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage2', baseContext);

      await ordersPage.goToOrder(page, 2);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(viewOrderPage.pageTitle);
    });

    it('should check that the payments number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber4', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(0);
    });

    it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusPaymentAccepted', baseContext);

      const textResult = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(textResult).to.equal(Statuses.paymentAccepted.status);
    });

    it('should check that the payments number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber5', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the payment details and get the invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment3', baseContext);

      const result = await viewOrderPage.getPaymentsDetails(page, 1);
      await Promise.all([
        expect(result.date).to.contain(today),
        expect(result.paymentMethod).to.equal('Bank transfer'),
        expect(result.transactionID).to.equal(''),
        expect(result.amount).to.equal(`€${totalOrder}`),
        expect(result.invoice).to.not.equal(''),
      ]);

      invoiceID = await viewOrderPage.getInvoiceID(page);
    });

    it(`should add the product '${Products.demo_5.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCustomizedProduct', baseContext);

      await viewOrderPage.searchProduct(page, Products.demo_5.name);

      const textResult = await viewOrderPage.addProductToCart(page, 1, true);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check that products number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await viewOrderPage.reloadPage(page);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(2);
    });

    it('should check that invoices number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber', baseContext);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(2);
    });

    it('should check that payments number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber6', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarningMessage', baseContext);

      const warningMessage = await viewOrderPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${totalOrder} paid instead of €57.74`);
    });

    it('should add payment when amount is equal to the rest to the new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountSupTotal', baseContext);

      const invoice = `#IN0000${invoiceID >= 10 ? '' : '0'}${invoiceID + 1}`;

      const validationMessage = await viewOrderPage.addPayment(page, paymentDataAmountEqualRest, invoice);
      expect(validationMessage, 'Successful message is not correct!').to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should check that payments number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentsNumber7', baseContext);

      const paymentsNumber = await viewOrderPage.getPaymentsNumber(page);
      await expect(paymentsNumber, 'Payments number is not correct! ').to.equal(2);
    });

    it('should download the invoice and check payment method and amount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceAndCheckPayment', baseContext);

      // Download invoice
      filePath = await viewOrderPage.downloadInvoice(page, 3);

      const exist = await files.doesFileExist(filePath);
      await expect(exist, 'File doesn\'t exist!').to.be.true;

      const paymentMethodExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.paymentMethod);
      await expect(paymentMethodExist, 'Payment method does not exist in invoice!').to.be.true;

      const amountExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
      await expect(amountExist, 'Payment amount does not exist in invoice!').to.be.true;
    });
  });

  // Post-condition - Delete currency
  describe('Delete created currency ', async () => {
    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.localizationLink,
      );

      await localizationPage.closeSfToolBar(page);

      const pageTitle = await localizationPage.getPageTitle(page);
      await expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage2', baseContext);

      await localizationPage.goToSubTabCurrencies(page);
      const pageTitle = await currenciesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it(`should filter by iso code of currency '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.mad.isoCode);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.mad.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await currenciesPage.deleteCurrency(page, 1);
      await expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
    });
  });
});
