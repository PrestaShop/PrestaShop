import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
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

const baseContext: string = 'functional_BO_catalog_products_virtualCombinations_CRUDVirtualCombinations';

/*
 * E2E scenario for the new `virtual_combinations` product type.
 *
 * Scenario:
 * 1. Create a `virtual_combinations` product in the BO.
 * 2. Generate 2 combinations (1 attribute "size" with 2 values).
 * 3. Upload a DISTINCT downloadable file to each combination.
 * 4. Save and assert each combination keeps its own file (download distinctness).
 * 5. (FO) Preview the product, select each combination and assert the
 *    download offered matches the file uploaded to that combination.
 *
 * IMPORTANT - companion library requirement:
 * This test depends on NEW page-object methods/selectors that must be added to the
 * external `@prestashop-core/ui-testing` library (see COMPANION_LIB_NOTES.md in this
 * folder). Calls to those not-yet-existing methods are flagged with a
 * `// COMPANION-LIB:` comment. Until the companion library PR lands, this file
 * compiles against the intended API but will only run green in CI once that PR
 * is merged and a shop is available. Do NOT assume it passes locally as-is.
 */
describe('BO - Catalog - Products : CRUD virtual combinations product', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Distinct downloadable file uploaded to each of the 2 combinations
  const firstCombinationFile: string = 'virtual_combination_1.txt';
  const secondCombinationFile: string = 'virtual_combination_2.txt';

  // Data to create the virtual_combinations product.
  // The single attribute "size" with two values produces exactly 2 combinations.
  const newProductData: FakerProduct = new FakerProduct({
    type: 'virtual_combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
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

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }

    // Generate two distinct text files, one per combination
    await Promise.all([
      utilsFile.createFile('./', firstCombinationFile, `Content of ${firstCombinationFile}`),
      utilsFile.createFile('./', secondCombinationFile, `Content of ${secondCombinationFile}`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
    await Promise.all([
      utilsFile.deleteFile(firstCombinationFile),
      utilsFile.deleteFile(secondCombinationFile),
    ]);
  });

  // 1 - Create the virtual_combinations product
  describe('Create virtual combinations product', async () => {
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

    it('should select \'Virtual product with combinations\' and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectVirtualCombinationsType', baseContext);

      // `selectProductType` is a generic "select by type string" helper, so the new
      // `virtual_combinations` value works without a new method as long as the option
      // is rendered in the modal (Task 8 added it to the BO selector).
      await boProductsPage.selectProductType(page, newProductData.type);

      // COMPANION-LIB: boProductsPage.virtualCombinationsProductDescription (getter)
      // A new description constant on the products page object is needed to assert the
      // modal copy for the new type. Falls back to a non-empty check meanwhile.
      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.not.be.equal('');
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2 - Generate the 2 combinations
  describe('Generate combinations', async () => {
    it('should go to \'Combinations\' tab and click on \'Attributes & Features\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAttributesAndFeaturesLink', baseContext);

      page = await boProductsCreateTabCombinationsPage.clickOnAttributesAndFeaturesLink(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should close \'Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeAttributesPage', baseContext);

      page = await boAttributesPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should set the attributes and check the generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setProductAttributes', baseContext);

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

  // 3 - Upload a distinct file to each combination
  describe('Upload a distinct virtual file to each combination', async () => {
    it('should open the edit modal of the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openFirstCombinationModal', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 1);
      expect(isVisible).to.eq(true);
    });

    it('should upload the first file to the first combination and save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadFirstCombinationFile', baseContext);

      // COMPANION-LIB: boProductsCreateTabCombinationsPage.setCombinationVirtualProductFile(page, firstCombinationFile)
      // New method: in the combination edit modal, the `virtual_combinations` type shows a
      // "Virtual product file" section (Task 7). This uploads the file then saves the modal
      // and returns the success message.
      const successMessage = await boProductsCreateTabCombinationsPage.setCombinationVirtualProductFile(
        page,
        firstCombinationFile,
      );
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should open the edit modal of the second combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openSecondCombinationModal', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 2);
      expect(isVisible).to.eq(true);
    });

    it('should upload the second file to the second combination and save', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uploadSecondCombinationFile', baseContext);

      // COMPANION-LIB: boProductsCreateTabCombinationsPage.setCombinationVirtualProductFile(page, secondCombinationFile)
      const successMessage = await boProductsCreateTabCombinationsPage.setCombinationVirtualProductFile(
        page,
        secondCombinationFile,
      );
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });
  });

  // 4 - Assert per-combination file distinctness in BO
  describe('Check per-combination files are distinct in BO', async () => {
    it('should re-open the first combination and read its uploaded file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCombinationFile', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 1);
      expect(isVisible).to.eq(true);

      // COMPANION-LIB: boProductsCreateTabCombinationsPage.getCombinationVirtualProductFileName(page)
      // New getter: returns the currently-attached virtual file name from the combination
      // edit modal. Used to assert combination 1 keeps file 1.
      const fileName = await boProductsCreateTabCombinationsPage.getCombinationVirtualProductFileName(page);
      expect(fileName).to.contains(firstCombinationFile.replace('.txt', ''));

      await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
    });

    it('should re-open the second combination and read its uploaded file name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondCombinationFile', baseContext);

      const isVisible = await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 2);
      expect(isVisible).to.eq(true);

      // COMPANION-LIB: boProductsCreateTabCombinationsPage.getCombinationVirtualProductFileName(page)
      const fileName = await boProductsCreateTabCombinationsPage.getCombinationVirtualProductFileName(page);
      // Distinctness assertion: combination 2 must carry file 2, not file 1.
      expect(fileName).to.contains(secondCombinationFile.replace('.txt', ''));
      expect(fileName).to.not.contains(firstCombinationFile.replace('.txt', ''));

      await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      const updateMessage = await boProductsCreatePage.saveProduct(page);
      expect(updateMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 5 - Check per-combination download in FO
  describe('Check per-combination download in FO', async () => {
    it('should preview the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      page = await boProductsCreatePage.previewProduct(page);
      await foHummingbirdProductPage.changeLanguage(page, 'en');

      const pageTitle = await foHummingbirdProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should select the first combination and check its download link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFirstCombinationDownloadFO', baseContext);

      const firstAttribute: ProductAttribute[] = [{name: 'size', value: 'S'}];
      await foHummingbirdProductPage.selectAttributes(page, 'radio', firstAttribute);

      // COMPANION-LIB: foHummingbirdProductPage.getProductDownloadFileName(page)
      // New getter: returns the file name of the downloadable file currently offered for the
      // selected combination on the FO product page. Used to assert FO surfaces the
      // combination-specific file.
      const downloadName = await foHummingbirdProductPage.getProductDownloadFileName(page);
      expect(downloadName).to.contains(firstCombinationFile.replace('.txt', ''));
    });

    it('should select the second combination and check a DIFFERENT download link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSecondCombinationDownloadFO', baseContext);

      const secondAttribute: ProductAttribute[] = [{name: 'size', value: 'M'}];
      await foHummingbirdProductPage.selectAttributes(page, 'radio', secondAttribute);

      // COMPANION-LIB: foHummingbirdProductPage.getProductDownloadFileName(page)
      const downloadName = await foHummingbirdProductPage.getProductDownloadFileName(page);
      // Distinctness assertion in the FO: switching combination switches the offered file.
      expect(downloadName).to.contains(secondCombinationFile.replace('.txt', ''));
      expect(downloadName).to.not.contains(firstCombinationFile.replace('.txt', ''));
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foHummingbirdProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  // 6 - Delete the product
  describe('Delete product', async () => {
    it('should delete the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
