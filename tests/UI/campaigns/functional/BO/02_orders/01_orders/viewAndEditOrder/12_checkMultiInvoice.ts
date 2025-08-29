// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {createProductTest, bulkDeleteProductsTest} from '@commonTests/BO/catalog/product';
import {createOrderSpecificProductTest} from '@commonTests/FO/classic/order';

import {
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataOrderStatuses,
  dataPaymentMethods,
  FakerOrder,
  FakerOrderShipping,
  FakerProduct,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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
  const firstProduct: FakerProduct = new FakerProduct({
    name: `First product ${prefixNewProduct}`,
    type: 'standard',
    taxRule: 'No tax',
    quantity: 20,
  });
  // Second product to create
  const secondProduct: FakerProduct = new FakerProduct({
    name: `second product ${prefixNewProduct}`,
    type: 'standard',
    taxRule: 'No tax',
    quantity: 20,
  });
  // New order by customer data
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: firstProduct,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const carrierDataToSelect: FakerOrderShipping = new FakerOrderShipping({
    trackingNumber: '',
    carrier: dataCarriers.myCarrier.name,
    carrierID: dataCarriers.myCarrier.id,
  });

  // Pre-condition: Create first product
  createProductTest(firstProduct, `${baseContext}_preTest_1`);

  // Pre-condition: Create second product
  createProductTest(secondProduct, `${baseContext}_preTest_2`);

  // Pre-condition: Create order by default customer
  createOrderSpecificProductTest(orderByCustomerData, `${baseContext}_preTest_3`);

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

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrderTable', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn, 'Lastname is not correct').to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });
  });

  // 2 - Create first invoice
  describe('Create the first invoice', async () => {
    it(`should change the order status to '${dataOrderStatuses.paymentAccepted.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.updateOrderStatus(page, dataOrderStatuses.paymentAccepted.name);
      expect(textResult).to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });

    it('should get the first invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getFirstInvoiceFileName', baseContext);

      firstFileName = await boOrdersViewBlockTabListPage.getFileName(page);
      expect(filePath).is.not.equal('');
    });
  });

  // 3 - Create second invoice
  describe('Create the second invoice', async () => {
    it('should add the same ordered product and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await boOrdersViewBlockProductsPage.searchProduct(page, firstProduct.name);

      const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.errorAddSameProductInInvoice(firstFileName));
    });

    it('should create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewInvoice1', baseContext);

      await boOrdersViewBlockProductsPage.selectInvoice(page);

      const carrierName = await boOrdersViewBlockProductsPage.getNewInvoiceCarrierName(page);
      expect(carrierName).to.contains(`Carrier : ${dataCarriers.clickAndCollect.name}`);

      const isSelected = await boOrdersViewBlockProductsPage.isFreeShippingSelected(page);
      expect(isSelected).to.eq(false);
    });

    it('should update the product price and add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePriceAddProduct', baseContext);

      await boOrdersViewBlockProductsPage.updateProductPrice(page, newProductPrice);

      const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page, 2, true);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
    });

    it('should check that order total price is correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPayIsCorrect', baseContext);

      const totalPrice = await boOrdersViewBlockProductsPage.getOrderTotalPrice(page);
      expect(totalPrice.toFixed(2)).to.equal((newProductPrice * 3).toFixed(2));
    });

    it('should check that products number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await boOrdersViewBlockProductsPage.reloadPage(page);

      const productCount = await boOrdersViewBlockProductsPage.getProductsNumber(page);
      expect(productCount).to.equal(2);
    });

    it('should check that invoices number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber', baseContext);

      const documentsNumber = await boOrdersViewBlockTabListPage.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(2);
    });

    it('should get the second invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getSecondInvoiceNumber', baseContext);

      secondFileName = await boOrdersViewBlockTabListPage.getFileName(page, 3);
      expect(filePath).is.not.equal('');
    });
  });

  // 4 - Check first invoice
  describe('Check the first invoice', async () => {
    it('should download the first invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadFirstInvoice', baseContext);

      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page, 1);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });

    it('should check that the \'Product reference, Product name\' are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference', baseContext);

      const productReferenceExist = await utilsFile.isTextInPDF(
        filePath,
        `${firstProduct.reference}, ,${firstProduct.name}`,
      );
      expect(productReferenceExist, 'Product name and reference are not correct!').to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUnitPrice', baseContext);

      const priceVisible = await utilsFile.isTextInPDF(
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

      await boOrdersViewBlockProductsPage.modifyProductPriceForMultiInvoice(page, 1, secondNewProductPrice);

      let result = await boOrdersViewBlockProductsPage.getProductDetails(page, 1);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated on first product').to.equal(secondNewProductPrice),
        expect(result.total, 'Total price was not updated on first product').to.equal(secondNewProductPrice),
      ]);

      result = await boOrdersViewBlockProductsPage.getProductDetails(page, 2);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated on second product').to.equal(secondNewProductPrice),
        expect(result.total, 'Total price was not updated on second product').to.equal(secondNewProductPrice * 2),
      ]);
    });
  });

  // 5 - Check multi invoice (1 + 2)
  describe('Check multi invoice', async () => {
    it('should click on \'View invoice\' button to download the 2 invoices '
      + 'check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

      filePath = await boOrdersViewBlockProductsPage.viewInvoice(page);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist, 'File is not downloaded!').to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct on the first invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceOnFirstInvoice', baseContext);

      const priceVisible = await utilsFile.isTextInPDF(
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

      const priceVisible = await utilsFile.isTextInPDF(
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

      const isTabOpened = await boOrdersViewBlockTabListPage.goToCarriersTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should click on \'Edit\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnEditLink(page);
      expect(isModalVisible, 'Edit shipping modal is not visible!').to.eq(true);
    });

    it(`should select the default not free carrier '${dataCarriers.myCarrier.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewCarrier', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.setShippingDetails(page, carrierDataToSelect);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
    });

    it(`should search for the product '${secondProduct.name}' and check that there is `
      + 'two invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSecondProduct', baseContext);

      await boOrdersViewBlockProductsPage.searchProduct(page, secondProduct.name);

      const invoices = await boOrdersViewBlockProductsPage.getInvoicesFromSelectOptions(page);
      expect(invoices).to.contains(`#${firstFileName}#${secondFileName}`);
    });

    it('should create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewInvoice2', baseContext);

      await boOrdersViewBlockProductsPage.selectInvoice(page);

      const carrierName = await boOrdersViewBlockProductsPage.getNewInvoiceCarrierName(page);
      expect(carrierName).to.contains(`Carrier : ${dataCarriers.myCarrier.name}`);

      const isSelected = await boOrdersViewBlockProductsPage.isFreeShippingSelected(page);
      expect(isSelected).to.eq(false);
    });

    it('should select \'Free shipping\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectFreeShippingCheckbox', baseContext);

      await boOrdersViewBlockProductsPage.selectFreeShippingCheckbox(page);

      const isSelected = await boOrdersViewBlockProductsPage.isFreeShippingSelected(page);
      expect(isSelected).to.eq(true);
    });

    it(`should add the product '${secondProduct.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondProductToCart', baseContext);

      const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page, 1, true);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
    });

    it('should check that invoices number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber2', baseContext);

      await boOrdersViewBlockProductsPage.reloadPage(page);

      const documentsNumber = await boOrdersViewBlockTabListPage.getDocumentsNumber(page);
      expect(documentsNumber).to.be.equal(3);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable', baseContext);

      const isVisible = await boOrdersViewBlockProductsPage.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is not visible').to.eq(true);
    });

    it('should check the discount \'[Generated] CartRule for Free Shipping\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName', baseContext);

      const discountName = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'name');
      expect(discountName).to.be.equal('[Generated] CartRule for Free Shipping');
    });
  });

  // 7 - Check the third invoice
  describe('Check the third invoice', async () => {
    it('should download the third invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadThirdInvoice', baseContext);

      filePath = await boOrdersViewBlockTabListPage.downloadInvoice(page, 5);
      expect(filePath).to.not.eq(null);

      const doesFileExist = await utilsFile.doesFileExist(filePath, 5000);
      expect(doesFileExist).to.eq(true);
    });

    it('should check that the \'Product reference, Product name\' are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference2', baseContext);

      const productReferenceExist = await utilsFile.isTextInPDF(
        filePath,
        `${secondProduct.reference}, ,${secondProduct.name}`,
      );
      expect(productReferenceExist, 'Product name and reference are not correct!').to.eq(true);
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceForThirdInvoice', baseContext);

      const priceVisible = await utilsFile.isTextInPDF(
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
        const isShippingCostVisible = await utilsFile.isTextInPDF(
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
});
