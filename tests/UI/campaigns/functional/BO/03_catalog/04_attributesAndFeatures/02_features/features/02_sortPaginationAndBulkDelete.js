require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');
const featuresPage = require('@pages/BO/catalog/features');
const addFeaturePage = require('@pages/BO/catalog/features/addFeature');

// Import data
const {Feature} = require('@data/faker/featureAndValue');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_features_features_sortPaginationAndBulkDelete';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfAttributes = 0;

/*
Go to Attributes & Features page
Go to Features tab
Create 18 new features
Pagination next and previous
Sort features table by ID, Name and Position
Delete the created features by bulk actions
 */
describe('Sort and pagination features', async () => {
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

  it('should go to \'Catalog > Attributes & Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.attributesAndFeaturesLink,
    );

    await attributesPage.closeSfToolBar(page);

    const pageTitle = await attributesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should go to Features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await attributesPage.goToFeaturesPage(page);

    const pageTitle = await featuresPage.getPageTitle(page);
    await expect(pageTitle).to.contains(featuresPage.pageTitle);
  });

  // 1 : Create 19 new features
  const creationTests = new Array(19).fill(0, 0, 19);
  describe('Create new features in BO', async () => {
    creationTests.forEach((test, index) => {
      const createFeatureData = new Feature({Name: `todelete${index}`});
      it('should go to add new feature page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewFeaturePage${index}`, baseContext);

        await featuresPage.goToAddFeaturePage(page);

        const pageTitle = await addFeaturePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addFeaturePage.createPageTitle);
      });

      it(`should create feature n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewValue${index}`, baseContext);

        const textResult = await addFeaturePage.setFeature(page, createFeatureData);
        await expect(textResult).to.contains(attributesPage.successfulCreationMessage);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the item number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await featuresPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await featuresPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await featuresPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the item number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await featuresPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });
});
