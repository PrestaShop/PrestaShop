import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createEmployeeTest, deleteEmployeeTest} from '@commonTests/BO/advancedParameters/employee';
import cleanTableStockMovements from '@commonTests/BO/catalog/stock';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boStockPage,
  boStockMovementsPage,
  type BrowserContext,
  dataCategories,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerEmployee,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  type ProductCombinationBulk,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_stocks_movements_filterMovements';

describe('BO - Stocks - Movements : Filter by category, movement type, employee and period', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let orderId: number;
  let numElementsBeforeFilter: number;

  const employeeData: FakerEmployee = new FakerEmployee({
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
  const dateYesterday: string = utilsDate.getDateFormat('yyyy-mm-dd', 'yesterday');
  const dateToday: string = utilsDate.getDateFormat('yyyy-mm-dd');
  const dateTomorrow: string = utilsDate.getDateFormat('yyyy-mm-dd', 'tomorrow');

  // Pre-condition: Create new employee
  createEmployeeTest(employeeData, `${baseContext}_preTest_1`);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check all filters', async () => {
    describe('BO - Bulk edit quantity by setting input value', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.stocksLink,
        );
        await boStockPage.closeSfToolBar(page);

        const pageTitle = await boStockPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockPage.pageTitle);
      });

      it('should add to quantities by setting input value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addToQuantities', baseContext);

        const updateMessage = await boStockPage.bulkEditQuantityWithInput(page, 120);
        expect(updateMessage).to.contains(boStockPage.successfulUpdateMessage);
      });
    });

    describe('BO - Check Filter "Movement Type" after Employee Edition', async () => {
      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPage', baseContext);

        await boStockPage.goToSubTabMovements(page);

        const pageTitle = await boStockMovementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockMovementsPage.pageTitle);
      });

      it('should check the filter "Movement Type"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFilterMovementType', baseContext);

        await boStockMovementsPage.setAdvancedFiltersVisible(page);

        const isAdvancedFiltersVisible = await boStockMovementsPage.isAdvancedFiltersVisible(page);
        expect(isAdvancedFiltersVisible).to.be.eq(true);

        const choices = await boStockMovementsPage.getAdvancedFiltersMovementTypeChoices(page);
        expect(choices).to.be.length(2);
        expect(choices).to.contains('None');
        expect(choices).to.contains('Employee Edition');
      });
    });

    describe('FO - Make an order', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

        page = await boStockMovementsPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const pageTitle = await foClassicHomePage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicHomePage.pageTitle);
      });

      it('should go to the first product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFirstProduct', baseContext);

        // Go to the first product page
        await foClassicHomePage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(dataProducts.demo_1.name);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Add the created product to the cart
        await foClassicProductPage.addProductToTheCart(page);

        const pageTitle = await foClassicCartPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicCartPage.pageTitle);
      });

      it('should proceed to checkout and sign in by default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'proceedToCheckoutAndSignIn', baseContext);

        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Personal information step - Login
        await foClassicCheckoutPage.clickOnSignIn(page);
        await foClassicCheckoutPage.customerLogin(page, dataCustomers.johnDoe);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.be.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.be.eq(true);
      });

      it('should choose payment method and confirm the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

        // Payment step - Choose payment step
        await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

        // Check the confirmation message
        const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
        expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);

        const pageTitle = await boStockMovementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockMovementsPage.pageTitle);
      });
    });

    describe('BO - Change the status to delivered', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should reset filter and get the last order ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

        await boOrdersPage.resetFilter(page);

        const result: string = await boOrdersPage.getTextColumn(page, 'id_order', 1);
        orderId = parseInt(result, 10);
        expect(orderId).to.be.at.least(1);
      });

      it('should update order status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

        const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
        expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
      });
    });

    describe('BO - Check Filter "Movement Type" after FO Order', async () => {
      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPageAfterFOOrder', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.stocksLink,
        );
        await boStockPage.closeSfToolBar(page);

        const pageTitle = await boStockPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockPage.pageTitle);
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPageAfterFOOrder', baseContext);

        await boStockPage.goToSubTabMovements(page);

        const pageTitle = await boStockMovementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockMovementsPage.pageTitle);
      });

      it('should check the filter "Movement Type"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFilterMovementTypeAfterFOOrder', baseContext);

        await boStockMovementsPage.setAdvancedFiltersVisible(page);

        const choices = await boStockMovementsPage.getAdvancedFiltersMovementTypeChoices(page);
        expect(choices).to.be.length(3);
        expect(choices).to.contains('None');
        expect(choices).to.contains('Employee Edition');
        expect(choices).to.contains('Customer Order');
      });
    });

    describe('BO - Filter by "Movement Type" to "Customer Order\'', async () => {
      it('should set the filter "Movement Type" to "Customer Order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterMovementTypeCustomerOrder', baseContext);

        await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersMovementType(page, 'Customer Order');

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.equal(1);
      });

      it('should check the filtered row', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFilteredRow', baseContext);

        const name = await boStockMovementsPage.getTextColumnFromTable(page, 1, 'product_name');
        expect(name).to.contains(dataProducts.demo_1.name);

        const reference = await boStockMovementsPage.getTextColumnFromTable(page, 1, 'reference');
        expect(reference).to.be.equal(`${dataProducts.demo_1.reference} ${dataProducts.demo_1.reference}`);

        const quantity = await boStockMovementsPage.getTextColumnFromTable(page, 1, 'quantity');
        expect(quantity).to.be.equal('-1');
      });

      it('should click on the link from the Column Type', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickLinkColumnType', baseContext);

        page = await boStockMovementsPage.clickOnMovementTypeLink(page, 1);

        const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
      });

      it('should close the new tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeTabViewOrder', baseContext);

        page = await boOrdersViewBlockProductsPage.closePage(browserContext, page, 0);

        const pageTitle = await boStockMovementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockMovementsPage.pageTitle);
      });

      it('should reset the filter "Movement Type"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterMovementType', baseContext);

        await boStockMovementsPage.resetAdvancedFilter(page);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.gt(1);
      });
    });

    describe(`BO - Edit a product with the employee ${employeeData.email}`, async () => {
      it(`should logout from the employee "${global.BO.EMAIL}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutFromBOPage', baseContext);

        await boDashboardPage.logoutBO(page);

        const pageTitle = await boLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLoginPage.pageTitle);
      });

      it(`should login from the employee "${employeeData.email}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBOPageWithEmployee', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, employeeData.email, employeeData.password);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it(`should filter by name '${dataProducts.demo_8.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

        await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_8.name);

        const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);
      });

      it('should go to the product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should go to the Combinations tab', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCombinationsTab', baseContext);

        await boProductsCreatePage.goToTab(page, 'combinations');

        const isTabActive = await boProductsCreatePage.isTabActive(page, 'combinations');
        expect(isTabActive).to.eq(true);
      });

      it(`should add ${editCombinationsData.stocks.quantity} to 4 combinations`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addQuantityToAllCombinations', baseContext);

        const isBulkActionsButtonVisible = await boProductsCreateTabCombinationsPage.selectAllCombinations(page);
        expect(isBulkActionsButtonVisible).to.be.eq(true);

        const modalTitle = await boProductsCreateTabCombinationsPage.clickOnEditCombinationsByBulkActions(page);
        expect(modalTitle).to.equal(boProductsCreateTabCombinationsPage.editCombinationsModalTitle(4));

        const successMessage = await boProductsCreateTabCombinationsPage.editCombinationsByBulkActions(
          page,
          editCombinationsData,
        );
        expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.editCombinationsModalMessage(4));
      });

      it(`should logout from the employee "${employeeData.email}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'logoutFromBOPageWithEmployee', baseContext);

        await boDashboardPage.logoutBO(page);

        const pageTitle = await boLoginPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLoginPage.pageTitle);
      });

      it(`should login from the employee "${global.BO.EMAIL}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBOPage', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });
    });

    describe('BO - Check Filter "Employee"', async () => {
      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPageAfterEmployeeEdition', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.stocksLink,
        );
        await boStockPage.closeSfToolBar(page);

        const pageTitle = await boStockPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockPage.pageTitle);
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPageAfterEmployeeEdition', baseContext);

        await boStockPage.goToSubTabMovements(page);

        const pageTitle = await boStockMovementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockMovementsPage.pageTitle);
      });

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34337
      it(`should set the filter "Employee" to "${employeeData.lastName} ${employeeData.firstName}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterEmployee', baseContext);

        this.skip();

        /*await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.gt(0);

        for (let i = 1; i <= numElements; i++) {
          const textColumn = await boStockMovementsPage.getTextColumnFromTable(page, i, 'product_name');
          expect(textColumn).to.contains(dataProducts.demo_8.name);
        }*/
      });

      it('should reset the filter "Employee"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterEmployee', baseContext);

        await boStockMovementsPage.resetAdvancedFilter(page);

        numElementsBeforeFilter = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElementsBeforeFilter).to.be.gt(0);
      });
    });

    describe('BO - Check Filter "Period"', async () => {
      it(`should set the filter "Period" to "${dateYesterday}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterPeriodToYesterday', baseContext);

        await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await boStockMovementsPage.setAdvancedFiltersDate(page, 'inf', dateYesterday, true);

        const textContent = await boStockMovementsPage.getTextForEmptyTable(page);
        expect(textContent).to.be.eq(boStockMovementsPage.emptyTableMessage);
      });

      it(`should set the filter "Period" to "${dateToday}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterPeriodToToday', baseContext);

        await boStockMovementsPage.resetAdvancedFilter(page);
        await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await boStockMovementsPage.setAdvancedFiltersDate(page, 'sup', dateToday, true);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });

      it(`should set the filter "Period" to "${dateTomorrow}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterPeriodToTomorrow', baseContext);

        await boStockMovementsPage.resetAdvancedFilter(page);
        await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await boStockMovementsPage.setAdvancedFiltersDate(page, 'sup', dateTomorrow, true);

        const textContent = await boStockMovementsPage.getTextForEmptyTable(page);
        expect(textContent).to.be.eq(boStockMovementsPage.emptyTableMessage);
      });

      it('should reset the filter "Period"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterPeriod', baseContext);

        await boStockMovementsPage.resetAdvancedFilter(page);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/34334
    describe('BO - Check Filter "Categories"', async () => {
      it(`should set the filter "Categories" to "${dataCategories.clothes.name}"`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterCategories', baseContext);

        this.skip();

        await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersEmployee(page, `${employeeData.lastName} ${employeeData.firstName}`);
        await boStockMovementsPage.setAdvancedFiltersCategory(page, dataCategories.clothes.name, true);

        const textContent = await boStockMovementsPage.getTextForEmptyTable(page);
        expect(textContent).to.be.eq(boStockMovementsPage.emptyTableMessage);
      });

      it('should reset the filter "Categories"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

        this.skip();

        await boStockMovementsPage.resetAdvancedFilter(page);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });
    });

    describe('BO - Disable a product', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageForDisabling', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it(`should filter by name '${dataProducts.demo_8.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEditForDisabling', baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_8.name);

        const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);
      });

      it('should go to the product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageForDisabling', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should disable the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableProduct', baseContext);

        await boProductsCreatePage.setProductStatus(page, false);

        const updateProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/33842
    describe('BO - Check Filter "Status"', async () => {
      it('should go to \'Catalog > Stocks\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPageAfterDisablingProduct', baseContext);

        this.skip();

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.stocksLink,
        );
        await boStockPage.closeSfToolBar(page);

        const pageTitle = await boStockPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockPage.pageTitle);
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPageAfterDisablingProduct', baseContext);

        this.skip();

        await boStockPage.goToSubTabMovements(page);

        const pageTitle = await boStockMovementsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boStockMovementsPage.pageTitle);
      });

      it('should set the filter "Status" to "Disabled"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setFilterStatus', baseContext);

        this.skip();

        await boStockMovementsPage.setAdvancedFiltersVisible(page);
        await boStockMovementsPage.setAdvancedFiltersStatus(page, false);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(4);
      });

      it('should reset the filter "Status"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterStatus', baseContext);

        this.skip();

        await boStockMovementsPage.resetAdvancedFilter(page);

        const numElements = await boStockMovementsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.be.eq(numElementsBeforeFilter);
      });
    });

    describe('BO - Enable a product', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageForEnabling', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.productsLink,
        );
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it(`should filter by name '${dataProducts.demo_8.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEditForEnabling', baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_8.name);

        const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numberOfProductsAfterFilter).to.be.eq(1);
      });

      it('should go to the product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageForEnabling', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should enable the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableProduct', baseContext);

        await boProductsCreatePage.setProductStatus(page, true);

        const updateProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });
    });
  });

  // Post-Condition : Clean Stock Movements
  cleanTableStockMovements(`${baseContext}_postTest_1`);

  // Post-Condition : Delete employee
  deleteEmployeeTest(employeeData, `${baseContext}_postTest_2`);
});
