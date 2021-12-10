require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderByCustomerTest} = require('@commonTests/FO/createOrder');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {PaymentMethods} = require('@data/demo/paymentMethods');
const {Statuses} = require('@data/demo/orderStatuses');
const {Address} = require('@data/demo/address');

// Import faker data
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_checkInvoice';

let browserContext;
let page;

// New order by customer data
const orderByCustomerData = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

const virtualProduct = new ProductFaker({
  name: 'Virtual product',
  type: 'Virtual product',
  taxRule: 'No tax',
  quantity: 20,
});

const customizedProduct = new ProductFaker({
  name: 'Customized product',
  type: 'Standard product',
  customization: {
    label: 'Type your text here',
    type: 'Text',
    required: true,
  },
});

const productWithSpecificPrice = new ProductFaker({
  name: 'Product with specific price',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  specificPrice: {
    discount: 50,
    startingAt: 2,
    reductionType: '%',
  },
});

const productWithEcoTax = new ProductFaker({
  name: 'Product with ecotax',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  minimumQuantity: 1,
  ecoTax: 10,
});

let numberOfProducts = 0;
let filePath;

/*
Pre-Conditions:
- Create virtual product
- Create customized product
- Create product with specific price
- Create product with ecotax
Scenario:

Post-condition
- Delete virtual product
- Delete customized product
- Delete product with specific price
- Delete product with ecotax

*/
describe('BO - Orders - View and edit order : Check invoice', async () => {
// Pre-condition - Create order from FO
  //createOrderByCustomerTest(orderByCustomerData, baseContext);

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

  // Pre-condition - Enable ecoTax
  /* describe('POST-TEST: Enable ecoTax', async () => {
     it('should go to \'International > Taxes\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

       await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

       await taxesPage.closeSfToolBar(page);

       const pageTitle = await taxesPage.getPageTitle(page);
       await expect(pageTitle).to.contains(taxesPage.pageTitle);
     });

     it('should enable EcoTax', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'enableEcoTax', baseContext);

       const textResult = await taxesPage.enableEcoTax(page, true);
       await expect(textResult).to.be.equal('Update successful');
     });
   });

   // Pre-condition - Create 4 products
   [virtualProduct,
     customizedProduct,
     productWithSpecificPrice,
     productWithEcoTax,
   ].forEach((product, index) => {
     describe(`POST-TEST: Create product '${product.name}'`, async () => {
       if (index === 0) {
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
   });*/

  // 1 - Go to view order page
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

    it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

      const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
      await expect(textColumn).to.contains(DefaultCustomer.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewOrderPage1', baseContext);

      await ordersPage.goToOrder(page, 1);

      const pageTitle = await viewOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
    });
  });

  // 2 - Check invoice
  describe('Check invoice', async () => {
    describe('Create invoice with only virtual product', async () => {
      it('should delete the ordered product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

        const textResult = await viewOrderPage.deleteProduct(page, 1);
        await expect(textResult).to.contains(viewOrderPage.successfulDeleteProductMessage);
      });

      it(`should search for the product '${virtualProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchSimpleProduct1', baseContext);

        await viewOrderPage.searchProduct(page, virtualProduct.name);
        const result = await viewOrderPage.getSearchedProductDetails(page);
        await expect(result.available).to.equal(virtualProduct.quantity - 1);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addSimpleProductToTheCart1', baseContext);

        const textResult = await viewOrderPage.addProductToCart(page, 2);
        await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
      });

      it('should change the \'Shipping address\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress', baseContext);

        const addressToSelect = `${Address.third.id}- ${Address.third.address} ${Address.third.secondAddress} `
          + `${Address.third.postalCode} ${Address.third.city}`;

        const alertMessage = await viewOrderPage.selectAnotherShippingAddress(page, addressToSelect);
        expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);

        const shippingAddress = await viewOrderPage.getShippingAddress(page);
        await expect(shippingAddress)
          .to.contain(Address.third.firstName)
          .and.to.contain(Address.third.lastName)
          .and.to.contain(Address.third.address)
          .and.to.contain(Address.third.postalCode)
          .and.to.contain(Address.third.city)
          .and.to.contain(Address.third.country);
      });
    });

    it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusPaymentAccepted', baseContext);

      const textResult = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
      await expect(textResult).to.equal(Statuses.paymentAccepted.status);
    });

    it('should check that there is no carrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

      const carriersNumber = await viewOrderPage.getCarriersNumber(page);
      await expect(carriersNumber).to.be.equal(0);
    });

    it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

      filePath = await viewOrderPage.viewInvoice(page);

      const doesFileExist = await files.doesFileExist(filePath, 5000);
      await expect(doesFileExist, 'File is not downloaded!').to.be.true;
    });
  });

  /* describe('Check the invoice', async () => {
     it('should check that the \'Delivery address\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const customerNameExist = files.isTextInPDF(filePath, `${Address.second.firstName} ${Address.second.lastName}`);
       await expect(customerNameExist, 'Customer name does not exist in invoice!').to.be.true;

       const customerCompanyExist = files.isTextInPDF(filePath, Address.second.company);
       await expect(customerCompanyExist, 'Customer company does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Billing address\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const billingAddressExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(billingAddressExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Invoice number\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const invoiceNumberExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(invoiceNumberExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Invoice date\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const invoiceDateExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(invoiceDateExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Order reference\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const orderReferenceExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(orderReferenceExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Order date\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const orderDateExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(orderDateExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Product reference\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const productReferenceExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(productReferenceExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Product name\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const productNameExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(productNameExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Product price\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const productPriceExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(productPriceExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Product quantity\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const productQuantityExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(productQuantityExist, 'Payment amount does not exist in invoice!').to.be.true;
     });

     it('should check that the \'Payment method\' is correct', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

       const paymentMethodExist = await files.isTextInPDF(filePath, paymentDataAmountEqualRest.amount);
       await expect(paymentMethodExist, 'Payment amount does not exist in invoice!').to.be.true;
     });
   });*/

  /* // Post-condition - Delete the created products
   describe('Post-condition : Delete the created products', async () => {
     it('should go to \'Catalog > Products\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

       await addProductPage.goToSubMenu(page, addProductPage.catalogParentLink, addProductPage.productsLink);

       const pageTitle = await productsPage.getPageTitle(page);
       await expect(pageTitle).to.contains(productsPage.pageTitle);
     });

     [virtualProduct,
       customizedProduct,
       productWithSpecificPrice,
       productWithEcoTax,
     ].forEach((product, index) => {
       it(`should delete product '${product.name}' from DropDown Menu`, async function () {
         await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

         const deleteTextResult = await productsPage.deleteProduct(page, product);
         await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
       });

       it('should reset all filters', async function () {
         await testContext.addContextItem(this, 'testIdentifier', `resetFiltersAfterDelete${index}`, baseContext);

         const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
         await expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts + 3 - index);
       });
     });
   });

   // Post-condition - Disable EcoTax
   describe('Disable Eco tax', async () => {
     it('should go to \'International > Taxes\' page', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage2', baseContext);

       await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

       await taxesPage.closeSfToolBar(page);

       const pageTitle = await taxesPage.getPageTitle(page);
       await expect(pageTitle).to.contains(taxesPage.pageTitle);
     });

     it('should disable EcoTax', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'disableEcoTax', baseContext);

       const textResult = await taxesPage.enableEcoTax(page, false);
       await expect(textResult).to.be.equal('Update successful');
     });
   });*/
});
