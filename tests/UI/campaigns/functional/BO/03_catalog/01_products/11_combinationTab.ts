import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  foClassicProductPage,
  type Page,
  type ProductAttribute,
  type ProductCombinationOptions,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_combinationTab';

describe('BO - Catalog - Products : Combination tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create product with combinations
  const newProductData: FakerProduct = new FakerProduct({
    type: 'combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    status: true,
  });

  // Data to edit the first combination
  const firstCombinationData: ProductCombinationOptions = {
    reference: 'efghigk',
    impactOnPriceTExc: 20,
    quantity: 150,
  };

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Create product with combination', async () => {
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
      expect(isModalVisible).eq(true);
    });

    it('should select the product with combination and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

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

    it('should go to \'Combinations\' tab and click on \'Attributes & Features\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAttributes&FeaturesLink', baseContext);

      page = await boProductsCreateTabCombinationsPage.clickOnAttributesAndFeaturesLink(page);

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should close \'Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await boAttributesPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on learn more link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnLearnMore', baseContext);

      page = await boProductsCreateTabCombinationsPage.clickOnLearnMoreButton(page);

      const currentURL = await boProductsCreateTabCombinationsPage.getCurrentURL(page);
      expect(currentURL).to.contains('creatingaproductwithcombinations');

      page = await boProductsCreateTabCombinationsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on generate combination button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnGenerateCombinationButton', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.clickOnGenerateCombinationButton(page);
      expect(isModalVisible).eq(true);
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnCancelButton', baseContext);

      const isModalNotVisible = await boProductsCreateTabCombinationsPage.clickOnCancelButton(page);
      expect(isModalNotVisible).eq(true);
    });

    it('should create combination by checking size and color checkboxes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombination', baseContext);

      await boProductsCreateTabCombinationsPage.clickOnGenerateCombinationButton(page);

      let generateCombinationsButton = await boProductsCreateTabCombinationsPage.selectAllValues(page, 'size');
      expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(4));

      generateCombinationsButton = await boProductsCreateTabCombinationsPage.selectAllValues(page, 'color');
      expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(56));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(56));
    });
  });

  describe('Edit combinations', async () => {
    it('should edit the first combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editFirstCombination', baseContext);

      await boProductsCreateTabCombinationsPage.clickOnEditIcon(page, 1);

      const successMessage = await boProductsCreateTabCombinationsPage.editCombinationFromModal(page, firstCombinationData);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);
    });

    it('should click on next combination button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNextButton', baseContext);

      await boProductsCreateTabCombinationsPage.clickOnNextCombinationButton(page);

      const combinationName = await boProductsCreateTabCombinationsPage.getCombinationNameFromModal(page);
      expect(combinationName).to.equal('Size - S, Color - Taupe');
    });

    it('should click on previous combination button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPreviousButton', baseContext);

      await boProductsCreateTabCombinationsPage.clickOnPreviousCombinationButton(page);

      const combinationName = await boProductsCreateTabCombinationsPage.getCombinationNameFromModal(page);
      expect(combinationName).to.equal('Size - S, Color - Gray');
    });

    it('should close the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

      const isModalVisible = await boProductsCreateTabCombinationsPage.closeEditCombinationModal(page);
      expect(isModalVisible).eq(false);
    });
  });

  describe('Check out of stock options', async () => {
    [
      {args: {option: 'Deny orders', isAddToCartButtonVisible: false}},
      {args: {option: 'Allow orders', isAddToCartButtonVisible: true}},
      {args: {option: 'Use default behavior', isAddToCartButtonVisible: false}},
    ].forEach((test, index) => {
      it(`should check the '${test.args.option}' option`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkOrderOption${index}`, baseContext);

        await boProductsCreateTabCombinationsPage.setOptionWhenOutOfStock(page, test.args.option);

        const createProductMessage = await boProductsCreatePage.saveProduct(page);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should preview product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `previewProduct${index}`, baseContext);

        // Click on preview button
        page = await boProductsCreatePage.previewProduct(page);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.contains(newProductData.name);
      });

      it('should select combination and check the add to cart button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `selectCombination${index}`, baseContext);
        const firstCombination: ProductAttribute[] = [
          {
            name: 'size',
            value: 'L',
          },
          {
            name: 'color',
            value: 'White',
          },
        ];
        await foClassicProductPage.selectDefaultAttributes(page, firstCombination);

        const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
        expect(isVisible).to.eq(test.args.isAddToCartButtonVisible);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

        // Go back to BO
        page = await foClassicProductPage.closePage(browserContext, page, 0);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });
    });

    it('should click on edit default behaviour link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editBehaviour', baseContext);

      page = await boProductsCreateTabCombinationsPage.clickOnEditDefaultBehaviourLink(page);

      const pageTitle = await boProductSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await boProductSettingsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should set label when in stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setLabelWhenInStock', baseContext);

      await boProductsCreateTabCombinationsPage.setLabelWhenInStock(page, 'Product available');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should select combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCombination3', baseContext);
      const firstCombination: ProductAttribute[] = [
        {
          name: 'size',
          value: 'S',
        },
        {
          name: 'color',
          value: 'Taupe',
        },
      ];
      await foClassicProductPage.selectDefaultAttributes(page, firstCombination);

      const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isVisible).eq(false);
    });

    it('should check the product availability label', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCombination4', baseContext);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out-of-Stock');
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await boProductSettingsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the allow orders option and set Label when out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDenyOrder', baseContext);

      await boProductsCreateTabCombinationsPage.setOptionWhenOutOfStock(page, 'Allow orders');

      await boProductsCreateTabCombinationsPage.setLabelWhenOutOfStock(page, 'Out of stock');

      const createProductMessage = await boProductsCreatePage.saveProduct(page);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should select combination', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCombination5', baseContext);
      const firstCombination: ProductAttribute[] = [
        {
          name: 'size',
          value: 'L',
        },
        {
          name: 'color',
          value: 'Taupe',
        },
      ];
      await foClassicProductPage.selectDefaultAttributes(page, firstCombination);

      const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isVisible).eq(true);
    });

    it('should check the label of out of stock', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCombination6', baseContext);

      const availabilityLabel = await foClassicProductPage.getProductAvailabilityLabel(page);
      expect(availabilityLabel).to.contains('Out of stock');

      const isVisible = await foClassicProductPage.isAddToCartButtonEnabled(page);
      expect(isVisible).eq(true);
    });

    it('should close the page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO5', baseContext);

      page = await boProductSettingsPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });
  });

  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
