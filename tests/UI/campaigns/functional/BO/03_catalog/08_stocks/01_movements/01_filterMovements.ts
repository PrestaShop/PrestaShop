// Import utils
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import cleanTableStockMovements from '@commonTests/BO/catalog/stock';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import combinationsTab from '@pages/BO/catalog/products/add/combinationsTab';
import loginPage from '@pages/BO/login';
import ordersPage from '@pages/BO/orders';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import stocksPage from '@pages/BO/catalog/stocks';
import movementsPage from '@pages/BO/catalog/stocks/movements';
// Import FO pages
import {cartPage} from '@pages/FO/classic/cart';
import {checkoutPage} from '@pages/FO/classic/checkout';
import {orderConfirmationPage} from '@pages/FO/classic/checkout/orderConfirmation';
import {homePage} from '@pages/FO/classic/home';
import {productPage as foProductPage} from '@pages/FO/classic/product';

// Import data
import Categories from '@data/demo/categories';
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import EmployeeData from '@data/faker/employee';
import {ProductCombinationBulk} from '@data/types/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_movements_filterMovements';

describe('BO - Stocks - Movements : Filter by category, movement type, employee and period', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderId: number;
  let numElementsBeforeFilter: number;

  const employeeData: EmployeeData = new EmployeeData({
    defaultPage: 'Dashboard',
    language: 'English (English)',
    permissionProfile: 'SuperAdmin',
  });
  const editCombinationsData: ProductCombinationBulk = {
    stocks: {
      quantityToEnable: true,
      quantity: 10,
      minimalQuantityToEnable: false,
      stockLocationToEnable: false,
    },
    retailPrice: {
      costPriceToEnable: false,
      impactOnPriceTIncToEnable: false,
      impactOnWeightToEnable: false,
    },
    specificReferences: {
      referenceToEnable: false,
    },
  };
  const dateYesterday: string = date.getDateFormat('yyyy-mm-dd', 'yesterday');
  const dateToday: string = date.getDateFormat('yyyy-mm-dd');
  const dateTomorrow: string = date.getDateFormat('yyyy-mm-dd', 'tomorrow');

  // Pre-condition: Create new employee
  createEmployeeTest(employeeData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Check all filters', async () => {
    describe('BO - Bulk edit quantity by setting input value', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.stocksLink,
        );
        await stocksPage.closeSfToolBar(page);

        const pageTitle = await stocksPage.getPageTitle(page);
        expect(pageTitle).to.contains(stocksPage.pageTitle);
      });

      it('should add to quantities by setting input value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addToQuantities', baseContext);

        const updateMessage = await stocksPage.bulkEditQuantityWithInput(page, 120);
        expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
      });
    });

    describe('BO - Check Filter "Movement Type" after Employee Edition', async () => {
      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPage', baseContext);

        await stocksPage.goToSubTabMovements(page);

        const pageTitle = await movementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(movementsPage.pageTitle);
      });

      it('should check the filter "Movement Type"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFilterMovementType', baseContext);

        await movementsPage.setAdvancedFiltersVisible(page);

        const isAdvancedFiltersVisible = await movementsPage.isAdvancedFiltersVisible(page);
        expect(isAdvancedFiltersVisible).to.be.eq(true);

        const choices = await movementsPage.getAdvancedFiltersMovementTypeChoices(page);
        expect(choices).to.be.length(2);
        expect(choices).to.contains('None');
        expect(choices).to.contains('Employee Edition');
      });
    });

    describe('FO - Make an order', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

        page = await movementsPage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const pageTitle = await homePage.getPageTitle(page);
        expect(pageTitle).to.contains(homePage.pageTitle);
      });

      it('should go to the first product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProduct', baseContext);

        // Go to the first product page
        await homePage.goToProductPage(page, 1);

        const pageTitle = await foProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(Products.demo_1.name);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Add the created product to the cart
        await foProductPage.addProductToTheCart(page);

        const pageTitle = await cartPage.getPageTitle(page);
        expect(pageTitle).to.equal(cartPage.pageTitle);
      });

      it('should proceed to checkout and sign in by default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

        // Proceed to checkout the shopping cart
        await cartPage.clickOnProceedToCheckout(page);

        // Personal information step - Login
        await checkoutPage.clickOnSignIn(page);
        await checkoutPage.customerLogin(page, Customers.johnDoe);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await checkoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.be.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await checkoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await checkoutPage.choosePaymentAndOrder(page, PaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await orderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(orderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        // Close tab and init other page objects with new current tab
        page = await orderConfirmationPage.closePage(browserContext, page, 0);

        const pageTitle = await movementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(movementsPage.pageTitle);
      });
    });

    describe('BO - Change the status to delivered', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should reset filter and get the last order ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

        await ordersPage.resetFilter(page);

        const result: string = await ordersPage.getTextColumn(page, 'id_order', 1);
        orderId = parseInt(result, 10);
        expect(orderId).to.be.at.least(1);
      });

      it('should update order status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const textResult = await ordersPage.setOrderStatus(page, 1, OrderStatuses.delivered);
        expect(textResult).to.equal(ordersPage.successfulUpdateMessage);
      });
    });

    describe('BO - Check Filter "Movement Type" after FO Order', async () => {
      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPageAfterFOOrder', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.stocksLink,
        );
        await stocksPage.closeSfToolBar(page);

        const pageTitle = await stocksPage.getPageTitle(page);
        expect(pageTitle).to.contains(stocksPage.pageTitle);
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPageAfterFOOrder', baseContext);

        await stocksPage.goToSubTabMovements(page);

        const pageTitle = await movementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(movementsPage.pageTitle);
      });

      it('should check the filter "Movement Type"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFilterMovementTypeAfterFOOrder', baseContext);

        await movementsPage.setAdvancedFiltersVisible(page);

        const choices = await movementsPage.getAdvancedFiltersMovementTypeChoices(page);
        expect(choices).to.be.length(3);
        expect(choices).to.contains('None');
        expect(choices).to.contains('Employee Edition');
        expect(choices).to.contains('Customer Order');
      });
    });

    describe('BO - Filter by "Movement Type" to "Customer Order\'', async () => {
      it('should set the filter "Movement Type" to "Customer Order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterMovementTypeCustomerOrder', baseContext);

        await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersMovementType(page, 'Customer Order');

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.equal(1);
      });

      it('should check the filtered row', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFilteredRow', baseContext);

        const name = await movementsPage.getTextColumnFromTable(page, 1, 'product_name');
        expect(name).to.contains(Products.demo_1.name);

        const reference = await movementsPage.getTextColumnFromTable(page, 1, 'reference');
        expect(reference).to.be.equal(`${Products.demo_1.reference} ${Products.demo_1.reference}`);

        const quantity = await movementsPage.getTextColumnFromTable(page, 1, 'quantity');
        expect(quantity).to.be.equal('-1');
      });

      it('should click on the link from the Column Type', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickLinkColumnType', baseContext);

        page = await movementsPage.clickOnMovementTypeLink(page, 1);

        const pageTitle = await orderPageProductsBlock.getPageTitle(page);
        expect(pageTitle).to.contains(orderPageProductsBlock.pageTitle);
      });

      it('should close the new tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeTabViewOrder', baseContext);

        page = await orderPageProductsBlock.closePage(browserContext, page, 0);

        const pageTitle = await movementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(movementsPage.pageTitle);
      });

      it('should reset the filter "Movement Type"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterMovementType', baseContext);

        await movementsPage.resetAdvancedFilter(page);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.gt(1);
      });
    });

    describe(`BO - Edit a product with the employee ${employeeData.email}`, async () => {
      it(`should logout from the employee "${global.BO.EMAIL}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutFromBOPage', baseContext);

        await loginCommon.logoutBO(this, page);

        const pageTitle = await loginPage.getPageTitle(page);
        expect(pageTitle).to.contains(loginPage.pageTitle);
      });

      it(`should login from the employee "${employeeData.email}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBOPageWithEmployee', baseContext);

        await loginCommon.loginBO(this, page, employeeData.email, employeeData.password);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it(`should filter by name '${Products.demo_8.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

        await productsPage.filterProducts(page, 'product_name', Products.demo_8.name);

        const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);
      });

      it('should go to the product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should go to the Combinations tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCombinationsTab', baseContext);

        await addProductPage.goToTab(page, 'combinations');

        const isTabActive = await addProductPage.isTabActive(page, 'combinations');
        expect(isTabActive).to.eq(true);
      });

      it(`should add ${editCombinationsData.stocks.quantity} to 4 combinations`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addQuantityToAllCombinations', baseContext);

        const isBulkActionsButtonVisible = await combinationsTab.selectAllCombinations(page);
        expect(isBulkActionsButtonVisible).to.be.eq(true);

        const modalTitle = await combinationsTab.clickOnEditCombinationsByBulkActions(page);
        expect(modalTitle).to.equal(combinationsTab.editCombinationsModalTitle(4));

        const successMessage = await combinationsTab.editCombinationsByBulkActions(page, editCombinationsData);
        expect(successMessage).to.equal(combinationsTab.editCombinationsModalMessage(4));
      });

      it(`should logout from the employee "${employeeData.email}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutFromBOPageWithEmployee', baseContext);

        await loginCommon.logoutBO(this, page);

        const pageTitle = await loginPage.getPageTitle(page);
        expect(pageTitle).to.contains(loginPage.pageTitle);
      });

      it(`should login from the employee "${global.BO.EMAIL}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBOPage', baseContext);

        await loginCommon.loginBO(this, page);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });
    });

    describe('BO - Check Filter "Employee"', async () => {
      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPageAfterEmployeeEdition', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.stocksLink,
        );
        await stocksPage.closeSfToolBar(page);

        const pageTitle = await stocksPage.getPageTitle(page);
        expect(pageTitle).to.contains(stocksPage.pageTitle);
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPageAfterEmployeeEdition', baseContext);

        await stocksPage.goToSubTabMovements(page);

        const pageTitle = await movementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(movementsPage.pageTitle);
      });

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34337
      it(`should set the filter "Employee" to "${employeeData.lastName} ${employeeData.firstName}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterEmployee', baseContext);

        this.skip();

        /*await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.gt(0);

        for (let i = 1; i <= numElements; i++) {
          const textColumn = await movementsPage.getTextColumnFromTable(page, i, 'product_name');
          expect(textColumn).to.contains(Products.demo_8.name);
        }*/
      });

      it('should reset the filter "Employee"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterEmployee', baseContext);

        await movementsPage.resetAdvancedFilter(page);

        numElementsBeforeFilter = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElementsBeforeFilter).to.be.gt(0);
      });
    });

    describe('BO - Check Filter "Period"', async () => {
      it(`should set the filter "Period" to "${dateYesterday}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterPeriodToYesterday', baseContext);

        await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await movementsPage.setAdvancedFiltersDate(page, 'inf', dateYesterday, true);

        const textContent = await movementsPage.getTextForEmptyTable(page);
        expect(textContent).to.be.eq(movementsPage.emptyTableMessage);
      });

      it(`should set the filter "Period" to "${dateToday}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterPeriodToToday', baseContext);

        await movementsPage.resetAdvancedFilter(page);
        await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await movementsPage.setAdvancedFiltersDate(page, 'sup', dateToday, true);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });

      it(`should set the filter "Period" to "${dateTomorrow}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterPeriodToTomorrow', baseContext);

        await movementsPage.resetAdvancedFilter(page);
        await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await movementsPage.setAdvancedFiltersDate(page, 'sup', dateTomorrow, true);

        const textContent = await movementsPage.getTextForEmptyTable(page);
        expect(textContent).to.be.eq(movementsPage.emptyTableMessage);
      });

      it('should reset the filter "Period"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterPeriod', baseContext);

        await movementsPage.resetAdvancedFilter(page);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/34334
    describe('BO - Check Filter "Categories"', async () => {
      it(`should set the filter "Categories" to "${Categories.clothes.name}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterCategories', baseContext);

        this.skip();

        await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await movementsPage.setAdvancedFiltersCategory(page, Categories.clothes.name, true);

        const textContent = await movementsPage.getTextForEmptyTable(page);
        expect(textContent).to.be.eq(movementsPage.emptyTableMessage);
      });

      it('should reset the filter "Categories"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

        this.skip();

        await movementsPage.resetAdvancedFilter(page);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });
    });

    describe('BO - Disable a product', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageForDisabling', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it(`should filter by name '${Products.demo_8.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEditForDisabling', baseContext);

        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'product_name', Products.demo_8.name);

        const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);
      });

      it('should go to the product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageForDisabling', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should disable the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableProduct', baseContext);

        await addProductPage.setProductStatus(page, false);

        const updateProductMessage = await addProductPage.saveProduct(page);
        expect(updateProductMessage).to.equal(addProductPage.successfulUpdateMessage);
      });
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/33842
    describe('BO - Check Filter "Status"', async () => {
      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPageAfterDisablingProduct', baseContext);

        this.skip();

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.stocksLink,
        );
        await stocksPage.closeSfToolBar(page);

        const pageTitle = await stocksPage.getPageTitle(page);
        expect(pageTitle).to.contains(stocksPage.pageTitle);
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPageAfterDisablingProduct', baseContext);

        this.skip();

        await stocksPage.goToSubTabMovements(page);

        const pageTitle = await movementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(movementsPage.pageTitle);
      });

      it('should set the filter "Status" to "Disabled"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterStatus', baseContext);

        this.skip();

        await movementsPage.setAdvancedFiltersVisible(page);
        await movementsPage.setAdvancedFiltersStatus(page, false);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(4);
      });

      it('should reset the filter "Status"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterStatus', baseContext);

        this.skip();

        await movementsPage.resetAdvancedFilter(page);

        const numElements = await movementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });
    });

    describe('BO - Enable a product', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageForEnabling', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it(`should filter by name '${Products.demo_8.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEditForEnabling', baseContext);

        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'product_name', Products.demo_8.name);

        const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);
      });

      it('should go to the product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageForEnabling', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle = await addProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should enable the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableProduct', baseContext);

        await addProductPage.setProductStatus(page, true);

        const updateProductMessage = await addProductPage.saveProduct(page);
        expect(updateProductMessage).to.equal(addProductPage.successfulUpdateMessage);
      });
    });
  });

  // Post-Condition : Clean Stock Movements
  cleanTableStockMovements(`${baseContext}_postTest_1`);

  // Post-Condition : Delete employee
  deleteEmployeeTest(employeeData, `${baseContext}_postTest_2`);
});
