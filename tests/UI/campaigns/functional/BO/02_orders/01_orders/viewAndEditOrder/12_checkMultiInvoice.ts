// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {createProductTest, bulkDeleteProductsTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderSpecificProductTest} from '@commonTests/FO/order';
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import BO pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import OrderData from '@data/faker/order';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import OrderShippingData from '@data/faker/orderShipping';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_checkMultiInvoice';

/*
Pre-condition:
- Create 2 products
- Create order by default customer
Scenario:
- Try to add same product to the cart => check error message
- Edit the product price and create new invoice
- Check the 2 invoices
- Add another product, create new invoice with free shipping
- Check the invoice
Post-condition:
- Delete the created products
- Delete 'Free shipping' cart rule
 */

describe('BO - Orders - View and edit order: Check multi invoice', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string | null;
  let firstFileName: string = '';
  let secondFileName: string = '';

  const newProductPrice: number = 35.50;
  const secondNewProductPrice: number = 25.55;
  // Prefix for the new products to simply delete them by bulk actions
  const prefixNewProduct: string = 'TOTEST';
  // First product to create
  const firstProduct: ProductData = new ProductData({
    name: `First product ${prefixNewProduct}`,
    type: 'Standard product',
    taxRule: 'No tax',
    quantity: 20,
  });
  // Second product to create
  const secondProduct: ProductData = new ProductData({
    name: `second product ${prefixNewProduct}`,
    type: 'Standard product',
    taxRule: 'No tax',
    quantity: 20,
  });
  // New order by customer data
  const orderByCustomerData: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: firstProduct,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  const carrierDataToSelect: OrderShippingData = new OrderShippingData({
    trackingNumber: '',
    carrier: Carriers.myCarrier.name,
    carrierID: Carriers.myCarrier.id,
  });

  // Pre-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, false, `${baseContext}_disableNewProduct`);

  // Pre-condition: Create first product
  createProductTest(firstProduct, `${baseContext}_preTest_1`);

  // Pre-condition: Create second product
  createProductTest(secondProduct, `${baseContext}_preTest_2`);

  // Pre-condition: Create order by default customer
  createOrderSpecificProductTest(orderByCustomerData, `${baseContext}_preTest_3`);

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

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );
      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrderTable', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn, 'Lastname is not correct').to.contains(Customers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await orderPageProductsBlock.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(orderPageProductsBlock.pageTitle);
    });
  });

  // 2 - Create first invoice
  describe('Create the first invoice', async () => {
    it(`should change the order status to '${OrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await orderPageTabListBlock.updateOrderStatus(page, OrderStatuses.paymentAccepted.name);
      expect(textResult).to.equal(orderPageProductsBlock.successfulUpdateMessage);
    });

    it('should get the first invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getFirstInvoiceFileName', baseContext);

      firstFileName = await orderPageTabListBlock.getFileName(page);
      expect(filePath).is.not.equal('');
    });
  });

  // 3 - Create second invoice
  describe('Create the second invoice', async () => {
    it('should add the same ordered product and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await orderPageProductsBlock.searchProduct(page, firstProduct.name);

      const textResult = await orderPageProductsBlock.addProductToCart(page);
      expect(textResult).to.contains(orderPageProductsBlock.errorAddSameProductInInvoice(firstFileName));
    });

    it('should create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewInvoice1', baseContext);

      await orderPageProductsBlock.selectInvoice(page);

      const carrierName = await orderPageProductsBlock.getNewInvoiceCarrierName(page);
      expect(carrierName).to.contains(`Carrier : ${Carriers.default.name}`);

      const isSelected = await orderPageProductsBlock.isFreeShippingSelected(page);
      expect(isSelected).to.eq(false);
    });

    it('should update the product price and add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePriceAddProduct', baseContext);

      await orderPageProductsBlock.updateProductPrice(page, newProductPrice);

      const textResult = await orderPageProductsBlock.addProductToCart(page, 2, true);
      expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
    });

    it('should check that order total price is correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPayIsCorrect', baseContext);

      const totalPrice = await orderPageProductsBlock.getOrderTotalPrice(page);
      expect(totalPrice.toFixed(2)).to.equal((newProductPrice * 3).toFixed(2));
    });

    it('should check that products number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await orderPageProductsBlock.reloadPage(page);

      const productCount = await orderPageProductsBlock.getProductsNumber(page);
      expect(productCount).to.equal(2);
    });

    it('should check that invoices number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber', baseContext);

      const documentsNumber = await orderPageTabListBlock.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(2);
    });

    it('should get the second invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getSecondInvoiceNumber', baseContext);

      secondFileName = await orderPageTabListBlock.getFileName(page, 3);
      expect(filePath).is.not.equal('');
    });
  });

  // 4 - Check first invoice
  describe('Check the first invoice', async () => {
    it('should download the first invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadFirstInvoice', baseContext);

      filePath = await orderPageTabListBlock.downloadInvoice(page, 1);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });

    it('should check that the \'Product reference, Product name\' are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference', baseContext);

      const productReferenceExist = await files.isTextInPDF(
        filePath,
        `${firstProduct.reference}, ,${firstProduct.name}`,
      );
      expect(productReferenceExist, 'Product name and reference are not correct!').to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUnitPrice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${firstProduct.name}, ,`
        + `€${newProductPrice.toFixed(2)}, ,`
        + '1, ,'
        + `€${newProductPrice.toFixed(2)}`,
      );
      expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct')
        .to.eq(true);
    });

    it('should edit the product price and check the price of the 2 products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductPrice', baseContext);

      await orderPageProductsBlock.modifyProductPriceForMultiInvoice(page, 1, secondNewProductPrice);

      let result = await orderPageProductsBlock.getProductDetails(page, 1);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated').to.equal(secondNewProductPrice),
        expect(result.total, 'Total price was not updated').to.equal(secondNewProductPrice),
      ]);

      result = await orderPageProductsBlock.getProductDetails(page, 2);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated').to.equal(secondNewProductPrice),
        expect(result.total, 'Total price was not updated').to.equal(secondNewProductPrice * 2),
      ]);
    });
  });

  // 5 - Check multi invoice (1 + 2)
  describe('Check multi invoice', async () => {
    it('should click on \'View invoice\' button to download the 2 invoices '
      + 'check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

      filePath = await orderPageProductsBlock.viewInvoice(page);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'File is not downloaded!').to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct on the first invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceOnFirstInvoice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${firstProduct.name}, ,`
        + `€${secondNewProductPrice.toFixed(2)}, ,`
        + '1, ,'
        + `€${secondNewProductPrice.toFixed(2)}`,
      );
      expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct on the first invoice')
        .to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct on the second invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceOnSecondInvoice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${firstProduct.name}, ,`
        + `€${(secondNewProductPrice).toFixed(2)}, ,`
        + '2, ,'
        + `€${(secondNewProductPrice * 2).toFixed(2)}`,
      );
      expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct on the second invoice')
        .to.eq(true);
    });
  });

  // 6 - Create a third invoice with free shipping
  describe('Create the third invoice and check the option \'Free shipping\'', async () => {
    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should click on \'Edit\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

      const isModalVisible = await orderPageTabListBlock.clickOnEditLink(page);
      expect(isModalVisible, 'Edit shipping modal is not visible!').to.eq(true);
    });

    it(`should select the default not free carrier '${Carriers.myCarrier.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewCarrier', baseContext);

      const textResult = await orderPageTabListBlock.setShippingDetails(page, carrierDataToSelect);
      expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
    });

    it(`should search for the product '${secondProduct.name}' and check that there is `
      + 'two invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSecondProduct', baseContext);

      await orderPageProductsBlock.searchProduct(page, secondProduct.name);

      const invoices = await orderPageProductsBlock.getInvoicesFromSelectOptions(page);
      expect(invoices).to.contains(`#${firstFileName}#${secondFileName}`);
    });

    it('should create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewInvoice2', baseContext);

      await orderPageProductsBlock.selectInvoice(page);

      const carrierName = await orderPageProductsBlock.getNewInvoiceCarrierName(page);
      expect(carrierName).to.contains(`Carrier : ${Carriers.myCarrier.name}`);

      const isSelected = await orderPageProductsBlock.isFreeShippingSelected(page);
      expect(isSelected).to.eq(false);
    });

    it('should select \'Free shipping\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectFreeShippingCheckbox', baseContext);

      await orderPageProductsBlock.selectFreeShippingCheckbox(page);

      const isSelected = await orderPageProductsBlock.isFreeShippingSelected(page);
      expect(isSelected).to.eq(true);
    });

    it(`should add the product '${secondProduct.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondProductToCart', baseContext);

      const textResult = await orderPageProductsBlock.addProductToCart(page, 1, true);
      expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
    });

    it('should check that invoices number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber2', baseContext);

      await orderPageProductsBlock.reloadPage(page);

      const documentsNumber = await orderPageTabListBlock.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(3);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable', baseContext);

      const isVisible = await orderPageProductsBlock.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is not visible').to.eq(true);
    });

    it('should check the discount \'[Generated] CartRule for Free Shipping\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName', baseContext);

      const discountName = await orderPageProductsBlock.getTextColumnFromDiscountTable(page, 'name');
      expect(discountName).to.be.equal('[Generated] CartRule for Free Shipping');
    });
  });

  // 7 - Check the third invoice
  describe('Check the third invoice', async () => {
    it('should download the third invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadThirdInvoice', baseContext);

      filePath = await orderPageTabListBlock.downloadInvoice(page, 5);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });

    it('should check that the \'Product reference, Product name\' are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference2', baseContext);

      const productReferenceExist = await files.isTextInPDF(
        filePath,
        `${secondProduct.reference}, ,${secondProduct.name}`,
      );
      expect(productReferenceExist, 'Product name and reference are not correct!').to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceForThirdInvoice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${secondProduct.name}, ,`
        + `€${secondProduct.price.toFixed(2)}, ,`
        + '1, ,'
        + `€${secondProduct.price.toFixed(2)}`,
      );
      expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct')
        .to.eq(true);
    });

    it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingForThirdInvoice', baseContext);

        // Total Products, Shipping Costs, Total (Tax excl.), Total
        const isShippingCostVisible = await files.isTextInPDF(
          filePath,
          `Total Products, ,€${secondProduct.price.toFixed(2)},`
          + 'Shipping Costs, ,Free Shipping,,'
          + `Total (Tax excl.), ,€${secondProduct.price.toFixed(2)},,`
          + `Total, ,€${secondProduct.price.toFixed(2)}`,
        );
        expect(
          isShippingCostVisible,
          'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
        ).to.eq(true);
      });
  });

  // Post-condition: Delete created products
  bulkDeleteProductsTest(prefixNewProduct, `${baseContext}_postTest_1`);

  // Post-condition: Delete 'Free shipping' cart rule
  deleteCartRuleTest(`${baseContext}_postTest_2`);

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
