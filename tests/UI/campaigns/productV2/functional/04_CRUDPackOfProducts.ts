// Import utils
import helper from '@utils/helpers';
import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import test context
import testContext from '@utils/testContext';
import date from '@utils/date';
import files from '@utils/files';
import basicHelper from '@utils/basicHelper';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import productsPage from '@pages/BO/catalog/productsV2';
import packTab from '@pages/BO/catalog/productsV2/add/packTab';
import pricingTab from '@pages/BO/catalog/productsV2/add/pricingTab';
import foProductPage from '@pages/FO/product';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {enableNewProductPageTest, disableNewProductPageTest} from '@commonTests/BO/advancedParameters/newFeatures';

// Import faker data
import ProductFaker from '@data/faker/product';

// Import demo data
import {DefaultEmployee} from '@data/demo/employees';
import {Products} from '@data/demo/products';

const baseContext = 'productV2_functional_CRUDPackOfProducts';

describe('BO - Catalog - Products : CRUD pack of products', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const todayDate: string = date.getDateFormat('yyyy-mm-dd');

  // Data to create standard product
  const newProductData: ProductFaker = new ProductFaker({
    type: 'pack',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    pack: {demo_11: 10},
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 0,
    minimumQuantity: 0,
    status: false,
  });

  const editPackData: object = {
    quantity: 100,
    minimalQuantity: 2,
    packQuantitiesOption: 'Decrement pack only',
  };

  // Data to edit the product price
  const pricingData: object = {
    price: 15,
    taxRule: 'FR Taux standard (20%)',
    priceTaxIncl: 18,
  };

  const editProductData: ProductFaker = new ProductFaker({
    type: 'pack',
    pack: {demo_19: 20},
    taxRule: 'FR Taux réduit (10%)',
    tax: 10,
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // Pre-condition: Enable new product page
  enableNewProductPageTest(`${baseContext}_enableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    await files.generateImage('cover.jpg');
    await files.generateImage('thumb.jpg');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('cover.jpg');
    await files.deleteFile('thumb.jpg');
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

        const pageTitle: string = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should click on \'New product\' button and check new product modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

        const isModalVisible: boolean = await productsPage.clickOnNewProductButton(page);
        await expect(isModalVisible).to.be.true;
      });

      it('should choose \'Pack of products\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'choosePackOfProducts', baseContext);

        await productsPage.selectProductType(page, newProductData.type);

        const pageTitle: string = await createProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should select the pack of products and check the description', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPackOfProductsDescription', baseContext);

        await productsPage.selectProductType(page, newProductData.type);

        const productTypeDescription: string = await productsPage.getProductDescription(page);
        await expect(productTypeDescription).to.contains(productsPage.packOfProductsDescription);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

        await productsPage.clickOnAddNewProduct(page);

        const pageTitle: string = await createProductsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });
    });

    describe('Add different types of products to the pack', async () => {
      it('should add a sample product to the pack', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

        await createProductsPage.closeSfToolBar(page);

        const createProductMessage: string = await createProductsPage.setProduct(page, newProductData);
        await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
      });

      it('should search for the same product and check that no results found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchSameProduct', baseContext);

        const searchResult: string = await packTab.searchProduct(page, 'demo_11');
        await expect(searchResult).to.equal('No results found for "demo_11"');
      });

      it('should search for a non existent product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchNonExistentProduct', baseContext);

        const searchResult: string = await packTab.searchProduct(page, 'Pack mug');
        await expect(searchResult).to.equal('No results found for "Pack mug"');
      });
      const tests: object = [
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
      tests.forEach((test, index) => {
        // 3 - Add product with combination
        it(`should search for a '${test.args.productType}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchProductToPack${index}`, baseContext);

          const searchResult: string = await packTab.searchProduct(page, test.args.product.name);
          await expect(searchResult).to.equal(test.args.resultOfSearch);
        });

        it('should check the number of product in list', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProductsInList${index}`, baseContext);

          const numberOfProducts: number = await packTab.getNumberOfSearchedProduct(page);
          await expect(numberOfProducts).to.equal(test.args.numberOfProducts);
        });

        if (test.args.productType === 'product with combination') {
          it('should choose the third combination', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdCombination', baseContext);

            const isListOfProductVisible: boolean = await packTab.selectProductFromList(page, 3);
            await expect(isListOfProductVisible).to.be.true;
          });
        } else {
          it('should choose the searched product', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `chooseProduct${index}`, baseContext);

            const isListOfProductVisible: boolean = await packTab.selectProductFromList(page);
            await expect(isListOfProductVisible).to.be.true;
          });
        }

        it('should check the number of products in the pack', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkNumberOfProductsInPack${index}`, baseContext);

          const numberOfProducts: number = await packTab.getNumberOfProductsInPack(page);
          await expect(numberOfProducts).to.equal(index + 2);
        });

        it('should check the selected product information', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductInformationBO${index}`, baseContext);

          const result: object = await packTab.getProductInPackInformation(page, index + 2);
          await Promise.all([
            await expect(result.image).to.contains(test.args.product.defaultImage),
            await expect(result.name).to.equal(test.args.productToChooseName),
            await expect(result.reference).to.equal(`Ref: ${test.args.product.reference}`),
            await expect(parseInt(result.quantity, 10)).to.equal(1),
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

      const errorMessage: string = await packTab.saveAndGetProductInPackErrorMessage(page, 1);
      await expect(errorMessage).to.equal('This value should be greater than or equal to 1.');
    });

    it('should try to edit the quantity of the customized product by a text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryToEditByText', baseContext);

      await packTab.setProductQuantity(page, 0, 'test');

      const errorMessage: string = await packTab.saveAndGetProductInPackErrorMessage(page, 1);
      await expect(errorMessage).to.equal('This value should be of type numeric.');
    });

    it('should set a valid quantity then save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setValidQuantity', baseContext);

      await packTab.setProductQuantity(page, 0, 15);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should try delete the customized product then cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelDeleteProduct', baseContext);

      const isModalVisible: boolean = await packTab.deleteProduct(page, 1, false);
      await expect(isModalVisible).to.be.true;
    });

    it('should delete the customized product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomizedProduct', baseContext);

      const firstProductInList: string = await packTab.deleteProduct(page, 1, true);
      await expect(firstProductInList).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should edit the quantity and the minimum quantity of the pack then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editMinimumQuantity', baseContext);

      await packTab.editPackOfProducts(page, editPackData);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStockMovement', baseContext);

      const result: object = await packTab.getStockMovement(page, 1);
      await Promise.all([
        await expect(result.dateTime).to.contains(todayDate),
        await expect(result.employee).to.equal(`${DefaultEmployee.firstName} ${DefaultEmployee.lastName}`),
        await expect(result.quantity).to.equal(editPackData.quantity),
      ]);
    });

    it('should go to Pricing tab and edit retail price, enable the product then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editRetailPrice', baseContext);

      await pricingTab.setProductPricing(page, pricingData);

      await createProductsPage.setProductStatus(page, true);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue: number = await basicHelper.percentage(pricingData.price, 20);

      const productHeaderSummary: object = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(pricingData.price.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(pricingData.price + taxValue).toFixed(2)} tax incl. (tax rule: 20%)`),
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

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAllProductInformation', baseContext);

      const taxValue: number = await basicHelper.percentage(pricingData.price, 20);

      const result: object = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(newProductData.name),
        await expect(result.price.toFixed(2)).to.equal((pricingData.price + taxValue).toFixed(2)),
        await expect(result.shortDescription).to.equal(newProductData.summary),
        await expect(result.description).to.equal(newProductData.description),
      ]);
    });

    const tests: object = [
      {args: {product: Products.demo_11, quantity: 15}},
      {args: {product: Products.demo_18, quantity: 1}},
      {args: {product: Products.demo_14, quantity: 1}},
    ];
    tests.forEach((test, index) => {
      it(`should check the product '${test.args.product.name}' in the pack`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductInPack${index}`, baseContext);

        const result: object = await foProductPage.getProductInPackList(page, index + 2);
        await Promise.all([
          await expect(result.image).to.contains(test.args.product.coverImage),
          await expect(result.name).to.equal(test.args.product.name),
          await expect(result.price).to.equal(`€${test.args.product.finalPrice.toFixed(2)}`),
          await expect(result.quantity).to.equal(test.args.quantity),
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

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage: string = await createProductsPage.setProduct(page, editProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  // 5 - Check edited product in FO
  describe('Check edited product in FO', async () => {
    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const taxValue: number = await basicHelper.percentage(editProductData.price, 10);

      const result: object = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(editProductData.name),
        await expect(result.price.toFixed(2)).to.equal((editProductData.price + taxValue).toFixed(2)),
        await expect(result.shortDescription).to.equal(editProductData.summary),
        await expect(result.description).to.equal(editProductData.description),
      ]);
    });

    const tests: object = [
      {args: {product: Products.demo_11, quantity: 15}},
      {args: {product: Products.demo_18, quantity: 1}},
      {args: {product: Products.demo_19, quantity: 20}},
      {args: {product: Products.demo_14, quantity: 1}},
    ];
    tests.forEach((test, index) => {
      it(`should check the product '${test.args.product.name}' in the pack`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkEditProductInPack${index}`, baseContext);

        const result: object = await foProductPage.getProductInPackList(page, index + 2);
        await Promise.all([
          await expect(result.image).to.contains(test.args.product.coverImage),
          await expect(result.name).to.equal(test.args.product.name),
          await expect(result.price).to.equal(`€${test.args.product.finalPrice.toFixed(2)}`),
          await expect(result.quantity).to.equal(test.args.quantity),
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

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage: string = await createProductsPage.deleteProduct(page);
      await expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);
});
