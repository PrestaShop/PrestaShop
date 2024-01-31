// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import date from '@utils/date';
import files from '@utils/files';
import basicHelper from '@utils/basicHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/products/add';
import productsPage from '@pages/BO/catalog/products';
import packTab from '@pages/BO/catalog/products/add/packTab';
import pricingTab from '@pages/BO/catalog/products/add/pricingTab';
import foProductPage from '@pages/FO/classic/product';

// Import data
import ProductData from '@data/faker/product';
import Employees from '@data/demo/employees';
import Products from '@data/demo/products';
import {ProductPackOptions} from '@data/types/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_CRUDPackOfProducts';

describe('BO - Catalog - Products : CRUD pack of products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const todayDate: string = date.getDateFormat('yyyy-mm-dd');

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'pack',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    pack: [
      {
        reference: 'demo_11',
        quantity: 10,
      },
    ],
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 0,
    minimumQuantity: 0,
    status: false,
  });

  const editPackData: ProductPackOptions = {
    quantity: 100,
    minimalQuantity: 2,
    packQuantitiesOption: 'Decrement pack only',
  };

  // Data to edit the product price
  const pricingData: ProductData = new ProductData({
    price: 18,
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
  });

  const editProductData: ProductData = new ProductData({
    type: 'pack',
    pack: [
      {
        reference: 'demo_19',
        quantity: 20,
      },
    ],
    taxRule: 'FR Taux réduit (10%)',
    tax: 10,
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    if (newProductData.coverImage) {
      await files.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    if (newProductData.coverImage) {
      await files.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await files.deleteFile(newProductData.thumbImage);
    }
  });

  // 1 - Create product
  describe('Create product', async () => {
    describe('Select pack of products type and go to create product page', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );

        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

        const isModalVisible = await productsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should choose \'Pack of products\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'choosePackOfProducts', baseContext);

        await productsPage.selectProductType(page, newProductData.type);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should select the pack of products and check the description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPackOfProductsDescription', baseContext);

        await productsPage.selectProductType(page, newProductData.type);

        const productTypeDescription = await productsPage.getProductDescription(page);
        expect(productTypeDescription).to.contains(productsPage.packOfProductsDescription);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

        await productsPage.clickOnAddNewProduct(page);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });
    });

    describe('Add different types of products to the pack', async () => {
      it('should add a sample product to the pack', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

        await createProductsPage.closeSfToolBar(page);

        const createProductMessage = await createProductsPage.setProduct(page, newProductData);
        expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
      });

      it('should search for the same product and check that no results found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchSameProduct', baseContext);

        const searchResult = await packTab.searchProduct(page, 'demo_11');
        expect(searchResult).to.equal('No results found for "demo_11"');
      });

      it('should search for a non existent product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchNonExistentProduct', baseContext);

        const searchResult = await packTab.searchProduct(page, 'Pack mug');
        expect(searchResult).to.equal('No results found for "Pack mug"');
      });
      const tests = [
        {
          args:
            {
              productType: 'product with combination',
              product: Products.demo_1,
              numberOfProducts: 8,
              resultOfSearch: `${Products.demo_1.name}: Size - S, Color - White(Ref: ${Products.demo_1.reference}) `
                + `${Products.demo_1.name}: Size - S, Color - Black(Ref: ${Products.demo_1.reference}) ${Products.demo_1.name}`
                + `: Size - M, Color - White(Ref: ${Products.demo_1.reference}) ${Products.demo_1.name}: Size - M, `
                + `Color - Black(Ref: ${Products.demo_1.reference}) ${Products.demo_1.name}: Size - L,`
                + ` Color - White(Ref: ${Products.demo_1.reference}) ${Products.demo_1.name}: Size - L, Color - Black(Ref:`
                + ` ${Products.demo_1.reference}) ${Products.demo_1.name}: Size - XL, Color - White(Ref: `
                + `${Products.demo_1.reference}) ${Products.demo_1.name}: Size - XL, Color - Black(Ref: `
                + `${Products.demo_1.reference})`,
              productToChooseNumber: 3,
              productToChooseName: `${Products.demo_1.name}: Size - M, Color - White`,
            },
        },
        {
          args:
            {
              productType: 'virtual product',
              product: Products.demo_18,
              resultOfSearch: `${Products.demo_18.name}(Ref: ${Products.demo_18.reference})`,
              numberOfProducts: 1,
              productToChooseName: Products.demo_18.name,
            },
        },
        {
          args:
            {
              productType: 'customized product',
              product: Products.demo_14,
              resultOfSearch: `${Products.demo_14.name}(Ref: ${Products.demo_14.reference})`,
              numberOfProducts: 1,
              productToChooseName: Products.demo_14.name,
            },
        },
      ];
      tests.forEach((test, index: number) => {
        // 3 - Add product with combination
        it(`should search for a '${test.args.productType}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProductToPack${index}`, baseContext);

          const searchResult = await packTab.searchProduct(page, test.args.product.name);
          expect(searchResult).to.equal(test.args.resultOfSearch);
        });

        it('should check the number of product in list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProductsInList${index}`, baseContext);

          const numberOfProducts = await packTab.getNumberOfSearchedProduct(page);
          expect(numberOfProducts).to.equal(test.args.numberOfProducts);
        });

        if (test.args.productType === 'product with combination') {
          it('should choose the third combination', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdCombination', baseContext);

            const isListOfProductVisible = await packTab.selectProductFromList(page, 3);
            expect(isListOfProductVisible).to.eq(true);
          });
        } else {
          it('should choose the searched product', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `chooseProduct${index}`, baseContext);

            const isListOfProductVisible = await packTab.selectProductFromList(page, 1);
            expect(isListOfProductVisible).to.eq(true);
          });
        }

        it('should check the number of products in the pack', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProductsInPack${index}`, baseContext);

          const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
          expect(numberOfProducts).to.equal(index + 2);
        });

        it('should check the selected product information', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductInformationBO${index}`, baseContext);

          const result = await packTab.getProductInPackInformation(page, index + 2);
          await Promise.all([
            expect(result.image).to.contains(test.args.product.defaultImage),
            expect(result.name).to.equal(test.args.productToChooseName),
            expect(result.reference).to.equal(`Ref: ${test.args.product.reference}`),
            expect(result.quantity).to.equal(1),
          ]);
        });
      });
    });
  });

  // 2 - Edit product in pack tab
  describe('Edit/Delete product in pack', async () => {
    it('should try to edit the quantity of the customized product by a negative value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryToEditByNegativeValue', baseContext);

      await packTab.setProductQuantity(page, 0, -1);

      const errorMessage = await packTab.saveAndGetProductInPackErrorMessage(page, 1);
      expect(errorMessage).to.equal('This value should be greater than or equal to 1.');
    });

    it('should try to edit the quantity of the customized product by a text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryToEditByText', baseContext);

      await packTab.setProductQuantity(page, 0, 'test');

      const errorMessage = await packTab.saveAndGetProductInPackErrorMessage(page, 1);
      expect(errorMessage).to.equal('This value should be of type numeric.');
    });

    it('should set a valid quantity then save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setValidQuantity', baseContext);

      await packTab.setProductQuantity(page, 0, 15);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should try delete the customized product then cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelDeleteProduct', baseContext);

      const isModalVisible = await packTab.deleteProduct(page, 1, false);
      expect(isModalVisible).to.eq(true);
    });

    it('should delete the customized product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomizedProduct', baseContext);

      const firstProductInList = await packTab.deleteProduct(page, 1, true);
      expect(firstProductInList).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should edit the quantity and the minimum quantity of the pack then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editMinimumQuantity', baseContext);

      await packTab.editPackOfProducts(page, editPackData);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockMovement', baseContext);

      const result = await packTab.getStockMovement(page, 1);
      await Promise.all([
        expect(result.dateTime).to.contains(todayDate),
        expect(result.employee).to.equal(`${Employees.DefaultEmployee.firstName} ${Employees.DefaultEmployee.lastName}`),
        expect(result.quantity).to.equal(editPackData.quantity),
      ]);
    });

    it('should go to Pricing tab and edit retail price, enable the product then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editRetailPrice', baseContext);

      await pricingTab.setProductPricing(page, pricingData);

      await createProductsPage.setProductStatus(page, true);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue = await basicHelper.percentage(pricingData.priceTaxExcluded, 20);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(pricingData.priceTaxExcluded.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(pricingData.priceTaxExcluded + taxValue).toFixed(2)} tax incl. (tax rule: 20%)`),
        expect(productHeaderSummary.quantity).to.equal('100 in stock'),
        expect(productHeaderSummary.reference).to.contains(newProductData.reference),
      ]);
    });
  });

  // 3 - Check product in FO
  describe('Check product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllProductInformation', baseContext);

      const taxValue = await basicHelper.percentage(pricingData.priceTaxExcluded, 20);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(newProductData.name),
        expect(result.price.toFixed(2)).to.equal((pricingData.priceTaxExcluded + taxValue).toFixed(2)),
        expect(result.summary).to.equal(newProductData.summary),
        expect(result.description).to.equal(newProductData.description),
      ]);
    });

    const tests = [
      {args: {product: Products.demo_11, quantity: 15}},
      {args: {product: Products.demo_18, quantity: 1}},
      {args: {product: Products.demo_14, quantity: 1}},
    ];
    tests.forEach((test, index: number) => {
      it(`should check the product '${test.args.product.name}' in the pack`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductInPack${index}`, baseContext);

        const result = await foProductPage.getProductInPackList(page, index + 1);
        await Promise.all([
          expect(result.image).to.contains(test.args.product.coverImage),
          expect(result.name).to.equal(test.args.product.name),
          expect(result.price).to.equal(`€${test.args.product.finalPrice.toFixed(2)}`),
          expect(result.quantity).to.equal(test.args.quantity),
        ]);
      });
    });
  });

  // 4 - Edit product
  describe('Edit product', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO1', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage = await createProductsPage.setProduct(page, editProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  // 5 - Check edited product in FO
  describe('Check edited product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue = await basicHelper.percentage(editProductData.priceTaxExcluded, 10);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        expect(result.name).to.equal(editProductData.name),
        expect(result.price.toFixed(2)).to.equal((editProductData.priceTaxExcluded + taxValue).toFixed(2)),
        expect(result.summary).to.equal(editProductData.summary),
        expect(result.description).to.equal(editProductData.description),
      ]);
    });

    const tests = [
      {args: {product: Products.demo_11, quantity: 15}},
      {args: {product: Products.demo_18, quantity: 1}},
      {args: {product: Products.demo_19, quantity: 20}},
      {args: {product: Products.demo_14, quantity: 1}},
    ];
    tests.forEach((test, index: number) => {
      it(`should check the product '${test.args.product.name}' in the pack`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkEditProductInPack${index}`, baseContext);

        const result = await foProductPage.getProductInPackList(page, index + 1);
        await Promise.all([
          expect(result.image).to.contains(test.args.product.coverImage),
          expect(result.name).to.equal(test.args.product.name),
          expect(result.price).to.equal(`€${test.args.product.finalPrice.toFixed(2)}`),
          expect(result.quantity).to.equal(test.args.quantity),
        ]);
      });
    });
  });

  // 6 - Delete product
  describe('Delete product', async () => {
    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await createProductsPage.deleteProduct(page);
      expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
