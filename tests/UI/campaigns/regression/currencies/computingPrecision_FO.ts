import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boCurrenciesPage,
  boCurrenciesCreatePage,
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  boOrdersPage,
  boSqlManagerPage,
  boSqlManagerCreatePage,
  boSqlManagerViewPage,
  type BrowserContext,
  dataCurrencies,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerCartRule,
  FakerOrder,
  FakerSqlQuery,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

    const percentCartRule: FakerCartRule = new FakerCartRule({
      name: 'discount15',
      code: 'discount15',
      discountType: 'Percent',
      discountPercent: 15,
    });
    const giftCartRule: FakerCartRule = new FakerCartRule({
      name: 'freeGiftMug',
      code: 'freeMug',
      discountType: 'None',
      freeGift: true,
      freeGiftProduct: dataProducts.demo_13,
    });
    // Create sql query data to get last order discount and total price
    const sqlQueryData: FakerSqlQuery = new FakerSqlQuery({
      name: 'Discount and ATI from last order',
      sqlQuery: '',
    });
    const sqlQueryTemplate = (orderRef: string) => 'SELECT total_discounts, total_paid_tax_incl '
      + `FROM  ${global.INSTALL.DB_PREFIX}orders WHERE reference = '${orderRef}'`;
    // Init data for the order
    const orderToMake: FakerOrder = new FakerOrder({
      products: [
        {
          product: dataProducts.demo_3,
          quantity: 4,
        },
      ],
      discountPercentValue: 20.678,
      discountGiftValue: giftCartRule.freeGiftProduct?.finalPrice,
      totalPrice: 117.178,
    });

    // before and after functions
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    describe('Create cart rules', async () => {
      it('should go to cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCartRulesPageToCreate', baseContext);

        await boCurrenciesCreatePage.goToSubMenu(
          page,
          boCurrenciesCreatePage.catalogParentLink,
          boCurrenciesCreatePage.discountsLink,
        );

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      describe('Create a percentage cart rule', async () => {
        it('should go to new cart rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage1', baseContext);

          await boCartRulesPage.goToAddNewCartRulesPage(page);

          const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
        });

        it('should create new cart rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createPercentCartRule', baseContext);

          const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, percentCartRule);
          expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
        });
      });

      describe('Create a gift cart rule', async () => {
        it('should go to new cart rule page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage2', baseContext);

          await boCartRulesPage.goToAddNewCartRulesPage(page);

          const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
        });

        it('should create new cart rule', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createGiftCartRule', baseContext);

          const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, giftCartRule);
          expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
        });
      });
    });

    describe('Change currency precision', async () => {
      it('should go to localization page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPageToChangePrecision', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.internationalParentLink,
          boDashboardPage.localizationLink,
        );
        await boLocalizationPage.closeSfToolBar(page);

        const pageTitle = await boLocalizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
      });

      it('should go to currencies page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPageToChangePrecision', baseContext);

        await boLocalizationPage.goToSubTabCurrencies(page);

        const pageTitle = await boCurrenciesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
      });

      it(`should filter by iso code '${dataCurrencies.euro.isoCode}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterCurrenciesToChangePrecision', baseContext);

        // Filter
        await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

        // Check number of currencies
        const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
        expect(numberOfCurrenciesAfterFilter).to.be.at.least(1);
      });

      it('should go to edit currency page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyToChangePrecision', baseContext);

        await boCurrenciesPage.goToEditCurrencyPage(page, 1);

        const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCurrenciesCreatePage.editCurrencyPage);
      });

      it('should set precision to 3', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changePrecision', baseContext);

        // Set currency precision to 3 and check successful update message
        const textResult = await boCurrenciesCreatePage.setCurrencyPrecision(page, 3);
        expect(textResult).to.contains(boCurrenciesPage.successfulUpdateMessage);
      });
    });

    describe('Place an order with discounts in FO', async () => {
      it('should go to FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

        page = await boCartRulesPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFOLoginPage', baseContext);

        await foClassicHomePage.goToLoginPage(page);

        const pageTitle = await foClassicLoginPage.getPageTitle(page);
        expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginInFO', baseContext);

        await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

        const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Go to home page
        await foClassicLoginPage.goToHomePage(page);
        // Go to product page after searching its name
        await foClassicHomePage.searchProduct(page, orderToMake.products[0].product.name);
        await foClassicSearchResultsPage.goToProductPage(page, 1);
        // Add the created product to the cart
        await foClassicProductPage.addProductToTheCart(page, orderToMake.products[0].quantity);

        // Check cart page
        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle, 'Fail to go to cart page').to.contains(foClassicCartPage.pageTitle);
      });

      it('should add percent discount and check that the discount was added', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPercentDiscount', baseContext);

        await foClassicCartPage.addPromoCode(page, percentCartRule.code);
        const firstSubtotalDiscountValue = await foClassicCartPage.getSubtotalDiscountValue(page);

        expect(firstSubtotalDiscountValue, 'First discount was not applied')
          .to.equal(-(orderToMake.discountPercentValue));
      });

      it('should add free gift discount and check that the discount was added', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addGiftDiscount', baseContext);

        await foClassicCartPage.addPromoCode(page, giftCartRule.code);
        const finalSubtotalDiscountValue = await foClassicCartPage.getSubtotalDiscountValue(page);

        expect(finalSubtotalDiscountValue, 'Second discount was not applied')
          .to.equal(-(orderToMake.discountPercentValue + orderToMake.discountGiftValue));
      });

      it('should check order total price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkToTalPriceInFO', baseContext);

        const totalPrice = await foClassicCartPage.getATIPrice(page);
        expect(totalPrice, 'Order total price is incorrect')
          .to.equal(orderToMake.totalPrice);
      });

      it('should confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);

        // Check the confirmation message
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });
    });

    describe('Check order total price with precision in orders page', async () => {
      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

        const pageTitle = await boCurrenciesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
      });

      it('should go to orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await boCartRulesPage.goToSubMenu(
          page,
          boCartRulesPage.ordersParentLink,
          boCartRulesPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should check order total price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkToTalPriceInBO', baseContext);

        // Get order reference to use in sql query
        orderToMake.setReference(await boOrdersPage.getTextColumn(page, 'reference', 1));

        // Check total price
        const totalPriceInOrdersPage = await boOrdersPage.getOrderATIPrice(page, 1);
        expect(totalPriceInOrdersPage, 'Order total price is incorrect').to.equal(orderToMake.totalPrice);
      });
    });

    describe('Check order total price with precision in database', async () => {
      it('should go to sql manager page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

        await boOrdersPage.goToSubMenu(
          page,
          boOrdersPage.advancedParametersLink,
          boOrdersPage.databaseLink,
        );

        const pageTitle = await boSqlManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
      });

      describe('Create new SQL query to get last order total price', async () => {
        it('should go to \'New SQL query\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCreateSqlQueryPage', baseContext);

          await boSqlManagerPage.goToNewSQLQueryPage(page);
          // Adding order reference to sql query
          sqlQueryData.sqlQuery = sqlQueryTemplate(orderToMake.reference);

          const pageTitle = await boSqlManagerCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boSqlManagerCreatePage.pageTitle);
        });

        it('should create new SQL query', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'createSqlQuery', baseContext);

          const textResult = await boSqlManagerCreatePage.createEditSQLQuery(page, sqlQueryData);
          expect(textResult).to.equal(boSqlManagerCreatePage.successfulCreationMessage);
        });
      });

      describe('Check last order total price in database', async () => {
        it('should filter list by name', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterSqlQueriesToView', baseContext);

          await boSqlManagerPage.resetFilter(page);
          await boSqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

          const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
          expect(sqlQueryName).to.contains(sqlQueryData.name);
        });

        it('should click on view button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewSqlQuery', baseContext);

          await boSqlManagerPage.goToViewSQLQueryPage(page, 1);

          const pageTitle = await boSqlManagerViewPage.getPageTitle(page);
          expect(pageTitle).to.contains(boSqlManagerViewPage.pageTitle);
        });

        it('should check order discount in database', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkLastOrderDiscountInDatabase', baseContext);

          // Get total discount from first column of the first row
          const discountInDatabase = await boSqlManagerViewPage.getTextColumn(page, 1, 'total_discounts');
          expect(parseFloat(discountInDatabase), 'Discount price is incorrect in database')
            .to.equal(orderToMake.discountPercentValue + orderToMake.discountGiftValue);
        });
        it('should check last order total price', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkToTalPriceInDatabase', baseContext);

          // Get total discount from second column of the first row
          const totalPriceInDatabase = await boSqlManagerViewPage.getTextColumn(page, 1, 'total_paid_tax_incl');
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

          await boSqlManagerPage.goToSubMenu(
            page,
            boSqlManagerPage.internationalParentLink,
            boSqlManagerPage.localizationLink,
          );

          const pageTitle = await boLocalizationPage.getPageTitle(page);
          expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
        });

        it('should go to currencies page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPageToReset', baseContext);

          await boLocalizationPage.goToSubTabCurrencies(page);

          const pageTitle = await boCurrenciesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
        });

        it(`should filter by iso code '${dataCurrencies.euro.isoCode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterCurrenciesToReset', baseContext);

          // Filter
          await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

          // Check number of currencies
          const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
          expect(numberOfCurrenciesAfterFilter).to.be.at.least(1);
        });

        it('should go to edit currency page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyPageToReset', baseContext);

          await boCurrenciesPage.goToEditCurrencyPage(page, 1);

          const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCurrenciesCreatePage.editCurrencyPage);
        });

        it('should reset currency precision', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencyPrecision', baseContext);

          // Set currency precision to 2 and check successful update message
          const textResult = await boCurrenciesCreatePage.setCurrencyPrecision(page, 2);
          expect(textResult).to.contains(boCurrenciesPage.successfulUpdateMessage);
        });
      });

      describe('Delete created cart rules', async () => {
        it('should go to cart rules page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToCartRulesPageToDelete', baseContext);

          await boCurrenciesPage.goToSubMenu(
            page,
            boCurrenciesPage.catalogParentLink,
            boCurrenciesPage.discountsLink,
          );

          const pageTitle = await boCartRulesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
        });

        it('should bulk delete cart rules', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRules', baseContext);

          const deleteTextResult = await boCartRulesPage.bulkDeleteCartRules(page);
          expect(deleteTextResult).to.be.contains(boCartRulesPage.successfulMultiDeleteMessage);
        });
      });

      describe('Delete SQL query', async () => {
        it('should go to \'SQL Manager\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPageToDelete', baseContext);

          await boCartRulesPage.goToSubMenu(
            page,
            boCartRulesPage.advancedParametersLink,
            boCartRulesPage.databaseLink,
          );

          const pageTitle = await boSqlManagerPage.getPageTitle(page);
          expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
        });

        it('should filter list by name', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterSQLQueriesToDelete', baseContext);

          await boSqlManagerPage.resetFilter(page);
          await boSqlManagerPage.filterSQLQuery(page, 'name', sqlQueryData.name);

          const sqlQueryName = await boSqlManagerPage.getTextColumnFromTable(page, 1, 'name');
          expect(sqlQueryName).to.contains(sqlQueryData.name);
        });

        it('should delete SQL query', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteSQLQuery', baseContext);

          const textResult = await boSqlManagerPage.deleteSQLQuery(page, 1);
          expect(textResult).to.equal(boSqlManagerPage.successfulDeleteMessage);
        });
      });
    });
  });
