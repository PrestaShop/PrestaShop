// Import utils
import helper from '@utils/helpers';
import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import test context
import testContext from '@utils/testContext';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import productsPage from '@pages/BO/catalog/productsV2';
import packTab from '@pages/BO/catalog/productsV2/add/packTab';
import foProductPage from '@pages/FO/product';
import basicHelper from '@utils/basicHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {enableNewProductPageTest, disableNewProductPageTest} from '@commonTests/BO/advancedParameters/newFeatures';

// Import faker data
import ProductFaker from '@data/faker/product';
import files from '@utils/files';

const baseContext = 'productV2_functional_CRUDPackOfProducts';

describe('BO - Catalog - Products : CRUD pack of products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData = new ProductFaker({
    type: 'pack',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    pack: {demo_11: 10},
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });

  const editPackData = {
    quantity: 100,
    minimumQuantity: 2,
    packQuantities: 'Decrement pack only',
  };

  const editProductData = new ProductFaker({
    type: 'pack',
    taxRule: 'FR Taux réduit (10%)',
    tax: 10,
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // Pre-condition: Enable new product page
  //enableNewProductPageTest(`${baseContext}_enableNewProduct`);

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
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Pack of products\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should select the pack of products and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await productsPage.getProductDescription(page);
      await expect(productTypeDescription).to.contains(productsPage.packOfProductsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    // 1 - Add simple product
    it('should create pack of products and add a sample product to the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPackOfProducts', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    // 2 - Add same product
    it('should search for the same product and check that no results found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchSameProduct', baseContext);

      const searchResult = await packTab.searchProductToPack(page, 'demo_11');
      await expect(searchResult).to.equal('No results found for "demo_11"');
    });

    // 3 - Add product with combination
    it('should search for a product with combinations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithCombination', baseContext);

      const searchResult = await packTab.searchProductToPack(page, 'Hummingbird printed t-shirt');
      await expect(searchResult).to.equal('Hummingbird printed t-shirt: Size - S, Color - White(Ref: demo_1) '
        + 'Hummingbird printed t-shirt: Size - S, Color - Black(Ref: demo_1) Hummingbird printed t-shirt: Size - M, Color - '
        + 'White(Ref: demo_1) Hummingbird printed t-shirt: Size - M, Color - Black(Ref: demo_1) Hummingbird printed t-shirt: '
        + 'Size - L, Color - White(Ref: demo_1) Hummingbird printed t-shirt: Size - L, Color - Black(Ref: demo_1) '
        + 'Hummingbird printed t-shirt: Size - XL, Color - White(Ref: demo_1) Hummingbird printed t-shirt: Size - XL, '
        + 'Color - Black(Ref: demo_1)');

      const numberOfProducts = await packTab.getNumberOfSearchedProduct(page);
      await expect(numberOfProducts).to.equal(8);
    });

    it('should choose the third combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdCombination', baseContext);

      const isListOfProductVisible = await packTab.selectProductFromList(page, 3);
      await expect(isListOfProductVisible).to.be.true;
    });

    it('should check the number of product in the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      await expect(numberOfProducts).to.equal(2);
    });

    it('should check the selected product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const result = await packTab.getProductInPackInformation(page, 2);
      await Promise.all([
        await expect(result.image).to.contains('2-home_default.jpg'),
        await expect(result.iconDelete).to.be.true,
        await expect(result.name).to.equal('Hummingbird printed t-shirt: Size - M, Color - White'),
        await expect(result.reference).to.equal('Ref: demo_1'),
        await expect(parseInt(result.quantity, 10)).to.equal(1),
      ]);
    });

    // 4 - Add virtual product
    it('should search for a virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithCombination', baseContext);

      const searchResult = await packTab.searchProductToPack(page, 'Mountain fox - Vector graphics');
      await expect(searchResult).to.equal('Mountain fox - Vector graphics(Ref: demo_18)');
    });

    it('should choose virtual product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdCombination', baseContext);

      const isListOfProductVisible = await packTab.selectProductFromList(page);
      await expect(isListOfProductVisible).to.be.true;
    });

    it('should check the number of product in the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      await expect(numberOfProducts).to.equal(3);
    });

    it('should check the selected product information', async function () {
      const result = await packTab.getProductInPackInformation(page, 3);
      await Promise.all([
        await expect(result.image).to.contains('15-home_default.jpg'),
        await expect(result.iconDelete).to.be.true,
        await expect(result.name).to.equal('Mountain fox - Vector graphics'),
        await expect(result.reference).to.equal('Ref: demo_18'),
        await expect(parseInt(result.quantity, 10)).to.equal(1),
      ]);
    });

    // 5 - Add customized product
    it('should search for a customized product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithCombination', baseContext);

      const searchResult = await packTab.searchProductToPack(page, 'Customizable mug');
      await expect(searchResult).to.equal('Customizable mug(Ref: demo_14)');
    });

    it('should choose customized product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseThirdCombination', baseContext);

      const isListOfProductVisible = await packTab.selectProductFromList(page);
      await expect(isListOfProductVisible).to.be.true;
    });

    it('should check the number of product in the pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

      const numberOfProducts = await packTab.getNumberOfProductsInPack(page);
      await expect(numberOfProducts).to.equal(4);
    });

    it('should check the selected product information', async function () {
      const result = await packTab.getProductInPackInformation(page, 4);
      await Promise.all([
        await expect(result.image).to.contains('22-home_default.jpg'),
        await expect(result.iconDelete).to.be.true,
        await expect(result.name).to.equal('Customizable mug'),
        await expect(result.reference).to.equal('Ref: demo_14'),
        await expect(parseInt(result.quantity, 10)).to.equal(1),
      ]);
    });

    // 6 - Add non existent product
    it('should search for a non existent product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProductWithCombination', baseContext);

      const searchResult = await packTab.searchProductToPack(page, 'Pack mug');
      await expect(searchResult).to.equal('No results found for "Pack mug"');
    });

    // 7 - Edit product in pack quantity
    it('should try to edit the quantity of the customized product by a negative value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryToEditByNegativeValue', baseContext);

      await packTab.setProductQuantity(page, 0, -1);

      const errorMessage = await packTab.saveAndGetProductInPackErrorMessage(page, 1);
      await expect(errorMessage).to.equal('This value should be greater than or equal to 1.');
    });

    it('should try to edit the quantity of the customized product by a text', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'tryToEditByNegativeValue', baseContext);

      await packTab.setProductQuantity(page, 0, 'test');

      const errorMessage = await packTab.saveAndGetProductInPackErrorMessage(page, 1);
      await expect(errorMessage).to.equal('This value should be of type numeric.');
    });

    it('should set a valid quantity then save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setValidQuantity', baseContext);

      await packTab.setProductQuantity(page, 0, 15);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    // Delete product from the pack
    it('should try delete the customized product then cancel', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelDeleteProduct', baseContext);

      const isModalVisible = await packTab.deleteProduct(page, 1, false);
      await expect(isModalVisible).to.be.true;
    });

    it('should delete the customized product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const firstProductInList = await packTab.deleteProduct(page, 1, true);
      await expect(firstProductInList).to.equal(createProductsPage.successfulUpdateMessage);

      const isModalVisible = await packTab.isDeleteModalVisible(page);
      await expect(isModalVisible).to.be.false;
    });

    // Edit pack of products
    it('should edit the quantity and the minimum quantity of the pack then save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackOfProducts', baseContext);

      await packTab.editPackOfProducts(page, editPackData);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the recent stock movement', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editPackOfProducts', baseContext);

      const result = await packTab.getStockMovement(page, 1);
      await Promise.all([
        await expect(result.dateTime).to.contains('2-home_default.jpg'),
        await expect(result.employee).to.equal('Marc Beier'),
        await expect(result.quantity).to.equal(100),
      ]);
    });

    // Save product
    /*it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct2', baseContext);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });*/

    /* it('should check the product header details', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

       const taxValue = await basicHelper.percentage(newProductData.price, newProductData.tax);

       const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
       await Promise.all([
         expect(productHeaderSummary.priceTaxExc).to.equal(`€${(newProductData.price.toFixed(2))} tax excl.`),
         expect(productHeaderSummary.priceTaxIncl).to.equal(
           `€${(newProductData.price + taxValue).toFixed(2)} tax incl. (tax rule: ${newProductData.tax}%)`),
         expect(productHeaderSummary.quantity).to.equal(`${newProductData.quantity} in stock`),
         expect(productHeaderSummary.reference).to.contains(newProductData.reference),
       ]);
     });

     it('should check that the save button is changed to \'Save and publish\'', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

       const saveButtonName = await createProductsPage.getSaveButtonName(page);
       await expect(saveButtonName).to.equal('Save and publish');
     });

     it('should preview product', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

       // Click on preview button
       page = await createProductsPage.previewProduct(page);

       await foProductPage.changeLanguage(page, 'en');

       const pageTitle = await foProductPage.getPageTitle(page);
       await expect(pageTitle).to.contains(newProductData.name);
     });

     it('should check all product information', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

       const taxValue = await basicHelper.percentage(newProductData.price, newProductData.tax);

       const result = await foProductPage.getProductInformation(page);
       await Promise.all([
         await expect(result.name).to.equal(newProductData.name),
         await expect(result.price.toFixed(2)).to.equal((newProductData.price + taxValue).toFixed(2)),
         await expect(result.shortDescription).to.equal(newProductData.summary),
         await expect(result.description).to.equal(newProductData.description),
       ]);
     });

     it('should go back to BO', async function () {
       await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

       // Go back to BO
       page = await foProductPage.closePage(browserContext, page, 0);

       const pageTitle = await createProductsPage.getPageTitle(page);
       await expect(pageTitle).to.contains(createProductsPage.pageTitle);
     });*/
  });
});
