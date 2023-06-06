// Import utils
import basicHelper from '@utils/basicHelper';
import date from '@utils/date';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {bulkDeleteProductsTest} from '@commonTests/BO/catalog/product';
import {enableEcoTaxTest, disableEcoTaxTest} from '@commonTests/BO/international/ecoTax';
import loginCommon from '@commonTests/BO/loginBO';
import {createOrderByCustomerTest, createOrderSpecificProductTest} from '@commonTests/FO/order';
import {
  disableNewProductPageTest,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import BO pages
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import orderPageCustomerBlock from '@pages/BO/orders/view/customerBlock';
import orderPagePaymentBlock from '@pages/BO/orders/view/paymentBlock';
import orderPageProductsBlock from '@pages/BO/orders/view/productsBlock';
import orderPageTabListBlock from '@pages/BO/orders/view/tabListBlock';

// Import data
import Addresses from '@data/demo/address';
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import OrderStatuses from '@data/demo/orderStatuses';
import PaymentMethods from '@data/demo/paymentMethods';
import Products from '@data/demo/products';
import ProductData from '@data/faker/product';
import OrderData from '@data/faker/order';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import OrderShippingData from '@data/faker/orderShipping';
import type {OrderPayment} from '@data/types/order';
import type {ProductDiscount} from '@data/types/product';

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_checkInvoice';

/*
Pre-conditions:
- Create virtual product
- Create customized product
- Create product with specific price
- Create product with ecotax
- Enable ecoTax
Scenario:
- Create invoice with virtual product then check all invoice
- Create invoice with customized product then check all invoice
- Add product with specific price then check all invoice
- Add product with ecoTax then check all invoice
- Change invoice address and delivery address then check invoice
- Change carrier then check invoice
- Add discount then check all invoice
- Add note then check invoice
- Add payment method then check invoice
Post-conditions:
- Delete created products
- Disable EcoTax
- Delete discount
*/
describe('BO - Orders - View and edit order: Check invoice', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let filePath: string | null;
  let fileName: string = '';
  let orderReference: string = '';

  const today: string = date.getDateFormat('mm/dd/yyyy');
  // Prefix for the new products to simply delete them by bulk actions
  const prefixNewProduct: string = 'TOTEST';
  // First order by customer data
  const firstOrderByCustomer: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: Products.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  // Customized product data
  const customizedProduct: ProductData = new ProductData({
    name: `Customized product ${prefixNewProduct}`,
    type: 'Standard product',
    reference: 'bbcdef',
    taxRule: 'No tax',
    customization: {
      label: 'Type your text here',
      type: 'Text',
      required: true,
    },
  });
  // Second order by customer
  const secondOrderByCustomer: OrderData = new OrderData({
    customer: Customers.johnDoe,
    products: [
      {
        product: customizedProduct,
        quantity: 1,
      },
    ],
    paymentMethod: PaymentMethods.wirePayment,
  });
  // Virtual product data
  const virtualProduct: ProductData = new ProductData({
    name: `Virtual product ${prefixNewProduct}`,
    type: 'Virtual product',
    quantity: 20,
    tax: 20,
    taxRule: 'FR Taux standard (20%)',
    stockLocation: 'stock 1',
  });
  // Product with specific price data
  const productWithSpecificPrice: ProductData = new ProductData({
    name: `Product with sp price ${prefixNewProduct}`,
    reference: 'abcdef',
    type: 'Standard product',
    taxRule: 'No tax',
    quantity: 20,
    specificPrice: {
      attributes: null,
      discount: 35,
      startingAt: 1,
      reductionType: '%',
    },
  });
  // Product with ecoTax data
  const productWithEcoTax: ProductData = new ProductData({
    name: `Product with ecotax ${prefixNewProduct}`,
    type: 'Standard product',
    taxRule: 'No tax',
    quantity: 20,
    minimumQuantity: 1,
  });
  // Discount data
  const discountData: ProductDiscount = {
    name: 'Discount',
    type: 'Percent',
    value: '65',
  };
  // Payment data
  const paymentData: OrderPayment = {
    date: today,
    paymentMethod: 'Payments by check',
    transactionID: 12190,
    amount: parseFloat((productWithEcoTax.price).toFixed(2)),
    currency: '€',
  };

  // Pre-condition - Create first order from FO
  createOrderByCustomerTest(firstOrderByCustomer, `${baseContext}_preTest_1`);

  // Pre-condition - Enable Ecotax
  enableEcoTaxTest(`${baseContext}_preTest_2`);

  // Pre-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition - Create 4 products
  [
    virtualProduct,
    customizedProduct,
    productWithSpecificPrice,
    productWithEcoTax,
  ].forEach((product: ProductData, index: number) => {
    describe(`PRE-TEST: Create product '${product.name}'`, async () => {
      if (index === 0) {
        it('should login in BO', async function () {
          await loginCommon.loginBO(this, page);
        });

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

          await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
          await productsPage.closeSfToolBar(page);

          const pageTitle = await productsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productsPage.pageTitle);
        });

        it('should reset all filters and get number of products', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersBeforeCreate', baseContext);

          numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
          await expect(numberOfProducts).to.be.above(0);
        });
      }

      it('should go to add product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

        if (index === 0) {
          await productsPage.goToAddProductPage(page);
        } else {
          await addProductPage.goToAddProductPage(page);
        }

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should create Product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, product);

        if (product === customizedProduct) {
          await addProductPage.addCustomization(page, product.customization);
        }
        if (product === productWithSpecificPrice) {
          await addProductPage.addSpecificPrices(page, product.specificPrice);
        }
        if (product === productWithEcoTax) {
          await addProductPage.addEcoTax(page, product.ecoTax);
        }
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });
  });

  // Pre-condition - Create second order from FO
  createOrderSpecificProductTest(secondOrderByCustomer, baseContext);

  // 1 - Create and check first invoice contain 'Virtual product'
  describe(`Check invoice contain '${virtualProduct.name}'`, async () => {
    describe('Go to view order page', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters1', baseContext);

        const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfOrders).to.be.above(0);
      });

      it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

        const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
        await expect(textColumn).to.contains(Customers.johnDoe.lastName);
      });

      it('should view the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock1', baseContext);

        await ordersPage.goToOrder(page, 2);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });
    });

    describe('Create invoice', async () => {
      it('should delete the ordered product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderedProduct', baseContext);

        const textResult = await orderPageProductsBlock.deleteProduct(page, 1);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulDeleteProductMessage);
      });

      it(`should search for the product '${virtualProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProduct1', baseContext);

        await orderPageProductsBlock.searchProduct(page, virtualProduct.name);

        const result = await orderPageProductsBlock.getSearchedProductInformation(page);
        await expect(result.available).to.equal(virtualProduct.quantity - 1);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page, 13);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
      });

      it('should change the \'Invoice address\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress1', baseContext);

        const addressToSelect = `${Addresses.third.id}- ${Addresses.third.address} ${Addresses.third.secondAddress} `
          + `${Addresses.third.postalCode} ${Addresses.third.city}`;

        const alertMessage = await orderPageCustomerBlock.selectAnotherInvoiceAddress(page, addressToSelect);
        expect(alertMessage).to.contains(orderPageCustomerBlock.successfulUpdateMessage);
      });

      it(`should change the order status to '${OrderStatuses.paymentAccepted.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus1', baseContext);

        const textResult = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.paymentAccepted.name);
        await expect(textResult).to.equal(OrderStatuses.paymentAccepted.name);
      });

      it('should check that there is no carrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

        const carriersNumber = await orderPageTabListBlock.getCarriersNumber(page);
        await expect(carriersNumber).to.be.equal(0);
      });

      it('should get the invoice file name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getInvoiceFileName1', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        await expect(filePath).is.not.equal('');
      });

      it('should get the order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference1', baseContext);

        orderReference = await orderPageTabListBlock.getOrderReference(page);
        await expect(orderReference).is.not.equal('');
      });

      it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice1', baseContext);

        filePath = await orderPageTabListBlock.viewInvoice(page);
        await expect(filePath).to.be.not.null;

        const doesFileExist = await files.doesFileExist(filePath, 5000);
        await expect(doesFileExist, 'File is not downloaded!').to.be.true;
      });
    });

    describe('Check the invoice', async () => {
      // Check: Header, Delivery address, Billing address, Invoice number, Invoice date, Order reference and date
      describe('Check Header', async () => {
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/22581
        it.skip('should check the header of the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkHeaderInvoice1', baseContext);

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber, 'Logo is not visible!').to.be.equal(1);

          const isVisible = await files.isTextInPDF(filePath, `INVOICE,,${today},,#${fileName}`);
          await expect(isVisible, 'File name header is not correct!').to.be.true;
        });

        it('should check that the \'Delivery address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryAddress1', baseContext);

          // Check delivery address
          const deliveryAddressExist = await files.isTextInPDF(
            filePath,
            'Delivery address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(deliveryAddressExist, 'Delivery address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Billing address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress1', baseContext);

          const billingAddressExist = await files.isTextInPDF(
            filePath,
            'Billing address,,'
            + `${Addresses.third.firstName} ${Addresses.third.lastName},`
            + `${Addresses.third.company},`
            + `${Addresses.third.address} ${Addresses.third.secondAddress},`
            + `${Addresses.third.city}, ${Addresses.third.state} ${Addresses.third.postalCode},`
            + `${Addresses.third.country},`
            + `${Addresses.third.phone}`,
          );
          await expect(billingAddressExist, 'Billing address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Invoice number, Invoice date, Order reference and Order date\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceNumber1', baseContext);

            const invoiceNumberExist = await files.isTextInPDF(
              filePath,
              'Invoice Number, ,Invoice Date, ,Order Reference, ,Order date,,'
              + `#${fileName}, ,${today}, ,${orderReference}, ,${today},`,
            );
            await expect(invoiceNumberExist, 'Invoice information are not correct!').to.be.true;
          });
      });

      // Check Products table: Check Product reference, Product name
      describe('Check Products table', async () => {
        it('should check that the \'Product reference, Product name\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference1', baseContext);

          const productReferenceExist = await files.isTextInPDF(
            filePath,
            `${virtualProduct.reference}, ,${virtualProduct.name}`,
          );
          await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
        });

        it('should check that the \'Product Tax Rate, Unit Price (tax excl.), quantity and Product Total price'
          + '(tax excl.)\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkTaxRate1', baseContext);

          const productPriceExist = await files.isTextInPDF(
            filePath,
            `${virtualProduct.name}, ,`
            + `${virtualProduct.tax} %, ,`
            + `€${virtualProduct.priceTaxExcluded.toFixed(2)}, ,`
            + '13, ,'
            + `€${(virtualProduct.priceTaxExcluded * 13).toFixed(2)}`,
          );
          await expect(
            productPriceExist,
            'Product Tax Rate, unit price (tax exl.), quantity and Total price (tax excl.) are not correct!',
          ).to.be.true;
        });
      });

      describe('Check Taxes table', async () => {
        it('should check that \'Tax Detail, Tax Rate, Base price, Total tax\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkTaxesTable1', baseContext);

          const taxDetailsVisible = await files.isTextInPDF(
            filePath,
            'Tax Detail, ,Tax Rate, ,Base price, ,Total Tax,,'
            + 'Products, ,'
            + '20.000 %, ,'
            + `€${(virtualProduct.priceTaxExcluded * 13).toFixed(2)}, ,`
            + `€${((virtualProduct.price - virtualProduct.priceTaxExcluded) * 13)
              .toFixed(2)}`,
          );
          await expect(
            taxDetailsVisible,
            'Tax detail, tax Rate, Base price and Total tax are not correct!',
          ).to.be.true;
        });
      });

      describe('Check Payments table', async () => {
        it('should check that the \'Payment method and Total\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMethod1', baseContext);

          const paymentMethodExist = await files.isTextInPDF(
            filePath,
            'Payment Method, ,Bank transfer, ,'
            + `€${(virtualProduct.price * 13).toFixed(2)}`,
          );
          await expect(paymentMethodExist, 'Payment method and total to pay are not correct!').to.be.true;
        });

        it('should check that the carrier is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierNotVisible', baseContext);

          const isCarrierVisible = await files.isTextInPDF(filePath, `Carrier, ${Carriers.default.name}`);
          await expect(isCarrierVisible, `Carrier '${Carriers.default.name}' is visible!`).to.be.false;
        });
      });

      describe('Check Total to pay table', async () => {
        it('should check that \'Total Products, Total(Tax exc.), Total Tax, Total\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPay1', baseContext);

            const totalPriceTaxExcl = virtualProduct.priceTaxExcluded * 13;
            const priceTaxIncl = virtualProduct.price * 13;
            const tax = virtualProduct.price - virtualProduct.priceTaxExcluded;

            // Total Products, Total (Tax excl.), Total Tax, Total
            const isPaymentTableCorrect = await files.isTextInPDF(
              filePath,
              `Total Products, ,€${totalPriceTaxExcl.toFixed(2)},,`
              + `Total (Tax excl.), ,€${totalPriceTaxExcl.toFixed(2)},,`
              + `Total Tax, ,€${(tax * 13).toFixed(2)},,`
              + `Total, ,€${priceTaxIncl.toFixed(2)}`,
            );
            await expect(
              isPaymentTableCorrect,
              'Total Products, Total(Tax exc.), Total Tax, Total are not correct!',
            ).to.be.true;
          });
      });
    });
  });

  // 2 - Create and check second invoice contain 'Customized product'
  describe(`Check invoice contain '${customizedProduct.name}'`, async () => {
    describe('Go to view order page', async () => {
      it('should go to \'Orders > Orders\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTableFilters2', baseContext);

        const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
        await expect(numberOfOrders).to.be.above(0);
      });

      it(`should filter the Orders table by 'Customer: ${Customers.johnDoe.lastName}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

        await ordersPage.filterOrders(page, 'input', 'customer', Customers.johnDoe.lastName);

        const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
        await expect(textColumn).to.contains(Customers.johnDoe.lastName);
      });

      it('should view the order', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderPageTabListBlock2', baseContext);

        await ordersPage.goToOrder(page, 1);

        const pageTitle = await orderPageTabListBlock.getPageTitle(page);
        await expect(pageTitle).to.contains(orderPageTabListBlock.pageTitle);
      });
    });

    describe('Create invoice', async () => {
      it(`should change the order status to '${OrderStatuses.paymentAccepted.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatus2', baseContext);

        const textResult = await orderPageTabListBlock.modifyOrderStatus(page, OrderStatuses.paymentAccepted.name);
        await expect(textResult).to.equal(OrderStatuses.paymentAccepted.name);
      });

      it('should get the invoice file name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getInvoiceFileName2', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        await expect(filePath).is.not.equal('');
      });

      it('should get the order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference2', baseContext);

        orderReference = await orderPageTabListBlock.getOrderReference(page);
        await expect(orderReference).is.not.equal('');
      });

      it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice2', baseContext);

        filePath = await orderPageTabListBlock.viewInvoice(page);
        await expect(filePath).is.not.null;

        const doesFileExist = await files.doesFileExist(filePath, 5000);
        await expect(doesFileExist, 'File is not downloaded!').to.be.true;
      });
    });

    describe('Check the invoice', async () => {
      // Check: Header, Delivery address, Billing address, Invoice number, Invoice date, Order reference and date
      describe('Check Header', async () => {
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/22581
        it.skip('should check the header of the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkHeaderInvoice2', baseContext);

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber, 'Logo is not visible!').to.be.equal(1);

          const isVisible = await files.isTextInPDF(filePath, `INVOICE,,${today},,#${fileName}`);
          await expect(isVisible, 'File name header is not correct!').to.be.true;
        });

        it('should check that the \'Delivery address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryAddress2', baseContext);

          // Check delivery address
          const deliveryAddressExist = await files.isTextInPDF(
            filePath,
            'Delivery address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(deliveryAddressExist, 'Delivery address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Billing address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress2', baseContext);

          const billingAddressExist = await files.isTextInPDF(
            filePath,
            'Billing address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(billingAddressExist, 'Billing address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Invoice number, Invoice date, Order reference and Order date\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceNumber2', baseContext);

            const invoiceNumberExist = await files.isTextInPDF(
              filePath,
              'Invoice Number, ,Invoice Date, ,Order Reference, ,Order date,,'
              + `#${fileName}, ,${today}, ,${orderReference}, ,${today},`,
            );
            await expect(invoiceNumberExist, 'Invoice information are not correct!').to.be.true;
          });
      });

      describe('Check Products table', async () => {
        it('should check that the \'Product reference, Product name\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference2', baseContext);

          const productReferenceExist = await files.isTextInPDF(
            filePath,
            `${customizedProduct.reference}, ,${customizedProduct.name}`,
          );
          await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
        });

        it('should check that the customized text is visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedText', baseContext);

          const isCustomizedTextVisible = await files.isTextInPDF(
            filePath,
            `${customizedProduct.customization}: text,(1)`,
          );
          await expect(isCustomizedTextVisible, 'Customized text is not visible!').to.be.false;
        });

        it('should check that the \'Unit Price (tax excl.), quantity and Product Total price '
          + '(tax excl.)\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductCustomizedProduct', baseContext);

          const productPriceExist = await files.isTextInPDF(
            filePath,
            `${customizedProduct.name}, ,`
            + `€${customizedProduct.price.toFixed(2)}, ,`
            + '1, ,'
            + `€${customizedProduct.price.toFixed(2)}`,
          );
          await expect(
            productPriceExist,
            'Unit Price (tax excl.), quantity and Product Total price are not correct!',
          ).to.be.true;
        });
      });

      describe('Check that Taxes table is not visible', async () => {
        it('should check that \'Tax Detail\' table is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkIsTaxesTableNotVisible', baseContext);

          const isTaxTableVisible = await files.isTextInPDF(filePath, 'Tax Detail,Tax Rate,Base price,Total Tax');
          await expect(isTaxTableVisible, 'Tax table is visible!').to.be.false;
        });
      });

      describe('Check Payments table', async () => {
        it('should check that the \'Payment method and Total\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMethod2', baseContext);

          const paymentMethodExist = await files.isTextInPDF(
            filePath,
            'Payment Method, ,Bank transfer, ,'
            + `€${(customizedProduct.price).toFixed(2)}`,
          );
          await expect(paymentMethodExist, 'Payment method and total to pay are not correct!').to.be.true;
        });

        it('should check that the carrier is visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierVisible2', baseContext);

          const isCarrierVisible = await files.isTextInPDF(filePath, Carriers.default.name);
          await expect(isCarrierVisible, `Carrier '${Carriers.default.name}' is not visible!`).to.be.true;
        });
      });

      describe('Check Total to pay table', async () => {
        it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPay2', baseContext);

            // Total Products, Shipping Costs, Total (Tax excl.), Total
            const isShippingCostVisible = await files.isTextInPDF(
              filePath,
              `Total Products, ,€${customizedProduct.price.toFixed(2)},`
              + 'Shipping Costs, ,Free Shipping,,'
              + `Total (Tax excl.), ,€${customizedProduct.price.toFixed(2)},,`
              + `Total, ,€${customizedProduct.price.toFixed(2)}`,
            );
            await expect(
              isShippingCostVisible,
              'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
            ).to.be.true;
          });
      });
    });
  });

  // 3 - Check invoice contain 'Customized product' and 'Product with specific price'
  describe(`Check invoice contain '${productWithSpecificPrice.name}' and '${customizedProduct.name}'`, async () => {
    describe('Create invoice', async () => {
      it(`should search for the product '${productWithSpecificPrice.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProduct2', baseContext);

        await orderPageProductsBlock.searchProduct(page, productWithSpecificPrice.name);

        const result = await orderPageProductsBlock.getSearchedProductInformation(page);
        await expect(result.available).to.equal(productWithSpecificPrice.quantity - 1);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page, 1);
        await expect(textResult).to.contains(orderPageTabListBlock.successfulAddProductMessage);
      });

      it('should get the invoice file name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getInvoiceFileName3', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        await expect(filePath).is.not.equal('');
      });

      it('should get the order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference3', baseContext);

        orderReference = await orderPageTabListBlock.getOrderReference(page);
        await expect(orderReference).is.not.equal('');
      });

      it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice3', baseContext);

        filePath = await orderPageTabListBlock.viewInvoice(page);
        await expect(filePath).to.be.not.null;

        const doesFileExist = await files.doesFileExist(filePath, 5000);
        await expect(doesFileExist, 'File is not downloaded!').to.be.true;
      });
    });

    describe('Check the invoice', async () => {
      // Check: Header, Delivery address, Billing address, Invoice number, Invoice date, Order reference and date
      describe('Check Header', async () => {
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/22581
        it.skip('should check the header of the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkHeaderInvoice3', baseContext);

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber, 'Logo is not visible!').to.be.equal(1);

          const isVisible = await files.isTextInPDF(filePath, `INVOICE,,${today},,#${fileName}`);
          await expect(isVisible, 'File name header is not correct!').to.be.true;
        });

        it('should check that the \'Delivery address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryAddress3', baseContext);

          // Check delivery address
          const deliveryAddressExist = await files.isTextInPDF(
            filePath,
            'Delivery address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(deliveryAddressExist, 'Delivery address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Billing address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress3', baseContext);

          const billingAddressExist = await files.isTextInPDF(
            filePath,
            'Billing address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(billingAddressExist, 'Billing address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Invoice number, Invoice date, Order reference and Order date\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceNumber3', baseContext);

            const invoiceNumberExist = await files.isTextInPDF(
              filePath,
              'Invoice Number, ,Invoice Date, ,Order Reference, ,Order date,,'
              + `#${fileName}, ,${today}, ,${orderReference}, ,${today},`,
            );
            await expect(invoiceNumberExist, 'Invoice information are not correct!').to.be.true;
          });
      });

      // Check Products table: Check Product reference, Product name
      describe('Check Products table', async () => {
        it('should check that the \'Product reference, Product name\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference3', baseContext);

          const productReferenceExist = await files.isTextInPDF(
            filePath,
            `${productWithSpecificPrice.reference}, ,${productWithSpecificPrice.name}`,
          );
          await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
        });

        it('should check that the column \'Base price (Tax excl.)\' is visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceColumnVisible', baseContext);

          const basePriceColumnVisible = await files.isTextInPDF(filePath, 'Base,price,(Tax excl.)');
          await expect(basePriceColumnVisible, 'Base price is not visible!').to.be.true;
        });

        it('should check that the \'Base price (Tax excl.), Unit Price, Quantity, Total (Tax excl.)\' '
          + 'are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceSpecificPrice', baseContext);

          const discountValue = await basicHelper.percentage(
            productWithSpecificPrice.price,
            productWithSpecificPrice.specificPrice.discount,
          );
          const unitPrice = productWithSpecificPrice.price - discountValue;

          const basePriceVisible = await files.isTextInPDF(
            filePath,
            `${productWithSpecificPrice.name}, ,`
            + `€${productWithSpecificPrice.price.toFixed(2)}, ,`
            + `€${unitPrice.toFixed(2)}, ,`
            + '1, ,'
            + `€${unitPrice.toFixed(2)}`,
          );
          await expect(
            basePriceVisible,
            'Base price (Tax excl.), Unit Price, Quantity, Total (Tax excl.) are not correct!').to.be.true;
        });
      });

      describe('Check that Taxes table is not visible', async () => {
        it('should check that \'Tax Detail\' table is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkIsTaxesTableNotVisible2', baseContext);

          const isTaxTableVisible = await files.isTextInPDF(filePath, 'Tax Detail,Tax Rate,Base price,Total Tax');
          await expect(isTaxTableVisible, 'Tax table is visible!').to.be.false;
        });
      });

      describe('Check Payments table', async () => {
        it('should check that the \'Payment method and Total\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMethod3', baseContext);

          const paymentMethodExist = await files.isTextInPDF(
            filePath,
            'Payment Method, ,Bank transfer, ,'
            + `€${customizedProduct.price.toFixed(2)}`,
          );
          await expect(paymentMethodExist, 'Payment method and total are not correct!').to.be.true;
        });

        it('should check that the carrier is visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierVisible3', baseContext);

          const isCarrierVisible = await files.isTextInPDF(filePath, Carriers.default.name);
          await expect(isCarrierVisible, `Carrier '${Carriers.default.name}' is not visible!`).to.be.true;
        });
      });

      describe('Check Total to pay table', async () => {
        it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPay3', baseContext);

            const discount = await basicHelper.percentage(
              productWithSpecificPrice.price,
              productWithSpecificPrice.specificPrice.discount,
            );
            const unitPrice = productWithSpecificPrice.price - discount;

            const totalPriceTaxExcl = unitPrice + customizedProduct.price;

            // Total Products, Shipping Costs, Total (Tax excl.), Total
            const isShippingCostVisible = await files.isTextInPDF(
              filePath,
              `Total Products, ,€${totalPriceTaxExcl.toFixed(2)},`
              + 'Shipping Costs, ,Free Shipping,,'
              + `Total (Tax excl.), ,€${totalPriceTaxExcl.toFixed(2)},,`
              + `Total, ,€${totalPriceTaxExcl.toFixed(2)}`,
            );
            await expect(
              isShippingCostVisible,
              'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
            ).to.be.true;
          });
      });

      describe('Delete the added product then recheck the invoice', async () => {
        it(`should delete the ordered product '${productWithSpecificPrice.name}' from the list`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteAddedProduct', baseContext);

          const textResult = await orderPageProductsBlock.deleteProduct(page, 1);
          await expect(textResult).to.contains(orderPageProductsBlock.successfulDeleteProductMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice4', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the \'Product name\' is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductName', baseContext);

          const productNameExist = await files.isTextInPDF(filePath, productWithSpecificPrice.name);
          await expect(productNameExist, 'Product name is visible!').to.be.false;
        });

        it('should check that the column \'Base price (Tax excl.)\' is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceColumn', baseContext);

          const basePriceColumnVisible = await files.isTextInPDF(filePath, 'Base,price,(Tax excl.)');
          await expect(basePriceColumnVisible, 'Base price is not visible!').to.be.false;
        });
      });
    });
  });

  // 4 - Check invoice contain 'Customized product' and 'Product with ecoTax'
  describe(`Check invoice contain '${productWithEcoTax.name}' and '${customizedProduct.name}'`, async () => {
    describe('Create invoice', async () => {
      it(`should search for the product '${productWithEcoTax.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProduct3', baseContext);

        await orderPageProductsBlock.searchProduct(page, productWithEcoTax.name);

        const result = await orderPageProductsBlock.getSearchedProductInformation(page);
        await expect(result.available).to.equal(productWithEcoTax.quantity - 1);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart3', baseContext);

        const textResult = await orderPageProductsBlock.addProductToCart(page, 1);
        await expect(textResult).to.contains(orderPageProductsBlock.successfulAddProductMessage);
      });

      it('should get the invoice file name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getInvoiceFileName4', baseContext);

        fileName = await orderPageTabListBlock.getFileName(page);
        await expect(filePath).is.not.equal('');
      });

      it('should get the order reference', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference4', baseContext);

        orderReference = await orderPageTabListBlock.getOrderReference(page);
        await expect(orderReference).is.not.equal('');
      });

      it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice4', baseContext);

        filePath = await orderPageTabListBlock.viewInvoice(page);
        await expect(filePath).to.be.not.null;

        const doesFileExist = await files.doesFileExist(filePath, 5000);
        await expect(doesFileExist, 'File is not downloaded!').to.be.true;
      });
    });

    describe('Check the invoice', async () => {
      // Check: Header, Delivery address, Billing address, Invoice number, Invoice date, Order reference and date
      describe('Check Header', async () => {
        // @todo : https://github.com/PrestaShop/PrestaShop/issues/22581
        it.skip('should check the header of the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkHeaderInvoice4', baseContext);

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber, 'Logo is not visible!').to.be.equal(1);

          const isVisible = await files.isTextInPDF(filePath, `INVOICE,,${today},,#${fileName}`);
          await expect(isVisible, 'File name header is not correct!').to.be.true;
        });

        it('should check that the \'Delivery address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkDeliveryAddress4', baseContext);

          // Check delivery address
          const deliveryAddressExist = await files.isTextInPDF(
            filePath,
            'Delivery address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(deliveryAddressExist, 'Delivery address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Billing address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBillingAddress4', baseContext);

          const billingAddressExist = await files.isTextInPDF(
            filePath,
            'Billing address,,'
            + `${Addresses.second.firstName} ${Addresses.second.lastName},`
            + `${Addresses.second.company},`
            + `${Addresses.second.address},`
            + `${Addresses.second.secondAddress},`
            + `${Addresses.second.postalCode} ${Addresses.second.city},`
            + `${Addresses.second.country},`
            + `${Addresses.second.phone}`,
          );
          await expect(billingAddressExist, 'Billing address is not correct in invoice!').to.be.true;
        });

        it('should check that the \'Invoice number, Invoice date, Order reference and Order date\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoiceNumber4', baseContext);

            const invoiceNumberExist = await files.isTextInPDF(
              filePath,
              'Invoice Number, ,Invoice Date, ,Order Reference, ,Order date,,'
              + `#${fileName}, ,${today}, ,${orderReference}, ,${today},`,
            );
            await expect(invoiceNumberExist, 'Invoice information are not correct!').to.be.true;
          });
      });

      // Check Products table: Check Product reference, Product name
      describe('Check Products table', async () => {
        it('should check that the \'Product reference, Product name\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference4', baseContext);

          const productReferenceExist = await files.isTextInPDF(
            filePath,
            `${productWithEcoTax.reference}, ,${productWithEcoTax.name}`,
          );
          await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
        });

        it('should check that the column \'Base price (Tax excl.)\' is not visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceColumnNotVisible', baseContext);

          const basePriceColumnVisible = await files.isTextInPDF(filePath, 'Base,price,(Tax excl.)');
          await expect(basePriceColumnVisible, 'Base price is visible!').to.be.false;
        });

        it('should check that the \'Unit price (Tax excl.), Ecotax, Quantity, Total (Tax excl.)\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceWithEcoTax', baseContext);

            const basePriceVisible = await files.isTextInPDF(
              filePath,
              `${productWithEcoTax.name}, ,`
              + `€${productWithEcoTax.price.toFixed(2)},,`
              + `Ecotax: €${productWithEcoTax.ecoTax.toFixed(2)},,`
              + '1, ,'
              + `€${productWithEcoTax.price.toFixed(2)}`,
            );
            await expect(basePriceVisible, 'Unit price (Tax excl.), Ecotax, Quantity, '
              + 'Total (Tax excl.) are not correct in invoice!').to.be.true;
          });
      });

      describe('Check Taxes table', async () => {
        it('should check that the \'Tax Detail, Tax Rate, Base price, Total Tax\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkEcoTax', baseContext);

            const taxDetailsVisible = await files.isTextInPDF(
              filePath,
              'Tax Detail, ,Tax Rate, ,Base price, ,Total Tax,,'
              + 'Ecotax, ,'
              + '0.000 %, ,'
              + `€${productWithEcoTax.ecoTax.toFixed(2)}, ,`
              + '€0.00',
            );
            await expect(
              taxDetailsVisible,
              'Tax detail, tax Rate, Base price and Total tax are not correct!',
            ).to.be.true;
          });
      });

      describe('Check Payments table', async () => {
        it('should check that the \'Payment method and Total\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMethod4', baseContext);

          const paymentMethodExist = await files.isTextInPDF(
            filePath,
            'Payment Method, ,Bank transfer, ,'
            + `€${customizedProduct.price.toFixed(2)}`,
          );
          await expect(paymentMethodExist, 'Payment method and total are not correct!').to.be.true;
        });

        it('should check that the carrier is visible', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierVisible4', baseContext);

          const isCarrierVisible = await files.isTextInPDF(filePath, Carriers.default.name);
          await expect(isCarrierVisible, `Carrier '${Carriers.default.name}' is not visible!`).to.be.true;
        });
      });

      describe('Check Total to pay table', async () => {
        it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPay4', baseContext);

            const totalPriceTaxExcl = productWithEcoTax.price + customizedProduct.price;

            // Total Products, Shipping Costs, Total (Tax excl.), Total
            const isShippingCostVisible = await files.isTextInPDF(
              filePath,
              `Total Products, ,€${totalPriceTaxExcl.toFixed(2)},`
              + 'Shipping Costs, ,Free Shipping,,'
              + `Total (Tax excl.), ,€${totalPriceTaxExcl.toFixed(2)},,`
              + `Total, ,€${totalPriceTaxExcl.toFixed(2)}`,
            );
            await expect(
              isShippingCostVisible,
              'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
            ).to.be.true;
          });
      });

      describe('Change \'Shipping address\' and \'Invoice address\' then check the invoice', async () => {
        it('should change the \'Shipping address\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress2', baseContext);

          const addressToSelect = `${Addresses.third.id}- ${Addresses.third.address} ${Addresses.third.secondAddress} `
            + `${Addresses.third.postalCode} ${Addresses.third.city}`;

          const alertMessage = await orderPageCustomerBlock.selectAnotherShippingAddress(page, addressToSelect);
          expect(alertMessage).to.contains(orderPageCustomerBlock.successfulUpdateMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice5', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the \'Delivery address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkEditedDeliveryAddress', baseContext);

          const deliveryAddressExist = await files.isTextInPDF(
            filePath,
            'Delivery address,,'
            + `${Addresses.third.firstName} ${Addresses.third.lastName},`
            + `${Addresses.third.company},`
            + `${Addresses.third.address} ${Addresses.third.secondAddress},`
            + `${Addresses.third.city}, ${Addresses.third.state} ${Addresses.third.postalCode},`
            + `${Addresses.third.country},`
            + `${Addresses.third.phone}`,
          );
          await expect(deliveryAddressExist, 'Delivery address is not correct!').to.be.true;
        });

        it('should change the \'Invoice address\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress3', baseContext);

          const addressToSelect = `${Addresses.third.id}- ${Addresses.third.address} ${Addresses.third.secondAddress} `
            + `${Addresses.third.postalCode} ${Addresses.third.city}`;

          const alertMessage = await orderPageCustomerBlock.selectAnotherInvoiceAddress(page, addressToSelect);
          expect(alertMessage).to.contains(orderPageCustomerBlock.successfulUpdateMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice6', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the \'Billing address\' is updated', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkEditedBillingAddress', baseContext);

          const deliveryAddressExist = await files.isTextInPDF(
            filePath,
            'Billing address,,'
            + `${Addresses.third.firstName} ${Addresses.third.lastName},`
            + `${Addresses.third.company},`
            + `${Addresses.third.address} ${Addresses.third.secondAddress},`
            + `${Addresses.third.city}, ${Addresses.third.state} ${Addresses.third.postalCode},`
            + `${Addresses.third.country},`
            + `${Addresses.third.phone}`,
          );
          await expect(deliveryAddressExist, 'Billing address is not correct!').to.be.true;
        });
      });

      describe('Add note and check the invoice', async () => {
        it('should click on \'Documents\' tab', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab', baseContext);

          const isTabOpened = await orderPageTabListBlock.goToDocumentsTab(page);
          await expect(isTabOpened).to.be.true;
        });

        it('should add note', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'addNote', baseContext);

          const textResult = await orderPageTabListBlock.setDocumentNote(page, 'Test note', 1);
          await expect(textResult).to.equal(orderPageTabListBlock.updateSuccessfullMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckNote1', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the note is visible in the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkIsNoteVisible', baseContext);

          const isNoteVisible = await files.isTextInPDF(filePath, 'Test note');
          await expect(isNoteVisible, 'Note does not exist in invoice!').to.be.true;
        });

        it('should click on \'Documents\' tab', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTabToDeleteNote', baseContext);

          const isTabOpened = await orderPageTabListBlock.goToDocumentsTab(page);
          await expect(isTabOpened).to.be.true;
        });

        it('should delete the note', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteNote', baseContext);

          const textResult = await orderPageTabListBlock.setDocumentNote(page, '', 1);
          await expect(textResult).to.equal(orderPageTabListBlock.updateSuccessfullMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckNote2', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the note is not visible in the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkIsNoteNotVisible', baseContext);

          const isNoteVisible = await files.isTextInPDF(filePath, 'Test note');
          await expect(isNoteVisible, 'Note does is visible in invoice!').to.be.false;
        });
      });

      describe('Change \'Carrier\' and check the invoice', async () => {
        it('should click on \'Carriers\' tab', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

          const isTabOpened = await orderPageTabListBlock.goToCarriersTab(page);
          await expect(isTabOpened).to.be.true;
        });

        it('should click on \'Edit\' link and check the modal', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

          const isModalVisible = await orderPageTabListBlock.clickOnEditLink(page);
          await expect(isModalVisible, 'Edit shipping modal is not visible!').to.be.true;
        });

        it('should update the carrier', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'updateCarrier', baseContext);

          const shippingDetailsData: OrderShippingData = new OrderShippingData({
            trackingNumber: '',
            carrier: Carriers.myCarrier.name,
            carrierID: 1,
          });

          const textResult = await orderPageTabListBlock.setShippingDetails(page, shippingDetailsData);
          await expect(textResult).to.equal(orderPageTabListBlock.successfulUpdateMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice7', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the edited \'Carrier\' is visible in the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkCarrier', baseContext);

          const isCarrierVisible = await files.isTextInPDF(filePath, `Carrier, ,${Carriers.myCarrier.name}`);
          await expect(isCarrierVisible, 'New carrier not exist in invoice!').to.be.true;
        });

        it('should check that \'Shipping cost, Total (Tax exl.), Total Tax and Total\' are changed',
          async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkShippingCostChanged', baseContext);

            const totalPrice = productWithEcoTax.price + customizedProduct.price;

            const isDiscountVisible = await files.isTextInPDF(
              filePath,
              // Total Products, ,€25.00,Shipping Costs, ,€7.00,,Total (Tax excl.), ,€32.00,,Total, ,€32.00
              `Total Products, ,€${totalPrice.toFixed(2)},`
              + 'Shipping Costs, ,€7.00,,'
              + `Total (Tax excl.), ,€${(totalPrice + 7.00).toFixed(2)},,`
              + `Total, ,€${(totalPrice + 7.00).toFixed(2)}`,
            );
            await expect(
              isDiscountVisible,
              'Shipping cost, Total (Tax exl.), Total Tax and Total are not correct in the invoice!')
              .to.be.true;
          });
      });

      describe('Add discount and check the invoice', async () => {
        it('should add discount', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercent', baseContext);

          const validationMessage = await orderPageProductsBlock.addDiscount(page, discountData);
          await expect(validationMessage, 'Validation message is not correct!')
            .to.equal(orderPageTabListBlock.successfulUpdateMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice8', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that \'Discounts\' table is visible in the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountsTable', baseContext);

          const totalPrice = productWithEcoTax.price + customizedProduct.price;
          const discount = await basicHelper.percentage(totalPrice, parseInt(discountData.value, 10));

          const isDiscountVisible = await files.isTextInPDF(
            filePath,
            'Discounts,,Discount, ,'
            + `- €${discount.toFixed(2)}`,
          );
          await expect(isDiscountVisible, 'Discounts table is not visible in the invoice!').to.be.true;
        });

        it('should check that \'Total discount, Total( Tax excl.) and total\' are correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscount', baseContext);

          const totalPrice = productWithEcoTax.price + customizedProduct.price;
          const discount = await basicHelper.percentage(totalPrice, parseInt(discountData.value, 10));

          const isDiscountVisible = await files.isTextInPDF(
            filePath,
            `Total Products, ,€${totalPrice.toFixed(2)},`
            + `Total Discounts, ,- €${discount.toFixed(2)},`
            + 'Shipping Costs, ,€7.00,,'
            + `Total (Tax excl.), ,€${(totalPrice - discount + 7.00).toFixed(2)},,`
            + `Total, ,€${(totalPrice - discount + 7.00).toFixed(2)}`,
          );
          await expect(isDiscountVisible, 'Discount is not visible in the invoice!').to.be.true;
        });

        it('should delete the discount', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount', baseContext);

          const validationMessage = await orderPageProductsBlock.deleteDiscount(page);
          await expect(validationMessage, 'Successful delete alert is not correct')
            .to.equal(orderPageTabListBlock.successfulUpdateMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckDiscount', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the discount is not visible in the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkIsDiscountNotVisible', baseContext);

          const totalPrice = productWithEcoTax.price + customizedProduct.price;
          const discount = await basicHelper.percentage(totalPrice, parseInt(discountData.value, 10));

          const isDiscountVisible = await files.isTextInPDF(
            filePath,
            ' Total Discounts,'
            + `-€${(totalPrice - discount).toFixed(2)}`,
          );
          await expect(isDiscountVisible, 'Total discount is visible in the invoice!').to.be.false;
        });
      });

      describe('Add payment method and check the invoice', async () => {
        it('should add payment', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'addPayment', baseContext);

          const validationMessage = await orderPagePaymentBlock.addPayment(page, paymentData);
          expect(
            validationMessage,
            'Successful message is not correct!',
          ).to.equal(orderPagePaymentBlock.successfulUpdateMessage);
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckPayment', baseContext);

          filePath = await orderPageTabListBlock.viewInvoice(page);
          await expect(filePath).to.be.not.null;

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });

        it('should check that the new payment is visible in the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkNewPaymentMethod', baseContext);

          const isPaymentMethodVisible = await files.isTextInPDF(
            filePath,
            `,Payment Method, ,Bank transfer, ,€${customizedProduct.price.toFixed(2)},,`
            + `${paymentData.paymentMethod}, ,€${paymentData.amount}`,
          );
          await expect(isPaymentMethodVisible, 'Payment method is no correct!').to.be.true;
        });
      });
    });
  });

  // Post-condition: Delete the created products
  bulkDeleteProductsTest(prefixNewProduct, `${baseContext}_postTest_1`);

  // Post-condition: Disable EcoTax
  disableEcoTaxTest(`${baseContext}_postTest_2`);

  // Post-condition: Delete discount
  deleteCartRuleTest(discountData.name, `${baseContext}_postTest_3`);

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
