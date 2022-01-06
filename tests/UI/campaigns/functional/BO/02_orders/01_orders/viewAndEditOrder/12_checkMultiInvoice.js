require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderSpecificProductTest} = require('@commonTests/FO/createOrder');
const {createProductTest, deleteProductTest} = require('@commonTests/BO/createDeleteProduct');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');
const {Carriers} = require('@data/demo/carriers');

// Import faker data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_checkMultiInvoice';

let browserContext;
let page;
let filePath;

let firstFileName = '';
let secondFileName = '';
const newProductPrice = 35.50;
const secondNewProductPrice = 25.55;

// First product to create
const firstProduct = new ProductFaker({
  name: 'First product',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
});

// Second product to create
const secondProduct = new ProductFaker({
  name: 'second product',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
});

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: firstProduct.name,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

const carrierDataToSelect = {trackingNumber: '', carrier: Carriers.myCarrier.name, shippingCost: '€8.40'};

/*
Pre-condition :
- Create 2 products
- Create order by default customer
Scenario :
- Try to add same product to the cart => check error message
- Edit the product price and create new invoice
- Check the 2 invoices
- Add another product, create new invoice with free shipping
- Check the invoice
Post-condition
- Delete the created products
 */

describe('BO - Orders - View and edit order: Check multi invoice', async () => {
  // PRE-condition: Create first product
  createProductTest(firstProduct, baseContext);

  // PRE-condition: Create second product
  createProductTest(secondProduct, baseContext);

  // Pre-condition: Create order by default customer
  createOrderSpecificProductTest(orderByCustomerData, baseContext);

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
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterOrderTable', baseContext);

      const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
      await expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn, 'Lastname is not correct').to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle, 'Error when view order page!').to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Create first invoice
  describe('Create the first invoice', async () => {
    it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus', baseContext);

      const textResult = await viewOrderPage.updateOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it('should get the first invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getFirstInvoiceFileName', baseContext);

      firstFileName = await viewOrderPage.getFileName(page);
      await expect(filePath).is.not.equal('');
    });
  });

  // 3 - Create second invoice
  describe('Create the second invoice', async () => {
    it('should add the same ordered product and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkErrorMessage', baseContext);

      await viewOrderPage.searchProduct(page, firstProduct.name);

      const textResult = await viewOrderPage.addProductToCart(page);
      await expect(textResult).to.contains(viewOrderPage.errorAddSameProductInInvoice(firstFileName));
    });

    it('should create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewInvoice1', baseContext);

      await viewOrderPage.createNewInvoice(page);

      const carrierName = await viewOrderPage.getNewInvoiceCarrierName(page);
      await expect(carrierName).to.contains(`Carrier : ${Carriers.default.name}`);

      const isSelected = await viewOrderPage.isFreeShippingSelected(page);
      await expect(isSelected).to.be.false;
    });

    it('should update the product price and add the product to the cart', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePriceAddProduct', baseContext);

      await viewOrderPage.updateProductPrice(page, newProductPrice);

      const textResult = await viewOrderPage.addProductToCart(page, 2, true);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check that order total price is correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPayIsCorrect', baseContext);

      const totalPrice = await viewOrderPage.getOrderTotalPrice(page);
      await expect(totalPrice.toFixed(2)).to.equal((newProductPrice * 3).toFixed(2));
    });

    it('should check that products number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      await viewOrderPage.reloadPage(page);

      const productCount = await viewOrderPage.getProductsNumber(page);
      await expect(productCount).to.equal(2);
    });

    it('should check that invoices number is equal to 2', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber', baseContext);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(2);
    });

    it('should get the second invoice file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getSecondInvoiceNumber', baseContext);

      secondFileName = await viewOrderPage.getFileName(page, 3);
      await expect(filePath).is.not.equal('');
    });
  });

  // 4 - Check first invoice
  describe('Check the first invoice', async () => {
    it('should download the first invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadFirstInvoice', baseContext);

      filePath = await viewOrderPage.downloadInvoice(page, 1);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });

    it('should check that the \'Product reference, Product name\' are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference', baseContext);

      const productReferenceExist = await files.isTextInPDF(
        filePath,
        `${firstProduct.reference}, ,  ${firstProduct.name}`,
      );
      await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUnitPrice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${firstProduct.name}, ,  `
        + `€${newProductPrice.toFixed(2)}, ,  `
        + '1, ,  '
        + `€${newProductPrice.toFixed(2)}`,
      );
      await expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct')
        .to.be.true;
    });

    it('should edit the product price and check the price of the 2 products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProductPrice', baseContext);

      await viewOrderPage.modifyProductPriceForMultiInvoice(page, 1, secondNewProductPrice);

      let result = await viewOrderPage.getProductDetails(page, 1);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated').to.equal(secondNewProductPrice),
        expect(result.total, 'Total price was not updated').to.equal(secondNewProductPrice * 2),
      ]);

      result = await viewOrderPage.getProductDetails(page, 2);
      await Promise.all([
        expect(result.basePrice, 'Base price was not updated').to.equal(secondNewProductPrice),
        expect(result.total, 'Total price was not updated').to.equal(secondNewProductPrice),
      ]);
    });
  });

  // 5 - Check multi invoice (1 + 2)
  describe('Check multi invoice', async () => {
    it('should click on \'View invoice\' button to download the 2 invoices '
      + 'check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

      filePath = await viewOrderPage.viewInvoice(page);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'File is not downloaded!').to.be.true;
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct on the first invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceOnFirstInvoice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${firstProduct.name}, ,  `
        + `€${secondNewProductPrice.toFixed(2)}, ,  `
        + '1, ,  '
        + `€${secondNewProductPrice.toFixed(2)}`,
      );
      await expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct on the first invoice')
        .to.be.true;
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct on the second invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceOnSecondInvoice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${firstProduct.name}, ,  `
        + `€${(secondNewProductPrice).toFixed(2)}, ,  `
        + '2, ,  '
        + `€${(secondNewProductPrice * 2).toFixed(2)}`,
      );
      await expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct on the second invoice')
        .to.be.true;
    });
  });

  // 6 - Create a third invoice with free shipping
  describe('Create the third invoice and check the option \'Free shipping\'', async () => {
    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await viewOrderPage.goToCarriersTab(page);
      await expect(isTabOpened).to.be.true;
    });

    it('should click on \'Edit\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

      const isModalVisible = await viewOrderPage.clickOnEditLink(page);
      await expect(isModalVisible, 'Edit shipping modal is not visible!').to.be.true;
    });

    it(`should select the default not free carrier '${Carriers.myCarrier.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectNewCarrier', baseContext);

      const textResult = await viewOrderPage.setShippingDetails(page, carrierDataToSelect);
      await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
    });

    it(`should search for the product '${secondProduct.name}' and check that there is `
      + 'two invoices', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSecondProduct', baseContext);

      await viewOrderPage.searchProduct(page, secondProduct.name);

      const invoices = await viewOrderPage.getInvoicesFromSelectOptions(page);
      await expect(invoices).to.contains(`#${firstFileName}#${secondFileName}`);
    });

    it('should create a new invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewInvoice2', baseContext);

      await viewOrderPage.createNewInvoice(page);

      const carrierName = await viewOrderPage.getNewInvoiceCarrierName(page);
      await expect(carrierName).to.contains(`Carrier : ${Carriers.myCarrier.name}`);

      const isSelected = await viewOrderPage.isFreeShippingSelected(page);
      await expect(isSelected).to.be.false;
    });

    it('should select \'Free shipping\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectFreeShippingCheckbox', baseContext);

      await viewOrderPage.selectFreeShippingCheckbox(page);

      const isSelected = await viewOrderPage.isFreeShippingSelected(page);
      await expect(isSelected).to.be.true;
    });

    it(`should add the product '${secondProduct.name}' to the cart`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addSecondProductToCart', baseContext);

      const textResult = await viewOrderPage.addProductToCart(page, 1, true);
      await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
    });

    it('should check that invoices number is equal to 3', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentsNumber2', baseContext);

      await viewOrderPage.reloadPage(page);

      const documentsNumber = await viewOrderPage.getDocumentsNumber(page);
      await expect(documentsNumber).to.be.equal(3);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable', baseContext);

      const isVisible = await viewOrderPage.isDiscountListTableVisible(page);
      await expect(isVisible, 'Discount list table is not visible').to.be.true;
    });

    it('should check the discount \'[Generated] CartRule for Free Shipping\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName', baseContext);

      const discountName = await viewOrderPage.getTextColumnFromDiscountTable(page, 'name');
      await expect(discountName).to.be.equal('[Generated] CartRule for Free Shipping');
    });
  });

  // 7 - Check the third invoice
  describe('Check the third invoice', async () => {
    it('should download the third invoice', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadThirdInvoice', baseContext);

      filePath = await viewOrderPage.downloadInvoice(page, 5);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist).to.be.true;
    });

    it('should check that the \'Product reference, Product name\' are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference2', baseContext);

      const productReferenceExist = await files.isTextInPDF(
        filePath,
        `${secondProduct.reference}, ,  ${secondProduct.name}`,
      );
      await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
    });

    it('should check that the \'Unit Price, Quantity, Total (Tax excl.)\' '
      + 'are correct', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPriceForThirdInvoice', baseContext);

      const priceVisible = await files.isTextInPDF(
        filePath,
        `${secondProduct.name}, ,  `
        + `€${secondProduct.price.toFixed(2)}, ,  `
        + '1, ,  '
        + `€${secondProduct.price.toFixed(2)}`,
      );
      await expect(
        priceVisible,
        'Unit Price, Quantity, Total (Tax excl.) are not correct')
        .to.be.true;
    });

    it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingForThirdInvoice', baseContext);

        // Total Products, Shipping Costs, Total (Tax excl.), Total
        const isShippingCostVisible = await files.isTextInPDF(
          filePath,
          `Total Products, ,  €${secondProduct.price.toFixed(2)},  `
          + 'Shipping Costs, ,  Free Shipping,,  '
          + `Total (Tax excl.), ,  €${secondProduct.price.toFixed(2)},,  `
          + `Total, ,  €${secondProduct.price.toFixed(2)}`,
        );
        await expect(
          isShippingCostVisible,
          'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
        ).to.be.true;
      });
  });


  // Post-condition - Delete the first created products
  deleteProductTest(firstProduct, baseContext);

  // Post-condition - Delete the second created products
  deleteProductTest(secondProduct, baseContext);
});
