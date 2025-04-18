import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {enableMerchandiseReturns, disableMerchandiseReturns} from '@commonTests/BO/customerService/merchandiseReturns';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';

import {
  boCustomersPage,
  boCustomersCreatePage,
  boCustomerServicePage,
  boDashboardPage,
  boLoginPage,
  boMerchandiseReturnsPage,
  boMonitoringPage,
  boOrdersPage,
  boOrdersViewBasePage,
  boProductsPage,
  boProductsCreatePage,
  boShoppingCartsPage,
  boStatisticsPage,
  type BrowserContext,
  dataCustomers,
  dataOrders,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerContactMessage,
  FakerCustomer,
  FakerProduct,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicContactUsPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyMerchandiseReturnsPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  foClassicProductPage,
  modProductCommentsBoMain,
  modPsEmailSubscriptionBoMain,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_dashboard_activityOverview';

describe('BO - Dashboard : Activity overview', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let activeShoppingCarts: number;
  let numberOfReturnExchanges: number;
  let ordersNumber: number;
  let outOfStockProductNumber: number;
  let messagesNumber: number;
  let newCustomersNumber: number;
  let newSubscriptionsNumber: number;
  let totalSubscribersNumber: number;

  // Data to create product out of stock
  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    quantity: 0,
    status: true,
  });

  const contactUsData: FakerContactMessage = new FakerContactMessage({
    subject: 'Customer service',
    reference: dataOrders.order_1.reference,
  });

  const createCustomerData: FakerCustomer = new FakerCustomer({newsletter: true});

  enableMerchandiseReturns(baseContext);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Check Online visitor & Active shopping carts', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    describe('Check Active shopping carts', async () => {
      it('should click on Active shopping carts link and check shopping carts page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickActiveShoppingCarts', baseContext);

        await boDashboardPage.clickOnActiveShoppingCartsLink(page);

        const pageTitle = await boShoppingCartsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boShoppingCartsPage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard2', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should get the number of active shopping carts', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfActiveShoppingCarts', baseContext);

        activeShoppingCarts = await boDashboardPage.getNumberOfActiveShoppingCarts(page);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

        page = await boDashboardPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFO2', baseContext);

        await foClassicHomePage.goToLoginPage(page);

        const pageTitle = await foClassicLoginPage.getPageTitle(page);
        expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
      });

      it('should sign in with default customer', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'sighInFO2', baseContext);

        await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

        const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
      });

      it('should add product to cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

        // Go to home page
        await foClassicLoginPage.goToHomePage(page);
        // Go to the first product page
        await foClassicHomePage.goToProductPage(page, 1);
        // Add the product to the cart
        await foClassicProductPage.addProductToTheCart(page);

        const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
        expect(notificationsNumber).to.be.equal(1);
      });

      it('should go to delivery step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

        // Proceed to checkout the shopping cart
        await foClassicCartPage.clickOnProceedToCheckout(page);

        // Address step - Go to delivery step
        const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
        expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
      });

      it('should go to payment step', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

        // Delivery step - Go to payment step
        const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
        expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
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
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

        // Close page and init page objects
        page = await foClassicCheckoutOrderConfirmationPage.closePage(browserContext, page, 0);
        await boShoppingCartsPage.reloadPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should check the number of active shopping carts', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfShoppingCarts', baseContext);

        const newActiveShoppingCarts = await boDashboardPage.getNumberOfActiveShoppingCarts(page);
        expect(newActiveShoppingCarts).to.eq(activeShoppingCarts + 1);
      });
    });
  });

  describe('Check currently pending block', async () => {
    describe('Check orders', async () => {
      it('should get Orders number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrdersNumber', baseContext);

        ordersNumber = await boDashboardPage.getNumberOfOrders(page);
      });

      it('should click on Orders link and check Orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnOrdersLink', baseContext);

        await boDashboardPage.clickOnOrdersLink(page);

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should change the first order status to Processing in progress', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeOrderStatus1', baseContext);

        const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.processingInProgress);
        expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard3', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should check Orders number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOrdersNumber', baseContext);

        const newOrdersNumber = await boDashboardPage.getNumberOfOrders(page);
        expect(newOrdersNumber).to.eq(ordersNumber + 1);
      });
    });

    describe('Check Return/Exchanges', async () => {
      it('should get Return/Exchange number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getReturnExchangeNumber', baseContext);

        numberOfReturnExchanges = await boDashboardPage.getNumberOfReturnExchange(page);
      });

      it('should click on Return/Exchange link and check merchandise returns page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnReturnExchangeLink', baseContext);

        await boDashboardPage.clickOnReturnExchangeLink(page);

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it('should go to orders page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should change the first order status to Delivered', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeOrderStatus2', baseContext);

        const textResult = await boOrdersPage.setOrderStatus(page, 1, dataOrderStatuses.delivered);
        expect(textResult).to.equal(boOrdersPage.successfulUpdateMessage);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard4', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop3', baseContext);

        page = await boOrdersViewBasePage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

        await foClassicHomePage.goToMyAccountPage(page);

        const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
      });

      it('should go to \'Order history and details\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await foClassicMyAccountPage.goToHistoryAndDetailsPage(page);

        const pageTitle = await foClassicMyOrderHistoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyOrderHistoryPage.pageTitle);
      });

      it('should go to the first order in the list and check the existence of order return form', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'isOrderReturnFormVisible', baseContext);

        await foClassicMyOrderHistoryPage.goToDetailsPage(page, 1);

        const result = await foClassicMyOrderDetailsPage.isOrderReturnFormVisible(page);
        expect(result).to.eq(true);
      });

      it('should create a merchandise return', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createMerchandiseReturn', baseContext);

        await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page, 'test', 1, [{quantity: 1}]);

        const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
      });

      it('should close the FO page and go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

        page = await foClassicMyOrderDetailsPage.closePage(browserContext, page, 0);
        await boDashboardPage.reloadPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should check Return/Exchange number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkReturnExchangeNumber', baseContext);

        const newNumberOfReturnExchanges = await boDashboardPage.getNumberOfReturnExchange(page);
        expect(newNumberOfReturnExchanges).to.eq(numberOfReturnExchanges + 1);
      });
    });

    describe('Check Abandoned carts', async () => {
      it('should check Abandoned carts number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getAbandonedCartsNumber', baseContext);

        const abandonedCartsNumber = await boDashboardPage.getNumberOfAbandonedCarts(page);
        expect(abandonedCartsNumber).to.eq(0);
      });

      it('should click on Abandoned carts link and check Shopping carts page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickAbandonedCartsLink', baseContext);

        await boDashboardPage.clickOnAbandonedCartsLink(page);

        const pageTitle = await boShoppingCartsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShoppingCartsPage.pageTitle);
      });
    });

    describe('Check out of stock products', async () => {
      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard5', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should get out of stock products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOutOfStockProducts', baseContext);

        outOfStockProductNumber = await boDashboardPage.getOutOfStockProducts(page);
      });

      it('should click on Out of stock products link and check Monitoring page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOutOfStockLink', baseContext);

        await boDashboardPage.clickOnOutOfStockProductsLink(page);

        const pageTitle = await boMonitoringPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMonitoringPage.pageTitle);
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

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should choose \'Standard product\' and go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

        await boProductsPage.selectProductType(page, productData.type);
        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should create out of stock product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

        const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard6', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should check out of stock products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkOutOfStockProductsNumber', baseContext);

        const newOutOfStockProductNumber = await boDashboardPage.getOutOfStockProducts(page);
        expect(newOutOfStockProductNumber).to.eq(outOfStockProductNumber + 1);
      });
    });
  });

  describe('Check notifications block', async () => {
    describe('Check new messages', async () => {
      it('should get new message number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNewMessagesNumber', baseContext);

        messagesNumber = await boDashboardPage.getNumberOfNewMessages(page);
      });

      it('should click on New messages link and check Customer service page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewMessagesLink', baseContext);

        await boDashboardPage.clickOnNewMessagesLink(page);

        const pageTitle = await boCustomerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
      });

      it('should view my store', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

        page = await boDashboardPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should to contact us page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goOnContactPage', baseContext);

        await foClassicHomePage.goToFooterLink(page, 'Contact us');

        const pageTitle = await foClassicContactUsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicContactUsPage.pageTitle);
      });

      it('should send message to customer service', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'sendMessage', baseContext);

        await foClassicContactUsPage.sendMessage(page, contactUsData);

        const validationMessage = await foClassicContactUsPage.getAlertSuccess(page);
        expect(validationMessage).to.equal(foClassicContactUsPage.validationMessage);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo3', baseContext);

        page = await foClassicContactUsPage.closePage(browserContext, page, 0);

        const pageTitle = await boCustomerServicePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCustomerServicePage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard7', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should check the number of new messages', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfNewMessages', baseContext);

        const newMessagesNumber = await boDashboardPage.getNumberOfNewMessages(page);
        expect(newMessagesNumber).to.eq(messagesNumber + 1);
      });
    });

    describe('Check Product reviews', async () => {
      it('should get the number of product reviews', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductReviews', baseContext);

        const productReviewsNumber = await boDashboardPage.getNumberOfProductReviews(page);
        expect(productReviewsNumber).to.eq(0);
      });

      it('should click on Products reviews link and check Product comments page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnProductReviewLink', baseContext);

        await boDashboardPage.clickOnProductReviewsLink(page);

        const pageTitle = await modProductCommentsBoMain.getPageSubTitle(page);
        expect(pageTitle).to.eq(modProductCommentsBoMain.pageTitle);
      });
    });
  });

  describe('Check Customers & Newsletters block', async () => {
    it('should go back to dashboard page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard8', baseContext);

      await boShoppingCartsPage.goToDashboardPage(page);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.eq(boDashboardPage.pageTitle);
    });

    it('should get the number of customers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfCustomers', baseContext);

      newCustomersNumber = await boDashboardPage.getNumberOfNewCustomers(page);
    });

    it('should get the number of new subscriptions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfNewSubscriptions', baseContext);

      newSubscriptionsNumber = await boDashboardPage.getNumberOfNewSubscriptions(page);
    });

    it('should get the number of total subscribers', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfTotalSubscribers', baseContext);

      totalSubscribersNumber = await boDashboardPage.getNumberOfTotalSubscribers(page);
    });

    describe('Check Customers', async () => {
      it('should click on New customers link and check Customers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewCustomersLink', baseContext);

        await boDashboardPage.clickOnNewCustomersLink(page);

        const pageTitle = await boCustomersPage.getPageTitle(page);
        expect(pageTitle).to.eq(boCustomersPage.pageTitle);
      });

      it('should create new customer and enable newsletter status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createNewCustomer', baseContext);

        await boCustomersPage.goToAddNewCustomerPage(page);

        const textResult = await boCustomersCreatePage.createEditCustomer(page, createCustomerData);
        expect(textResult).to.equal(boCustomersPage.successfulCreationMessage);

        await boCustomersPage.setNewsletterStatus(page, 1, true);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard9', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });

      it('should check the number of new customers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfNewCustomer', baseContext);

        const newCustomers = await boDashboardPage.getNumberOfNewCustomers(page);
        expect(newCustomers).to.eq(newCustomersNumber + 1);
      });
    });

    describe('Check New Subscriptions', async () => {
      it('should check the number of new subscriptions', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfNewSubscriptions', baseContext);

        const newSubscriptions = await boDashboardPage.getNumberOfNewSubscriptions(page);
        expect(newSubscriptions).to.eq(newSubscriptionsNumber + 1);
      });

      it('should click on new subscriptions link and check Stats page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewSubscriptionsLink', baseContext);

        await boDashboardPage.clickOnNewSubscriptionsLink(page);

        const pageTitle = await boStatisticsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boStatisticsPage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard10', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });
    });

    describe('Check Total subscribers', async () => {
      it('should check the number of total subscribers', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfTotalSubscribers', baseContext);

        const newTotalSubscribers = await boDashboardPage.getNumberOfTotalSubscribers(page);
        expect(newTotalSubscribers).to.eq(totalSubscribersNumber + 1);
      });

      it('should click on Total subscribers link and check Newsletter subscription page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnTotalSubscribersLink', baseContext);

        await boDashboardPage.clickOnTotalSubscribersLink(page);

        const pageTitle = await modPsEmailSubscriptionBoMain.getPageSubTitle(page);
        expect(pageTitle).to.eq(modPsEmailSubscriptionBoMain.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard11', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });
    });
  });

  describe('Check Traffic block', async () => {
    describe('Check Visits', async () => {
      it('should get the number of visits', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfVisits', baseContext);

        newCustomersNumber = await boDashboardPage.getNumberOfVisits(page);
      });

      it('should click on Visits link and check Stats page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnVisitsLink', baseContext);

        await boDashboardPage.clickOnVisitsLink(page);

        const pageTitle = await boStatisticsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boStatisticsPage.pageTitle);
      });

      it('should go back to dashboard page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToDashboard12', baseContext);

        await boShoppingCartsPage.goToDashboardPage(page);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.eq(boDashboardPage.pageTitle);
      });
    });

    describe('Check Traffic Sources', async () => {
      it('should check traffic sources', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkTrafficSources', baseContext);

        const trafficSources = await boDashboardPage.getTrafficSources(page);

        expect(trafficSources.length).to.equals(3);
        expect(trafficSources[0].label).to.equals('Direct link');
        expect(trafficSources[1].label).to.equals('prestashop.com');
        expect(trafficSources[2].label).to.equals('localhost');
      });
    });
  });

  describe('Configuration', async () => {
    it('should click on configure link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnConfigureLink', baseContext);

      const isConfigureFormVisible = await boDashboardPage.clickOnConfigureActivityOverviewLink(page);
      expect(isConfigureFormVisible).to.eq(true);
    });

    it('should update the form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setFormActivityOverview', baseContext);

      await boDashboardPage.setFormActivityOverview(page, 45, 45, 12, 96);
      await boDashboardPage.reloadPage(page);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.eq(boDashboardPage.pageTitle);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/37033
    it('should update the form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFormActivityOverview', baseContext);

      const isConfigureFormVisible = await boDashboardPage.clickOnConfigureActivityOverviewLink(page);
      expect(isConfigureFormVisible).to.eq(true);

      this.skip();

      const numActiveCarts = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'active_cart'), 10);
      expect(numActiveCarts).to.equals(45);

      const numOnlineVisitor = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'online_visitor'), 10);
      expect(numOnlineVisitor).to.equals(45);

      const numAbandonedCartMin = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'abandoned_cart_min'), 10);
      expect(numAbandonedCartMin).to.equals(12);

      const numAbandonedCartMax = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'abandoned_cart_max'), 10);
      expect(numAbandonedCartMax).to.equals(96);
    });

    it('should reset the form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFormActivityOverview', baseContext);

      await boDashboardPage.setFormActivityOverview(page, 30, 30, 24, 48);
      await boDashboardPage.reloadPage(page);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.eq(boDashboardPage.pageTitle);
    });

    it('should update the form', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFormActivityOverviewReset', baseContext);

      const isConfigureFormVisible = await boDashboardPage.clickOnConfigureActivityOverviewLink(page);
      expect(isConfigureFormVisible).to.eq(true);

      const numActiveCarts = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'active_cart'), 10);
      expect(numActiveCarts).to.equals(30);

      const numOnlineVisitor = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'online_visitor'), 10);
      expect(numOnlineVisitor).to.equals(30);

      const numAbandonedCartMin = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'abandoned_cart_min'), 10);
      expect(numAbandonedCartMin).to.equals(24);

      const numAbandonedCartMax = parseInt(await boDashboardPage.getFormActivityOverviewValue(page, 'abandoned_cart_max'), 10);
      expect(numAbandonedCartMax).to.equals(48);
    });
  });

  // Post-condition : Delete created customer
  deleteCustomerTest(createCustomerData, baseContext);

  // Post-condition : Delete created product
  deleteProductTest(productData, baseContext);

  // Post-condition : Disable merchandise returns
  disableMerchandiseReturns(baseContext);
});
