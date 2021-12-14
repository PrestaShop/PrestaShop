require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const {getDateFormat} = require('@utils/date');

// Import common tests
const loginCommon = require('@commonTests/loginBO');
const {createOrderByCustomerTest, createOrderSpecificProductTest} = require('@commonTests/FO/createOrder');

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
const {Carriers} = require('@data/demo/carriers');
const Address = require('@data/demo/address');

// Import faker data
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_checkInvoice';

let browserContext;
let page;
const today = getDateFormat('mm/dd/yyyy');

// First order by customer data
const firstOrderByCustomer = {
  customer: DefaultCustomer,
  product: 1,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

const customizedProduct = new ProductFaker({
  name: 'Customized product',
  type: 'Standard product',
  taxRule: 'No tax',
  customization: {
    label: 'Type your text here',
    type: 'Text',
    required: true,
  },
});

const virtualProduct = new ProductFaker({
  name: 'Virtual product',
  type: 'Virtual product',
  quantity: 20,
  price: 17.00,
  priceTaxExcluded: 14.166667,
  taxRule: 'FR Taux standard (20%)',
  stockLocation: 'stock 1',
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

// Second order
const secondOrderByCustomer = {
  customer: DefaultCustomer,
  product: customizedProduct.name,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

let numberOfProducts = 0;
let filePath;
let fileName = '';
let orderReference = '';

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
  // Pre-condition - Create first order from FO
  createOrderByCustomerTest(firstOrderByCustomer, baseContext);

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
  /* describe('PRE-TEST: Enable ecoTax', async () => {
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
  }); */

  // Pre-condition - Create 4 products
  [virtualProduct,
    customizedProduct,
    /* productWithSpecificPrice,
    productWithEcoTax, */
  ].forEach((product, index) => {
    describe(`PRE-TEST: Create product '${product.name}'`, async () => {
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
  });

  // Pre-condition - Create second order from FO
  createOrderSpecificProductTest(secondOrderByCustomer, baseContext);

  [
    {
      args: {
        orderRow: 2,
        product: virtualProduct,
        productQuantity: 13,
        deliveryAddress: Address.second,
        billingAddress: Address.third,
      },
    },
    {
      args: {
        orderRow: 1,
        product: customizedProduct,
        productQuantity: 1,
        deliveryAddress: Address.second,
        billingAddress: Address.second,
      },
    },
  ].forEach((test, index) => {
    describe(`Check invoice contain '${test.args.product.name}'`, async () => {
      // 1 - View order page
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

          await ordersPage.goToOrder(page, test.args.orderRow);

          const pageTitle = await viewOrderPage.getPageTitle(page);
          await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
        });
      });

      // 2 - Create invoice
      describe('Create invoice', async () => {
        if (index === 0) {
          it('should delete the ordered product', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

            const textResult = await viewOrderPage.deleteProduct(page, 1);
            await expect(textResult).to.contains(viewOrderPage.successfulDeleteProductMessage);
          });

          it(`should search for the product '${test.args.product.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'searchSimpleProduct1', baseContext);

            await viewOrderPage.searchProduct(page, virtualProduct.name);
            const result = await viewOrderPage.getSearchedProductDetails(page);
            await expect(result.available).to.equal(virtualProduct.quantity - 1);
          });

          it('should add the product to the cart', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'addSimpleProductToTheCart1', baseContext);

            const textResult = await viewOrderPage.addProductToCart(page, 13);
            await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
          });

          it('should change the \'Invoice address\'', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress', baseContext);

            const addressToSelect = `${Address.third.id}- ${Address.third.address} ${Address.third.secondAddress} `
              + `${Address.third.zipCode} ${Address.third.city}`;

            const alertMessage = await viewOrderPage.selectAnotherInvoiceAddress(page, addressToSelect);
            expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);
          });
        }

        it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'updateOrderStatusPaymentAccepted', baseContext);

          const textResult = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
          await expect(textResult).to.equal(Statuses.paymentAccepted.status);
        });

        if (test.args.product === virtualProduct) {
          it('should check that there is no carrier', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

            const carriersNumber = await viewOrderPage.getCarriersNumber(page);
            await expect(carriersNumber).to.be.equal(0);
          });
        }

        it('should get the invoice number', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'getInvoiceFileName', baseContext);

          fileName = await viewOrderPage.getFileName(page);
          await expect(filePath).is.not.equal('');
        });

        it('should get the order reference', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'getOrderReference', baseContext);

          orderReference = await viewOrderPage.getOrderReference(page);
          await expect(orderReference).is.not.equal('');
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

          filePath = await viewOrderPage.viewInvoice(page);

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });
      });

      // 3 - Check invoice
      describe('Check the invoice', async () => {
        it('should check the header of the invoice', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkHeaderInvoice', baseContext);

          const isTitleVisible = await files.isTextInPDF(filePath, 'INVOICE');
          await expect(isTitleVisible, 'File type is not correct!').to.be.true;

          const imageNumber = await files.getImageNumberInPDF(filePath);
          await expect(imageNumber, 'Logo is not visible!').to.be.equal(1);

          const isDateExist = await files.isTextInPDF(filePath, today);
          await expect(isDateExist, 'Invoice date is not correct!').to.be.true;
        });

        it('should check that the \'Delivery address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

          // Check first name and lastname of the customer
          const customerNameExist = await files.isTextInPDF(
            filePath,
            `${test.args.deliveryAddress.firstName} ${test.args.deliveryAddress.lastName}`,
          );
          await expect(customerNameExist, 'Customer name and lastname does not exist in invoice!').to.be.true;

          // Check company
          const customerCompanyExist = await files.isTextInPDF(filePath, test.args.deliveryAddress.company);
          await expect(customerCompanyExist, 'Customer name and lastname does not exist in invoice!').to.be.true;

          // Check first and second
          const firstAddressExist = await files.isTextInPDF(filePath, `${test.args.deliveryAddress.address}`);
          await expect(firstAddressExist, 'Customer first address does not exist in invoice!').to.be.true;

          const secondAddressExist = await files.isTextInPDF(filePath, `${test.args.deliveryAddress.secondAddress}`);
          await expect(secondAddressExist, 'Customer second address does not exist in invoice!').to.be.true;

          // Check postal address
          const customerPostalAddressExist = await files.isTextInPDF(
            filePath,
            `${test.args.deliveryAddress.zipCode} ${test.args.deliveryAddress.city}`,
          );
          await expect(
            customerPostalAddressExist,
            'Customer zip code and city does not exist in invoice!').to.be.true;

          // Check country
          const customerCountryExist = await files.isTextInPDF(filePath, test.args.deliveryAddress.country);
          await expect(customerCountryExist, 'Customer country does not exist in invoice!').to.be.true;

          // Check phone
          const customerPhoneExist = await files.isTextInPDF(filePath, test.args.deliveryAddress.phone);
          await expect(customerPhoneExist, 'Customer phone does not exist in invoice!').to.be.true;
        });

        it('should check that the \'Billing address\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

          // Check first name and lastname of the customer
          const customerNameExist = await files.isTextInPDF(
            filePath,
            `${test.args.billingAddress.firstName} ${test.args.billingAddress.lastName}`,
          );
          await expect(customerNameExist, 'Customer name and lastname does not exist in invoice!').to.be.true;

          // Check company
          const customerCompanyExist = await files.isTextInPDF(filePath, test.args.billingAddress.company);
          await expect(customerCompanyExist, 'Customer name and lastname does not exist in invoice!').to.be.true;

          // Check first and second address
          if (test.args.billingAddress === Address.third) {
            const customerAddressExist = await files.isTextInPDF(
              filePath,
              `${test.args.billingAddress.address} ${test.args.billingAddress.secondAddress}`,
            );
            await expect(customerAddressExist, 'Customer address does not exist in invoice!').to.be.true;
          } else {
            const firstAddressExist = await files.isTextInPDF(filePath, `${test.args.billingAddress.address}`);
            await expect(firstAddressExist, 'Customer first address does not exist in invoice!').to.be.true;

            const secondAddressExist = await files.isTextInPDF(filePath, `${test.args.billingAddress.secondAddress}`);
            await expect(secondAddressExist, 'Customer second address does not exist in invoice!').to.be.true;
          }
          // Check city, state zip code
          if (test.args.billingAddress === Address.second) {
            const customerPostalAddressExist = await files.isTextInPDF(
              filePath,
              `${test.args.billingAddress.zipCode} ${test.args.billingAddress.city}`,
            );
            await expect(
              customerPostalAddressExist,
              'Customer state, country and zip code does not exist in invoice!').to.be.true;
          } else {
            const customerPostalAddressExist = await files.isTextInPDF(
              filePath,
              `${test.args.deliveryAddress.zipCode} ${test.args.deliveryAddress.city}`,
            );
            await expect(
              customerPostalAddressExist,
              'Customer zip code and city does not exist in invoice!').to.be.true;
          }
          // Check country
          const customerCountryExist = await files.isTextInPDF(filePath, test.args.billingAddress.country);
          await expect(customerCountryExist, 'Customer country does not exist in invoice!').to.be.true;

          // Check phone
          const customerPhoneExist = await files.isTextInPDF(filePath, test.args.billingAddress.phone);
          await expect(customerPhoneExist, 'Customer phone does not exist in invoice!').to.be.true;
        });

        it('should check that the \'Invoice number\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

          const invoiceNumberExist = await files.isTextInPDF(filePath, fileName);
          await expect(invoiceNumberExist, 'Invoice number is not correct!').to.be.true;
        });

        it('should check that the \'Order reference\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkOrderReference', baseContext);

          const orderReferenceExist = await files.isTextInPDF(filePath, orderReference);
          await expect(orderReferenceExist, 'Order reference is not correct!').to.be.true;
        });

        it('should check that the \'Product reference\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductReference', baseContext);

          const productReferenceExist = await files.isTextInPDF(filePath, test.args.product.reference);
          await expect(productReferenceExist, 'Product reference is not correct!').to.be.true;
        });

        it('should check that the \'Product name\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductName', baseContext);

          const productNameExist = await files.isTextInPDF(filePath, test.args.product.name);
          await expect(productNameExist, 'Product name is not correct!').to.be.true;
        });

        if (test.args.product === virtualProduct) {
          it('should check that the product \'Tax Rate\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkProductTaxRate', baseContext);

            const productTaxRateExist = await files.isTextInPDF(filePath, '20 %');
            await expect(productTaxRateExist, 'Product tax rate is not correct!').to.be.true;
          });
        }
        it('should check that the \'Product unit price (tax excl.)\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

          const productPriceExist = await files.isTextInPDF(
            filePath,
            `€${test.args.product.priceTaxExcluded.toFixed(2)}`,
          );
          await expect(productPriceExist, 'Product price (tax exl.) is not correct!').to.be.true;
        });

        it('should check that the \'Product quantity\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductQuantity', baseContext);

          const productQuantityExist = await files.isTextInPDF(filePath, test.args.productQuantity);
          await expect(productQuantityExist, 'Product quantity is not correct!').to.be.true;
        });

        it('should check that the \'Product Total price (tax excl.)\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

          const productTotalPriceExist = await files.isTextInPDF(
            filePath,
            `€${(test.args.product.priceTaxExcluded * test.args.productQuantity).toFixed(2)}`,
          );
          await expect(productTotalPriceExist, 'Product total price (tax excl.) is not correct!').to.be.true;
        });

        if (test.args.product === virtualProduct) {
          it('should check that \'Tax Detail\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

            const taxDetailExist = await files.isTextInPDF(filePath, 'Products');
            await expect(taxDetailExist, 'Taw detail is not correct!').to.be.true;
          });

          it('should check that \'Tax Rate\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

            const taxDetailExist = await files.isTextInPDF(filePath, '20.000 %');
            await expect(taxDetailExist, 'Taw Rate is not correct!').to.be.true;
          });

          it('should check that the \'Base price\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

            const basePriceExist = await files.isTextInPDF(
              filePath, `€${(virtualProduct.priceTaxExcluded * 13).toFixed(2)}`,
            );
            await expect(basePriceExist, 'Base price is not correct!').to.be.true;
          });

          it('should check that the \'Total Tax\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

            const basePriceExist = await files.isTextInPDF(
              filePath,
              `€${((virtualProduct.price - virtualProduct.priceTaxExcluded) * test.args.productQuantity).toFixed(2)}`,
            );
            await expect(basePriceExist, 'Total tax is not correct!').to.be.true;
          });
        }

        it('should check that the \'Payment method\' is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkPaymentMethod', baseContext);

          const paymentMethodExist = await files.isTextInPDF(filePath, 'Bank transfer');
          await expect(paymentMethodExist, 'Payment method is not correct!').to.be.true;
        });

        it('should check that the total to pay is correct', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkTotalToPay', baseContext);

          const totalToPayExist = await files.isTextInPDF(
            filePath,
            `€${(test.args.product.priceTaxExcluded * test.args.productQuantity).toFixed(2)}`,
          );
          await expect(totalToPayExist, 'Payment method is not correct!').to.be.true;
        });

        // Issue => https://github.com/PrestaShop/PrestaShop/issues/26977
        /* it('should check that the carrier is not visible', async function () {
           await testContext.addContextItem(this, 'testIdentifier', 'checkCarrier', baseContext);

           const isCarrierVisible = await files.isTextInPDF(filePath, Carriers.default.name);
           await expect(isCarrierVisible, 'Payment method is visible!').to.be.false;
         }); */
      });
    });
  });

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
   }); */
});
