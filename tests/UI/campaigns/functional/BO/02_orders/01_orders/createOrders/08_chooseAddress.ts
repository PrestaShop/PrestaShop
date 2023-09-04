// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {createAddressTest, bulkDeleteAddressesTest} from '@commonTests/BO/customers/address';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import addAddressPage from '@pages/BO/customers/addresses/add';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';
// Import FO pages
import {homePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';
import orderDetailsPage from '@pages/FO/myAccount/orderDetails';
import {orderHistoryPage} from '@pages/FO/myAccount/orderHistory';

// Import data
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import AddressData from '@data/faker/address';

import {expect} from 'chai';
import type {BrowserContext, Frame, Page} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_createOrders_chooseAddress';

/*
Pre-condition:
- Create address
Scenario:
- Create order with the created address
- Go to Orders > New order page and search for the default customer
- Choose the created address in Pre-condition as delivery and invoice address
- Update the created address
- Check that the created address is not updated in the first order in BO
- Check that the created address is not updated in the first order in FO
- Choose the updated address as invoice address then update it
- Add new address from 'Add new order page' and check it
Post-condition:
- Bulk delete created addresses
 */
describe('BO - Orders - Create order : Choose address', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let editAddressIframe: Frame|null;
  let addAddressIframe: Frame|null;
  // Variable used for the created order ID
  let orderID : number;

  // Const used for the payment status
  const paymentMethodModuleName: string = PaymentMethods.checkPayment.moduleName;
  // Variable used to create new address in Pre-condition
  const newAddressToCreate: AddressData = new AddressData({email: Customers.johnDoe.email, lastName: 'test', country: 'France'});
  // Variable used to edit demo address
  const addressToEditData: AddressData = new AddressData({country: 'France'});
  // Variable used to add new address from new order page
  const newAddressData: AddressData = new AddressData({lastName: 'test', country: 'France'});

  // Pre-condition: Create new address
  createAddressTest(newAddressToCreate, `${baseContext}_preTest_1`);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Create new order
  describe('Create first order and choose the created address in PRE-TEST', async () => {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });

    it('should add to cart the product \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      const productToSelect = `${Products.demo_12.name} - €${Products.demo_12.priceTaxExcluded.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_12, productToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_12.thumbImage),
        expect(result.description).to.equal(Products.demo_12.name),
        expect(result.reference).to.equal(Products.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(Products.demo_12.priceTaxExcluded),
      ]);
    });

    it(`should choose the delivery address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedDeliveryAddress', baseContext);

      const newAddress = await addOrderPage.chooseDeliveryAddress(page, newAddressToCreate.alias);
      await expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });

    it(`should choose the invoice address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedInvoiceAddress', baseContext);

      const newAddress = await addOrderPage.chooseInvoiceAddress(page, newAddressToCreate.alias);
      await expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await addOrderPage.setSummaryAndCreateOrder(page, paymentMethodModuleName, OrderStatuses.paymentAccepted);

      const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
      await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await orderPageCustomerBlock.getOrderID(page);
      await expect(orderID).to.be.at.least(5);
    });
  });

  // 2 - Create second order
  describe('Create second order and choose the created address in PRE-TEST', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage2', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer2', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });

    it(`should choose the delivery address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedDeliveryAddress2', baseContext);

      const newAddress = await addOrderPage.chooseDeliveryAddress(page, newAddressToCreate.alias);
      await expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });

    it(`should choose the invoice address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedInvoiceAddress2', baseContext);

      const newAddress = await addOrderPage.chooseInvoiceAddress(page, newAddressToCreate.alias);
      await expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });
  });

  // 3 - Edit address
  describe('Edit delivery and invoice addresses', async () => {
    describe('Edit delivery address', async () => {
      it('should click on edit address and check if edit address iframe is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditAddress', baseContext);

        const isIframeVisible = await addOrderPage.clickOnEditDeliveryAddressButton(page);
        await expect(isIframeVisible, 'Edit address iframe is not visible!').to.be.true;
      });

      it('should edit the address and check it', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAddress', baseContext);

        editAddressIframe = await addOrderPage.getEditAddressIframe(page);
        await expect(editAddressIframe).to.be.not.null;

        await addAddressPage.createEditAddress(editAddressIframe!, addressToEditData, true, false);

        const editedAddress = await addOrderPage.getDeliveryAddressDetails(page);
        await expect(editedAddress).to.be.equal(`${addressToEditData.firstName} ${addressToEditData.lastName}`
          + `${addressToEditData.company}${addressToEditData.address}${addressToEditData.secondAddress}`
          + `${addressToEditData.postalCode} ${addressToEditData.city}${addressToEditData.country}`
          + `${addressToEditData.phone}`);
      });
    });

    describe('Check that the edited address is not changed in the first created order in BO', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage3', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should filter the list by order ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrdersTableByID', baseContext);

        await ordersPage.filterOrders(page, 'input', 'id_order', orderID.toString());

        const numberOfOrdersAfterFilter = await ordersPage.getNumberOfElementInGrid(page);
        await expect(numberOfOrdersAfterFilter).to.be.equal(1);
      });

      it('should view the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock1', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
      });

      it('should check the shipping address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

        const shippingAddress = await orderPageCustomerBlock.getShippingAddress(page);
        await expect(shippingAddress)
          .to.contain(newAddressToCreate.firstName)
          .and.to.contain(newAddressToCreate.lastName)
          .and.to.contain(newAddressToCreate.address)
          .and.to.contain(newAddressToCreate.postalCode)
          .and.to.contain(newAddressToCreate.city)
          .and.to.contain(newAddressToCreate.country)
          .and.to.contain(newAddressToCreate.phone);
      });

      it('should check the invoice address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddress', baseContext);

        const shippingAddress = await orderPageCustomerBlock.getInvoiceAddress(page);
        await expect(shippingAddress)
          .to.contain(newAddressToCreate.firstName)
          .and.to.contain(newAddressToCreate.lastName)
          .and.to.contain(newAddressToCreate.address)
          .and.to.contain(newAddressToCreate.postalCode)
          .and.to.contain(newAddressToCreate.city)
          .and.to.contain(newAddressToCreate.country)
          .and.to.contain(newAddressToCreate.phone);
      });
    });

    describe('Check that the edited address is not changed in the first order in FO', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

        // Click on view my shop
        page = await orderPageCustomerBlock.viewMyShop(page);
        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage, 'Home page is not displayed').to.be.true;
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

        await homePage.goToLoginPage(page);

        const pageTitle = await foLoginPage.getPageTitle(page);
        await expect(pageTitle, 'Fail to open FO login page').to.contains(foLoginPage.pageTitle);
      });

      it('should sign in with customer credentials', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

        await foLoginPage.customerLogin(page, Customers.johnDoe);

        const isCustomerConnected = await foLoginPage.isCustomerConnected(page);
        await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
      });

      it('should go to account page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

        await homePage.goToMyAccountPage(page);

        const pageTitle = await myAccountPage.getPageTitle(page);
        await expect(pageTitle).to.contains(myAccountPage.pageTitle);
      });

      it('should go to \'Order history and details\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

        await myAccountPage.goToHistoryAndDetailsPage(page);

        const pageTitle = await orderHistoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(orderHistoryPage.pageTitle);
      });

      it('should click on details link of the first created order and check the delivery address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryAddressFO', baseContext);

        await orderHistoryPage.goToOrderDetailsPage(page, orderID);

        const deliveryAddress = await orderDetailsPage.getDeliveryAddress(page);
        await expect(deliveryAddress).to.contain(newAddressToCreate.firstName)
          .and.to.contain(newAddressToCreate.lastName)
          .and.to.contain(newAddressToCreate.address)
          .and.to.contain(newAddressToCreate.postalCode)
          .and.to.contain(newAddressToCreate.city)
          .and.to.contain(newAddressToCreate.country)
          .and.to.contain(newAddressToCreate.phone);
      });

      it('should check the invoice address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddressFO', baseContext);

        const deliveryAddress = await orderDetailsPage.getInvoiceAddress(page);
        await expect(deliveryAddress).to.contain(newAddressToCreate.firstName)
          .and.to.contain(newAddressToCreate.lastName)
          .and.to.contain(newAddressToCreate.address)
          .and.to.contain(newAddressToCreate.postalCode)
          .and.to.contain(newAddressToCreate.city)
          .and.to.contain(newAddressToCreate.country)
          .and.to.contain(newAddressToCreate.phone);
      });

      it('should close the FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeFo', baseContext);

        page = await orderDetailsPage.closePage(browserContext, page, 0);

        const pageTitle = await orderPageCustomerBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageCustomerBlock.pageTitle);
      });
    });

    describe('Edit invoice address', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage4', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.ordersParentLink,
          dashboardPage.ordersLink,
        );

        const pageTitle = await ordersPage.getPageTitle(page);
        await expect(pageTitle).to.contains(ordersPage.pageTitle);
      });

      it('should go to create order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage3', baseContext);

        await ordersPage.goToCreateOrderPage(page);

        const pageTitle = await addOrderPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addOrderPage.pageTitle);
      });

      it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer3', baseContext);

        await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

        const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
        await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
      });

      it(`should choose the address '${addressToEditData.alias}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseMyAddress', baseContext);

        const editedAddress = await addOrderPage.chooseInvoiceAddress(page, addressToEditData.alias);
        await expect(editedAddress).to.be.equal(`${addressToEditData.firstName} ${addressToEditData.lastName}`
          + `${addressToEditData.company}${addressToEditData.address}${addressToEditData.secondAddress}`
          + `${addressToEditData.postalCode} ${addressToEditData.city}${addressToEditData.country}`
          + `${addressToEditData.phone}`);
      });

      it('should click on edit address and check if edit address iframe is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditAddress2', baseContext);

        const isIframeVisible = await addOrderPage.clickOnEditInvoiceAddressButton(page);
        await expect(isIframeVisible, 'Edit address iframe is not visible!').to.be.true;
      });

      it('should edit the address and check it', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAddress2', baseContext);

        editAddressIframe = await addOrderPage.getEditAddressIframe(page);
        await expect(editAddressIframe).to.be.not.null;

        await addAddressPage.createEditAddress(editAddressIframe!, newAddressToCreate, true, false);

        const editedAddress = await addOrderPage.getInvoiceAddressDetails(page);
        await expect(editedAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
          + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
          + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
          + `${newAddressToCreate.phone}`);
      });
    });
  });

  // 4 - Add new address
  describe('Add new address', async () => {
    it('should click on add delivery address and check if add new address iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditAddress3', baseContext);

      const isIframeVisible = await addOrderPage.clickOnAddNewAddressButton(page);
      await expect(isIframeVisible, 'Add address iframe is not visible!').to.be.true;
    });

    it('should add new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewAddress', baseContext);

      addAddressIframe = await addOrderPage.getAddAddressIframe(page);
      await expect(addAddressIframe).to.be.not.null;

      await addAddressPage.createEditAddress(addAddressIframe!, newAddressData, true, false);

      const deliveryAddress = await addOrderPage.getDeliveryAddressList(page);
      await expect(deliveryAddress).to.contains(newAddressData.alias);
    });

    it('should choose the new delivery address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseNewDeliveryAddress', baseContext);

      const newAddress = await addOrderPage.chooseDeliveryAddress(page, newAddressData.alias);
      await expect(newAddress).to.be.equal(`${newAddressData.firstName} ${newAddressData.lastName}`
        + `${newAddressData.company}${newAddressData.address}${newAddressData.secondAddress}`
        + `${newAddressData.postalCode} ${newAddressData.city}${newAddressData.country}`
        + `${newAddressData.phone}`);
    });

    it('should select the created address as an invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewInvoiceAddress', baseContext);

      const newAddress = await addOrderPage.chooseInvoiceAddress(page, newAddressData.alias);
      await expect(newAddress).to.be.equal(`${newAddressData.firstName} ${newAddressData.lastName}`
        + `${newAddressData.company}${newAddressData.address}${newAddressData.secondAddress}`
        + `${newAddressData.postalCode} ${newAddressData.city}${newAddressData.country}`
        + `${newAddressData.phone}`);
    });
  });

  // Post-condition: Bulk delete created addresses
  bulkDeleteAddressesTest('lastname', 'test', `${baseContext}_postTest_1`);
});
