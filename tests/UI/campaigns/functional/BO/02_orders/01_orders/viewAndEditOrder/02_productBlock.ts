import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import commonTests
import {deleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {bulkDeleteProductsTest} from '@commonTests/BO/catalog/product';
import {deleteCustomerTest} from '@commonTests/BO/customers/customer';
import {enableEcoTaxTest, disableEcoTaxTest} from '@commonTests/BO/international/ecoTax';
import {createOrderByGuestTest} from '@commonTests/FO/classic/order';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductsCreateTabPricingPage,
  type BrowserContext,
  dataPaymentMethods,
  dataProducts,
  FakerAddress,
  FakerCartRule,
  FakerCustomer,
  FakerOrder,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_productBlock';

/*
Pre-condition:
- Create order by guest in FO
- Enable eco tax
- Create 9 products in BO:
  - Out of stock allowed
  - Out of stock not allowed
  - Simple
  - Standard with combination
  - Pack
  - Virtual
  - With specific price
  - With ecoTax
  - With cart rule
- Create cart rule for the product with cart rule
Scenario:
Check product block content:
  - Check number of products
  - Update the price of an ordered
  - Update the quantity of an ordered product
  - Add and check product out of stock not allowed
  - Add and check product out of stock allowed
  - Add all types of products
  - Pagination next and previous
  - Delete product
Post-condition:
- Delete guest customer
- Disable ecoTax
- Delete created products
- Delete cart rule
*/
describe('BO - Orders - View and edit order : Check product block in view order page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let productNumber: number = 0;
  let createProductMessage: string | null = '';
  let updateProductMessage: string | null = '';

  // Prefix for the new products to simply delete them by bulk actions
  const prefixNewProduct: string = 'TOTEST';
  const customerData: FakerCustomer = new FakerCustomer({password: ''});
  const addressData: FakerAddress = new FakerAddress({country: 'France'});
  // New order by guest data
  const orderData: FakerOrder = new FakerOrder({
    customer: customerData,
    products: [
      {
        product: dataProducts.demo_5,
        quantity: 1,
      },
    ],
    deliveryAddress: addressData,
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const productOutOfStockAllowed: FakerProduct = new FakerProduct({
    name: `Out of stock allowed ${prefixNewProduct}`,
    reference: 'd12345',
    type: 'standard',
    taxRule: 'No tax',
    quantity: -12,
    minimumQuantity: 1,
    lowStockLevel: 3,
    behaviourOutOfStock: 'Allow orders',
  });
  const productOutOfStockNotAllowed: FakerProduct = new FakerProduct({
    name: `Out of stock not allowed ${prefixNewProduct}`,
    reference: 'e12345',
    type: 'standard',
    taxRule: 'No tax',
    quantity: -36,
    minimumQuantity: 3,
    stockLocation: 'stock 3',
    lowStockLevel: 3,
    behaviourOutOfStock: 'Deny orders',
  });
  const packOfProducts: FakerProduct = new FakerProduct({
    name: `Pack of products ${prefixNewProduct}`,
    reference: 'c12345',
    type: 'pack',
    pack: [
      {
        reference: 'demo_13',
        quantity: 1,
      },
      {
        reference: 'demo_7',
        quantity: 1,
      },
    ],
    taxRule: 'No tax',
    quantity: 197,
    minimumQuantity: 3,
    stockLocation: 'stock 3',
    lowStockLevel: 3,
    behaviourOutOfStock: 'Default behavior',
  });
  const virtualProduct: FakerProduct = new FakerProduct({
    name: `Virtual product ${prefixNewProduct}`,
    reference: 'b12345',
    type: 'virtual',
    taxRule: 'No tax',
    quantity: 20,
  });
  const combinationProduct: FakerProduct = new FakerProduct({
    name: `Product with combination ${prefixNewProduct}`,
    reference: 'a12345',
    type: 'combinations',
    productHasCombinations: true,
    taxRule: 'No tax',
    quantity: 197,
    minimumQuantity: 1,
    lowStockLevel: 3,
    behaviourOutOfStock: 'Default behavior',
  });
  const simpleProduct: FakerProduct = new FakerProduct({
    name: `Simple product ${prefixNewProduct}`,
    reference: 'i12345',
    type: 'standard',
    taxRule: 'No tax',
    quantity: 50,
    price: 20,
    minimumQuantity: 1,
    stockLocation: 'stock 1',
    lowStockLevel: 3,
    behaviourOutOfStock: 'Default behavior',
  });
  const productWithSpecificPrice: FakerProduct = new FakerProduct({
    name: `Product with specific price ${prefixNewProduct}`,
    reference: 'f12345',
    type: 'standard',
    taxRule: 'No tax',
    quantity: 20,
    specificPrice: {
      attributes: null,
      discount: 50,
      startingAt: 2,
      reductionType: '%',
    },
  });
  const productWithEcoTax: FakerProduct = new FakerProduct({
    name: `Product with ecotax ${prefixNewProduct}`,
    reference: 'g12345',
    type: 'standard',
    taxRule: 'No tax',
    quantity: 20,
    minimumQuantity: 1,
    ecoTax: 10,
  });
  const productWithCartRule: FakerProduct = new FakerProduct({
    name: `Product with cart rule ${prefixNewProduct}`,
    reference: 'h12345',
    type: 'standard',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    stockLocation: 'stock 1',
    lowStockLevel: 3,
    behaviourOutOfStock: 'Default behavior',
  });
  const newCartRuleData: FakerCartRule = new FakerCartRule({
    code: '4QABV6L3',
    discountType: 'Percent',
    discountPercent: 20,
    applyDiscountTo: 'Specific product',
    product: productWithCartRule.name,
  });
  const newQuantity: number = 2;
  const newPrice: number = 25;

  // Pre-condition: Create order by guest
  createOrderByGuestTest(orderData, `${baseContext}_preTest_1`);

  // Pre-condition: Enable EcoTax
  enableEcoTaxTest(`${baseContext}_preTest_2`);

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

  // Pre-condition: Create 9 products, Enable ecoTax, Create cart rule
  describe('PRE-TEST: Create 9 products in BO', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should reset all filters and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersBeforeCreate', baseContext);

      const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    [
      productOutOfStockAllowed,
      productOutOfStockNotAllowed,
      packOfProducts,
      virtualProduct,
      combinationProduct,
      simpleProduct,
      productWithSpecificPrice,
      productWithEcoTax,
      productWithCartRule,
    ].forEach((product: FakerProduct, index: number) => {
      describe(`Create product : '${product.name}'`, async () => {
        if (index === 0) {
          it('should click on \'New product\' button and check new product modal', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

            const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
            expect(isModalVisible).to.be.eq(true);
          });

          it(`should choose '${product.type} product'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

            await boProductsPage.selectProductType(page, product.type);

            const pageTitle = await boProductsCreatePage.getPageTitle(page);
            expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
          });
        }

        it('should go to new product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${index}`, baseContext);

          if (index !== 0) {
            await boProductsCreatePage.clickOnNewProductButton(page);
          } else {
            await boProductsPage.clickOnAddNewProduct(page);
          }

          const pageTitle = await boProductsCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
        });

        if (index !== 0) {
          it(`should choose '${product.type} product'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `chooseProductType${index}`, baseContext);

            await boProductsCreatePage.chooseProductType(page, product.type);

            const pageTitle = await boProductsCreatePage.getPageTitle(page);
            expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
          });
        }

        it(`create product '${product.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

          createProductMessage = await boProductsCreatePage.setProduct(page, product);
          expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

          // Add specific price
          if (product === productWithSpecificPrice) {
            await boProductsCreatePage.goToTab(page, 'pricing');
            await boProductsCreateTabPricingPage.clickOnAddSpecificPriceButton(page);

            createProductMessage = await boProductsCreateTabPricingPage.setSpecificPrice(
              page,
              productWithSpecificPrice.specificPrice,
            );
            expect(createProductMessage).to.equal(boProductsCreatePage.successfulCreationMessage);
          }
          // Add eco tax
          if (product === productWithEcoTax) {
            await boProductsCreatePage.goToTab(page, 'pricing');
            await boProductsCreateTabPricingPage.addEcoTax(page, productWithEcoTax.ecoTax);

            updateProductMessage = await boProductsCreatePage.saveProduct(page);
            expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
          }
        });

        if (product.type === 'combinations') {
          it('should create combinations and check generate combinations button', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

            const generateCombinationsButton = await boProductsCreateTabCombinationsPage.setProductAttributes(
              page,
              product.attributes,
            );
            expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(4));
          });

          it('should click on generate combinations button', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

            const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
            expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(4));
          });

          it('should close combinations generation modal', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed2', baseContext);

            const isModalClosed = await boProductsCreateTabCombinationsPage.generateCombinationModalIsClosed(page);
            expect(isModalClosed).to.be.eq(true);

            await boProductsCreateTabCombinationsPage.editCombinationRowQuantity(page, 1, combinationProduct.quantity);

            const successMessage = await boProductsCreateTabCombinationsPage.saveCombinationsForm(page);
            expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);

            updateProductMessage = await boProductsCreatePage.saveProduct(page);
            expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
          });
        }
      });
    });

    describe(`Create cart rule and apply the discount to : '${productWithCartRule.name}'`, async () => {
      it('should go to \'Catalog > Discounts\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.discountsLink,
        );

        const pageTitle = await boCartRulesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
      });

      it('should go to new cart rule page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCartRulePage', baseContext);

        await boCartRulesPage.goToAddNewCartRulesPage(page);

        const pageTitle = await boCartRulesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCartRulesCreatePage.pageTitle);
      });

      it('should create new cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCartRule', baseContext);

        const validationMessage = await boCartRulesCreatePage.createEditCartRules(page, newCartRuleData);
        expect(validationMessage).to.contains(boCartRulesCreatePage.successfulCreationMessage);
      });
    });
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
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders).to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${customerData.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterTable', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', customerData.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn).to.contains(customerData.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });
  });

  // 2 - Check product block
  describe('Check product block', async () => {
    it('should delete the ordered product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const textResult = await boOrdersViewBlockProductsPage.deleteProduct(page, 1);
      expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulDeleteProductMessage);
    });

    it('should check number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts0', baseContext);

      const productCount = await boOrdersViewBlockProductsPage.getProductsNumber(page);
      expect(productCount).to.equal(productNumber);
    });

    describe('Add \'Simple product\' 2 times and check the error message', async () => {
      it(`should search for the product '${simpleProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchSimpleProduct1', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, simpleProduct.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(simpleProduct.stockLocation),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
          expect(result.price).to.equal(simpleProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addSimpleProductToTheCart1', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSimpleProductDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(simpleProduct.name),
          expect(result.reference).to.equal(`Reference number: ${simpleProduct.reference}`),
          expect(result.basePrice).to.equal(simpleProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(simpleProduct.quantity - 1),
          expect(result.total).to.equal(simpleProduct.price),
        ]);
      });

      it('should add the same product and check the error message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'orderSimpleProduct2', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, simpleProduct.name);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.errorAddSameProduct);
      });

      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOncancelButton1', baseContext);

        await boOrdersViewBlockProductsPage.cancelAddProductToCart(page);

        const isVisible = await boOrdersViewBlockProductsPage.isAddProductTableRowVisible(page);
        expect(isVisible).to.eq(false);
      });
    });

    describe('Add \'Standard product with combination\'', async () => {
      it(`should search for the product '${combinationProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCombinationProduct', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, combinationProduct.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.equal(combinationProduct.quantity - 1),
          expect(result.price).to.equal(combinationProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addCombinationProduct', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCombinationProductDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.name).to.equal(`${combinationProduct.name} (Size: `
            + `${combinationProduct.attributes[1].values[0]} - Color: ${combinationProduct.attributes[0].values[0]})`),
          expect(result.reference).to.equal(`Reference number: ${combinationProduct.reference}`),
          expect(result.basePrice).to.equal(combinationProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(combinationProduct.quantity - 1),
          expect(result.total).to.equal(combinationProduct.price),
        ]);
      });
    });

    describe('Add \'Virtual product\'', async () => {
      it(`should search for the product '${virtualProduct.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVirtualProduct', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, virtualProduct.name);
        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.equal(virtualProduct.quantity - 1),
          expect(result.price).to.equal(virtualProduct.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addVirtualProductToTheCart', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkVirtualProductDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 2);
        await Promise.all([
          expect(result.name).to.equal(virtualProduct.name),
          expect(result.reference).to.equal(`Reference number: ${virtualProduct.reference}`),
          expect(result.basePrice).to.equal(virtualProduct.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(virtualProduct.quantity - 1),
          expect(result.total).to.equal(virtualProduct.price),
        ]);
      });
    });

    describe('Add \'Pack of products\'', async () => {
      it(`should search for the product '${packOfProducts.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchPackOfProducts', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, packOfProducts.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(packOfProducts.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(packOfProducts.price),
        ]);
      });

      it('should try to add the product to the cart and check the error message for the minimal '
        + 'quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addPackOfProductsToTheCart', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.errorMinimumQuantityMessage);
      });

      it('should increase the quantity and check ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPackOfProductsDetails', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page, packOfProducts.minimumQuantity);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 3);
        await Promise.all([
          expect(result.name).to.equal(packOfProducts.name),
          expect(result.reference).to.equal(`Reference number: ${packOfProducts.reference}`),
          expect(result.basePrice).to.equal(packOfProducts.price),
          expect(result.quantity).to.equal(packOfProducts.minimumQuantity),
          expect(result.available).to.equal(packOfProducts.quantity - packOfProducts.minimumQuantity),
          expect(result.total).to.equal(packOfProducts.price * packOfProducts.minimumQuantity),
        ]);

        productNumber += 1;
      });
    });

    describe('Add \'Customized product\'', async () => {
      it(`should search for the product '${dataProducts.demo_14.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCustomizedProduct', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, dataProducts.demo_14.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(dataProducts.demo_14.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addCustomizedProductToTheCart', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCustomizedProductDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 4);
        await Promise.all([
          expect(result.name).to.equal(dataProducts.demo_14.name),
          expect(result.reference).to.equal(`Reference number: ${dataProducts.demo_14.reference}`),
          expect(result.basePrice).to.equal(dataProducts.demo_14.price),
          expect(result.quantity).to.equal(1),
          expect(result.total).to.equal(dataProducts.demo_14.price),
        ]);
      });
    });

    describe('Add product \'Out of stock allowed\'', async () => {
      it(`should search for the product '${productOutOfStockAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockAllowed', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, productOutOfStockAllowed.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.price).to.equal(productOutOfStockAllowed.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductOutOfStockAllowed', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductOutOfStockAllowedDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 4);
        await Promise.all([
          expect(result.name).to.equal(productOutOfStockAllowed.name),
          expect(result.basePrice).to.equal(productOutOfStockAllowed.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productOutOfStockAllowed.quantity - 1),
          expect(result.total).to.equal(productOutOfStockAllowed.price),
        ]);
      });

      it('should update quantity of the product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityOutOfStockAllowed', baseContext);

        const quantity = await boOrdersViewBlockProductsPage.modifyProductQuantity(page, 1, newQuantity);
        expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });
    });

    describe('Add product \'Out of stock not allowed\'', async () => {
      it(`should search for the product '${productOutOfStockNotAllowed.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductOutOfStockNotAllowed', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, productOutOfStockNotAllowed.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productOutOfStockNotAllowed.stockLocation),
          expect(result.available).to.equal(productOutOfStockNotAllowed.quantity - 1),
          expect(result.price).to.equal(productOutOfStockNotAllowed.price),
        ]);
      });

      it('should check that add button is disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatAddButtonIsDisabled', baseContext);

        const isDisabled = await boOrdersViewBlockProductsPage.isAddButtonDisabled(page);
        expect(isDisabled).to.eq(true);
      });

      it('should increase the quantity of the product and check that cancel button still disabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantityOutOfStockNotAllowed', baseContext);

        await boOrdersViewBlockProductsPage.addQuantity(page, newQuantity);

        const isDisabled = await boOrdersViewBlockProductsPage.isAddButtonDisabled(page);
        expect(isDisabled).to.eq(true);
      });

      it('should click on cancel button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton2', baseContext);

        await boOrdersViewBlockProductsPage.cancelAddProductToCart(page);

        const isVisible = await boOrdersViewBlockProductsPage.isAddProductTableRowVisible(page);
        expect(isVisible).to.eq(false);
      });
    });

    describe('Add \'Product with specific price\'', async () => {
      it(`should search for the product '${productWithSpecificPrice.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithSpecificPrice', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, productWithSpecificPrice.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithSpecificPrice.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithSpecificPriceToTheCart', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithSpecificPriceDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 6);
        await Promise.all([
          expect(result.name).to.equal(productWithSpecificPrice.name),
          expect(result.basePrice).to.equal(productWithSpecificPrice.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithSpecificPrice.quantity - 1),
          expect(result.total).to.equal(productWithSpecificPrice.price),
        ]);
      });
    });

    describe('Add \'Product with eco tax\'', async () => {
      it(`should search for the product '${productWithEcoTax.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithEcoTax', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, productWithEcoTax.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productWithEcoTax.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithEcoTax.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithEcoTax', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);

        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);
        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithEcoTaxDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 7);
        await Promise.all([
          expect(result.name).to.equal(productWithEcoTax.name),
          expect(result.basePrice).to.equal(productWithEcoTax.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithEcoTax.quantity - 1),
          expect(result.total).to.equal(productWithEcoTax.price),
        ]);
      });
    });

    describe('Add \'Product with cart rule\'', async () => {
      it(`should search for the product '${productWithCartRule.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithCartRule', baseContext);

        await boOrdersViewBlockProductsPage.searchProduct(page, productWithCartRule.name);

        const result = await boOrdersViewBlockProductsPage.getSearchedProductDetails(page);
        await Promise.all([
          expect(result.stockLocation).to.equal(productWithCartRule.stockLocation),
          expect(result.available).to.be.above(0),
          expect(result.price).to.equal(productWithCartRule.price),
        ]);
      });

      it('should add the product to the cart', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addProductWithCartRuleToTheCart', baseContext);

        const textResult = await boOrdersViewBlockProductsPage.addProductToCart(page);
        expect(textResult).to.contains(boOrdersViewBlockProductsPage.successfulAddProductMessage);

        productNumber += 1;
      });

      it('should check the ordered product details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductWithCartRuleDetails', baseContext);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 8);
        await Promise.all([
          expect(result.name).to.equal(productWithCartRule.name),
          expect(result.basePrice).to.equal(productWithCartRule.price),
          expect(result.quantity).to.equal(1),
          expect(result.available).to.equal(productWithCartRule.quantity - 1),
          expect(result.total).to.equal(productWithCartRule.price),
        ]);
      });
    });

    describe('Update price and quantity of the first ordered product in the list', async () => {
      it('should update the quantity', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updateQuantity', baseContext);

        const quantity = await boOrdersViewBlockProductsPage.modifyProductQuantity(page, 1, newQuantity);
        expect(quantity, 'Quantity was not updated').to.equal(newQuantity);
      });

      it('should update the price', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'updatePrice', baseContext);

        await boOrdersViewBlockProductsPage.modifyProductPrice(page, 1, newPrice);

        const result = await boOrdersViewBlockProductsPage.getProductDetails(page, 1);
        await Promise.all([
          expect(result.basePrice, 'Base price was not updated').to.equal(newPrice),
          expect(result.total, 'Total price was not updated').to.equal(newPrice * newQuantity),
        ]);
      });
    });

    describe('Check items per page', async () => {
      it('should check number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

        const productCount = await boOrdersViewBlockProductsPage.getProductsNumber(page);
        expect(productCount).to.equal(productNumber);
      });

      describe('Paginate between pages', async () => {
        it('should display 8 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayDefaultItemsNumber', baseContext);

          const isNextLinkVisible = await boOrdersViewBlockProductsPage.selectPaginationLimit(page, 8);
          expect(isNextLinkVisible).to.eq(true);
        });

        it('should click on next', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

          const paginationNumber = await boOrdersViewBlockProductsPage.paginationNext(page);
          expect(paginationNumber).to.equal('2');
        });

        it('should click on previous', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

          const paginationNumber = await boOrdersViewBlockProductsPage.paginationPrevious(page);
          expect(paginationNumber).to.equal('1');
        });

        it('should display 20 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'displayAllItems', baseContext);

          const isNextLinkVisible = await boOrdersViewBlockProductsPage.selectPaginationLimit(page, 20);
          expect(isNextLinkVisible).to.eq(false);
        });

        it('should display 8 items', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'redisplayDefaultItemsNumber', baseContext);

          const isNextLinkVisible = await boOrdersViewBlockProductsPage.selectPaginationLimit(page, 8);
          expect(isNextLinkVisible).to.eq(true);
        });
      });
    });
  });

  // Post-condition: Delete the created customer
  deleteCustomerTest(customerData, `${baseContext}_postTest_1`);

  // Post-condition: Delete the created products
  bulkDeleteProductsTest(prefixNewProduct, `${baseContext}_postTest_2`);

  // Post-condition: Delete cart rule
  deleteCartRuleTest(newCartRuleData.name, `${baseContext}_postTest_3`);

  // Post-condition: Disable EcoTax
  disableEcoTaxTest(`${baseContext}_postTest_4`);
});
