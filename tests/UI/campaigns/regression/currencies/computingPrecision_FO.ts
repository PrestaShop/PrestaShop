// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Common tests login BO
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';
import addSqlQueryPage from '@pages/BO/advancedParameters/database/sqlManager/add';
import viewSqlQueryPage from '@pages/BO/advancedParameters/database/sqlManager/view';
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
import currenciesPage from '@pages/BO/international/currencies';
import addCurrencyPage from '@pages/BO/international/currencies/add';
import localizationPage from '@pages/BO/international/localization';
import ordersPage from '@pages/BO/orders';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {homePage} from '@pages/FO/classic/home';
import {loginPage as foLoginPage} from '@pages/FO/classic/login';
import productPage from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

// Import data
import Currencies from '@data/demo/currencies';
import Customers from '@data/demo/customers';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import SqlQueryData from '@data/faker/sqlQuery';

const baseContext: string = 'regression_currencies_computingPrecision_FO';

// Browser and tab;

/*
Create 2 cart rules
  1. 15% discount
  2. Product 'Mug today is a good day' as a gift product
Change euro decimal to 3
Place an order in FO with the cart rules created
  1. Check discount value after first cart rule added
  2. Check discount value after second cart rule added
  3. Check ATI after second cart rule added
  4. Finish the order
Go Back To BO and check ATI price in Orders page
Create new sql query to check discount value and ATI price in database
 */
describe(
  'Regression - Currencies: Change currency precision and check orders total price in FO, BO and database',
  async () => {
    let browserContext: BrowserContext;
    let page: Page;

    const percentCartRule: CartRuleData = new CartRuleData({
      name: 'discount15',
      code: 'discount15',
      discountType: 'Percent',
      discountPercent: 15,
    });
    const giftCartRule: CartRuleData = new CartRuleData({
      name: 'freeGiftMug',
      code: 'freeMug',
      discountType: 'None',
      freeGift: true,
      freeGiftProduct: Products.demo_13,
    });
    // Create sql query data to get last order discount and total price
    const sqlQueryData = new SqlQueryData({
      name: 'Discount and ATI from last order',
      sqlQuery: '',
    });
    const sqlQueryTemplate = (orderRef: string) => 'SELECT total_discounts, total_paid_tax_incl '
      + `FROM  ${global.INSTALL.DB_PREFIX}orders WHERE reference = '${orderRef}'`;
    // Init data for the order
    const orderToMake: OrderData = new OrderData({
      products: [
        {
          product: Products.demo_3,
          quantity: 4,
        },
      ],
      discountPercentValue: 20.678,
      discountGiftValue: giftCartRule.freeGiftProduct?.price,
      totalPrice: 117.178,
    });

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

    describe('Create cart rules', async () => {
      it('should go to cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCartRulesPageToCreate', baseContext);

        await addCurrencyPage.goToSubMenu(
          page,
          addCurrencyPage.catalogParentLink,
          addCurrencyPage.discountsLink,
        );

        const pageTitle = await cartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(cartRulesPage.pageTitle);
      });

      describe('Create a percentage cart rule', async () => {
        it('should go to new cart rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage1', baseContext);

          await cartRulesPage.goToAddNewCartRulesPage(page);

          const pageTitle = await addCartRulePage.getPageTitle(page);
          expect(pageTitle).to.contains(addCartRulePage.pageTitle);
        });

        it('should create new cart rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createPercentCartRule', baseContext);

          const validationMessage = await addCartRulePage.createEditCartRules(page, percentCartRule);
          expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
        });
      });

      describe('Create a gift cart rule', async () => {
        it('should go to new cart rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

          await cartRulesPage.goToAddNewCartRulesPage(page);

          const pageTitle = await addCartRulePage.getPageTitle(page);
          expect(pageTitle).to.contains(addCartRulePage.pageTitle);
        });

        it('should create new cart rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createGiftCartRule', baseContext);

          const validationMessage = await addCartRulePage.createEditCartRules(page, giftCartRule);
          expect(validationMessage).to.contains(addCartRulePage.successfulCreationMessage);
        });
      });
    });

    describe('Change currency precision', async () => {
      it('should go to localization page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPageToChangePrecision', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );
        await localizationPage.closeSfToolBar(page);

        const pageTitle = await localizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should go to currencies page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPageToChangePrecision', baseContext);

        await localizationPage.goToSubTabCurrencies(page);

        const pageTitle = await currenciesPage.getPageTitle(page);
        expect(pageTitle).to.contains(currenciesPage.pageTitle);
      });

      it(`should filter by iso code '${Currencies.euro.isoCode}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterCurrenciesToChangePrecision', baseContext);

        // Filter
        await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.euro.isoCode);

        // Check number of currencies
        const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
        expect(numberOfCurrenciesAfterFilter).to.be.at.least(1);
      });

      it('should go to edit currency page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyToChangePrecision', baseContext);

        await currenciesPage.goToEditCurrencyPage(page, 1);

        const pageTitle = await addCurrencyPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCurrencyPage.editCurrencyPage);
      });

      it('should set precision to 3', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changePrecision', baseContext);

        // Set currency precision to 3 and check successful update message
        const textResult = await addCurrencyPage.setCurrencyPrecision(page, 3);
        expect(textResult).to.contains(currenciesPage.successfulUpdateMessage);
      });
    });

    describe('Place an order with discounts in FO', async () => {
      it('should go to FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

        page = await cartRulesPage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

        await homePage.goToLoginPage(page);

        const pageTitle = await foLoginPage.getPageTitle(page);
        expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginInFO', baseContext);

        await foLoginPage.customerLogin(page, Customers.johnDoe);

        const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Go to home page
        await foLoginPage.goToHomePage(page);
        // Go to product page after searching its name
        await homePage.searchProduct(page, orderToMake.products[0].product.name);
        await searchResultsPage.goToProductPage(page, 1);
        // Add the created product to the cart
        await productPage.addProductToTheCart(page, orderToMake.products[0].quantity);

        // Check cart page
        const pageTitle = await cartPage.getPageTitle(page);
        expect(pageTitle, 'Fail to go to cart page').to.contains(cartPage.pageTitle);
      });

      it('should add percent discount and check that the discount was added', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPercentDiscount', baseContext);

        await cartPage.addPromoCode(page, percentCartRule.code);
        const firstSubtotalDiscountValue = await cartPage.getSubtotalDiscountValue(page);

        expect(firstSubtotalDiscountValue, 'First discount was not applied')
          .to.equal(-(orderToMake.discountPercentValue));
      });

      it('should add free gift discount and check that the discount was added', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addGiftDiscount', baseContext);

        await cartPage.addPromoCode(page, giftCartRule.code);
        const finalSubtotalDiscountValue = await cartPage.getSubtotalDiscountValue(page);

        expect(finalSubtotalDiscountValue, 'Second discount was not applied')
          .to.equal(-(orderToMake.discountPercentValue + orderToMake.discountGiftValue));
      });

      it('should check order total price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkToTalPriceInFO', baseContext);

        const totalPrice = await cartPage.getATIPrice(page);
        expect(totalPrice, 'Order total price is incorrect')
          .to.equal(orderToMake.totalPrice);
      });

      it('should confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);

        // Check the confirmation message
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });
    });

    describe('Check order total price with precision in orders page', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

        // Close tab and init other page objects with new current tab
        page = await orderConfirmationPage.closePage(browserContext, page, 0);

        const pageTitle = await currenciesPage.getPageTitle(page);
        expect(pageTitle).to.contains(currenciesPage.pageTitle);
      });

      it('should go to orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await cartRulesPage.goToSubMenu(
          page,
          cartRulesPage.ordersParentLink,
          cartRulesPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should check order total price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkToTalPriceInBO', baseContext);

        // Get order reference to use in sql query
        orderToMake.reference = await ordersPage.getTextColumn(page, 'reference', 1);

        // Check total price
        const totalPriceInOrdersPage = await ordersPage.getOrderATIPrice(page, 1);
        expect(totalPriceInOrdersPage, 'Order total price is incorrect').to.equal(orderToMake.totalPrice);
      });
    });

    describe('Check order total price with precision in database', async () => {
      it('should go to sql manager page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

        await ordersPage.goToSubMenu(
          page,
          ordersPage.advancedParametersLink,
          ordersPage.databaseLink,
        );

        const pageTitle = await sqlManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
      });

      describe('Create new SQL query to get last order total price', async () => {
        it('should go to \'New SQL query\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCreateSqlQueryPage', baseContext);

          await sqlManagerPage.goToNewSQLQueryPage(page);
          // Adding order reference to sql query
          sqlQueryData.sqlQuery = sqlQueryTemplate(orderToMake.reference);

          const pageTitle = await addSqlQueryPage.getPageTitle(page);
          expect(pageTitle).to.contains(addSqlQueryPage.pageTitle);
        });

        it('should create new SQL query', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createSqlQuery', baseContext);

          const textResult = await addSqlQueryPage.createEditSQLQuery(page, sqlQueryData);
          expect(textResult).to.equal(addSqlQueryPage.successfulCreationMessage);
        });
      });

      describe('Check last order total price in database', async () => {
        it('should filter list by name', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterSqlQueriesToView', baseContext);

          await sqlManagerPage.resetFilter(page);
          await sqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

          const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
          expect(sqlQueryName).to.contains(sqlQueryData.name);
        });

        it('should click on view button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewSqlQuery', baseContext);

          await sqlManagerPage.goToViewSQLQueryPage(page, 1);

          const pageTitle = await viewSqlQueryPage.getPageTitle(page);
          expect(pageTitle).to.contains(viewSqlQueryPage.pageTitle);
        });

        it('should check order discount in database', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkLastOrderDiscountInDatabase', baseContext);

          // Get total discount from first column of the first row
          const discountInDatabase = await viewSqlQueryPage.getTextColumn(page, 1, 'total_discounts');
          expect(parseFloat(discountInDatabase), 'Discount price is incorrect in database')
            .to.equal(orderToMake.discountPercentValue + orderToMake.discountGiftValue);
        });
        it('should check last order total price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkToTalPriceInDatabase', baseContext);

          // Get total discount from second column of the first row
          const totalPriceInDatabase = await viewSqlQueryPage.getTextColumn(page, 1, 'total_paid_tax_incl');
          expect(parseFloat(totalPriceInDatabase), 'Total price is incorrect in database')
            .to.equal(orderToMake.totalPrice);
        });
      });
    });

    /*
      Reset Currency precision
      Delete cart rules created
      Delete Sql query created
    */
    describe('Reset currency precision and delete created data', async () => {
      describe('Reset currency precision', async () => {
        it('should go to localization page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPageToReset', baseContext);

          await sqlManagerPage.goToSubMenu(
            page,
            sqlManagerPage.internationalParentLink,
            sqlManagerPage.localizationLink,
          );

          const pageTitle = await localizationPage.getPageTitle(page);
          expect(pageTitle).to.contains(localizationPage.pageTitle);
        });

        it('should go to currencies page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPageToReset', baseContext);

          await localizationPage.goToSubTabCurrencies(page);

          const pageTitle = await currenciesPage.getPageTitle(page);
          expect(pageTitle).to.contains(currenciesPage.pageTitle);
        });

        it(`should filter by iso code '${Currencies.euro.isoCode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterCurrenciesToReset', baseContext);

          // Filter
          await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.euro.isoCode);

          // Check number of currencies
          const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
          expect(numberOfCurrenciesAfterFilter).to.be.at.least(1);
        });

        it('should go to edit currency page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyPageToReset', baseContext);

          await currenciesPage.goToEditCurrencyPage(page, 1);

          const pageTitle = await addCurrencyPage.getPageTitle(page);
          expect(pageTitle).to.contains(addCurrencyPage.editCurrencyPage);
        });

        it('should reset currency precision', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencyPrecision', baseContext);

          // Set currency precision to 2 and check successful update message
          const textResult = await addCurrencyPage.setCurrencyPrecision(page, 2);
          expect(textResult).to.contains(currenciesPage.successfulUpdateMessage);
        });
      });

      describe('Delete created cart rules', async () => {
        it('should go to cart rules page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCartRulesPageToDelete', baseContext);

          await currenciesPage.goToSubMenu(
            page,
            currenciesPage.catalogParentLink,
            currenciesPage.discountsLink,
          );

          const pageTitle = await cartRulesPage.getPageTitle(page);
          expect(pageTitle).to.contains(cartRulesPage.pageTitle);
        });

        it('should bulk delete cart rules', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRules', baseContext);

          const deleteTextResult = await cartRulesPage.bulkDeleteCartRules(page);
          expect(deleteTextResult).to.be.contains(cartRulesPage.successfulMultiDeleteMessage);
        });
      });

      describe('Delete SQL query', async () => {
        it('should go to \'SQL Manager\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPageToDelete', baseContext);

          await cartRulesPage.goToSubMenu(
            page,
            cartRulesPage.advancedParametersLink,
            cartRulesPage.databaseLink,
          );

          const pageTitle = await sqlManagerPage.getPageTitle(page);
          expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
        });

        it('should filter list by name', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterSQLQueriesToDelete', baseContext);

          await sqlManagerPage.resetFilter(page);
          await sqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

          const sqlQueryName = await sqlManagerPage.getTextColumnFromTable(page, 1, 'name');
          expect(sqlQueryName).to.contains(sqlQueryData.name);
        });

        it('should delete SQL query', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

          const textResult = await sqlManagerPage.deleteSQLQuery(page, 1);
          expect(textResult).to.equal(sqlManagerPage.successfulDeleteMessage);
        });
      });
    });
  });
