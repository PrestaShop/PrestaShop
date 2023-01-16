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
import foProductPage from '@pages/FO/product';
import basicHelper from '@utils/basicHelper';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {enableNewProductPageTest, disableNewProductPageTest} from '@commonTests/BO/advancedParameters/newFeatures';

// Import faker data
import ProductFaker from '@data/faker/product';
import files from '@utils/files';

const baseContext = 'productV2_functional_CRUDStandardProduct';

describe('BO - Catalog - Products : CRUD standard product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData = new ProductFaker({
    type: 'standard',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });
  const editProductData = new ProductFaker({
    type: 'standard',
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

    it('should check the standard product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      const productTypeDescription = await productsPage.getProductDescription(page);
      await expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
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
    });
  });

  // 2 - Edit product
  describe('Edit product', async () => {
    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage = await createProductsPage.setProduct(page, editProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductHeaderDetails', baseContext);

      const taxValue = await basicHelper.percentage(editProductData.price, editProductData.tax);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(editProductData.price.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(editProductData.price + taxValue).toFixed(2)} tax incl. (tax rule: ${editProductData.tax}%)`),
        expect(productHeaderSummary.quantity).to.equal(`${editProductData.quantity + newProductData.quantity} in stock`),
        expect(productHeaderSummary.reference).to.contains(editProductData.reference),
      ]);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEditedProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      const taxValue = await basicHelper.percentage(editProductData.price, editProductData.tax);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(editProductData.name),
        await expect(result.price.toFixed(2)).to.equal((editProductData.price + taxValue).toFixed(2)),
        await expect(result.description).to.equal(editProductData.description),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  // 3 - Delete product
  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await createProductsPage.deleteProduct(page);
      await expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);
});
