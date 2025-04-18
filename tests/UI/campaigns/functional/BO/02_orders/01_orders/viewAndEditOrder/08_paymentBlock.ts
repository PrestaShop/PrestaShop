import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {createCurrencyTest, deleteCurrencyTest} from '@commonTests/BO/international/currency';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockPaymentsPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCurrencies,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  type OrderPayment,
  type Page,
  utilsDate,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_paymentBlock';

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
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string|null;
  let invoiceID: number = 0;

  const today: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const todayToCheck: string = utilsDate.getDateFormat('mm/dd/yyyy');
  const totalOrder: number = 22.94;
  // New order by customer data
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const paymentDataAmountInfTotal: OrderPayment = {
    date: today,
    paymentMethod: 'Payment by check',
    transactionID: 12156,
    amount: 12.25,
    currency: '€',
  };
  const paymentDataAmountEqualTotal: OrderPayment = {
    date: today,
    paymentMethod: 'Bank transfer',
    transactionID: 12190,
    amount: parseFloat((totalOrder - paymentDataAmountInfTotal.amount).toFixed(2)),
    currency: '€',
  };
  const paymentDataAmountSupTotal: OrderPayment = {
    date: today,
    paymentMethod: 'Bank transfer',
    transactionID: 12639,
    amount: 30.56,
    currency: '€',
  };
  const paymentDataWithNewCurrency: OrderPayment = {
    date: today,
    paymentMethod: 'Bank transfer',
    transactionID: 12640,
    amount: 5.25,
    currency: dataCurrencies.mad.isoCode,
  };
  const paymentDataAmountEqualRest: OrderPayment = {
    date: today,
    paymentMethod: 'Bank transfer',
    transactionID: 12190,
    amount: dataProducts.demo_5.price,
    currency: '€',
  };

  // Pre-condition: Create first order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_1`);

  // Pre-condition: Create second order by default customer
  createOrderByCustomerTest(orderByCustomerData, `${baseContext}_preTest_2`);

  // Pre-condition: Create currency
  createCurrencyTest(dataCurrencies.mad, `${baseContext}_preTest_3`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsFile.deleteFile(filePath);
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn, 'Lastname is not correct').to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageMessagesBlock1', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockPaymentsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockPaymentsPage.pageTitle);
    });
  });

  // 2 - Add payment inferior to the total
  describe('Add payment inferior to the total', async () => {
    it('should check that payments number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayments1', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(0);
    });

    it('should add payment when amount is inferior to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountInferiorTotal', baseContext);

      const validationMessage = await boOrdersViewBlockPaymentsPage.addPayment(page, paymentDataAmountInfTotal);
      expect(validationMessage, 'Successful message is not correct!')
        .to.equal(boOrdersViewBlockPaymentsPage.successfulUpdateMessage);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarning', baseContext);

      const warningMessage = await boOrdersViewBlockPaymentsPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${paymentDataAmountInfTotal.amount} paid instead of €${totalOrder}`);
    });

    it('should check that the payments number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments2', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment', baseContext);

      const result = await boOrdersViewBlockPaymentsPage.getPaymentsDetails(page);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountInfTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountInfTotal.transactionID.toString()),
        expect(result.amount).to.equal(`€${paymentDataAmountInfTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 3 - Add payment equal to the total
  describe('Add payment equal to the total', async () => {
    it('should add payment when amount is equal to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountEqualTotal', baseContext);

      const validationMessage = await boOrdersViewBlockPaymentsPage.addPayment(page, paymentDataAmountEqualTotal);
      expect(validationMessage, 'Successful message is not correct!')
        .to.equal(boOrdersViewBlockPaymentsPage.successfulUpdateMessage);
    });

    it('should check that the payments number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments3', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(2);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentDetails1', baseContext);

      const result = await boOrdersViewBlockPaymentsPage.getPaymentsDetails(page, 3);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountEqualTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountEqualTotal.transactionID.toString()),
        expect(result.amount).to.equal(`€${paymentDataAmountEqualTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 4 - Add payment superior to the total
  describe('Add payment superior to the total', async () => {
    it('should add payment when amount is superior to the total', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountSupTotal1', baseContext);

      const validationMessage = await boOrdersViewBlockPaymentsPage.addPayment(page, paymentDataAmountSupTotal);
      expect(validationMessage, 'Successful message is not correct!')
        .to.equal(boOrdersViewBlockPaymentsPage.successfulUpdateMessage);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarning2', baseContext);

      const warningMessage = await boOrdersViewBlockPaymentsPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${(paymentDataAmountSupTotal.amount + totalOrder).toFixed(2)}`
        + ` paid instead of €${totalOrder}`);
    });

    it('should check that the payments number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments4', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(3);
    });

    it('should check the payment details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentDetail1', baseContext);

      const result = await boOrdersViewBlockPaymentsPage.getPaymentsDetails(page, 5);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal(paymentDataAmountSupTotal.paymentMethod),
        expect(result.transactionID).to.equal(paymentDataAmountSupTotal.transactionID.toString()),
        expect(result.amount).to.equal(`€${paymentDataAmountSupTotal.amount}`),
        expect(result.invoice).to.equal(''),
      ]);
    });
  });

  // 5 - Check details button
  describe('Check details button', async () => {
    it('should click on \'Details\' button and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayPaymentDetail', baseContext);

      const result = await boOrdersViewBlockPaymentsPage.displayPaymentDetail(page);
      expect(result)
        .to.contain('Card number Not defined')
        .and.to.contain('Card type Not defined')
        .and.to.contain('Expiration date Not defined')
        .and.to.contain('Cardholder name Not defined');
    });
  });

  // 6 - Add payment with new currency and check details
  describe('Add payment with new currency and check details', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageMessagesBlock2', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockPaymentsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockPaymentsPage.pageTitle);
    });

    it('should check that the new currency is visible on select options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSelectOption', baseContext);

      const listOfCurrencies = await boOrdersViewBlockPaymentsPage.getCurrencySelectOptions(page);
      expect(listOfCurrencies).to.contain('€')
        .and.to.contain(dataCurrencies.mad.isoCode);
    });

    it('should add payment with new currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'paymentWithNewCurrency', baseContext);

      const validationMessage = await boOrdersViewBlockPaymentsPage.addPayment(page, paymentDataWithNewCurrency);
      expect(validationMessage, 'Successful message is not correct!')
        .to.equal(boOrdersViewBlockPaymentsPage.successfulUpdateMessage);
    });
  });

  // 7 - Click on payment accepted and check result
  describe('Click on payment accepted and check result', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters3', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer3', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 2);
      expect(textColumn).to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageMessagesBlock3', baseContext);

      await boOrdersPage.goToOrder(page, 2);

      const pageTitle = await boOrdersViewBlockPaymentsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockPaymentsPage.pageTitle);
    });

    it('should check that the payments number is equal to 0', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments5', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(0);
    });

    it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusPaymentAccepted', baseContext);

      const textResult = await boOrdersViewBlockPaymentsPage.modifyOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
      expect(textResult).to.equal(dataOrderStatuses.paymentAccepted.name);
    });

    it('should check that the payments number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments6', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the payment details and get the invoice number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPayment3', baseContext);

      const result = await boOrdersViewBlockPaymentsPage.getPaymentsDetails(page, 1);
      await Promise.all([
        expect(result.date).to.contain(todayToCheck),
        expect(result.paymentMethod).to.equal('Bank transfer'),
        expect(result.transactionID).to.equal(''),
        expect(result.amount).to.equal(`€${totalOrder}`),
        expect(result.invoice).to.not.equal(''),
      ]);

      invoiceID = await boOrdersViewBlockPaymentsPage.getInvoiceID(page);
    });

    it(`should add the product '${dataProducts.demo_5.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCustomizedProduct', baseContext);

      await boOrdersViewBlockProductsPage.searchProduct(page, dataProducts.demo_5.name);
      await boOrdersViewBlockProductsPage.selectInvoice(page);

      const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page, 1, true);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
    });

    it('should check that products number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await boOrdersViewBlockPaymentsPage.reloadPage(page);

      const productCount = await boOrdersViewBlockProductsPage.getProductsNumber(page);
      expect(productCount).to.equal(2);
    });

    it('should check that invoices number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber', baseContext);

      const documentsNumber = await boOrdersViewBlockTabListPage.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(2);
    });

    it('should check that payments number is equal to 1', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments7', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(1);
    });

    it('should check the warning message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWarningMessage', baseContext);

      const warningMessage = await boOrdersViewBlockPaymentsPage.getPaymentWarning(page);
      expect(warningMessage, 'Warning message is not correct!')
        .to.equal(`Warning €${totalOrder} paid instead of €57.74`);
    });

    it('should add payment when amount is equal to the rest to the new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'testAmountSupTotal2', baseContext);

      const invoice = `#IN0000${(invoiceID + 1) >= 10 ? '' : '0'}${invoiceID + 1}`;

      const validationMessage = await boOrdersViewBlockPaymentsPage.addPayment(page, paymentDataAmountEqualRest, invoice);
      expect(validationMessage, 'Successful message is not correct!')
        .to.equal(boOrdersViewBlockPaymentsPage.successfulUpdateMessage);
    });

    it('should check that payments number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkpayments8', baseContext);

      const paymentsNumber = await boOrdersViewBlockPaymentsPage.getPaymentsNumber(page);
      expect(paymentsNumber, 'Payments number is not correct! ').to.equal(2);
    });

    it('should download the invoice and check payment method and amount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadInvoiceAndCheckPayment', baseContext);

      // Download invoice
      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page, 3);
      expect(filePath).to.not.eq(null);

      const exist = await utilsFile.doesFileExist(filePath);
      expect(exist, 'File doesn\'t exist!').to.eq(true);

      const paymentMethodExist = await utilsFile.isTextInPDF(filePath, paymentDataAmountEqualRest.paymentMethod);
      expect(paymentMethodExist, 'Payment method does not exist in invoice!').to.eq(true);

      const amountExist = await utilsFile.isTextInPDF(filePath, paymentDataAmountEqualRest.amount.toString());
      expect(amountExist, 'Payment amount does not exist in invoice!').to.eq(true);
    });
  });

  // Post-condition - Delete currency
  deleteCurrencyTest(dataCurrencies.mad, `${baseContext}_postTest_1`);
});
