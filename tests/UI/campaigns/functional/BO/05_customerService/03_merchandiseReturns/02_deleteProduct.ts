import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boMerchandiseReturnsPage,
  boMerchandiseReturnsEditPage,
  boOrdersPage,
  boOrdersViewBasePage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  foClassicCartPage,
  foClassicCheckoutPage,
  foClassicCheckoutOrderConfirmationPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicModalBlockCartPage,
  foClassicModalQuickViewPage,
  foClassicMyAccountPage,
  foClassicMyMerchandiseReturnsPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customerService_merchandiseReturns_deleteProduct';

/*
Pre-condition:
- Create order in FO
- Activate merchandise returns
- Change the first order status in the list to shipped
Scenario
- Create merchandise returns in FO (3 products)
- GO to BO > merchandise returns page > Edit
- Delete the first product (Click on Take me out of here!)
- Delete the second product (Click on I understand the risks...)
- Try to delete the last product and check the error message
Post-condition:
- Deactivate merchandise returns
 */
describe('BO - Customer Service - Merchandise Returns : Delete product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('PRE-TEST: Create order in FO', async () => {
    it('should go to FO page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await foClassicHomePage.goToFo(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to login page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

      await foClassicHomePage.goToLoginPage(page);

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
    });

    it('should sign in with default customer', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'sighInFO', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
    });

    it('should add 3 products to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      await foClassicLoginPage.goToHomePage(page);

      // Add first product to cart by quick view
      await foClassicHomePage.quickViewProduct(page, 1);
      await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 2);
      await foClassicModalBlockCartPage.continueShopping(page);

      // Add second product to cart by quick view
      await foClassicHomePage.quickViewProduct(page, 2);
      await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 2);
      await foClassicModalBlockCartPage.continueShopping(page);

      // Add third product to cart by quick view
      await foClassicHomePage.quickViewProduct(page, 3);
      await foClassicModalQuickViewPage.setQuantityAndAddToCart(page, 2);
      await foClassicModalBlockCartPage.proceedToCheckout(page);

      const notificationsNumber = await foClassicCartPage.getCartNotificationsNumber(page);
      expect(notificationsNumber).to.be.equal(6);
    });

    it('should go to delivery step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDeliveryStep', baseContext);

      await foClassicCartPage.clickOnProceedToCheckout(page);

      const isStepAddressComplete = await foClassicCheckoutPage.goToDeliveryStep(page);
      expect(isStepAddressComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should go to payment step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToPaymentStep', baseContext);

      const isStepDeliveryComplete = await foClassicCheckoutPage.goToPaymentStep(page);
      expect(isStepDeliveryComplete, 'Step Address is not complete').to.eq(true);
    });

    it('should choose payment method and confirm the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmOrder', baseContext);

      await foClassicCheckoutPage.choosePaymentAndOrder(page, dataPaymentMethods.wirePayment.moduleName);

      const cardTitle = await foClassicCheckoutOrderConfirmationPage.getOrderConfirmationCardTitle(page);
      expect(cardTitle).to.contains(foClassicCheckoutOrderConfirmationPage.orderConfirmationCardTitle);
    });
  });

  describe('PRE-TEST: Enable merchandise returns', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
      );
      await boMerchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should enable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, true);
      expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
    });
  });

  describe('PRE-TEST: Change order status to \'Shipped\'', async () => {
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

    it('should go to the first order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFirstOrderPage', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });

    it(`should change the order status to '${dataOrderStatuses.shipped.name}' and check it`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const result = await boOrdersViewBasePage.modifyOrderStatus(page, dataOrderStatuses.shipped.name);
      expect(result).to.equal(dataOrderStatuses.shipped.name);
    });

    it('should check if the button \'Return products\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkReturnProductsButton', baseContext);

      const result = await boOrdersViewBasePage.isReturnProductsButtonVisible(page);
      expect(result).to.eq(true);
    });
  });

  describe('FO : Create merchandise returns', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

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

      await foClassicMyOrderDetailsPage.requestMerchandiseReturn(page,
        'test',
        3,
        [{quantity: 1}, {quantity: 1}, {quantity: 2}]);

      const pageTitle = await foClassicMyMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyMerchandiseReturnsPage.pageTitle);
    });

    it('should close the FO page and go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFoAndGoBackToBO', baseContext);

      page = await foClassicMyOrderDetailsPage.closePage(browserContext, page, 0);

      const pageTitle = await boOrdersViewBasePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBasePage.pageTitle);
    });
  });

  describe('BO : Delete products from merchandise return', async () => {
    [1, 2].forEach((index: number) => {
      it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMerchandiseReturnsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.customerServiceParentLink,
          boDashboardPage.merchandiseReturnsLink,
        );

        const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
      });

      it('should go to edit merchandise returns page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditReturnsPage${index}`, baseContext);

        await boMerchandiseReturnsPage.goToMerchandiseReturnPage(page);

        const pageTitle = await boMerchandiseReturnsEditPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsEditPage.pageTitle);
      });

      it('should delete the first returned product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteFirstProduct${index}`, baseContext);

        const successMessage = await boMerchandiseReturnsEditPage.deleteProduct(page, 1);
        expect(successMessage).to.contains(boMerchandiseReturnsEditPage.successfulUpdateMessage);

        const pageTitle = await boMerchandiseReturnsEditPage.getPageTitle(page);
        expect(pageTitle).to.contains(boMerchandiseReturnsEditPage.pageTitle);
      });
    });

    it('should try to delete the last returned product and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLastProduct', baseContext);

      const errorMessage = await boMerchandiseReturnsEditPage.clickOnDeleteLastProductButton(page);
      expect(errorMessage).to.contains(boMerchandiseReturnsPage.errorDeletionMessage);
    });
  });

  describe('POST-TEST: Disable merchandise returns', async () => {
    it('should disable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, false);
      expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
    });
  });
});
