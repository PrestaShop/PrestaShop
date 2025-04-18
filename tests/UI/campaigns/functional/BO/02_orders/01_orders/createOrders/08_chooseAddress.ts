import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {createAddressTest, bulkDeleteAddressesTest} from '@commonTests/BO/customers/address';

import {
  boAddressesCreatePage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  boOrdersViewBlockCustomersPage,
  type BrowserContext,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyOrderDetailsPage,
  foClassicMyOrderHistoryPage,
  type Frame,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
  const paymentMethodModuleName: string = dataPaymentMethods.checkPayment.moduleName;
  // Variable used to create new address in Pre-condition
  const newAddressToCreate: FakerAddress = new FakerAddress({
    email: dataCustomers.johnDoe.email,
    lastName: 'test',
    country: 'France',
  });
  // Variable used to edit demo address
  const addressToEditData: FakerAddress = new FakerAddress({country: 'France'});
  // Variable used to add new address from new order page
  const newAddressData: FakerAddress = new FakerAddress({lastName: 'test', country: 'France'});

  // Pre-condition: Create new address
  createAddressTest(newAddressToCreate, `${baseContext}_preTest_1`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Create new order
  describe('Create first order and choose the created address in PRE-TEST', async () => {
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

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it(`should choose customer ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const isCartsTableVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });

    it('should add to cart the product \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart', baseContext);

      const productToSelect = `${dataProducts.demo_12.name} - €${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`;
      await boOrdersCreatePage.addProductToCart(page, dataProducts.demo_12, productToSelect);

      const result = await boOrdersCreatePage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_12.thumbImage),
        expect(result.description).to.equal(dataProducts.demo_12.name),
        expect(result.reference).to.equal(dataProducts.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(dataProducts.demo_12.priceTaxExcluded),
      ]);
    });

    it(`should choose the delivery address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedDeliveryAddress', baseContext);

      const newAddress = await boOrdersCreatePage.chooseDeliveryAddress(page, newAddressToCreate.alias);
      expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });

    it(`should choose the invoice address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedInvoiceAddress', baseContext);

      const newAddress = await boOrdersCreatePage.chooseInvoiceAddress(page, newAddressToCreate.alias);
      expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });

    it('should complete the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'completeOrder', baseContext);

      await boOrdersCreatePage.setSummaryAndCreateOrder(page, paymentMethodModuleName, dataOrderStatuses.paymentAccepted);

      const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
    });

    it('should get the order ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getOrderID', baseContext);

      orderID = await boOrdersViewBlockCustomersPage.getOrderID(page);
      expect(orderID).to.be.at.least(5);
    });
  });

  // 2 - Create second order
  describe('Create second order and choose the created address in PRE-TEST', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage2', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it(`should choose customer ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer2', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const isCartsTableVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });

    it(`should choose the delivery address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedDeliveryAddress2', baseContext);

      const newAddress = await boOrdersCreatePage.chooseDeliveryAddress(page, newAddressToCreate.alias);
      expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
        + `${newAddressToCreate.company}${newAddressToCreate.address}${newAddressToCreate.secondAddress}`
        + `${newAddressToCreate.postalCode} ${newAddressToCreate.city}${newAddressToCreate.country}`
        + `${newAddressToCreate.phone}`);
    });

    it(`should choose the invoice address '${newAddressToCreate.alias}' and check details`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseCreatedInvoiceAddress2', baseContext);

      const newAddress = await boOrdersCreatePage.chooseInvoiceAddress(page, newAddressToCreate.alias);
      expect(newAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
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

        const isIframeVisible = await boOrdersCreatePage.clickOnEditDeliveryAddressButton(page);
        expect(isIframeVisible, 'Edit address iframe is not visible!').to.eq(true);
      });

      it('should edit the address and check it', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAddress', baseContext);

        editAddressIframe = boOrdersCreatePage.getEditAddressIframe(page);
        expect(editAddressIframe).to.not.eq(null);

        await boAddressesCreatePage.createEditAddress(editAddressIframe!, addressToEditData, true, false);

        const editedAddress = await boOrdersCreatePage.getDeliveryAddressDetails(page);
        expect(editedAddress).to.be.equal(`${addressToEditData.firstName} ${addressToEditData.lastName}`
          + `${addressToEditData.company}${addressToEditData.address}${addressToEditData.secondAddress}`
          + `${addressToEditData.postalCode} ${addressToEditData.city}${addressToEditData.country}`
          + `${addressToEditData.phone}`);
      });
    });

    describe('Check that the edited address is not changed in the first created order in BO', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage3', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should filter the list by order ID', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterOrdersTableByID', baseContext);

        await boOrdersPage.filterOrders(page, 'input', 'id_order', orderID.toString());

        const numberOfOrdersAfterFilter = await boOrdersPage.getNumberOfElementInGrid(page);
        expect(numberOfOrdersAfterFilter).to.be.equal(1);
      });

      it('should view the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderPageCustomerBlock1', baseContext);

        await boOrdersPage.goToOrder(page, 1);

        const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
      });

      it('should check the shipping address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkShippingAddress', baseContext);

        const shippingAddress = await boOrdersViewBlockCustomersPage.getShippingAddress(page);
        expect(shippingAddress)
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

        const shippingAddress = await boOrdersViewBlockCustomersPage.getInvoiceAddress(page);
        expect(shippingAddress)
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
        page = await boOrdersViewBlockCustomersPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Home page is not displayed').to.eq(true);
      });

      it('should go to login page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPageFO', baseContext);

        await foClassicHomePage.goToLoginPage(page);

        const pageTitle = await foClassicLoginPage.getPageTitle(page);
        expect(pageTitle, 'Fail to open FO login page').to.contains(foClassicLoginPage.pageTitle);
      });

      it('should sign in with customer credentials', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'signInFO', baseContext);

        await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

        const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
        expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
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

      it('should click on details link of the first created order and check the delivery address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryAddressFO', baseContext);

        await foClassicMyOrderHistoryPage.goToOrderDetailsPage(page, orderID);

        const deliveryAddress = await foClassicMyOrderDetailsPage.getDeliveryAddress(page);
        expect(deliveryAddress).to.contain(newAddressToCreate.firstName)
          .and.to.contain(newAddressToCreate.lastName)
          .and.to.contain(newAddressToCreate.address)
          .and.to.contain(newAddressToCreate.postalCode)
          .and.to.contain(newAddressToCreate.city)
          .and.to.contain(newAddressToCreate.country)
          .and.to.contain(newAddressToCreate.phone);
      });

      it('should check the invoice address', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceAddressFO', baseContext);

        const deliveryAddress = await foClassicMyOrderDetailsPage.getInvoiceAddress(page);
        expect(deliveryAddress).to.contain(newAddressToCreate.firstName)
          .and.to.contain(newAddressToCreate.lastName)
          .and.to.contain(newAddressToCreate.address)
          .and.to.contain(newAddressToCreate.postalCode)
          .and.to.contain(newAddressToCreate.city)
          .and.to.contain(newAddressToCreate.country)
          .and.to.contain(newAddressToCreate.phone);
      });

      it('should close the FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeFo', baseContext);

        page = await foClassicMyOrderDetailsPage.closePage(browserContext, page, 0);

        const pageTitle = await boOrdersViewBlockCustomersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersViewBlockCustomersPage.pageTitle);
      });
    });

    describe('Edit invoice address', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage4', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.ordersParentLink,
          boDashboardPage.ordersLink,
        );

        const pageTitle = await boOrdersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersPage.pageTitle);
      });

      it('should go to create order page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage3', baseContext);

        await boOrdersPage.goToCreateOrderPage(page);

        const pageTitle = await boOrdersCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
      });

      it(`should choose customer ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer3', baseContext);

        await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

        const isCartsTableVisible = await boOrdersCreatePage.chooseCustomer(page);
        expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
      });

      it(`should choose the address '${addressToEditData.alias}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'chooseMyAddress', baseContext);

        const editedAddress = await boOrdersCreatePage.chooseInvoiceAddress(page, addressToEditData.alias);
        expect(editedAddress).to.be.equal(`${addressToEditData.firstName} ${addressToEditData.lastName}`
          + `${addressToEditData.company}${addressToEditData.address}${addressToEditData.secondAddress}`
          + `${addressToEditData.postalCode} ${addressToEditData.city}${addressToEditData.country}`
          + `${addressToEditData.phone}`);
      });

      it('should click on edit address and check if edit address iframe is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditAddress2', baseContext);

        const isIframeVisible = await boOrdersCreatePage.clickOnEditInvoiceAddressButton(page);
        expect(isIframeVisible, 'Edit address iframe is not visible!').to.eq(true);
      });

      it('should edit the address and check it', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'editAddress2', baseContext);

        editAddressIframe = boOrdersCreatePage.getEditAddressIframe(page);
        expect(editAddressIframe).to.not.eq(null);

        await boAddressesCreatePage.createEditAddress(editAddressIframe!, newAddressToCreate, true, false);

        const editedAddress = await boOrdersCreatePage.getInvoiceAddressDetails(page);
        expect(editedAddress).to.be.equal(`${newAddressToCreate.firstName} ${newAddressToCreate.lastName}`
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

      const isIframeVisible = await boOrdersCreatePage.clickOnAddNewAddressButton(page);
      expect(isIframeVisible, 'Add address iframe is not visible!').to.eq(true);
    });

    it('should add new address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewAddress', baseContext);

      addAddressIframe = boOrdersCreatePage.getAddAddressIframe(page);
      expect(addAddressIframe).to.not.eq(null);

      await boAddressesCreatePage.createEditAddress(addAddressIframe!, newAddressData, true, false);

      const deliveryAddress = await boOrdersCreatePage.getDeliveryAddressList(page);
      expect(deliveryAddress).to.contains(newAddressData.alias);
    });

    it('should choose the new delivery address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseNewDeliveryAddress', baseContext);

      const newAddress = await boOrdersCreatePage.chooseDeliveryAddress(page, newAddressData.alias);
      expect(newAddress).to.be.equal(`${newAddressData.firstName} ${newAddressData.lastName}`
        + `${newAddressData.company}${newAddressData.address}${newAddressData.secondAddress}`
        + `${newAddressData.postalCode} ${newAddressData.city}${newAddressData.country}`
        + `${newAddressData.phone}`);
    });

    it('should select the created address as an invoice address', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewInvoiceAddress', baseContext);

      const newAddress = await boOrdersCreatePage.chooseInvoiceAddress(page, newAddressData.alias);
      expect(newAddress).to.be.equal(`${newAddressData.firstName} ${newAddressData.lastName}`
        + `${newAddressData.company}${newAddressData.address}${newAddressData.secondAddress}`
        + `${newAddressData.postalCode} ${newAddressData.city}${newAddressData.country}`
        + `${newAddressData.phone}`);
    });
  });

  // Post-condition: Bulk delete created addresses
  bulkDeleteAddressesTest('lastname', 'test', `${baseContext}_postTest_1`);
});
