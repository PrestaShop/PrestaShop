import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  type BrowserContext,
  FakerProduct,
  foHummingbirdProductPage,
  type Page,
  type ProductAttribute,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_virtualCombinations_perCombinationVirtual';

/*
Create a product of type 'combinations' with one attribute generating two combinations
Mark combination #1 as virtual and upload a distinct downloadable file
Mark combination #2 as physical (is_virtual = false)
Save and assert in BO that combination #1 keeps its file
Preview in FO, select each combination and assert the virtual one offers its download file
Assert the two combinations differ (only the virtual one has a download)
Delete the product
 */
describe('BO - Catalog - Products : Per-combination virtual flag and downloadable file', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // The distinct files uploaded to the virtual combination
  const virtualFile: string = 'virtual_combination_S.txt';
  const physicalFileShouldStayEmpty: string = '';

  // Data to create a product with combinations
  const newProductData: FakerProduct = new FakerProduct({
    type: 'combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
    price: 20,
    quantity: 50,
    minimumQuantity: 1,
    attributes: [
      {
        name: 'size',
        values: ['S', 'M'],
      },
    ],
    status: true,
  });

  // Combinations selectors used in FO (one attribute, two values -> two combinations)
  const virtualCombination: ProductAttribute[] = [
    {
      name: 'size',
      value: 'S',
    },
  ];
  const physicalCombination: ProductAttribute[] = [
    {
      name: 'size',
      value: 'M',
    },
  ];

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
    // Generate the distinct file uploaded to the virtual combination
    await utilsFile.createFile('.', virtualFile, `Virtual combination download - ${virtualFile}`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
    await utilsFile.deleteFile(virtualFile);
  });

  describe('Create product with combinations', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should select the product with combinations type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCombinationsType', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Generate combinations', async () => {
    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton = await boProductsCreateTabCombinationsPage.setProductAttributes(
        page,
        newProductData.attributes,
      );
      expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(2));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(2));
    });

    it('combinations generation modal should be closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed', baseContext);

      const isModalClosed = await boProductsCreateTabCombinationsPage.generateCombinationModalIsClosed(page);
      expect(isModalClosed).to.eq(true);
    });
  });

  describe('Set the per-combination virtual flag and file', async () => {
    it('should open the edit modal of the first combination (S)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFirstCombination', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 1);
      expect(isVisible).to.eq(true);
    });

    it('should mark the first combination as virtual', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setFirstCombinationVirtual', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.setCombinationIsVirtual(page, true);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should upload a distinct downloadable file to the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadFirstCombinationFile', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.setCombinationVirtualProductFile(page, virtualFile);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should close the first combination modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFirstCombination', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
      expect(isModalVisible).to.eq(false);
    });

    it('should open the edit modal of the second combination (M)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openSecondCombination', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 2);
      expect(isVisible).to.eq(true);
    });

    it('should keep the second combination physical (is_virtual = false)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setSecondCombinationPhysical', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.setCombinationIsVirtual(page, false);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should close the second combination modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeSecondCombination', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
      expect(isModalVisible).to.eq(false);
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      const updateProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  describe('Assert in BO that the virtual combination keeps its file', async () => {
    it('should re-open the first combination (S)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'reopenFirstCombination', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 1);
      expect(isVisible).to.eq(true);
    });

    it('should check that the first combination still has its uploaded file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCombinationFile', baseContext);

      const fileName = await boProductsCreateTabCombinationsPage.getCombinationVirtualProductFileName(page);
      expect(fileName).to.contains(virtualFile);
    });

    it('should close the first combination modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFirstCombinationAfterCheck', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
      expect(isModalVisible).to.eq(false);
    });
  });

  describe('Check the per-combination download in FO', async () => {
    it('should preview the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foHummingbirdProductPage.changeLanguage(page, 'en');

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should select the virtual combination (S) and check it offers its download file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectVirtualCombination', baseContext);

      await foHummingbirdProductPage.selectDefaultAttributes(page, virtualCombination);

      const downloadFileName = await foHummingbirdProductPage.getProductDownloadFileName(page);
      expect(downloadFileName).to.contains(virtualFile);
    });

    it('should select the physical combination (M) and check it offers no download file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectPhysicalCombination', baseContext);

      await foHummingbirdProductPage.selectDefaultAttributes(page, physicalCombination);

      const downloadFileName = await foHummingbirdProductPage.getProductDownloadFileName(page);
      // The physical combination must not differ from the virtual one only by offering no download
      expect(downloadFileName).to.equal(physicalFileShouldStayEmpty);
      expect(downloadFileName).to.not.contains(virtualFile);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foHummingbirdProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  describe('Delete product', async () => {
    it('should delete the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
