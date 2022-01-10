require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const basicHelper = require('@utils/basicHelper');
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
const cartRulesPage = require('@pages/BO/catalog/discounts');

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

// Customized product data
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

// Second order by customer
const secondOrderByCustomer = {
  customer: DefaultCustomer,
  product: customizedProduct.name,
  productQuantity: 1,
  paymentMethod: PaymentMethods.wirePayment.moduleName,
};

// Virtual product data
const virtualProduct = new ProductFaker({
  name: 'Virtual product',
  type: 'Virtual product',
  quantity: 20,
  tax: 20,
  taxRule: 'FR Taux standard (20%)',
  stockLocation: 'stock 1',
});

// Product with specific price data
const productWithSpecificPrice = new ProductFaker({
  name: 'Product with specific price',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  specificPrice: {
    discount: 35,
    startingAt: 1,
    reductionType: '%',
  },
});

// Product with ecoTax data
const productWithEcoTax = new ProductFaker({
  name: 'Product with ecotax',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  minimumQuantity: 1,
});

// Discount data
const discountData = {
  name: 'Discount',
  type: 'Percent',
  value: 65,
};

// Payment data
const paymentData = {
  date: today,
  paymentMethod: 'Payments by check',
  transactionID: '12190',
  amount: (productWithEcoTax.price).toFixed(2),
  currency: '€',
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
Post-condition
- Delete virtual product
- Delete customized product
- Delete product with specific price
- Delete product with ecotax
- Disable EcoTax
- Delete discount
*/
describe('BO - Orders - View and edit order: Check invoice', async () => {
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
  describe('PRE-TEST: Enable ecoTax', async () => {
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
  [
    virtualProduct,
    customizedProduct,
    productWithSpecificPrice,
    productWithEcoTax,
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

  // Create and check invoices on different cases
  [
    // Data to edit the order row n°2 then to check the invoice
    {
      args: {
        orderRow: 2,
        product: virtualProduct,
        productQuantity: 13,
        deliveryAddress: Address.second,
        billingAddress: Address.third,
        tax: `${virtualProduct.tax} %`,
      },
    },
    // Data to edit the order row n°1 then to check the invoice
    {
      args: {
        orderRow: 1,
        product: customizedProduct,
        productQuantity: 1,
        deliveryAddress: Address.second,
        billingAddress: Address.second,
      },
    },
    // Data to edit the order row n°1 then to check the invoice
    {
      args: {
        product: productWithSpecificPrice,
        productQuantity: 1,
        deliveryAddress: Address.second,
        billingAddress: Address.second,
      },
    },
    // Data to edit the order row n°1 then to check the invoice
    {
      args: {
        product: productWithEcoTax,
        productQuantity: 1,
        deliveryAddress: Address.second,
        billingAddress: Address.second,
      },
    },
  ].forEach((test, index) => {
    describe(`Check invoice contain '${test.args.product.name}'`, async () => {
      if (index === 0 || index === 1) {
        // 1 - View order page
        describe('Go to view order page', async () => {
          it('should go to \'Orders > Orders\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToOrdersPage${index}`, baseContext);

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
            await testContext.addContextItem(this, 'testIdentifier', `resetOrderTableFilters${index}`, baseContext);

            const numberOfOrders = await ordersPage.resetAndGetNumberOfLines(page);
            await expect(numberOfOrders).to.be.above(0);
          });

          it(`should filter the Orders table by 'Customer: ${DefaultCustomer.lastName}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `filterByCustomer${index}`, baseContext);

            await ordersPage.filterOrders(page, 'input', 'customer', DefaultCustomer.lastName);

            const textColumn = await ordersPage.getTextColumn(page, 'customer', 1);
            await expect(textColumn).to.contains(DefaultCustomer.lastName);
          });

          it('should view the order', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `viewOrderPage${index}`, baseContext);

            await ordersPage.goToOrder(page, test.args.orderRow);

            const pageTitle = await viewOrderPage.getPageTitle(page);
            await expect(pageTitle).to.contains(viewOrderPage.pageTitle);
          });
        });
      }

      // 2 - Create invoice
      describe('Create invoice', async () => {
        if (index === 0) {
          it('should delete the ordered product', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'deleteOrderedProduct', baseContext);

            const textResult = await viewOrderPage.deleteProduct(page, 1);
            await expect(textResult).to.contains(viewOrderPage.successfulDeleteProductMessage);
          });
        }

        if (index !== 1) {
          it(`should search for the product '${test.args.product.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `searchProduct${index}`, baseContext);

            await viewOrderPage.searchProduct(page, test.args.product.name);
            const result = await viewOrderPage.getSearchedProductInformation(page);
            await expect(result.available).to.equal(test.args.product.quantity - 1);
          });

          it('should add the product to the cart', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `addProductToCart${index}`, baseContext);

            const textResult = await viewOrderPage.addProductToCart(page, test.args.productQuantity);
            await expect(textResult).to.contains(viewOrderPage.successfulAddProductMessage);
          });
        }

        if (index === 0) {
          it('should change the \'Invoice address\'', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress', baseContext);

            const addressToSelect = `${Address.third.id}- ${Address.third.address} ${Address.third.secondAddress} `
              + `${Address.third.zipCode} ${Address.third.city}`;

            const alertMessage = await viewOrderPage.selectAnotherInvoiceAddress(page, addressToSelect);
            expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);
          });
        }

        if (index === 0 || index === 1) {
          it(`should change the order status to '${Statuses.paymentAccepted.status}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `updateOrderStatus${index}`, baseContext);

            const textResult = await viewOrderPage.modifyOrderStatus(page, Statuses.paymentAccepted.status);
            await expect(textResult).to.equal(Statuses.paymentAccepted.status);
          });
        }

        if (test.args.product === virtualProduct) {
          it('should check that there is no carrier', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkCarriersNumber', baseContext);

            const carriersNumber = await viewOrderPage.getCarriersNumber(page);
            await expect(carriersNumber).to.be.equal(0);
          });
        }

        it('should get the invoice number', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `getInvoiceNumber${index}`, baseContext);

          fileName = await viewOrderPage.getFileName(page);
          await expect(filePath).is.not.equal('');
        });

        it('should get the order reference', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `getOrderReference${index}`, baseContext);

          orderReference = await viewOrderPage.getOrderReference(page);
          await expect(orderReference).is.not.equal('');
        });

        it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `clickOnViewInvoice${index}`, baseContext);

          filePath = await viewOrderPage.viewInvoice(page);

          const doesFileExist = await files.doesFileExist(filePath, 5000);
          await expect(doesFileExist, 'File is not downloaded!').to.be.true;
        });
      });

      // 3 - Check invoice
      describe('Check the invoice', async () => {
        // Check: Header, Delivery address, Billing address, Invoice number, Invoice date, Order reference and date
        describe('Check Header', async () => {
          it('should check the header of the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkHeaderInvoice${index}`, baseContext);

            const imageNumber = await files.getImageNumberInPDF(filePath);
            await expect(imageNumber, 'Logo is not visible!').to.be.equal(1);

            const isVisible = await files.isTextInPDF(filePath, `INVOICE,,${today},,#${fileName}`);
            await expect(isVisible, 'File name header is not correct!').to.be.true;
          });

          it('should check that the \'Delivery address\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkDeliveryAddress${index}`, baseContext);

            // Check delivery address
            const deliveryAddressExist = await files.isTextInPDF(
              filePath,
              'Delivery Address,,'
              + `${test.args.deliveryAddress.firstName} ${test.args.deliveryAddress.lastName},`
              + `${test.args.deliveryAddress.company},`
              + `${test.args.deliveryAddress.address},`
              + `${test.args.deliveryAddress.secondAddress},`
              + `${test.args.deliveryAddress.zipCode} ${test.args.deliveryAddress.city},`
              + `${test.args.deliveryAddress.country},`
              + `${test.args.deliveryAddress.phone}`,
            );
            await expect(deliveryAddressExist, 'Delivery address is not correct in invoice!').to.be.true;
          });

          it('should check that the \'Billing address\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkBillingAddress${index}`, baseContext);

            let billingAddressExist;
            if (test.args.billingAddress === Address.second) {
              billingAddressExist = await files.isTextInPDF(
                filePath,
                'Billing Address,,'
                + `${Address.second.firstName} ${Address.second.lastName},`
                + `${Address.second.company},`
                + `${Address.second.address},`
                + `${Address.second.secondAddress},`
                + `${Address.second.zipCode} ${Address.second.city},`
                + `${Address.second.country},`
                + `${Address.second.phone}`,
              );
            } else {
              billingAddressExist = await files.isTextInPDF(
                filePath,
                'Billing Address,,'
                + `${Address.third.firstName} ${Address.third.lastName},`
                + `${Address.third.company},`
                + `${Address.third.address} ${Address.third.secondAddress},`
                + `${Address.third.city}, ${Address.third.state} ${Address.third.zipCode},`
                + `${Address.third.country},`
                + `${Address.third.phone}`,
              );
            }
            await expect(billingAddressExist, 'Billing address is not correct in invoice!').to.be.true;
          });

          it('should check that the \'Invoice number, Invoice date, Order reference and Order date\' are correct',
            async function () {
              await testContext.addContextItem(this, 'testIdentifier', `checkInvoiceNumber${index}`, baseContext);

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
            await testContext.addContextItem(this, 'testIdentifier', `checkProductReference${index}`, baseContext);

            const productReferenceExist = await files.isTextInPDF(
              filePath,
              `${test.args.product.reference}, ,  ${test.args.product.name}`,
            );
            await expect(productReferenceExist, 'Product name and reference are not correct!').to.be.true;
          });

          // If invoice contain virtual product: Check Tax Rate, Unit Price, Quantity and total price
          if (test.args.product === virtualProduct) {
            it('should check that the \'Product Tax Rate, Unit Price (tax excl.), quantity and Product Total price'
              + '(tax excl.)\' are correct', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkTaxRateVirtualProduct', baseContext);

              const productPriceExist = await files.isTextInPDF(
                filePath,
                `${test.args.product.name}, ,  `
                + `${test.args.tax}, ,  `
                + `€${test.args.product.priceTaxExcluded.toFixed(2)}, ,  `
                + `${test.args.productQuantity}, ,  `
                + `€${(test.args.product.priceTaxExcluded * test.args.productQuantity).toFixed(2)}`,
              );
              await expect(
                productPriceExist,
                'Product Tax Rate, unit price (tax exl.), quantity and Total price (tax excl.) are not correct!',
              ).to.be.true;
            });
          }

          // If invoice contain customized product: Check customized text, Unit price, quantity and total price
          if (test.args.product === customizedProduct) {
            it('should check that the customized text is visible', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedText', baseContext);

              const isCustomizedTextVisible = await files.isTextInPDF(
                filePath,
                `${test.args.product.customization}: text,  (1)`,
              );
              await expect(isCustomizedTextVisible, 'Customized text is not visible!').to.be.false;
            });

            it('should check that the \'Unit Price (tax excl.), quantity and Product Total price '
              + '(tax excl.)\' are correct', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkProductCustomizedProduct', baseContext);

              const productPriceExist = await files.isTextInPDF(
                filePath,
                `${test.args.product.name}, ,  `
                + `€${test.args.product.price.toFixed(2)}, ,  `
                + `${test.args.productQuantity}, ,  `
                + `€${(test.args.product.price * test.args.productQuantity).toFixed(2)}`,
              );
              await expect(
                productPriceExist,
                'Unit Price (tax excl.), quantity and Product Total price are not correct!',
              ).to.be.true;
            });
          }

          // If invoice contain product with specific price: Check base price, Unit price, quantity and total price
          if (test.args.product === productWithSpecificPrice) {
            it('should check that the column \'Base price (Tax excl.)\' is visible', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceColumnVisible', baseContext);

              const basePriceColumnVisible = await files.isTextInPDF(filePath, 'Base,price,(Tax excl.)');
              await expect(basePriceColumnVisible, 'Base price is not visible!').to.be.true;
            });

            it('should check that the \'Base price (Tax excl.), Unit Price, Quantity, Total (Tax excl.)\' '
              + 'are correct', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceSpecificPrice', baseContext);

              const discountValue = await basicHelper.percentage(
                test.args.product.price,
                test.args.product.specificPrice.discount,
              );
              const unitPrice = test.args.product.price - discountValue;

              const basePriceVisible = await files.isTextInPDF(
                filePath,
                `${test.args.product.name}, ,  `
                + `€${test.args.product.price.toFixed(2)}, ,  `
                + `€${unitPrice.toFixed(2)}, ,  `
                + `${test.args.productQuantity}, ,  `
                + `€${unitPrice.toFixed(2)}`,
              );
              await expect(
                basePriceVisible,
                'Base price (Tax excl.), Unit Price, Quantity, Total (Tax excl.) are not correct!').to.be.true;
            });
          }

          // If invoice contain product with specific price: Check unit price, EcoTax, quantity and total price
          if (test.args.product === productWithEcoTax) {
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
                  `${test.args.product.name}, ,  `
                  + `€${test.args.product.price.toFixed(2)},  ,`
                  + `Ecotax: €${test.args.product.ecoTax.toFixed(2)},,  `
                  + `${test.args.productQuantity}, ,  `
                  + `€${test.args.product.price.toFixed(2)}`,
                );
                await expect(basePriceVisible, 'Unit price (Tax excl.), Ecotax, Quantity, '
                  + 'Total (Tax excl.) are not correct in invoice!').to.be.true;
              });
          }
        });

        // If invoice contain customized product or product with specific price check that taxes table is not visible
        if (test.args.product === customizedProduct || test.args.product === productWithSpecificPrice) {
          describe('Check that Taxes table is not visible', async () => {
            it('should check that \'Tax Detail\' table is not visible', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkIsTaxesTableNotVisible', baseContext);

              const isTaxTableVisible = await files.isTextInPDF(filePath, 'Tax Detail,Tax Rate,Base price,Total Tax');
              await expect(isTaxTableVisible, 'Tax table is visible!').to.be.false;
            });
          });
        }

        // If invoice contain virtual product or product with ecoTax check: Tax Detail, Tax Rate, Base price, Total tax
        if (test.args.product === virtualProduct || test.args.product === productWithEcoTax) {
          describe('Check Taxes table', async () => {
            if (test.args.product === virtualProduct) {
              it('should check that \'Tax Detail, Tax Rate, Base price, Total tax\' are correct', async function () {
                await testContext.addContextItem(this, 'testIdentifier', 'checkTaxesTableVirtualProduct', baseContext);

                const taxDetailsVisible = await files.isTextInPDF(
                  filePath,
                  'Tax Detail, ,Tax Rate, ,Base price, ,Total Tax,,  '
                  + 'Products, ,  '
                  + '20.000 %, ,  '
                  + `€${(test.args.product.priceTaxExcluded * test.args.productQuantity).toFixed(2)}, ,  `
                  + `€${((test.args.product.price - test.args.product.priceTaxExcluded) * test.args.productQuantity)
                    .toFixed(2)}`,
                );
                await expect(
                  taxDetailsVisible,
                  'Tax detail, tax Rate, Base price and Total tax are not correct!',
                ).to.be.true;
              });
            }

            if (test.args.product === productWithEcoTax) {
              it('should check that the \'Tax Detail, Tax Rate, Base price, Total Tax\' are correct',
                async function () {
                  await testContext.addContextItem(this, 'testIdentifier', 'checkEcotax', baseContext);

                  const taxDetailsVisible = await files.isTextInPDF(
                    filePath,
                    'Tax Detail, ,Tax Rate, ,Base price, ,Total Tax,,  '
                    + 'Ecotax, ,  '
                    + '0.000 %, ,  '
                    + `€${productWithEcoTax.ecoTax.toFixed(2)}, ,  `
                    + '€0.00',
                  );
                  await expect(
                    taxDetailsVisible,
                    'Tax detail, tax Rate, Base price and Total tax are not correct!',
                  ).to.be.true;
                });
            }
          });
        }

        // Check payments table: Payment method, Total and carrier name
        describe('Check Payments table', async () => {
          if (test.args.product === virtualProduct || test.args.product === customizedProduct) {
            it('should check that the \'Payment method and Total\' are correct', async function () {
              await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

              const paymentMethodExist = await files.isTextInPDF(
                filePath,
                'Payment Method, ,Bank transfer, ,'
                + `€${(test.args.product.price * test.args.productQuantity).toFixed(2)}`,
              );
              await expect(paymentMethodExist, 'Payment method and total to pay are not correct!').to.be.true;
            });
          }

          if (test.args.product === productWithEcoTax || test.args.product === productWithSpecificPrice) {
            it('should check that the \'Payment method and Total\' are correct', async function () {
              await testContext.addContextItem(this, 'testIdentifier', `checkPaymentMethod${index}`, baseContext);

              const paymentMethodExist = await files.isTextInPDF(
                filePath,
                'Payment Method, ,Bank transfer, ,'
                + `€${customizedProduct.price.toFixed(2)}`,
              );
              await expect(paymentMethodExist, 'Payment method and total are not correct!').to.be.true;
            });
          }

          // If invoice contain virtual product the carrier is not visible
          // Issue => https://github.com/PrestaShop/PrestaShop/issues/26977
          if (test.args.product === virtualProduct) {
            it.skip('should check that the carrier is not visible', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkCarrierNotVisible', baseContext);

              const isCarrierVisible = await files.isTextInPDF(filePath, `Carrier, ${Carriers.default.name}`);
              await expect(isCarrierVisible, `Carrier '${Carriers.default.name}' is visible!`).to.be.false;
            });
          }

          it('should check that the carrier is visible', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `checkCarrierVisible${index}`, baseContext);

            const isCarrierVisible = await files.isTextInPDF(filePath, Carriers.default.name);
            await expect(isCarrierVisible, `Carrier '${Carriers.default.name}' is not visible!`).to.be.true;
          });
        });

        // Check total to pay table
        describe('Check Total to pay table', async () => {
          // If invoice contain virtual product check: Total Products, Total(Tax exc.), Total Tax, Total
          if (test.args.product === virtualProduct) {
            it('should check that \'Total Products, Total(Tax exc.), Total Tax, Total\' are correct',
              async function () {
                await testContext.addContextItem(this, 'testIdentifier', `checkTotal${index}`, baseContext);

                const totalPriceTaxExcl = test.args.product.priceTaxExcluded * test.args.productQuantity;
                const priceTaxIncl = test.args.product.price * test.args.productQuantity;
                const tax = test.args.product.price - test.args.product.priceTaxExcluded;

                // Total Products, Total (Tax excl.), Total Tax, Total
                const isPaymentTableCorrect = await files.isTextInPDF(
                  filePath,
                  `Total Products, ,  €${totalPriceTaxExcl.toFixed(2)},,  `
                  + `Total (Tax excl.), ,  €${totalPriceTaxExcl.toFixed(2)},,  `
                  + `Total Tax, ,  €${(tax * test.args.productQuantity).toFixed(2)},,  `
                  + `Total, ,  €${priceTaxIncl.toFixed(2)}`,
                );
                await expect(
                  isPaymentTableCorrect,
                  'Total Products, Total(Tax exc.), Total Tax, Total are not correct!',
                ).to.be.true;
              });
          }

          // If invoice contain customized product check: Total Products, Shipping Costs, Total(Tax exc.), Total
          if (test.args.product === customizedProduct) {
            it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
              async function () {
                await testContext.addContextItem(this, 'testIdentifier', `checkTotal${index}`, baseContext);

                const totalPriceTaxExcl = test.args.product.price * test.args.productQuantity;

                // Total Products, Shipping Costs, Total (Tax excl.), Total
                const isShippingCostVisible = await files.isTextInPDF(
                  filePath,
                  `Total Products, ,  €${totalPriceTaxExcl.toFixed(2)},  `
                  + 'Shipping Costs, ,  Free Shipping,,  '
                  + `Total (Tax excl.), ,  €${totalPriceTaxExcl.toFixed(2)},,  `
                  + `Total, ,  €${totalPriceTaxExcl.toFixed(2)}`,
                );
                await expect(
                  isShippingCostVisible,
                  'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
                ).to.be.true;
              });
          }

          // If invoice contain product with specific price check: Total Products, Shipping Costs, Total(Tax exc.),Total
          if (test.args.product === productWithSpecificPrice) {
            it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
              async function () {
                await testContext.addContextItem(this, 'testIdentifier', `checkTotal${index}`, baseContext);

                const discount = await basicHelper.percentage(
                  test.args.product.price,
                  test.args.product.specificPrice.discount,
                );
                const unitPrice = test.args.product.price - discount;

                const totalPriceTaxExcl = unitPrice + customizedProduct.price;

                // Total Products, Shipping Costs, Total (Tax excl.), Total
                const isShippingCostVisible = await files.isTextInPDF(
                  filePath,
                  `Total Products, ,  €${totalPriceTaxExcl.toFixed(2)},  `
                  + 'Shipping Costs, ,  Free Shipping,,  '
                  + `Total (Tax excl.), ,  €${totalPriceTaxExcl.toFixed(2)},,  `
                  + `Total, ,  €${totalPriceTaxExcl.toFixed(2)}`,
                );
                await expect(
                  isShippingCostVisible,
                  'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
                ).to.be.true;
              });
          }

          // If invoice contain product with ecoTax check: Total Products, Shipping Costs, Total(Tax exc.), Total
          if (test.args.product === productWithEcoTax) {
            it('should check that \'Total Products, Shipping Costs, Total(Tax exc.), Total\' are correct',
              async function () {
                await testContext.addContextItem(this, 'testIdentifier', `checkTotal${index}`, baseContext);

                const totalPriceTaxExcl = test.args.product.price + customizedProduct.price;

                // Total Products, Shipping Costs, Total (Tax excl.), Total
                const isShippingCostVisible = await files.isTextInPDF(
                  filePath,
                  `Total Products, ,  €${totalPriceTaxExcl.toFixed(2)},  `
                  + 'Shipping Costs, ,  Free Shipping,,  '
                  + `Total (Tax excl.), ,  €${totalPriceTaxExcl.toFixed(2)},,  `
                  + `Total, ,  €${totalPriceTaxExcl.toFixed(2)}`,
                );
                await expect(
                  isShippingCostVisible,
                  'Total Products, Shipping Costs, Total(Tax exc.), Total are not correct!',
                ).to.be.true;
              });
          }
        });

        if (index === 2) {
          // Delete the added product and recheck the invoice
          describe('Delete the added product then recheck the invoice', async () => {
            it(`should delete the ordered product '${test.args.product.name}' from the list`, async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'deleteAddedProduct', baseContext);

              const textResult = await viewOrderPage.deleteProduct(page, 1);
              await expect(textResult).to.contains(viewOrderPage.successfulDeleteProductMessage);
            });

            it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'viewInvoice', baseContext);

              filePath = await viewOrderPage.viewInvoice(page);

              const doesFileExist = await files.doesFileExist(filePath, 5000);
              await expect(doesFileExist, 'File is not downloaded!').to.be.true;
            });

            it('should check that the \'Product name\' is not visible', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkProductName', baseContext);

              const productNameExist = await files.isTextInPDF(filePath, test.args.product.name);
              await expect(productNameExist, 'Product name is visible!').to.be.false;
            });

            it('should check that the column \'Base price (Tax excl.)\' is not visible', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'checkBasePriceColumn', baseContext);

              const basePriceColumnVisible = await files.isTextInPDF(filePath, 'Base,price,(Tax excl.)');
              await expect(basePriceColumnVisible, 'Base price is not visible!').to.be.false;
            });
          });
        }
      });

      if (index === 3) {
        describe('Change \'Shipping address\' and \'Invoice address\' then check the invoice', async () => {
          it('should change the \'Shipping address\'', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress', baseContext);

            const addressToSelect = `${Address.third.id}- ${Address.third.address} ${Address.third.secondAddress} `
              + `${Address.third.zipCode} ${Address.third.city}`;

            const alertMessage = await viewOrderPage.selectAnotherShippingAddress(page, addressToSelect);
            expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'File is not downloaded!').to.be.true;
          });

          it('should check that the \'Delivery address\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

            const deliveryAddressExist = await files.isTextInPDF(
              filePath,
              'Delivery Address,,'
              + `${Address.third.firstName} ${Address.third.lastName},`
              + `${Address.third.company},`
              + `${Address.third.address} ${Address.third.secondAddress},`
              + `${Address.third.city}, ${Address.third.state} ${Address.third.zipCode},`
              + `${Address.third.country},`
              + `${Address.third.phone}`,
            );
            await expect(deliveryAddressExist, 'Delivery address is not correct!').to.be.true;
          });

          it('should change the \'Invoice address\'', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'changeBillingAddress', baseContext);

            const addressToSelect = `${Address.third.id}- ${Address.third.address} ${Address.third.secondAddress} `
              + `${Address.third.zipCode} ${Address.third.city}`;

            const alertMessage = await viewOrderPage.selectAnotherInvoiceAddress(page, addressToSelect);
            expect(alertMessage).to.contains(viewOrderPage.successfulUpdateMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'File is not downloaded!').to.be.true;
          });

          it('should check that the \'Billing address\' is correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkInvoice', baseContext);

            const deliveryAddressExist = await files.isTextInPDF(
              filePath,
              'Billing Address,,'
              + `${Address.third.firstName} ${Address.third.lastName},`
              + `${Address.third.company},`
              + `${Address.third.address} ${Address.third.secondAddress},`
              + `${Address.third.city}, ${Address.third.state} ${Address.third.zipCode},`
              + `${Address.third.country},`
              + `${Address.third.phone}`,
            );
            await expect(deliveryAddressExist, 'Billing address is not correct!').to.be.true;
          });
        });

        describe('Add note and check the invoice', async () => {
          it('should click on \'Documents\' tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTab', baseContext);

            const isTabOpened = await viewOrderPage.goToDocumentsTab(page);
            await expect(isTabOpened).to.be.true;
          });

          it('should add note', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'addNote', baseContext);

            const textResult = await viewOrderPage.setDocumentNote(page, 'Test note', 1);
            await expect(textResult).to.equal(viewOrderPage.updateSuccessfullMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckNote1', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'File is not downloaded!').to.be.true;
          });

          it('should check that the note is visible in the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkIsNoteNotVisible', baseContext);

            const isNoteVisible = await files.isTextInPDF(filePath, 'Test note');
            await expect(isNoteVisible, 'Note does not exist in invoice!').to.be.true;
          });

          it('should click on \'Documents\' tab', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'displayDocumentsTabToDeleteNote', baseContext);

            const isTabOpened = await viewOrderPage.goToDocumentsTab(page);
            await expect(isTabOpened).to.be.true;
          });

          it('should delete the note', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'deleteNote', baseContext);

            const textResult = await viewOrderPage.setDocumentNote(page, '', 1);
            await expect(textResult).to.equal(viewOrderPage.updateSuccessfullMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckNote2', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'File is not downloaded!').to.be.true;
          });

          it('should check that the note is not visible in the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkIsNoteVisible', baseContext);

            const isNoteVisible = await files.isTextInPDF(filePath, 'Test note');
            await expect(isNoteVisible, 'Note does is visible in invoice!').to.be.false;
          });
        });

        describe('Change \'Carrier\' and check the invoice', async () => {
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

          it('should update the carrier', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'updateTrackingNumber', baseContext);

            const shippingDetailsData = {trackingNumber: '', carrier: Carriers.myCarrier.name};

            const textResult = await viewOrderPage.setShippingDetails(page, shippingDetailsData);
            await expect(textResult).to.equal(viewOrderPage.successfulUpdateMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoice', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

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
                ` Total Products, ,  €${totalPrice.toFixed(2)},  `
                + 'Shipping Costs, ,  €7.00,,  '
                + `Total (Tax excl.), ,  €${(totalPrice + 7.00).toFixed(2)},,  `
                + `Total, ,  €${(totalPrice + 7.00).toFixed(2)}`,
              );
              await expect(
                isDiscountVisible,
                'Shipping cost, Total (Tax exl.), Total Tax and Total are not correct in the invoice!')
                .to.be.true;
            });
        });

        describe('Add discount and check the invoice', async () => {
          it('should add discount', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentGoodValue', baseContext);

            const validationMessage = await viewOrderPage.addDiscount(page, discountData);
            await expect(validationMessage, 'Validation message is not correct!')
              .to.equal(viewOrderPage.successfulUpdateMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'clickOnViewInvoiceToCheckInvoice', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'File is not downloaded!').to.be.true;
          });

          it('should check that \'Discounts\' table is visible in the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountsTable', baseContext);

            const totalPrice = productWithEcoTax.price + customizedProduct.price;
            const discount = await basicHelper.percentage(totalPrice, discountData.value);

            const isDiscountVisible = await files.isTextInPDF(
              filePath,
              'Discounts,,  Discount, ,  '
              + `- €${discount.toFixed(2)}`,
            );
            await expect(isDiscountVisible, 'Discounts table is not visible in the invoice!').to.be.true;
          });

          it('should check that \'Total discount, Total( Tax excl.) and total\' are correct', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscount', baseContext);

            const totalPrice = productWithEcoTax.price + customizedProduct.price;
            const discount = await basicHelper.percentage(totalPrice, discountData.value);

            const isDiscountVisible = await files.isTextInPDF(
              filePath,
              ` Total Products, ,  €${totalPrice.toFixed(2)},  `
              + `Total Discounts, ,  - €${discount.toFixed(2)},  `
              + 'Shipping Costs, ,  €7.00,,  '
              + `Total (Tax excl.), ,  €${(totalPrice - discount + 7.00).toFixed(2)},,  `
              + `Total, ,  €${(totalPrice - discount + 7.00).toFixed(2)}`,
            );
            await expect(isDiscountVisible, 'Discount is not visible in the invoice!').to.be.true;
          });

          it('should delete the discount', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount', baseContext);

            const validationMessage = await viewOrderPage.deleteDiscount(page);
            await expect(validationMessage, 'Successful delete alert is not correct')
              .to.equal(viewOrderPage.successfulUpdateMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckDiscount', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

            const doesFileExist = await files.doesFileExist(filePath, 5000);
            await expect(doesFileExist, 'File is not downloaded!').to.be.true;
          });

          it('should check that the discount is not visible in the invoice', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'checkIsDiscountNotVisible', baseContext);

            const totalPrice = productWithEcoTax.price + customizedProduct.price;
            const discount = await basicHelper.percentage(totalPrice, discountData.value);

            const isDiscountVisible = await files.isTextInPDF(
              filePath,
              ' Total Discounts,  '
              + `-€${(totalPrice - discount).toFixed(2)}`,
            );
            await expect(isDiscountVisible, 'Total discount is visible in the invoice!').to.be.false;
          });
        });

        describe('Add payment method and check the invoice', async () => {
          it('should add payment', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'addPayment', baseContext);

            const validationMessage = await viewOrderPage.addPayment(page, paymentData);
            expect(
              validationMessage,
              'Successful message is not correct!',
            ).to.equal(viewOrderPage.successfulUpdateMessage);
          });

          it('should click on \'View invoice\' button and check that the file is downloaded', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'viewInvoiceToCheckPayment', baseContext);

            filePath = await viewOrderPage.viewInvoice(page);

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
      }
    });
  });

  // Post-condition - Delete the created products
  describe('POS-TEST: Delete the created products', async () => {
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

  // POST-condition - Disable EcoTax
  describe('POS-TEST: Disable EcoTax', async () => {
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
  });

  // POST-condition - Delete discount
  describe('POST-TEST: Delete cart rules', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage3', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCartRules', baseContext);

      const validationMessage = await cartRulesPage.deleteCartRule(page);
      await expect(validationMessage).to.contains(cartRulesPage.successfulDeleteMessage);
    });
  });
});
