import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  boProductSettingsPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_productSettings_productsGeneral_maxSizeShortDescription';

/*
Update max size of short description to 5
Check the error message when the description size is more than 5 characters
Go back to the default max size short description
Check the validation message when the description is less than 800 characters
 */
describe('BO - Shop Parameters - Product Settings : Update max size of short description', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const productData: FakerProduct = new FakerProduct({type: 'standard', status: false});
  const maxSummarySizeValue: number = 5;
  const defaultSummarySizeValue: number = 800;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Update max size of short description', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    const tests = [
      {args: {descriptionSize: maxSummarySizeValue}},
      {args: {descriptionSize: defaultSummarySizeValue}},
    ];

    tests.forEach((test, index: number) => {
      it('should go to \'Shop parameters > Product Settings\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductSettingsPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.productSettingsLink,
        );
        await boProductSettingsPage.closeSfToolBar(page);

        const pageTitle = await boProductSettingsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductSettingsPage.pageTitle);
      });

      it(`should update max size of short description to ${test.args.descriptionSize}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `updateMaxSizeSummaryValue${index}`, baseContext);

        const result = await boProductSettingsPage.setMaxSizeOfSummaryValue(page, test.args.descriptionSize);
        expect(result).to.contains(boProductSettingsPage.successfulUpdateMessage);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCatalogProductsPage${index}`, baseContext);

        await boProductSettingsPage.goToSubMenu(
          page,
          boProductSettingsPage.catalogParentLink,
          boProductSettingsPage.productsLink,
        );

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should click on new product button and go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductPage${index}`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.be.equal(true);

        await boProductsPage.selectProductType(page, productData.type);
        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      if (test.args.descriptionSize === maxSummarySizeValue) {
        it(`should create a product with a summary more than ${test.args.descriptionSize} characters
      and check the error message`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index}`, baseContext);

          await boProductsCreateTabDescriptionPage.setProductDescription(page, productData);

          const errorMessage = await boProductsCreatePage.getErrorMessageWhenSummaryIsTooLong(page);
          expect(errorMessage).to.contains(
            boProductsCreatePage.errorMessageWhenSummaryTooLong(maxSummarySizeValue),
          );
        });
      } else {
        it(`should create a product with a summary less than ${test.args.descriptionSize} characters`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `testSummarySize${index}`, baseContext);

          const successMessage = await boProductsCreatePage.setProduct(page, productData);
          expect(successMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
        });
      }
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await boProductsCreatePage.deleteProduct(page);
      expect(testResult).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
